<?php
// config/database.php - CarEngine POS Database Connection & Auto-Initializer

class Database {
    private static $instance = null;
    private $pdo;
    
    
    private $host = "localhost";
    private $db_name = "carrent";
    private $username = "root";
    private $password = "";
    private $driver = "mysql";

    private function __construct() {
        // Read environment variables with fallback to local defaults (Support Docker Apache $_SERVER/$_ENV)
        // If running via XAMPP, environment variables won't exist, so it falls back to localhost and empty password.
        $this->host = $_SERVER['DB_HOST'] ?? $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: "localhost";
        $this->db_name = $_SERVER['DB_NAME'] ?? $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: "carrent";
        $this->username = $_SERVER['DB_USER'] ?? $_ENV['DB_USER'] ?? getenv('DB_USER') ?: "root";
        $pass = $_SERVER['DB_PASS'] ?? $_ENV['DB_PASS'] ?? getenv('DB_PASS');
        $this->password = $pass !== false ? $pass : "";

        try {
            // Attempt MySQL PDO connection
            $dsn = "mysql:host={$this->host};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);

            // Ensure database exists
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $this->pdo->exec("USE `{$this->db_name}`;");

            $this->autoInitializeMySQLSchema();

        } catch (PDOException $e) {
            die(json_encode([
                "status" => "error",
                "message" => "Database Connection Error: " . $e->getMessage() . " (Host: {$this->host})"
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function getDriver() {
        return $this->driver;
    }

    private function autoInitializeMySQLSchema() {
        // Users Table
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `fullname` VARCHAR(100) NOT NULL,
            `email` VARCHAR(100) DEFAULT NULL,
            `phone` VARCHAR(20) DEFAULT NULL,
            `profile_image` VARCHAR(255) DEFAULT NULL,
            `driver_license_image` VARCHAR(255) DEFAULT NULL,
            `national_id` VARCHAR(30) DEFAULT NULL,
            `driver_license` VARCHAR(50) DEFAULT NULL,
            `address` TEXT DEFAULT NULL,
            `role` VARCHAR(20) DEFAULT 'member',
            `level` INT NOT NULL DEFAULT 1,
            `is_verified` INT NOT NULL DEFAULT 0,
            `id_card_image` VARCHAR(255) DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Safely alter table if upgrading from earlier version
        try { $this->pdo->exec("ALTER TABLE `users` ADD COLUMN `level` INT NOT NULL DEFAULT 1;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `users` ADD COLUMN `is_verified` INT NOT NULL DEFAULT 0;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `users` ADD COLUMN `id_card_image` LONGTEXT DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `users` MODIFY `id_card_image` LONGTEXT DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `users` ADD COLUMN `email` VARCHAR(100) DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `users` ADD COLUMN `phone` VARCHAR(20) DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `users` ADD COLUMN `profile_image` LONGTEXT DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `users` MODIFY `profile_image` LONGTEXT DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `users` ADD COLUMN `driver_license_image` LONGTEXT DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `users` MODIFY `driver_license_image` LONGTEXT DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `users` ADD COLUMN `national_id` VARCHAR(30) DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `users` ADD COLUMN `driver_license` VARCHAR(50) DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `users` ADD COLUMN `address` TEXT DEFAULT NULL;"); } catch (Exception $ex) {}

        // Auto-Deduplicate Users table to fix duplicate users during export/import
        try {
            $this->pdo->exec("DELETE u1 FROM `users` u1 INNER JOIN `users` u2 WHERE u1.id > u2.id AND LOWER(u1.username) = LOWER(u2.username);");
            $this->pdo->exec("ALTER TABLE `users` ADD UNIQUE KEY `uq_users_username` (`username`);");
        } catch (Exception $ex) {}

        // Removed admin user seeding as requested

        // Customers Table
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS `customers` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT DEFAULT NULL,
            `name` VARCHAR(100) NOT NULL,
            `phone` VARCHAR(20) NOT NULL,
            `email` VARCHAR(100) DEFAULT NULL,
            `national_id` VARCHAR(30) DEFAULT NULL,
            `driver_license` VARCHAR(50) DEFAULT NULL,
            `address` TEXT DEFAULT NULL,
            `id_card_image` LONGTEXT DEFAULT NULL,
            `driver_license_image` LONGTEXT DEFAULT NULL,
            `profile_image` LONGTEXT DEFAULT NULL,
            `is_verified` INT NOT NULL DEFAULT 0,
            `notes` TEXT DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        try { $this->pdo->exec("ALTER TABLE `customers` ADD COLUMN `is_verified` INT NOT NULL DEFAULT 0;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `customers` ADD COLUMN `id_card_image` LONGTEXT DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `customers` MODIFY `id_card_image` LONGTEXT DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `customers` ADD COLUMN `email` VARCHAR(100) DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `customers` ADD COLUMN `driver_license_image` LONGTEXT DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `customers` MODIFY `driver_license_image` LONGTEXT DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `customers` ADD COLUMN `profile_image` LONGTEXT DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `customers` MODIFY `profile_image` LONGTEXT DEFAULT NULL;"); } catch (Exception $ex) {}
        try { $this->pdo->exec("ALTER TABLE `customers` ADD COLUMN `user_id` INT DEFAULT NULL AFTER `id`;"); } catch (Exception $ex) {}
        
        // Auto-link existing customers to users if they match
        try { $this->pdo->exec("UPDATE `customers` c JOIN `users` u ON c.phone = u.username OR c.phone = u.phone OR c.id = u.id SET c.user_id = u.id WHERE c.user_id IS NULL;"); } catch(Exception $ex) {}

        // Auto-merge duplicate customer records with same phone or user_id
        try {
            $this->pdo->exec("UPDATE `rentals` r JOIN `customers` c1 ON r.customer_id = c1.id JOIN `customers` c2 ON ((c1.phone != '' AND c1.phone = c2.phone) OR (c1.user_id IS NOT NULL AND c1.user_id = c2.user_id)) AND c1.id > c2.id SET r.customer_id = c2.id;");
            $this->pdo->exec("DELETE c1 FROM `customers` c1 INNER JOIN `customers` c2 WHERE c1.id > c2.id AND ((c1.phone != '' AND c1.phone = c2.phone) OR (c1.user_id IS NOT NULL AND c1.user_id = c2.user_id));");
        } catch (Exception $ex) {}

        // Removed default customers seeding as requested

        // Vehicles Table
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS `vehicles` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `plate_number` VARCHAR(20) NOT NULL UNIQUE,
            `brand` VARCHAR(50) NOT NULL,
            `model` VARCHAR(50) NOT NULL,
            `category` VARCHAR(50) DEFAULT 'Sedan',
            `daily_rate` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `hourly_rate` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `deposit_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `status` VARCHAR(20) DEFAULT 'Available',
            `image_url` LONGTEXT DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Safely alter category and image_url columns
        try {
            $this->pdo->exec("SET SESSION sql_mode = '';");
            $this->pdo->exec("ALTER TABLE `vehicles` MODIFY `category` VARCHAR(50) NOT NULL DEFAULT 'Sedan';");
            $this->pdo->exec("ALTER TABLE `vehicles` MODIFY `image_url` LONGTEXT DEFAULT NULL;");
            // Auto-heal vehicle categories for motorcycles
            $this->pdo->exec("UPDATE `vehicles` SET `category` = 'Motorcycle' WHERE (category = '' OR category IS NULL OR category = 'Sedan') AND (
                LOWER(brand) LIKE '%honda%' AND (LOWER(model) LIKE '%wave%' OR LOWER(model) LIKE '%click%' OR LOWER(model) LIKE '%pcx%' OR LOWER(model) LIKE '%forza%' OR LOWER(model) LIKE '%scoopy%' OR LOWER(model) LIKE '%rebel%' OR LOWER(model) LIKE '%cub%' OR LOWER(model) LIKE '%adv%' OR LOWER(model) LIKE '%lead%' OR LOWER(model) LIKE '%giorno%' OR LOWER(model) LIKE '%110%')
                OR LOWER(brand) LIKE '%yamaha%' AND (LOWER(model) LIKE '%xmax%' OR LOWER(model) LIKE '%nmax%' OR LOWER(model) LIKE '%filano%' OR LOWER(model) LIKE '%aerox%' OR LOWER(model) LIKE '%fazzio%')
                OR LOWER(brand) LIKE '%vespa%'
                OR LOWER(brand) LIKE '%kawasaki%'
                OR LOWER(brand) LIKE '%gpx%'
                OR LOWER(brand) LIKE '%ducati%'
                OR LOWER(brand) LIKE '%dream%'
                OR LOWER(brand) LIKE '%dreeam%'
                OR LOWER(model) LIKE '%wave%'
                OR LOWER(model) LIKE '%110%'
                OR LOWER(model) LIKE '%cub%'
                OR LOWER(model) LIKE '%dream%'
                OR LOWER(model) LIKE '%dreeam%'
                OR LOWER(model) LIKE '%click%'
                OR LOWER(model) LIKE '%pcx%'
                OR LOWER(model) LIKE '%forza%'
                OR LOWER(model) LIKE '%xmax%'
                OR LOWER(model) LIKE '%scooter%'
                OR LOWER(model) LIKE '%motorcycle%'
            );");
        } catch (Exception $ex) {}

        // Seed Vehicles if empty
        $stmtVeh = $this->pdo->query("SELECT COUNT(*) FROM `vehicles`");
        if ($stmtVeh->fetchColumn() == 0) {
            $this->pdo->exec("INSERT INTO `vehicles` (`plate_number`, `brand`, `model`, `category`, `daily_rate`, `hourly_rate`, `deposit_amount`, `status`, `image_url`) VALUES
            ('กข 1234 กทม', 'Toyota', 'Camry 2.5 HEV Premium', 'Sedan', 2500.00, 300.00, 5000.00, 'Available', 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=600&auto=format&fit=crop&q=80'),
            ('ขก 9999 กทม', 'Honda', 'CR-V e:HEV RS', 'SUV', 3200.00, 400.00, 5000.00, 'Available', 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=600&auto=format&fit=crop&q=80'),
            ('EV 8888 กทม', 'BYD', 'SEAL Performance AWD', 'EV', 3500.00, 450.00, 6000.00, 'Available', 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&auto=format&fit=crop&q=80'),
            ('รวย 7777 กทม', 'Mercedes-Benz', 'E-Class E300e AMG', 'Luxury', 5900.00, 800.00, 10000.00, 'Available', 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=600&auto=format&fit=crop&q=80'),
            ('ฮฮ 5555 กทม', 'Toyota', 'Alphard 2.5 VIP', 'Van', 6500.00, 900.00, 10000.00, 'Available', 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=600&auto=format&fit=crop&q=80'),
            ('กท 4321 นนทบุรี', 'Honda', 'Civic FE 1.5 Turbo RS', 'Sedan', 1800.00, 250.00, 3000.00, 'Available', 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?w=600&auto=format&fit=crop&q=80'),
            ('มข 111 กทม', 'Honda', 'Forza 350', 'Motorcycle', 800.00, 100.00, 1500.00, 'Available', 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=600&auto=format&fit=crop&q=80'),
            ('มข 222 กทม', 'Yamaha', 'XMAX 300 Tech MAX', 'Motorcycle', 850.00, 110.00, 1500.00, 'Available', 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?w=600&auto=format&fit=crop&q=80')");
        }

        // Rentals Table
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS `rentals` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `rental_code` VARCHAR(30) NOT NULL UNIQUE,
            `customer_id` INT NOT NULL,
            `vehicle_id` INT NOT NULL,
            `start_date` DATETIME NOT NULL,
            `end_date` DATETIME NOT NULL,
            `actual_return_date` DATETIME DEFAULT NULL,
            `daily_rate` DECIMAL(10,2) NOT NULL,
            `total_days` INT NOT NULL DEFAULT 1,
            `total_amount` DECIMAL(10,2) NOT NULL,
            `deposit_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `extra_charges` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `discount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `grand_total` DECIMAL(10,2) NOT NULL,
            `status` VARCHAR(20) DEFAULT 'Active',
            `notes` TEXT DEFAULT NULL,
            `created_by` INT DEFAULT 1,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Payments Table
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS `payments` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `rental_id` INT NOT NULL,
            `payment_method` VARCHAR(30) DEFAULT 'Cash',
            `amount` DECIMAL(10,2) NOT NULL,
            `payment_type` VARCHAR(30) DEFAULT 'RentalFee',
            `payment_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `remarks` VARCHAR(255) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Drop Settings Table (No longer used)
        try {
            $this->pdo->exec("DROP TABLE IF EXISTS `settings`;");
        } catch (Exception $ex) {}
    }

    private function autoInitializeSQLiteSchema() {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            fullname TEXT NOT NULL,
            role TEXT DEFAULT 'member',
            level INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        try {
            $this->pdo->exec("ALTER TABLE users ADD COLUMN level INTEGER DEFAULT 1;");
        } catch (Exception $ex) {}

        // Auto-Deduplicate Users table in SQLite
        try {
            $this->pdo->exec("DELETE FROM users WHERE id NOT IN (SELECT MIN(id) FROM users GROUP BY LOWER(username));");
        } catch (Exception $ex) {}

        // Removed SQLite admin user seeding as requested

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS customers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            phone TEXT NOT NULL,
            national_id TEXT,
            driver_license TEXT,
            address TEXT,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        // Removed SQLite default customers seeding as requested

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS vehicles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            plate_number TEXT NOT NULL UNIQUE,
            brand TEXT NOT NULL,
            model TEXT NOT NULL,
            category TEXT DEFAULT 'Sedan',
            daily_rate REAL DEFAULT 0.0,
            hourly_rate REAL DEFAULT 0.0,
            deposit_amount REAL DEFAULT 0.0,
            status TEXT DEFAULT 'Available',
            image_url TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        try {
            $this->pdo->exec("UPDATE vehicles SET category = 'Motorcycle' WHERE (category = '' OR category IS NULL OR category = 'Sedan') AND (
                LOWER(brand) LIKE '%honda%' AND (LOWER(model) LIKE '%wave%' OR LOWER(model) LIKE '%click%' OR LOWER(model) LIKE '%pcx%' OR LOWER(model) LIKE '%forza%' OR LOWER(model) LIKE '%scoopy%' OR LOWER(model) LIKE '%rebel%' OR LOWER(model) LIKE '%cub%' OR LOWER(model) LIKE '%adv%' OR LOWER(model) LIKE '%lead%' OR LOWER(model) LIKE '%giorno%' OR LOWER(model) LIKE '%110%')
                OR LOWER(brand) LIKE '%yamaha%' AND (LOWER(model) LIKE '%xmax%' OR LOWER(model) LIKE '%nmax%' OR LOWER(model) LIKE '%filano%' OR LOWER(model) LIKE '%aerox%' OR LOWER(model) LIKE '%fazzio%')
                OR LOWER(brand) LIKE '%vespa%'
                OR LOWER(brand) LIKE '%kawasaki%'
                OR LOWER(brand) LIKE '%gpx%'
                OR LOWER(brand) LIKE '%ducati%'
                OR LOWER(brand) LIKE '%dream%'
                OR LOWER(brand) LIKE '%dreeam%'
                OR LOWER(model) LIKE '%wave%'
                OR LOWER(model) LIKE '%110%'
                OR LOWER(model) LIKE '%cub%'
                OR LOWER(model) LIKE '%dream%'
                OR LOWER(model) LIKE '%dreeam%'
                OR LOWER(model) LIKE '%click%'
                OR LOWER(model) LIKE '%pcx%'
                OR LOWER(model) LIKE '%forza%'
                OR LOWER(model) LIKE '%xmax%'
                OR LOWER(model) LIKE '%scooter%'
                OR LOWER(model) LIKE '%motorcycle%'
            );");
        } catch (Exception $ex) {}

        $stmtVeh = $this->pdo->query("SELECT COUNT(*) FROM vehicles");
        if ($stmtVeh->fetchColumn() == 0) {
            $this->pdo->exec("INSERT INTO vehicles (plate_number, brand, model, category, daily_rate, hourly_rate, deposit_amount, status, image_url) VALUES
            ('กข 1234 กทม', 'Toyota', 'Camry 2.5 HEV Premium', 'Sedan', 2500.00, 300.00, 5000.00, 'Available', 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=600&auto=format&fit=crop&q=80'),
            ('ขก 9999 กทม', 'Honda', 'CR-V e:HEV RS', 'SUV', 3200.00, 400.00, 5000.00, 'Available', 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=600&auto=format&fit=crop&q=80'),
            ('EV 8888 กทม', 'BYD', 'SEAL Performance AWD', 'EV', 3500.00, 450.00, 6000.00, 'Available', 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&auto=format&fit=crop&q=80'),
            ('รวย 7777 กทม', 'Mercedes-Benz', 'E-Class E300e AMG', 'Luxury', 5900.00, 800.00, 10000.00, 'Available', 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=600&auto=format&fit=crop&q=80'),
            ('ฮฮ 5555 กทม', 'Toyota', 'Alphard 2.5 VIP', 'Van', 6500.00, 900.00, 10000.00, 'Available', 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=600&auto=format&fit=crop&q=80'),
            ('กท 4321 นนทบุรี', 'Honda', 'Civic FE 1.5 Turbo RS', 'Sedan', 1800.00, 250.00, 3000.00, 'Available', 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?w=600&auto=format&fit=crop&q=80'),
            ('มข 111 กทม', 'Honda', 'Forza 350', 'Motorcycle', 800.00, 100.00, 1500.00, 'Available', 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=600&auto=format&fit=crop&q=80'),
            ('มข 222 กทม', 'Yamaha', 'XMAX 300 Tech MAX', 'Motorcycle', 850.00, 110.00, 1500.00, 'Available', 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?w=600&auto=format&fit=crop&q=80')");
        }

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS rentals (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            rental_code TEXT NOT NULL UNIQUE,
            customer_id INTEGER NOT NULL,
            vehicle_id INTEGER NOT NULL,
            start_date DATETIME NOT NULL,
            end_date DATETIME NOT NULL,
            actual_return_date DATETIME,
            daily_rate REAL NOT NULL,
            total_days INTEGER DEFAULT 1,
            total_amount REAL NOT NULL,
            deposit_amount REAL DEFAULT 0.0,
            extra_charges REAL DEFAULT 0.0,
            discount REAL DEFAULT 0.0,
            grand_total REAL NOT NULL,
            status TEXT DEFAULT 'Active',
            notes TEXT,
            created_by INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS payments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            rental_id INTEGER NOT NULL,
            payment_method TEXT DEFAULT 'Cash',
            amount REAL NOT NULL,
            payment_type TEXT DEFAULT 'RentalFee',
            payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            remarks TEXT
        );");
        // Drop Settings Table (No longer used)
        try {
            $this->pdo->exec("DROP TABLE IF EXISTS settings;");
        } catch (Exception $ex) {}
    }
}
