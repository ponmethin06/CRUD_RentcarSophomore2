<?php
require_once __DIR__ . '/../config/database.php';
$db = Database::getInstance()->getConnection();
$users = $db->query("SELECT id, fullname FROM users")->fetchAll();
$customers = $db->query("SELECT id, name FROM customers")->fetchAll();
echo json_encode(['users' => $users, 'customers' => $customers]);
