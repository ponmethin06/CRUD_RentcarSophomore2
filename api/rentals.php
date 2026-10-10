<?php
// api/rentals.php - POS Rental Booking & Vehicle Return Settlement API
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $id = intval($_GET['id'] ?? 0);
    $status = trim($_GET['status'] ?? '');
    $search = trim($_GET['search'] ?? '');
    $now = date('Y-m-d H:i:s');

    if ($id > 0) {
        $stmt = $db->prepare("
            SELECT r.*,
                   c.name as customer_name, c.phone as customer_phone, c.national_id, c.driver_license, c.address as customer_address,
                   v.brand, v.model, v.plate_number, v.category, v.image_url,
                   u.fullname as staff_name
            FROM rentals r
            JOIN customers c ON r.customer_id = c.id
            JOIN vehicles v ON r.vehicle_id = v.id
            LEFT JOIN users u ON r.created_by = u.id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        $rental = $stmt->fetch();

        if ($rental) {
            // Compute date-sensitive status display
            if ($rental['status'] === 'Cancelled') {
                $rental['computed_status'] = 'Cancelled';
                $rental['status_label'] = 'ยกเลิกแล้ว';
            } elseif ($rental['status'] === 'Completed' || $rental['status'] === 'Returned') {
                $rental['computed_status'] = 'Completed';
                $rental['status_label'] = 'คืนเรียบร้อย';
            } elseif ($rental['start_date'] > $now) {
                $rental['computed_status'] = 'Booked';
                $rental['status_label'] = 'จองแล้ว (รอรับรถ)';
            } else {
                $rental['computed_status'] = 'Active';
                $rental['status_label'] = 'กำลังเช่าอยู่';
            }

            // Fetch Payments
            $stmtPay = $db->prepare("SELECT * FROM payments WHERE rental_id = ? ORDER BY id ASC");
            $stmtPay->execute([$id]);
            $rental['payments'] = $stmtPay->fetchAll();

            // Shop Settings for Invoice/Receipt (Hardcoded since table was removed)
            $rental['shop'] = [
                'shop_name' => 'CarEngine Luxury Rental POS',
                'shop_phone' => '02-123-4567',
                'shop_address' => '88/9 ถนนสุขุมวิท กรุงเทพฯ',
                'tax_rate' => 7.00
            ];

            echo json_encode(['status' => 'success', 'data' => $rental], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลการเช่า']);
        }
        exit;
    }

    $query = "
        SELECT r.*,
               c.name as customer_name, c.phone as customer_phone,
               v.brand, v.model, v.plate_number, v.category
        FROM rentals r
        JOIN customers c ON r.customer_id = c.id
        JOIN vehicles v ON r.vehicle_id = v.id
        WHERE 1=1
    ";
    $params = [];

    if (!empty($status) && $status !== 'All') {
        $query .= " AND r.status = ?";
        $params[] = $status;
    }

    if (!empty($search)) {
        $query .= " AND (r.rental_code LIKE ? OR c.name LIKE ? OR c.phone LIKE ? OR v.plate_number LIKE ?)";
        $term = "%{$search}%";
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
    }

    $query .= " ORDER BY r.id DESC";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $rentals = $stmt->fetchAll();

    foreach ($rentals as &$r) {
        if ($r['status'] === 'Cancelled') {
            $r['computed_status'] = 'Cancelled';
            $r['status_label'] = 'ยกเลิกแล้ว';
        } elseif ($r['status'] === 'Completed' || $r['status'] === 'Returned') {
            $r['computed_status'] = 'Completed';
            $r['status_label'] = 'คืนเรียบร้อย';
        } elseif ($r['start_date'] > $now) {
            $r['computed_status'] = 'Booked';
            $r['status_label'] = 'จองแล้ว (รอรับรถ)';
        } else {
            $r['computed_status'] = 'Active';
            $r['status_label'] = 'กำลังเช่าอยู่';
        }
    }

    echo json_encode(['status' => 'success', 'data' => $rentals], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $input['action'] ?? '';

    // Create POS Rental Checkout
    if ($action === 'create') {
        $customer_id = intval($input['customer_id'] ?? 0);
        $vehicle_id = intval($input['vehicle_id'] ?? 0);
        $start_date = trim($input['start_date'] ?? '');
        $end_date = trim($input['end_date'] ?? '');
        $daily_rate = floatval($input['daily_rate'] ?? 0);
        $total_days = intval($input['total_days'] ?? 1);
        $deposit_amount = floatval($input['deposit_amount'] ?? 0);
        $discount = floatval($input['discount'] ?? 0);
        $payment_method = trim($input['payment_method'] ?? 'Cash');
        $notes = trim($input['notes'] ?? '');

        // Identity Verification Guard for Member bookings
        $currentUser = $_SESSION['user'] ?? null;
        if ($currentUser && ($currentUser['level'] ?? 1) < 50) {
            // Fetch latest verification status from DB
            $stmtVer = $db->prepare("SELECT is_verified FROM users WHERE id = ?");
            $stmtVer->execute([$currentUser['id']]);
            $isVer = intval($stmtVer->fetchColumn());

            if ($isVer !== 1) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'ยังไม่ได้ยืนยันตัวตน โปรดยืนยันตัวตนอีกครั้ง',
                    'unverified' => true
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        if ($customer_id <= 0) {
            $stmtDef = $db->query("SELECT id FROM customers WHERE name LIKE '%ทั่วไป%' OR name LIKE '%Walk-in%' LIMIT 1");
            $defId = $stmtDef ? $stmtDef->fetchColumn() : null;
            if (!$defId) {
                $defId = $db->query("SELECT id FROM customers ORDER BY id ASC LIMIT 1")->fetchColumn();
            }
            if (!$defId) {
                $db->exec("INSERT INTO customers (name, phone) VALUES ('ลูกค้าทั่วไป (Walk-in)', '000-000-0000')");
                $defId = $db->lastInsertId();
            }
            $customer_id = intval($defId);
        }

        if ($vehicle_id <= 0 || empty($start_date) || empty($end_date)) {
            echo json_encode(['status' => 'error', 'message' => 'กรุณาระบุเลือกรถเช่า และวันที่เช่าให้ครบถ้วน']);
            exit;
        }

        // Exact Date Range Overlap Check against database (Overlap: start_date < new_end AND end_date > new_start)
        $stmtConflict = $db->prepare("
            SELECT r.*, c.name as customer_name
            FROM rentals r
            JOIN customers c ON r.customer_id = c.id
            WHERE r.vehicle_id = ?
              AND r.status IN ('Active', 'Booked')
              AND r.start_date < ?
              AND r.end_date > ?
            LIMIT 1
        ");
        $stmtConflict->execute([$vehicle_id, $end_date, $start_date]);
        $conflict = $stmtConflict->fetch();

        if ($conflict) {
            $confStart = date('d/m/Y H:i', strtotime($conflict['start_date']));
            $confEnd = date('d/m/Y H:i', strtotime($conflict['end_date']));
            echo json_encode([
                'status' => 'error',
                'date_conflict' => true,
                'message' => "⚠️ ขออภัย รถคันนี้มีผู้จองแล้วในช่วงเวลาดังกล่าว ({$confStart} ถึง {$confEnd}) กรุณาเลือกช่วงเวลาใหม่หรือเลือกรถคันอื่น"
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        $diff = max(0, $end_ts - $start_ts);
        $computed_total_days = ceil($diff / 86400);
        if ($computed_total_days <= 0) $computed_total_days = 1;

        $total_amount = $daily_rate * $computed_total_days;
        $grand_total = max(0, $total_amount - $discount);
        $rental_code = 'RENT-' . date('Ymd') . '-' . rand(1000, 9999);
        $created_by = $_SESSION['user']['id'] ?? 1;

        try {
            $db->beginTransaction();

            // 1. Insert Rental
            $stmtInsert = $db->prepare("
                INSERT INTO rentals (rental_code, customer_id, vehicle_id, start_date, end_date, daily_rate, total_days, total_amount, deposit_amount, discount, grand_total, status, notes, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active', ?, ?)
            ");
            $stmtInsert->execute([$rental_code, $customer_id, $vehicle_id, $start_date, $end_date, $daily_rate, $computed_total_days, $total_amount, $deposit_amount, $discount, $grand_total, $notes, $created_by]);
            $rental_id = $db->lastInsertId();

            // 2. Update Vehicle Status to 'Rented' ONLY if rental is active right now
            $now = date('Y-m-d H:i:s');
            if ($start_date <= $now && $end_date >= $now) {
                $stmtUpdV = $db->prepare("UPDATE vehicles SET status = 'Rented' WHERE id = ?");
                $stmtUpdV->execute([$vehicle_id]);
            } else {
                $stmtUpdV = $db->prepare("UPDATE vehicles SET status = 'Available' WHERE id = ?");
                $stmtUpdV->execute([$vehicle_id]);
            }

            // 3. Record Initial Payment (Deposit or Advance Fee)
            $stmtPay = $db->prepare("INSERT INTO payments (rental_id, payment_method, amount, payment_type, remarks) VALUES (?, ?, ?, 'Deposit', 'มัดจำการเช่ารถ')");
            $stmtPay->execute([$rental_id, $payment_method, $deposit_amount]);

            $db->commit();

            echo json_encode([
                'status' => 'success',
                'message' => 'บันทึกการปล่อยรถเช่าสำเร็จ!',
                'rental_id' => $rental_id,
                'rental_code' => $rental_code
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage()]);
        }
        exit;
    }

    // Return Vehicle Settlement
    if ($action === 'return_vehicle') {
        $rental_id = intval($input['rental_id'] ?? 0);
        $actual_return_date = trim($input['actual_return_date'] ?? date('Y-m-d H:i:s'));
        $extra_charges = floatval($input['extra_charges'] ?? 0);
        $damage_notes = trim($input['damage_notes'] ?? '');
        $final_payment_method = trim($input['payment_method'] ?? 'Cash');

        if ($rental_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลสัญญาเช่า']);
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM rentals WHERE id = ? AND status = 'Active'");
        $stmt->execute([$rental_id]);
        $rental = $stmt->fetch();

        if (!$rental) {
            echo json_encode(['status' => 'error', 'message' => 'รายการเช่านี้ไม่พบหรือถูกคืนรถไปแล้ว']);
            exit;
        }

        $new_grand_total = $rental['grand_total'] + $extra_charges;

        try {
            $db->beginTransaction();

            // 1. Update Rental Record
            $stmtUpdR = $db->prepare("
                UPDATE rentals
                SET actual_return_date = ?, extra_charges = ?, grand_total = ?, status = 'Completed', notes = CONCAT(IFNULL(notes,''), '\n[คืนรถ: ', ?)
                WHERE id = ?
            ");
            $stmtUpdR->execute([$actual_return_date, $extra_charges, $new_grand_total, $damage_notes . ']', $rental_id]);

            // 2. Update Vehicle Status to 'Available'
            $stmtUpdV = $db->prepare("UPDATE vehicles SET status = 'Available' WHERE id = ?");
            $stmtUpdV->execute([$rental['vehicle_id']]);

            // 3. Payment record for remaining balance/extra charges if any
            $remaining = $new_grand_total - $rental['deposit_amount'];
            if ($remaining != 0) {
                $stmtPay = $db->prepare("INSERT INTO payments (rental_id, payment_method, amount, payment_type, remarks) VALUES (?, ?, ?, 'FullPayment', 'ชำระค่าเช่าส่วนคงเหลือ/ค่าบริการเพิ่มเติม')");
                $stmtPay->execute([$rental_id, $final_payment_method, $remaining]);
            }

            $db->commit();

            echo json_encode([
                'status' => 'success',
                'message' => 'รับคืนรถเรียบร้อยแล้ว สถานะรถปรับเป็นพร้อมใช้งาน!',
                'rental_id' => $rental_id
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการรับคืนรถ: ' . $e->getMessage()]);
        }
        exit;
    }

    // Live Check Date Overlap Availability Action
    if ($action === 'check_availability') {
        $vehicle_id = intval($input['vehicle_id'] ?? 0);
        $start_date = trim($input['start_date'] ?? '');
        $end_date = trim($input['end_date'] ?? '');

        if ($vehicle_id <= 0 || empty($start_date) || empty($end_date)) {
            echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
            exit;
        }

        $stmtConflict = $db->prepare("
            SELECT r.*, c.name as customer_name
            FROM rentals r
            JOIN customers c ON r.customer_id = c.id
            WHERE r.vehicle_id = ?
              AND r.status IN ('Active', 'Booked')
              AND r.start_date < ?
              AND r.end_date > ?
            LIMIT 1
        ");
        $stmtConflict->execute([$vehicle_id, $end_date, $start_date]);
        $conflict = $stmtConflict->fetch();

        if ($conflict) {
            $confStart = date('d/m/Y H:i', strtotime($conflict['start_date']));
            $confEnd = date('d/m/Y H:i', strtotime($conflict['end_date']));
            echo json_encode([
                'status' => 'error',
                'conflict' => true,
                'message' => "⚠️ รถคันนี้มีผู้จองแล้วในช่วงเวลา ({$confStart} ถึง {$confEnd}) กรุณาเปลี่ยนวันรับ-คืนรถ"
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'status' => 'success',
                'available' => true,
                'message' => '🟢 รถว่าง สามารถจองในช่วงเวลานี้ได้'
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    // Cancel User Booking
    if ($action === 'cancel_user_booking') {
        $rental_id = intval($input['rental_id'] ?? 0);
        if ($rental_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID การจองไม่ถูกต้อง']);
            exit;
        }

        $stmt = $db->prepare("SELECT * FROM rentals WHERE id = ?");
        $stmt->execute([$rental_id]);
        $rental = $stmt->fetch();

        if (!$rental) {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลการจองนี้']);
            exit;
        }

        if ($rental['status'] === 'Completed' || $rental['status'] === 'Returned') {
            echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถยกเลิกรายการที่ส่งคืนรถเรียบร้อยแล้วได้']);
            exit;
        }

        try {
            $db->beginTransaction();

            // 1. Update Rental Status to Cancelled
            $stmtUpdR = $db->prepare("UPDATE rentals SET status = 'Cancelled', notes = CONCAT(IFNULL(notes,''), '\n[ผู้ใช้งานยกเลิกการจองเมื่อ: ', NOW(), ']') WHERE id = ?");
            $stmtUpdR->execute([$rental_id]);

            // 2. Update Vehicle Status to Available
            $stmtUpdV = $db->prepare("UPDATE vehicles SET status = 'Available' WHERE id = ?");
            $stmtUpdV->execute([$rental['vehicle_id']]);

            $db->commit();

            echo json_encode([
                'status' => 'success',
                'message' => 'ดำเนินการยกเลิกเรียบร้อย กรุณารอดำเนินการในการคืนเงินจากระบบ'
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการยกเลิก: ' . $e->getMessage()]);
        }
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Invalid Request Method']);
