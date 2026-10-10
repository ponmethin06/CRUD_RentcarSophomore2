<?php
// api/users.php - User Management, Profile Update & Admin ID Verification API
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

// Helper to check admin permission
$currentUser = $_SESSION['user'] ?? null;
$isAdmin = $currentUser && (($currentUser['level'] ?? 0) >= 50 || ($currentUser['role'] ?? '') === 'admin');

if ($method === 'GET') {
    $id = intval($_GET['id'] ?? 0);
    $search = trim($_GET['search'] ?? '');

    // Get Single User Detail
    if ($id > 0) {
        $stmt = $db->prepare("
            SELECT u.id, u.username, u.fullname, u.email, u.role, u.level, u.is_verified,
                   COALESCE(u.id_card_image, c.id_card_image) AS id_card_image,
                   COALESCE(u.driver_license_image, c.driver_license_image) AS driver_license_image,
                   COALESCE(u.profile_image, c.profile_image) AS profile_image,
                   u.created_at,
                   COALESCE(NULLIF(c.phone, ''), u.phone) AS phone,
                   COALESCE(NULLIF(c.national_id, ''), u.national_id) AS national_id,
                   COALESCE(NULLIF(c.driver_license, ''), u.driver_license) AS driver_license,
                   COALESCE(NULLIF(c.address, ''), u.address) AS address
            FROM users u
            LEFT JOIN customers c ON (c.user_id = u.id OR (c.phone != '' AND c.phone = u.phone) OR (c.phone != '' AND c.phone = u.username))
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if ($user) {
            // Fetch User's Rental History
            $stmtHist = $db->prepare("SELECT r.*, v.brand, v.model, v.plate_number FROM rentals r JOIN vehicles v ON r.vehicle_id = v.id JOIN customers c ON r.customer_id = c.id WHERE c.name = ? OR c.phone = ? ORDER BY r.id DESC");
            $stmtHist->execute([$user['fullname'], $user['username']]);
            $user['history'] = $stmtHist->fetchAll();

            echo json_encode(['status' => 'success', 'data' => $user], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลผู้ใช้งาน']);
        }
        exit;
    }

    // Admin List All Users
    if (!$isAdmin) {
        echo json_encode(['status' => 'error', 'message' => 'ไม่มีสิทธิ์เข้าถึงข้อมูล']);
        exit;
    }

    $query = "
        SELECT u.id, u.username, u.fullname, u.email, u.role, u.level, u.is_verified,
               COALESCE(u.id_card_image, c.id_card_image) AS id_card_image,
               COALESCE(u.driver_license_image, c.driver_license_image) AS driver_license_image,
               COALESCE(u.profile_image, c.profile_image) AS profile_image,
               u.created_at,
               COALESCE(NULLIF(c.phone, ''), u.phone) AS phone,
               COALESCE(NULLIF(c.national_id, ''), u.national_id) AS national_id,
               COALESCE(NULLIF(c.driver_license, ''), u.driver_license) AS driver_license,
               COALESCE(NULLIF(c.address, ''), u.address) AS address
        FROM users u
        LEFT JOIN customers c ON (c.user_id = u.id OR (c.phone != '' AND c.phone = u.phone) OR (c.phone != '' AND c.phone = u.username))
        WHERE 1=1
    ";
    $params = [];

    if (!empty($search)) {
        $query .= " AND (u.fullname LIKE ? OR u.username LIKE ? OR c.phone LIKE ? OR c.national_id LIKE ?)";
        $term = "%{$search}%";
        $params = [$term, $term, $term, $term];
    }

    $query .= " ORDER BY u.id DESC";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $users = $stmt->fetchAll();

    echo json_encode(['status' => 'success', 'data' => $users], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $input['action'] ?? '';

    // 1. MEMBER UPDATE PROFILE & UPLOAD ID CARD / DRIVER LICENSE IMAGES
    if ($action === 'update_profile') {
        if (!$currentUser) {
            echo json_encode(['status' => 'error', 'message' => 'กรุณาเข้าสู่ระบบก่อนทำรายการ']);
            exit;
        }

        $userId = $currentUser['id'];
        $fullname = trim($input['fullname'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $email = trim($input['email'] ?? '');
        $national_id = trim($input['national_id'] ?? '');
        $driver_license = trim($input['driver_license'] ?? '');
        $address = trim($input['address'] ?? '');

        if (empty($fullname) || empty($phone)) {
            echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกชื่อ-นามสกุล และ เบอร์โทรศัพท์']);
            exit;
        }

        // Fetch existing images and data to prevent overwriting with blanks or creating duplicates
        $stmtExist = $db->prepare("SELECT fullname, phone, profile_image, id_card_image, driver_license_image FROM users WHERE id = ?");
        $stmtExist->execute([$userId]);
        $exist = $stmtExist->fetch();

        $old_fullname = $exist['fullname'] ?? '';
        $old_phone = $exist['phone'] ?? '';
        $profile_image = $exist['profile_image'] ?? '';
        $id_card_image = $exist['id_card_image'] ?? '';
        $driver_license_image = $exist['driver_license_image'] ?? '';

        $uploadDir = __DIR__ . '/../assets/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Helper function to safely save image from $_FILES or Base64 payload
        $processUploadedImage = function($fileKey, $base64Key, $prefix, $currentVal) use ($uploadDir, $input) {
            // 1. First attempt: Standard multipart file upload
            if (!empty($_FILES[$fileKey]['tmp_name']) && is_uploaded_file($_FILES[$fileKey]['tmp_name'])) {
                $ext = strtolower(pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION) ?: 'jpg');
                $filename = $prefix . '_' . time() . '_' . rand(100, 999) . '.' . $ext;
                $dest = $uploadDir . $filename;
                if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $dest) || copy($_FILES[$fileKey]['tmp_name'], $dest)) {
                    return 'assets/uploads/' . $filename;
                }
            }

            // 2. Second attempt: Base64 data URI (bypasses PHP post_max_size / upload_max_filesize limit)
            $rawBase64 = $input[$base64Key] ?? ($_POST[$base64Key] ?? '');
            if (!empty($rawBase64) && strpos($rawBase64, 'data:image') === 0) {
                if (preg_match('/^data:image\/(\w+);base64,/', $rawBase64, $m)) {
                    $ext = strtolower($m[1] === 'jpeg' ? 'jpg' : $m[1]);
                    $content = base64_decode(substr($rawBase64, strpos($rawBase64, ',') + 1));
                    if ($content !== false) {
                        $filename = $prefix . '_' . time() . '_' . rand(100, 999) . '.' . $ext;
                        if (file_put_contents($uploadDir . $filename, $content) !== false) {
                            return 'assets/uploads/' . $filename;
                        }
                    }
                }
                // If disk write fails, save Base64 data directly to database (since columns are LONGTEXT)
                return $rawBase64;
            }

            return $currentVal;
        };

        $profile_image = $processUploadedImage('profile_image', 'profile_image_base64', 'prof', $profile_image);
        $id_card_image = $processUploadedImage('id_card_image', 'id_card_image_base64', 'idcard', $id_card_image);
        $driver_license_image = $processUploadedImage('driver_license_image', 'driver_license_image_base64', 'driver', $driver_license_image);

        try {
            $db->beginTransaction();

            // Update Users table
            $stmtU = $db->prepare("UPDATE users SET fullname = ?, phone = ?, email = ?, profile_image = ?, driver_license_image = ?, national_id = ?, driver_license = ?, address = ?, id_card_image = ? WHERE id = ?");
            $stmtU->execute([$fullname, $phone, $email, $profile_image, $driver_license_image, $national_id, $driver_license, $address, $id_card_image, $userId]);

            // 1. Find existing customer by user_id first, then session customer_id, then phone/name
            $stmtC = $db->prepare("SELECT id FROM customers WHERE user_id = ? LIMIT 1");
            $stmtC->execute([$userId]);
            $cust = $stmtC->fetch();

            if (!$cust && !empty($_SESSION['user']['customer_id'])) {
                $stmtC = $db->prepare("SELECT id FROM customers WHERE id = ? LIMIT 1");
                $stmtC->execute([$_SESSION['user']['customer_id']]);
                $cust = $stmtC->fetch();
            }

            if (!$cust) {
                $stmtC = $db->prepare("SELECT id FROM customers WHERE phone = ? OR (phone = ? AND ? != '') OR (name = ? AND ? != '') LIMIT 1");
                $stmtC->execute([$phone, $old_phone, $old_phone, $old_fullname, $old_fullname]);
                $cust = $stmtC->fetch();
            }

            if ($cust) {
                $customerId = $cust['id'];
                $stmtUpdC = $db->prepare("UPDATE customers SET user_id=?, name=?, phone=?, email=?, national_id=?, driver_license=?, address=?, id_card_image=?, driver_license_image=?, profile_image=? WHERE id=?");
                $stmtUpdC->execute([$userId, $fullname, $phone, $email, $national_id, $driver_license, $address, $id_card_image, $driver_license_image, $profile_image, $customerId]);
            } else {
                $stmtInsC = $db->prepare("INSERT INTO customers (user_id, name, phone, email, national_id, driver_license, address, id_card_image, driver_license_image, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmtInsC->execute([$userId, $fullname, $phone, $email, $national_id, $driver_license, $address, $id_card_image, $driver_license_image, $profile_image]);
                $customerId = $db->lastInsertId();
            }

            // Auto-clean any duplicate customer records for this same user or phone created previously
            try {
                $stmtDupes = $db->prepare("SELECT id FROM customers WHERE (user_id = ? OR phone = ?) AND id != ?");
                $stmtDupes->execute([$userId, $phone, $customerId]);
                $dupeIds = $stmtDupes->fetchAll(PDO::FETCH_COLUMN);

                foreach ($dupeIds as $dupeId) {
                    $db->prepare("UPDATE rentals SET customer_id = ? WHERE customer_id = ?")->execute([$customerId, $dupeId]);
                    $db->prepare("DELETE FROM customers WHERE id = ?")->execute([$dupeId]);
                }
            } catch (Exception $exClean) {}

            $db->commit();

            // Update Session Data
            $_SESSION['user']['fullname'] = $fullname;
            $_SESSION['user']['phone'] = $phone;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['profile_image'] = $profile_image;
            $_SESSION['user']['driver_license_image'] = $driver_license_image;
            $_SESSION['user']['national_id'] = $national_id;
            $_SESSION['user']['driver_license'] = $driver_license;
            $_SESSION['user']['address'] = $address;
            $_SESSION['user']['id_card_image'] = $id_card_image;
            $_SESSION['user']['customer_id'] = $customerId;

            echo json_encode([
                'status' => 'success',
                'message' => 'อัปเดตข้อมูลส่วนตัวและหลักฐานยืนยันตัวตนเรียบร้อยแล้ว!',
                'user' => $_SESSION['user']
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
        exit;
    }

    // 2. ADMIN TOGGLE VERIFY STATUS (อนุมัติ/ยกเลิก การยืนยันตัวตน)
    if ($action === 'toggle_verify') {
        if (!$isAdmin) {
            echo json_encode(['status' => 'error', 'message' => 'เฉพาะแอดมินเท่านั้นที่มีสิทธิ์ตรวจอนุมัติยืนยันตัวตน']);
            exit;
        }

        $userId = intval($input['user_id'] ?? 0);
        $newStatus = intval($input['is_verified'] ?? 1); // 1 = Verified, 0 = Unverified

        if ($userId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'User ID ไม่ถูกต้อง']);
            exit;
        }

        try {
            $db->beginTransaction();

            // 1. Update users table
            $stmtU = $db->prepare("UPDATE users SET is_verified = ? WHERE id = ?");
            $stmtU->execute([$newStatus, $userId]);

            // Get user fullname & username
            $stmtName = $db->prepare("SELECT fullname, username FROM users WHERE id = ?");
            $stmtName->execute([$userId]);
            $u = $stmtName->fetch();

            if ($u) {
                // 2. Update customers table
                $stmtC = $db->prepare("UPDATE customers SET is_verified = ? WHERE name = ? OR phone = ?");
                $stmtC->execute([$newStatus, $u['fullname'], $u['username']]);
            }

            $db->commit();

            $msg = ($newStatus == 1) ? 'อนุมัติการยืนยันตัวตนสำเร็จ! สถานะเป็นสีเขียว (Verified)' : 'ยกเลิกการยืนยันตัวตนเรียบร้อย';
            echo json_encode(['status' => 'success', 'message' => $msg], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Invalid Request Method']);
