<?php
// api/auth.php - Secure Authentication & Role/Level Permission Controller
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $input['action'] ?? $action;

    // 1. LOGIN ACTION (STRICT DATABASE PASSWORD_VERIFY & LEVEL CHECK)
    if ($action === 'login') {
        $username = trim($input['username'] ?? '');
        $password = trim($input['password'] ?? '');

        if (empty($username) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'กรุณากรอก ชื่อผู้ใช้/เบอร์โทรศัพท์ และ รหัสผ่าน']);
            exit;
        }

        // Query User by Username OR Phone Number
        $cleanPhone = preg_replace('/[^0-9]/', '', $username);

        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR phone = ? OR (REPLACE(REPLACE(phone, '-', ''), ' ', '') = ? AND ? != '')");
        $stmt->execute([$username, $username, $cleanPhone, $cleanPhone]);
        $user = $stmt->fetch();

        // Verify password hash against database record (or fallback to plain text if manually inserted via DB)
        $isPasswordValid = false;
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $isPasswordValid = true;
            } elseif ($user['password'] === $password) { // Plain text match (e.g. from phpMyAdmin)
                $isPasswordValid = true;
                // Auto-upgrade to bcrypt hash
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $db->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$newHash, $user['id']]);
            }
        }

        if ($isPasswordValid) {
            
            // Find linked customer profile if any
            $stmtCust = $db->prepare("SELECT id, phone, national_id, driver_license, address, id_card_image, is_verified FROM customers WHERE name = ? OR phone = ? LIMIT 1");
            $stmtCust->execute([$user['fullname'], $user['username']]);
            $cust = $stmtCust->fetch();

            $userLevel = intval($user['level'] ?? 0);
            if ($user['role'] === 'admin') {
                $userLevel = max(100, $userLevel);
            } else if ($user['role'] === 'staff') {
                $userLevel = max(50, $userLevel);
            } else {
                if ($userLevel <= 0) $userLevel = 1;
            }

            $isVerified = intval($user['is_verified'] ?? $cust['is_verified'] ?? 0);
            $idCardImage = $user['id_card_image'] ?? $cust['id_card_image'] ?? '';

            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'fullname' => $user['fullname'],
                'role' => $user['role'],
                'level' => $userLevel, // 100 = Super Admin, 50 = Staff, 1 = Member
                'is_verified' => $isVerified, // 1 = Verified (Green), 0 = Unverified
                'id_card_image' => $idCardImage,
                'profile_image' => $user['profile_image'] ?? $cust['profile_image'] ?? '',
                'driver_license_image' => $user['driver_license_image'] ?? $cust['driver_license_image'] ?? '',
                'customer_id' => $cust['id'] ?? null,
                'phone' => $cust['phone'] ?? $user['username'],
                'national_id' => $cust['national_id'] ?? '',
                'driver_license' => $cust['driver_license'] ?? '',
                'address' => $cust['address'] ?? ''
            ];

            echo json_encode([
                'status' => 'success',
                'message' => 'เข้าสู่ระบบสำเร็จ',
                'user' => $_SESSION['user']
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
        }
        exit;
    }

    // 2. MEMBER REGISTRATION (HASHED PASSWORD, LEVEL = 1)
    if ($action === 'register') {
        $fullname = trim($input['fullname'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $username = trim($input['username'] ?? '');
        $password = trim($input['password'] ?? '');
        $national_id = trim($input['national_id'] ?? '');
        $driver_license = trim($input['driver_license'] ?? '');

        if (empty($fullname) || empty($phone) || empty($username) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลสำคัญให้ครบถ้วน (ชื่อ, เบอร์โทร, Username, Password)']);
            exit;
        }

        // 1. Check Password safety (password must not match username or phone number)
        if (strtolower($password) === strtolower($username) || strtolower($password) === strtolower($phone)) {
            echo json_encode(['status' => 'error', 'message' => 'รหัสผ่าน (Password) ไม่ควรตั้งซ้ำหรือเหมือนกับ ชื่อผู้ใช้ (Username) หรือ เบอร์โทรศัพท์']);
            exit;
        }

        if (mb_strlen($password) < 4) {
            echo json_encode(['status' => 'error', 'message' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 4 ตัวอักษรขึ้นไป']);
            exit;
        }

        // 2. Check Duplicate Username in users table
        $stmtChkUser = $db->prepare("SELECT COUNT(*) FROM users WHERE LOWER(username) = LOWER(?)");
        $stmtChkUser->execute([$username]);
        if ($stmtChkUser->fetchColumn() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'ชื่อผู้ใช้ (Username: ' . htmlspecialchars($username) . ') นี้ถูกสมัครใช้งานแล้ว โปรดตั้ง Username อื่น']);
            exit;
        }

        // 3. Check Duplicate Phone Number (only in users table)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        $stmtChkPhoneUser = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR phone = ? OR (REPLACE(REPLACE(phone, '-', ''), ' ', '') = ? AND ? != '')");
        $stmtChkPhoneUser->execute([$phone, $phone, $cleanPhone, $cleanPhone]);
        $phoneInUser = $stmtChkPhoneUser->fetchColumn();

        if ($phoneInUser > 0) {
            echo json_encode(['status' => 'error', 'message' => 'เบอร์โทรศัพท์หรือชื่อผู้ใช้นี้ (' . htmlspecialchars($phone) . ') เคยลงทะเบียนใช้งานแล้ว']);
            exit;
        }

        try {
            $db->beginTransaction();

            // Insert User (Member Level = 1)
            $passHash = password_hash($password, PASSWORD_DEFAULT);
            $stmtUser = $db->prepare("INSERT INTO users (username, password, fullname, phone, role, level) VALUES (?, ?, ?, ?, 'member', 1)");
            $stmtUser->execute([$username, $passHash, $fullname, $phone]);
            $userId = $db->lastInsertId();

            // Check if customer already exists (created by admin previously)
            $stmtExistCust = $db->prepare("SELECT id FROM customers WHERE phone = ? OR (REPLACE(REPLACE(phone, '-', ''), ' ', '') = ? AND ? != '') LIMIT 1");
            $stmtExistCust->execute([$phone, $cleanPhone, $cleanPhone]);
            $existingCust = $stmtExistCust->fetch();

            if ($existingCust) {
                $customerId = $existingCust['id'];
                // Update existing customer info and link to user_id
                $stmtUpdCust = $db->prepare("UPDATE customers SET user_id = ?, name = IF(name='', ?, name), national_id = IF(national_id IS NULL OR national_id='', ?, national_id), driver_license = IF(driver_license IS NULL OR driver_license='', ?, driver_license) WHERE id = ?");
                $stmtUpdCust->execute([$userId, $fullname, $national_id, $driver_license, $customerId]);
            } else {
                // Insert Customer record (Attempt to match User ID)
                try {
                    $stmtCust = $db->prepare("INSERT INTO customers (id, user_id, name, phone, national_id, driver_license) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmtCust->execute([$userId, $userId, $fullname, $phone, $national_id, $driver_license]);
                    $customerId = $userId;
                } catch (Exception $ex) {
                    // Fallback to Auto-Increment if ID is already taken by a POS customer
                    $stmtCust = $db->prepare("INSERT INTO customers (user_id, name, phone, national_id, driver_license) VALUES (?, ?, ?, ?, ?)");
                    $stmtCust->execute([$userId, $fullname, $phone, $national_id, $driver_license]);
                    $customerId = $db->lastInsertId();
                }
            }

            $db->commit();

            // Set Session
            $_SESSION['user'] = [
                'id' => $userId,
                'username' => $username,
                'fullname' => $fullname,
                'role' => 'member',
                'level' => 1,
                'customer_id' => $customerId,
                'phone' => $phone,
                'national_id' => $national_id,
                'driver_license' => $driver_license
            ];

            echo json_encode([
                'status' => 'success',
                'message' => 'สมัครสมาชิกและเข้าสู่ระบบสำเร็จ!',
                'user' => $_SESSION['user']
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการสมัครสมาชิก: ' . $e->getMessage()]);
        }
        exit;
    }

    // 3. LOGOUT ACTION
    if ($action === 'logout') {
        unset($_SESSION['user']);
        session_destroy();
        echo json_encode(['status' => 'success', 'message' => 'ออกจากระบบเรียบร้อย']);
        exit;
    }
}

// 4. CHECK AUTH STATUS
if ($action === 'check') {
    if (isset($_SESSION['user'])) {
        echo json_encode(['status' => 'success', 'logged_in' => true, 'user' => $_SESSION['user']]);
    } else {
        echo json_encode(['status' => 'success', 'logged_in' => false]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid Request']);
