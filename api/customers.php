<?php
// api/customers.php - Real-Time Customer CRUD & Verification Management REST API
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

$currentUser = $_SESSION['user'] ?? null;
$isAdmin = $currentUser && (($currentUser['level'] ?? 0) >= 50 || ($currentUser['role'] ?? '') === 'admin');

if ($method === 'GET') {
    $search = trim($_GET['search'] ?? '');
    $id = intval($_GET['id'] ?? 0);

    if ($id > 0) {
        $stmt = $db->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        $customer = $stmt->fetch();

        if ($customer) {
            // Also fetch customer rental history
            $stmtHistory = $db->prepare("
                SELECT r.*, v.brand, v.model, v.plate_number
                FROM rentals r
                JOIN vehicles v ON r.vehicle_id = v.id
                WHERE r.customer_id = ?
                ORDER BY r.id DESC
            ");
            $stmtHistory->execute([$id]);
            $customer['history'] = $stmtHistory->fetchAll();

            echo json_encode(['status' => 'success', 'data' => $customer], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลลูกค้า']);
        }
        exit;
    }

    $query = "SELECT * FROM customers WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $query .= " AND (name LIKE ? OR phone LIKE ? OR national_id LIKE ? OR driver_license LIKE ?)";
        $term = "%{$search}%";
        $params = [$term, $term, $term, $term];
    }

    $query .= " ORDER BY id DESC";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $customers = $stmt->fetchAll();

    echo json_encode(['status' => 'success', 'data' => $customers], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $input['action'] ?? 'create';

    if ($action === 'create') {
        $name = trim($input['name'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $national_id = trim($input['national_id'] ?? '');
        $driver_license = trim($input['driver_license'] ?? '');
        $address = trim($input['address'] ?? '');
        $id_card_image = trim($input['id_card_image'] ?? '');
        $is_verified = intval($input['is_verified'] ?? 0);
        $notes = trim($input['notes'] ?? '');

        if (empty($name) || empty($phone)) {
            echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกชื่อและเบอร์โทรศัพท์ลูกค้า']);
            exit;
        }

        // Handle uploaded file if present
        if (!empty($_FILES['id_card_file']['tmp_name'])) {
            $uploadDir = __DIR__ . '/../assets/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['id_card_file']['name'], PATHINFO_EXTENSION);
            $filename = 'cust_idcard_' . time() . '_' . rand(100, 999) . '.' . $ext;
            if (move_uploaded_file($_FILES['id_card_file']['tmp_name'], $uploadDir . $filename)) {
                $id_card_image = 'assets/uploads/' . $filename;
            }
        }

        // Check if customer already exists by phone or user_id to prevent duplicates
        $userId = $currentUser['id'] ?? null;
        $existingCust = null;
        if ($userId) {
            $stmtChk = $db->prepare("SELECT id FROM customers WHERE user_id = ? LIMIT 1");
            $stmtChk->execute([$userId]);
            $existingCust = $stmtChk->fetch();
        }
        if (!$existingCust && !empty($phone)) {
            $stmtChk = $db->prepare("SELECT id FROM customers WHERE phone = ? LIMIT 1");
            $stmtChk->execute([$phone]);
            $existingCust = $stmtChk->fetch();
        }

        if ($existingCust) {
            $custId = $existingCust['id'];
            $stmtUpd = $db->prepare("UPDATE customers SET user_id = COALESCE(user_id, ?), name = ?, national_id = IF(? != '', ?, national_id), driver_license = IF(? != '', ?, driver_license), address = IF(? != '', ?, address), id_card_image = IF(? != '', ?, id_card_image), notes = IF(? != '', ?, notes) WHERE id = ?");
            $stmtUpd->execute([$userId, $name, $national_id, $national_id, $driver_license, $driver_license, $address, $address, $id_card_image, $id_card_image, $notes, $notes, $custId]);

            echo json_encode([
                'status' => 'success',
                'message' => 'อัปเดตข้อมูลลูกค้าเดิมเรียบร้อย',
                'id' => $custId
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            $stmt = $db->prepare("INSERT INTO customers (user_id, name, phone, national_id, driver_license, address, id_card_image, is_verified, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $name, $phone, $national_id, $driver_license, $address, $id_card_image, $is_verified, $notes]);
            $newId = $db->lastInsertId();

            echo json_encode([
                'status' => 'success',
                'message' => 'เพิ่มข้อมูลลูกค้าสำเร็จ',
                'id' => $newId
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'update') {
        $id = intval($input['id'] ?? 0);
        $name = trim($input['name'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $national_id = trim($input['national_id'] ?? '');
        $driver_license = trim($input['driver_license'] ?? '');
        $address = trim($input['address'] ?? '');
        $id_card_image = trim($input['id_card_image'] ?? '');
        $is_verified = intval($input['is_verified'] ?? 0);
        $notes = trim($input['notes'] ?? '');

        if ($id <= 0 || empty($name) || empty($phone)) {
            echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน']);
            exit;
        }

        // Handle uploaded file if present
        if (!empty($_FILES['id_card_file']['tmp_name'])) {
            $uploadDir = __DIR__ . '/../assets/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['id_card_file']['name'], PATHINFO_EXTENSION);
            $filename = 'cust_idcard_' . time() . '_' . rand(100, 999) . '.' . $ext;
            if (move_uploaded_file($_FILES['id_card_file']['tmp_name'], $uploadDir . $filename)) {
                $id_card_image = 'assets/uploads/' . $filename;
            }
        }

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("UPDATE customers SET name=?, phone=?, national_id=?, driver_license=?, address=?, id_card_image=?, is_verified=?, notes=? WHERE id=?");
            $stmt->execute([$name, $phone, $national_id, $driver_license, $address, $id_card_image, $is_verified, $notes, $id]);

            // Sync with users table
            $stmtU = $db->prepare("UPDATE users SET fullname=?, id_card_image=?, is_verified=? WHERE fullname=? OR username=? OR phone=?");
            $stmtU->execute([$name, $id_card_image, $is_verified, $name, $phone, $phone]);

            $db->commit();

            echo json_encode(['status' => 'success', 'message' => 'อัปเดตข้อมูลลูกค้าเรียบร้อยแล้ว'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
        exit;
    }

    // TOGGLE VERIFY STATUS
    if ($action === 'toggle_verify') {
        if (!$isAdmin) {
            echo json_encode(['status' => 'error', 'message' => 'เฉพาะแอดมินเท่านั้นที่มีสิทธิ์ตรวจอนุมัติยืนยันตัวตน']);
            exit;
        }

        $id = intval($input['id'] ?? 0);
        $newStatus = intval($input['is_verified'] ?? 1);

        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID ไม่ถูกต้อง']);
            exit;
        }

        try {
            $db->beginTransaction();

            // 1. Update Customers Table
            $stmtC = $db->prepare("UPDATE customers SET is_verified = ? WHERE id = ?");
            $stmtC->execute([$newStatus, $id]);

            // Get customer name & phone
            $stmtName = $db->prepare("SELECT name, phone FROM customers WHERE id = ?");
            $stmtName->execute([$id]);
            $c = $stmtName->fetch();

            if ($c) {
                // 2. Update Users Table
                $stmtU = $db->prepare("UPDATE users SET is_verified = ? WHERE fullname = ? OR username = ?");
                $stmtU->execute([$newStatus, $c['name'], $c['phone']]);
            }

            $db->commit();

            $msg = ($newStatus == 1) ? 'อนุมัติยืนยันตัวตนสำเร็จ! สถานะเป็นสีเขียว (Verified)' : 'ยกเลิกการยืนยันตัวตนเรียบร้อย';
            echo json_encode(['status' => 'success', 'message' => $msg], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'delete') {
        $id = intval($input['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID ไม่ถูกต้อง']);
            exit;
        }

        try {
            $stmt = $db->prepare("DELETE FROM customers WHERE id=?");
            $stmt->execute([$id]);

            echo json_encode(['status' => 'success', 'message' => 'ลบข้อมูลลูกค้าสำเร็จ'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถลบได้ เนื่องจากลูกค้ารายนี้มีประวัติการเช่า']);
        }
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Invalid Request Method']);
