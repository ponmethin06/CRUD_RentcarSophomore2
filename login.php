<?php
// login.php - Dedicated Standalone Login & Member Registration Page
session_start();

// If already logged in, redirect appropriately
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    if (($user['level'] ?? 0) >= 50 || ($user['role'] ?? '') === 'admin') {
        header('Location: index.php?page=dashboard');
    } else {
        header('Location: index.php');
    }
    exit;
}

$tab = $_GET['tab'] ?? 'login';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ / สมัครสมาชิก - CarEngine POS System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem; background-image: radial-gradient(at 0% 0%, rgba(124, 58, 237, 0.1) 0px, transparent 50%), radial-gradient(at 100% 100%, rgba(139, 92, 246, 0.1) 0px, transparent 50%); background-color: #f8fafc;">
    
    <div style="background: #ffffff; border: 1px solid var(--border-color); width: 100%; max-width: 490px; border-radius: 24px; padding: 2.5rem; box-shadow: 0 25px 60px -15px rgba(124, 58, 237, 0.18);">
        
        <!-- Brand Header -->
        <div style="text-align: center; margin-bottom: 2rem;">
            <img src="ล้อหมุน.png" style="width: 70px; height: 70px; object-fit: contain; margin-bottom: 0.75rem; border-radius: 18px;" alt="CarEngine Logo">
            <h2 style="font-size: 1.7rem; font-weight: 800; color: #0f172a;">Carrent by ล้อหมุน</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">ระบบเช่ารถยนต์ & จักรยานยนต์ ออนไลน์</p>
        </div>

        <!-- Tab Controls (Login vs Register) -->
        <div style="display: flex; gap: 0.75rem; margin-bottom: 1.75rem; background: #faf5ff; padding: 0.35rem; border-radius: 16px; border: 1px solid #e9d5ff;">
            <button id="tab-btn-login" class="btn btn-sm <?= $tab === 'login' ? 'btn-primary' : 'btn-secondary' ?>" style="flex: 1; padding: 0.65rem; font-size: 0.95rem; border-radius: 12px;" onclick="switchAuthPageTab('login')">
                 เข้าสู่ระบบ
            </button>
            <button id="tab-btn-register" class="btn btn-sm <?= $tab === 'register' ? 'btn-primary' : 'btn-secondary' ?>" style="flex: 1; padding: 0.65rem; font-size: 0.95rem; border-radius: 12px;" onclick="switchAuthPageTab('register')">
                 สมัครสมาชิกใหม่
            </button>
        </div>

        <div id="auth-alert" style="display: none; background: #fee2e2; border: 1px solid #fca5a5; color: #dc2626; padding: 0.85rem 1rem; border-radius: 12px; font-size: 0.88rem; margin-bottom: 1.5rem; text-align: center; font-weight: 600;"></div>

        <!-- 1. LOGIN FORM (Admin & Members) -->
        <div id="form-login-section" style="display: <?= $tab === 'login' ? 'block' : 'none' ?>;">
            <form onsubmit="handleAuthPageLogin(event)">
                <div class="form-group">
                    <label class="form-label">ชื่อผู้ใช้งาน หรือ เบอร์โทรศัพท์ *</label>
                    <input type="text" id="login-user-input" class="form-control" placeholder="กรอก Username หรือ เบอร์โทร" required>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">รหัสผ่าน *</label>
                    <input type="password" id="login-pass-input" class="form-control" placeholder="กรอก Password" required>
                </div>

              

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem; border-radius: 14px; margin-bottom: 1rem;">
                 เข้าสู่ระบบ (Sign In)
                </button>
            </form>
        </div>

        <!-- 2. REGISTER FORM (New Members) -->
        <div id="form-register-section" style="display: <?= $tab === 'register' ? 'block' : 'none' ?>;">
            <form onsubmit="handleAuthPageRegister(event)">
                <div class="form-group">
                    <label class="form-label">ชื่อ-นามสกุล *</label>
                    <input type="text" id="reg-fullname-input" class="form-control" placeholder="เช่น คุณสมชาย ใจดี" required>
                </div>

                <div class="form-group">
                    <label class="form-label">เบอร์โทรศัพท์ *</label>
                    <input type="text" id="reg-phone-input" class="form-control" placeholder="เช่น 081-234-5678" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div class="form-group">
                        <label class="form-label">ตั้ง Username *</label>
                        <input type="text" id="reg-user-input" class="form-control" placeholder="username" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">ตั้ง รหัสผ่าน *</label>
                        <input type="password" id="reg-pass-input" class="form-control" placeholder="password" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">เลขบัตรประชาชน</label>
                        <input type="text" id="reg-natid-input" class="form-control" placeholder="เลข 13 หลัก">
                    </div>
                    <div class="form-group">
                        <label class="form-label">เลขใบขับขี่</label>
                        <input type="text" id="reg-license-input" class="form-control" placeholder="เลขใบขับขี่">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem; border-radius: 14px; margin-bottom: 1rem;">
                     ยืนยันสมัครสมาชิก (Register)
                </button>
            </form>
        </div>


        <div style="text-align: center; margin-top: 1.5rem; font-size: 0.8rem; color: var(--text-muted);">
            &copy; 2026 CarEngine POS System. Premium White & Purple Car Rental Management.
        </div>
    </div>
</div>

<script src="assets/js/app.js"></script>
<script>
function switchAuthPageTab(tab) {
    const loginSec = document.getElementById('form-login-section');
    const regSec = document.getElementById('form-register-section');
    const loginBtn = document.getElementById('tab-btn-login');
    const regBtn = document.getElementById('tab-btn-register');
    const alertDiv = document.getElementById('auth-alert');
    if (alertDiv) alertDiv.style.display = 'none';

    if (tab === 'login') {
        loginSec.style.display = 'block';
        regSec.style.display = 'none';
        loginBtn.className = 'btn btn-sm btn-primary';
        regBtn.className = 'btn btn-sm btn-secondary';
    } else {
        loginSec.style.display = 'none';
        regSec.style.display = 'block';
        loginBtn.className = 'btn btn-sm btn-secondary';
        regBtn.className = 'btn btn-sm btn-primary';
    }
}

async function handleAuthPageLogin(e) {
    e.preventDefault();
    const alertDiv = document.getElementById('auth-alert');
    alertDiv.style.display = 'none';

    const username = document.getElementById('login-user-input').value;
    const password = document.getElementById('login-pass-input').value;

    try {
        const res = await fetch('api/auth.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'login', username, password })
        });

        const json = await res.json();
        if (json.status === 'success') {
            if (json.user.role === 'admin' || (json.user.level || 0) >= 50) {
                window.location.href = 'index.php?page=dashboard';
            } else {
                window.location.href = 'index.php';
            }
        } else {
            alertDiv.innerText = json.message;
            alertDiv.style.display = 'block';
        }
    } catch (err) {
        alertDiv.innerText = 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์';
        alertDiv.style.display = 'block';
    }
}

async function handleAuthPageRegister(e) {
    e.preventDefault();
    const alertDiv = document.getElementById('auth-alert');
    alertDiv.style.display = 'none';

    const fullname = document.getElementById('reg-fullname-input').value.trim();
    const phone = document.getElementById('reg-phone-input').value.trim();
    const username = document.getElementById('reg-user-input').value.trim();
    const password = document.getElementById('reg-pass-input').value.trim();
    const national_id = document.getElementById('reg-natid-input').value.trim();
    const driver_license = document.getElementById('reg-license-input').value.trim();

    if (username.toLowerCase() === password.toLowerCase() || phone.toLowerCase() === password.toLowerCase()) {
        alertDiv.innerText = 'รหัสผ่าน (Password) ต้องไม่ตั้งซ้ำหรือเหมือนกับ ชื่อผู้ใช้ (Username) หรือ เบอร์โทรศัพท์';
        alertDiv.style.display = 'block';
        return;
    }

    if (password.length < 4) {
        alertDiv.innerText = 'รหัสผ่านต้องมีความยาวอย่างน้อย 4 ตัวอักษรขึ้นไป';
        alertDiv.style.display = 'block';
        return;
    }

    try {
        const res = await fetch('api/auth.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'register', fullname, phone, username, password, national_id, driver_license })
        });

        const json = await res.json();
        if (json.status === 'success') {
            window.location.href = 'index.php';
        } else {
            alertDiv.innerText = json.message;
            alertDiv.style.display = 'block';
        }
    } catch (err) {
        alertDiv.innerText = 'เกิดข้อผิดพลาดในการสมัครสมาชิก';
        alertDiv.style.display = 'block';
    }
}
</script>
</body>
</html>
