<?php
// api/dashboard.php - Real-Time Dashboard Analytics API
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

try {
    // 1. Total Revenue
    $stmtRev = $db->query("SELECT COALESCE(SUM(grand_total), 0) AS total_revenue FROM rentals WHERE status = 'Completed'");
    $totalRevenue = (float)$stmtRev->fetchColumn();

    // 2. Active Rentals Count
    $stmtActive = $db->query("SELECT COUNT(*) AS active_count FROM rentals WHERE status = 'Active'");
    $activeRentals = (int)$stmtActive->fetchColumn();

    // 3. Vehicles Available Count
    $stmtAvail = $db->query("SELECT COUNT(*) AS avail_count FROM vehicles WHERE status = 'Available'");
    $availableVehicles = (int)$stmtAvail->fetchColumn();

    // 4. Total Vehicles
    $stmtTotalVeh = $db->query("SELECT COUNT(*) AS total_veh FROM vehicles");
    $totalVehicles = (int)$stmtTotalVeh->fetchColumn();

    // 5. Total Customers
    $stmtCust = $db->query("SELECT COUNT(*) AS total_cust FROM customers");
    $totalCustomers = (int)$stmtCust->fetchColumn();

    // 6. Recent Rentals (Last 5)
    $stmtRecent = $db->query("
        SELECT r.*, c.name as customer_name, c.phone as customer_phone,
               v.brand, v.model, v.plate_number
        FROM rentals r
        LEFT JOIN customers c ON r.customer_id = c.id
        LEFT JOIN vehicles v ON r.vehicle_id = v.id
        ORDER BY r.created_at DESC
        LIMIT 5
    ");
    $recentRentals = $stmtRecent->fetchAll();

    // 7. Vehicle Category Distribution
    $stmtCat = $db->query("SELECT category, COUNT(*) as count FROM vehicles GROUP BY category");
    $categoryStats = $stmtCat->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => [
            'total_revenue' => $totalRevenue,
            'active_rentals' => $activeRentals,
            'available_vehicles' => $availableVehicles,
            'total_vehicles' => $totalVehicles,
            'total_customers' => $totalCustomers,
            'recent_rentals' => $recentRentals,
            'category_stats' => $categoryStats
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
