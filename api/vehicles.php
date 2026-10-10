<?php
// api/vehicles.php - Real-Time Vehicle CRUD REST API
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $search = trim($_GET['search'] ?? '');
    $category = trim($_GET['category'] ?? '');
    $status = trim($_GET['status'] ?? '');
    $mode = trim($_GET['mode'] ?? '');
    $id = intval($_GET['id'] ?? 0);

    if ($id > 0) {
        $stmt = $db->prepare("SELECT * FROM vehicles WHERE id = ?");
        $stmt->execute([$id]);
        $vehicle = $stmt->fetch();
        if ($vehicle) {
            $combo = strtolower(($vehicle['brand'] ?? '') . ' ' . ($vehicle['model'] ?? ''));
            if ($vehicle['category'] === 'Motorcycle' || $vehicle['category'] === 'จักรยานยนต์' || preg_match('/(wave|dream|dreeam|click|pcx|forza|xmax|nmax|filano|scoopy|rebel|110|cub|vespa|aerox|fazzio|lead|giorno|adv|super cub)/i', $combo)) {
                $vehicle['category'] = 'Motorcycle';
            }
            echo json_encode(['status' => 'success', 'data' => $vehicle], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลรถ']);
        }
        exit;
    }

    $query = "SELECT * FROM vehicles WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $query .= " AND (plate_number LIKE ? OR brand LIKE ? OR model LIKE ? OR category LIKE ?)";
        $term = "%{$search}%";
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
    }

    if (!empty($category) && $category !== 'All') {
        if ($category === 'Motorcycle' || $category === 'จักรยานยนต์' || $category === 'มอเตอร์ไซค์') {
            $query .= " AND (LOWER(category) IN ('motorcycle', 'จักรยานยนต์', 'มอเตอร์ไซค์') 
                OR LOWER(brand) LIKE '%vespa%' OR LOWER(brand) LIKE '%yamaha%' OR LOWER(brand) LIKE '%kawasaki%' OR LOWER(brand) LIKE '%gpx%' OR LOWER(brand) LIKE '%ducati%' OR LOWER(brand) LIKE '%dream%' OR LOWER(brand) LIKE '%dreeam%' 
                OR LOWER(model) LIKE '%wave%' OR LOWER(model) LIKE '%click%' OR LOWER(model) LIKE '%pcx%' OR LOWER(model) LIKE '%forza%' OR LOWER(model) LIKE '%xmax%' OR LOWER(model) LIKE '%nmax%' OR LOWER(model) LIKE '%filano%' OR LOWER(model) LIKE '%aerox%' OR LOWER(model) LIKE '%fazzio%' OR LOWER(model) LIKE '%scoopy%' OR LOWER(model) LIKE '%rebel%' OR LOWER(model) LIKE '%110%' OR LOWER(model) LIKE '%cub%' OR LOWER(model) LIKE '%dream%' OR LOWER(model) LIKE '%dreeam%' OR LOWER(model) LIKE '%lead%' OR LOWER(model) LIKE '%giorno%' OR LOWER(model) LIKE '%adv%')";
        } else {
            $query .= " AND (category = ? OR LOWER(category) = LOWER(?))";
            $params[] = $category;
            $params[] = $category;
        }
    }

    // If mode is catalog or pos, do not hide Rented vehicles! Return all matching vehicles with status
    if ($mode !== 'catalog' && $mode !== 'pos' && !empty($status) && $status !== 'All') {
        $query .= " AND status = ?";
        $params[] = $status;
    }

    $query .= " ORDER BY id DESC";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $vehicles = $stmt->fetchAll();

    // Compute real-time active rental check for each vehicle (Thailand local time)
    $now = date('Y-m-d H:i:s');
    foreach ($vehicles as &$v) {
        // Normalize Motorcycle category
        $combo = strtolower(($v['brand'] ?? '') . ' ' . ($v['model'] ?? ''));
        if ($v['category'] === 'Motorcycle' || $v['category'] === 'จักรยานยนต์' || preg_match('/(wave|dream|dreeam|click|pcx|forza|xmax|nmax|filano|scoopy|rebel|110|cub|vespa|aerox|fazzio|lead|giorno|adv|super cub)/i', $combo)) {
            $v['category'] = 'Motorcycle';
        }

        // Vehicle is ONLY occupied if CURRENT TIME is between start_date and end_date of an active rental
        $checkStmt = $db->prepare("SELECT COUNT(*) FROM rentals WHERE vehicle_id = ? AND status = 'Active' AND ? BETWEEN start_date AND end_date");
        $checkStmt->execute([$v['id'], $now]);
        $isRentedRightNow = $checkStmt->fetchColumn() > 0;
        
        $v['is_currently_rented'] = $isRentedRightNow;

        // Auto sync database status if vehicle was marked 'Rented' but has no active rental right now
        if (!$isRentedRightNow && $v['status'] === 'Rented') {
            $v['status'] = 'Available';
            $db->prepare("UPDATE vehicles SET status = 'Available' WHERE id = ?")->execute([$v['id']]);
        }
    }

    echo json_encode(['status' => 'success', 'data' => $vehicles], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $jsonInput = !empty($rawInput) ? json_decode($rawInput, true) : null;
    $input = (is_array($jsonInput) && !empty($jsonInput)) ? $jsonInput : $_POST;
    $action = $_POST['action'] ?? ($input['action'] ?? 'create');

    // Convert uploaded image file into binary Base64 string for direct database storage
    $uploadedImagePath = null;
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK && !empty($_FILES['image_file']['tmp_name'])) {
        $fileTmp = $_FILES['image_file']['tmp_name'];
        $fileType = $_FILES['image_file']['type'] ?? 'image/jpeg';
        if (empty($fileType)) $fileType = 'image/jpeg';
        
        $binaryData = file_get_contents($fileTmp);
        if ($binaryData !== false) {
            $base64Data = base64_encode($binaryData);
            $uploadedImagePath = 'data:' . $fileType . ';base64,' . $base64Data;
        }
    }

    if ($action === 'create') {
        $plate_number = trim($_POST['plate_number'] ?? ($input['plate_number'] ?? ''));
        $brand = trim($_POST['brand'] ?? ($input['brand'] ?? ''));
        $model = trim($_POST['model'] ?? ($input['model'] ?? ''));
        $category = trim($_POST['category'] ?? ($input['category'] ?? 'Sedan'));
        $daily_rate = floatval($_POST['daily_rate'] ?? ($input['daily_rate'] ?? 0));
        $hourly_rate = floatval($_POST['hourly_rate'] ?? ($input['hourly_rate'] ?? 0));
        $deposit_amount = floatval($_POST['deposit_amount'] ?? ($input['deposit_amount'] ?? 0));
        $status = trim($_POST['status'] ?? ($input['status'] ?? 'Available'));
        $image_url = $uploadedImagePath ?? trim($_POST['image_url'] ?? ($input['image_url'] ?? ''));

        if ($category === 'จักรยานยนต์' || $category === 'มอเตอร์ไซค์') {
            $category = 'Motorcycle';
        }

        if (empty($plate_number) || empty($brand) || empty($model)) {
            echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลสำคัญให้ครบถ้วน (ทะเบียน, ยี่ห้อ, รุ่น)']);
            exit;
        }

        try {
            $stmt = $db->prepare("INSERT INTO vehicles (plate_number, brand, model, category, daily_rate, hourly_rate, deposit_amount, status, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$plate_number, $brand, $model, $category, $daily_rate, $hourly_rate, $deposit_amount, $status, $image_url]);

            echo json_encode(['status' => 'success', 'message' => 'เพิ่มข้อมูลรถสำเร็จ', 'id' => $db->lastInsertId()], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ทะเบียนรถอาจจะซ้ำในระบบ (' . $e->getMessage() . ')']);
        }
        exit;
    }

    if ($action === 'update') {
        $id = intval($_POST['id'] ?? ($input['id'] ?? 0));
        $plate_number = trim($_POST['plate_number'] ?? ($input['plate_number'] ?? ''));
        $brand = trim($_POST['brand'] ?? ($input['brand'] ?? ''));
        $model = trim($_POST['model'] ?? ($input['model'] ?? ''));
        $category = trim($_POST['category'] ?? ($input['category'] ?? 'Sedan'));
        if ($category === 'จักรยานยนต์' || $category === 'มอเตอร์ไซค์') {
            $category = 'Motorcycle';
        }
        $daily_rate = floatval($_POST['daily_rate'] ?? ($input['daily_rate'] ?? 0));
        $hourly_rate = floatval($_POST['hourly_rate'] ?? ($input['hourly_rate'] ?? 0));
        $deposit_amount = floatval($_POST['deposit_amount'] ?? ($input['deposit_amount'] ?? 0));
        $status = trim($_POST['status'] ?? ($input['status'] ?? 'Available'));
        $existingImage = trim($_POST['image_url'] ?? ($input['image_url'] ?? ''));
        $image_url = $uploadedImagePath ?? $existingImage;

        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID ไม่ถูกต้อง']);
            exit;
        }

        try {
            $stmt = $db->prepare("UPDATE vehicles SET plate_number=?, brand=?, model=?, category=?, daily_rate=?, hourly_rate=?, deposit_amount=?, status=?, image_url=? WHERE id=?");
            $stmt->execute([$plate_number, $brand, $model, $category, $daily_rate, $hourly_rate, $deposit_amount, $status, $image_url, $id]);

            echo json_encode(['status' => 'success', 'message' => 'อัปเดตข้อมูลรถสำเร็จ'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'delete') {
        $id = intval($_POST['id'] ?? ($input['id'] ?? 0));
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID ไม่ถูกต้อง']);
            exit;
        }

        try {
            // Delete any linked rental history records to prevent FK constraint failure
            $db->prepare("DELETE FROM rentals WHERE vehicle_id=?")->execute([$id]);

            $stmt = $db->prepare("DELETE FROM vehicles WHERE id=?");
            $stmt->execute([$id]);

            echo json_encode(['status' => 'success', 'message' => 'ลบข้อมูลรถสำเร็จ'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการลบ: ' . $e->getMessage()]);
        }
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Invalid Request Method']);
