<?php
// index.php - Main Application Router & Entry Controller
session_start();

require_once __DIR__ . '/config/database.php';

// Auto-refresh is_verified status in session to reflect admin approval in real-time
if (isset($_SESSION['user']['id'])) {
    $db = Database::getInstance()->getConnection();
    $stmtCheckVerify = $db->prepare("SELECT is_verified FROM users WHERE id = ?");
    $stmtCheckVerify->execute([$_SESSION['user']['id']]);
    $currentVerify = $stmtCheckVerify->fetchColumn();
    if ($currentVerify !== false) {
        $_SESSION['user']['is_verified'] = intval($currentVerify);
    }
}

$page = $_GET['page'] ?? 'user_catalog';

// Check Authentication Status & User Permission Level
$isLoggedIn = isset($_SESSION['user']);
$userLevel = intval($_SESSION['user']['level'] ?? 0);
$userRole = $_SESSION['user']['role'] ?? 'member';
$isAdminOrStaff = ($userLevel >= 50) || ($userRole === 'admin') || ($userRole === 'staff');

if ($page === 'user') $page = 'user_catalog';
if ($page === 'users_admin') $page = 'customers';

// If user requests login page, redirect to standalone login.php
if ($page === 'login') {
    header('Location: login.php');
    exit;
}

// Admin Pages (Requires Level >= 50 or Admin/Staff Role)
$adminPages = ['dashboard', 'pos', 'vehicles', 'customers', 'rentals', 'users_admin', 'settings'];

if (!$isLoggedIn && in_array($page, $adminPages)) {
    header('Location: login.php');
    exit;
}

// Guard: Members attempting to access Admin pages are redirected to user_catalog
if ($isLoggedIn && !$isAdminOrStaff && in_array($page, $adminPages)) {
    $page = 'user_catalog';
}

$pageTitles = [
    'dashboard' => 'แดชบอร์ดสรุปผลภาพรวม (Dashboard)',
    'pos' => 'จุดขายปล่อยรถเช่า (POS Checkout)',
    'vehicles' => 'จัดการข้อมูลรถเช่า (Vehicles Management)',
    'customers' => 'จัดการข้อมูลลูกค้า (Customers Management)',
    'rentals' => 'รายการเช่ารถ & รับคืน (Rentals & Returns)',
    'users_admin' => 'ค้นหาและอนุมัติยืนยันตัวตนสมาชิก (User Verification)',
    'user_catalog' => 'บริการเช่ารถยนต์ & จักรยานยนต์ ออนไลน์ (Customer Catalog)',
    'user_bookings' => 'ประวัติการจองรถของฉัน (My Bookings)',
    'user_invoices' => 'ใบเสร็จรับเงิน & เอกสารสัญญาเช่า (My Invoices)',
    'user_support' => 'ศูนย์ช่วยเหลือ & ความช่วยเหลือฉุกเฉิน 24 ชม. (Help & Support)',
    'user_profile' => 'แก้ไขข้อมูลส่วนตัว & ยืนยันตัวตน (Profile & ID Verification)',
    
    'login' => 'เข้าสู่ระบบ CarEngine POS'
];

$title = $pageTitles[$page] ?? 'CarEngine POS';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - CarEngine POS System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .icon-svg {
            width: 18px;
            height: 18px;
            stroke-width: 2;
            stroke: currentColor;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
            vertical-align: middle;
            display: inline-block;
        }
        .nav-item a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 4px;
        }
        .status-dot.green { background-color: #10b981; }
        .status-dot.red { background-color: #ef4444; }
    </style>
</head>
<body>

<!-- Background Blob Animation -->
<svg class="bg-blob-svg" preserveAspectRatio="xMidYMid slice" viewBox="10 10 80 80">
    <defs>
        <style>
            @keyframes rotateBlob {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            .out-top { animation: rotateBlob 20s linear infinite; transform-origin: 13px 25px; }
            .in-top { animation: rotateBlob 10s linear infinite; transform-origin: 13px 25px; }
            .out-bottom { animation: rotateBlob 25s linear infinite; transform-origin: 84px 93px; }
            .in-bottom { animation: rotateBlob 15s linear infinite; transform-origin: 84px 93px; }
            .bg-blob-svg {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100vh;
                z-index: -1;
                opacity: 0.15;
            }
        </style>
    </defs>
    <!-- CarEngine Theme Colors (Orange/Red/Yellow) -->
    <path fill="#ff5500" class="out-top" d="M37-5C25.1-14.7,5.7-19.1-9.2-10-28.5,1.8-32.7,31.1-19.8,49c15.5,21.5,52.6,22,67.2,2.3C59.4,35,53.7,8.5,37-5Z"/>
    <path fill="#ff7e5f" class="in-top" d="M20.6,4.1C11.6,1.5-1.9,2.5-8,11.2-16.3,23.1-8.2,45.6,7.4,50S42.1,38.9,41,24.5C40.2,14.1,29.4,6.6,20.6,4.1Z"/>
    <path fill="#d84315" class="out-bottom" d="M105.9,48.6c-12.4-8.2-29.3-4.8-39.4.8-23.4,12.8-37.7,51.9-19.1,74.1s63.9,15.3,76-5.6c7.6-13.3,1.8-31.1-2.3-43.8C117.6,63.3,114.7,54.3,105.9,48.6Z"/>
    <path fill="#bf360c" class="in-bottom" d="M102,67.1c-9.6-6.1-22-3.1-29.5,2-15.4,10.7-19.6,37.5-7.6,47.8s35.9,3.9,44.5-12.5C115.5,92.6,113.9,74.6,102,67.1Z"/>
</svg>

<?php if ($page === 'login'): ?>
    <!-- Render Standalone Login View -->
    <?php include __DIR__ . '/views/login.php'; ?>
<?php else: ?>
    <!-- Render Main Application Master Shell -->
    <div class="app-container">
        
        <!-- Left Sidebar Navigation -->
        <aside class="sidebar">
            <div class="brand-header">
                <img src="ล้อหมุน.png" style="width: 50px; height: 50px; object-fit: contain; border-radius: 12px;" alt="CarEngine Logo">
                <div>
                    <div class="brand-title">ล้อหมุน</div>
                    <div class="brand-subtitle">-บริการเช่ารถ-</div>
                </div>
            </div>

            <ul class="nav-menu">
                <li class="nav-item <?= $page === 'user_catalog' ? 'active' : '' ?>">
                    <a href="index.php?page=user_catalog">
                        <svg class="icon-svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        <span>เช่าพาหนะออนไลน์</span>
                    </a>
                </li>
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item <?= $page === 'user_bookings' ? 'active' : '' ?>">
                        <a href="index.php?page=user_bookings">
                            <svg class="icon-svg" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            <span>ประวัติการเช่า/ยกเลิก</span>
                        </a>
                    </li>
                    <li class="nav-item <?= $page === 'user_invoices' ? 'active' : '' ?>">
                        <a href="index.php?page=user_invoices">
                            <svg class="icon-svg" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                            <span>ใบเสร็จ & สัญญาเช่า</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <li class="nav-item <?= $page === 'user_support' ? 'active' : '' ?>">
                    <a href="index.php?page=user_support">
                        <svg class="icon-svg" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        <span>ศูนย์ช่วยเหลือ 24 ชม.</span>
                    </a>
                </li>

                <?php if ($isLoggedIn): ?>
                    <li class="nav-item <?= $page === 'user_profile' ? 'active' : '' ?>">
                        <a href="index.php?page=user_profile" style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="display:flex; align-items:center; gap:0.5rem;">
                                <svg class="icon-svg" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <span>โปรไฟล์ & ยืนยันตัวตน</span>
                            </span>
                            <?php if (intval($_SESSION['user']['is_verified'] ?? 0) === 1): ?>
                                <small style="color:#34d399; font-weight:bold;"><span class="status-dot green"></span>ยืนยันแล้ว</small>
                            <?php else: ?>
                                <small style="color:#f87171; font-weight:bold;"><span class="status-dot red"></span>ยังไม่ได้ยืนยัน</small>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($isAdminOrStaff): ?>
                    <li style="border-top:1px solid var(--border-color); margin:0.5rem 0; padding-top:0.5rem;"></li>
                    <li class="nav-item <?= $page === 'dashboard' ? 'active' : '' ?>">
                        <a href="index.php?page=dashboard">
                            <svg class="icon-svg" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                            <span>แดชบอร์ด</span>
                        </a>
                    </li>
                    <li class="nav-item <?= $page === 'pos' ? 'active' : '' ?>">
                        <a href="index.php?page=pos">
                            <svg class="icon-svg" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                            <span>จุดขาย/ปล่อยรถ POS</span>
                        </a>
                    </li>
                    <li class="nav-item <?= $page === 'vehicles' ? 'active' : '' ?>">
                        <a href="index.php?page=vehicles">
                            <svg class="icon-svg" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                            <span>จัดการรถเช่า</span>
                        </a>
                    </li>
                    <li class="nav-item <?= $page === 'customers' ? 'active' : '' ?>">
                        <a href="index.php?page=customers">
                            <svg class="icon-svg" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <span>จัดการลูกค้า & ยืนยันตัวตน</span>
                        </a>
                    </li>
                    <li class="nav-item <?= $page === 'rentals' ? 'active' : '' ?>">
                        <a href="index.php?page=rentals">
                            <svg class="icon-svg" viewBox="0 0 24 24"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.778-7.778zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
                            <span>รายการเช่า/คืนรถ</span>
                        </a>
                    </li>
                   
                <?php endif; ?>
            </ul>

            <?php if ($isLoggedIn): ?>
                <div class="user-footer">
                    <div class="user-info">
                        <img src="<?= htmlspecialchars(!empty($_SESSION['user']['profile_image']) ? $_SESSION['user']['profile_image'] : 'assets/images/default-avatar.svg') ?>" class="avatar" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid #fca084;" alt="Profile Avatar" onerror="this.src='assets/images/default-avatar.svg'">
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 600; color: #0f172a;">
                                <?= htmlspecialchars($_SESSION['user']['fullname'] ?? 'ผู้ใช้งาน') ?>
                            </div>
                            <div style="font-size: 0.7rem; color: var(--primary);">
                                <?= htmlspecialchars($_SESSION['user']['role'] ?? 'member') ?>
                            </div>
                        </div>
                    </div>
                    <button onclick="logoutUser()" title="ออกจากระบบ" style="background: transparent; border: none; font-size: 1.1rem; cursor: pointer; color: var(--danger); display:flex; align-items:center;">
                        <svg class="icon-svg" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </button>
                </div>
            <?php else: ?>
                <div class="user-footer" style="display: flex; flex-direction: column; gap: 0.5rem; padding: 0.85rem 1rem;">
                    <a href="login.php?tab=login" class="btn btn-primary" style="width: 100%; justify-content: center; font-size: 0.85rem; padding: 0.55rem; border-radius: 10px;">
                        เข้าสู่ระบบ
                    </a>
                    <a href="login.php?tab=register" class="btn btn-secondary" style="width: 100%; justify-content: center; font-size: 0.82rem; padding: 0.45rem; border-radius: 10px;">
                        สมัครสมาชิกใหม่
                    </a>
                </div>
            <?php endif; ?>
        </aside>

        <!-- Main Workspace Right Container -->
        <div class="main-wrapper">
            
            <!-- Sticky Top Header -->
            <header class="top-navbar">
                <div class="page-title-group">
                    <h1><?= htmlspecialchars($title) ?></h1>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <?php if ($isAdminOrStaff): ?>
                        <a href="index.php?page=pos" class="btn btn-primary">
                            <svg class="icon-svg" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                            POS จุดขาย
                        </a>
                    <?php endif; ?>
                </div>
            </header>

            <!-- Dynamic Body View Content -->
            <main class="content-body">
                <?php
                $viewFile = __DIR__ . '/views/' . $page . '.php';
                if (file_exists($viewFile)) {
                    include $viewFile;
                } else {
                    echo "<div style='color:var(--danger);'>ไม่พบหน้าเพจที่ต้องการ (404 Not Found)</div>";
                }
                ?>
            </main>

        </div>
    </div>
<?php endif; ?>

<!-- Global Modal: Invoice / Receipt View & Print -->
<div class="modal-overlay" id="modal-invoice-print">
    <div class="modal-card" style="max-width: 700px; background: white; color: #0f172a;">
        <div class="modal-header no-print" style="border-bottom: 1px solid #e2e8f0;">
            <h3 style="color:#0f172a; display:flex; align-items:center; gap:0.5rem;">
                <svg class="icon-svg" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <span>สัญญาเช่ารถ / ใบเสร็จรับเงิน</span>
            </h3>
            <button class="close-btn" onclick="closeModal('modal-invoice-print')">&times;</button>
        </div>
        
        <div id="invoice-modal-content">
            <!-- Populated via JS openInvoicePrintModal -->
        </div>

        <div class="no-print" style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem;">
            <button class="btn btn-secondary" onclick="closeModal('modal-invoice-print')">ปิดหน้าต่าง</button>
            <button class="btn btn-primary" onclick="window.print()" style="display:inline-flex; align-items:center; gap:0.35rem;">
                <svg class="icon-svg" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                <span>พิมพ์ใบเสร็จ / สัญญาเช่า</span>
            </button>
        </div>
    </div>
</div>

<!-- Global Modal: Confirmation Action Box -->
<div class="modal-overlay" id="modal-confirm-action">
    <div class="modal-card" style="max-width: 440px; text-align: center;">
        <div style="width: 64px; height: 64px; background: #fee2e2; color: #dc2626; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 1rem; margin-left: auto; margin-right: auto;">
            ⚠️
        </div>
        <h3 id="confirm-modal-title" style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;">ยืนยันการทำรายการ</h3>
        <p id="confirm-modal-message" style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.25rem; line-height: 1.5;"></p>
        <div style="display: flex; gap: 0.75rem; justify-content: center;">
            <button class="btn btn-secondary" onclick="closeModal('modal-confirm-action')" style="flex: 1;">
                ยกเลิก
            </button>
            <button class="btn btn-danger" id="confirm-modal-btn" onclick="executeGlobalConfirmAction()" style="flex: 1;">
                ✅ ยืนยันทำรายการ
            </button>
        </div>
    </div>
</div>

<script src="assets/js/app.js"></script>
<script>
    // Ensure light mode is consistently used
    document.body.removeAttribute('data-theme');
    localStorage.removeItem('carengine_theme');
</script>
</body>
</html>