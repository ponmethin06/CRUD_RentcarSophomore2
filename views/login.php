<!-- views/login.php - Authentication Interface (Admin Login & Customer Portal Link) -->
<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: rgba(30, 41, 59, 0.85); border: 1px solid var(--border-color); backdrop-filter: blur(16px); width: 100%; max-width: 440px; border-radius: 24px; padding: 2.5rem; box-shadow: 0 25px 60px rgba(0,0,0,0.5);">
        
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 18px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 1rem; box-shadow: var(--shadow-glow);">
                🚗
            </div>
            <h2 style="font-size: 1.6rem; font-weight: 700; color: #ffffff;">CarEngine POS</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.3rem;">ระบบบริหารจัดการร้านเช่ารถยนต์ & ระบบจองสมาชิก</p>
        </div>

        <div id="login-error-alert" style="display: none; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 0.85rem 1rem; border-radius: 12px; font-size: 0.85rem; margin-bottom: 1.5rem; text-align: center;"></div>

        <form id="login-form" onsubmit="handleLoginSubmit(event)">
            <div class="form-group">
                <label class="form-label">ชื่อผู้ใช้งาน (Username หรือ เบอร์โทรศัพท์)</label>
                <input type="text" id="login-username" class="form-control" placeholder="กรอก Username หรือ เบอร์โทร"  required>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">รหัสผ่าน (Password)</label>
                <input type="password" id="login-password" class="form-control" placeholder="กรอก Password"  required>
            </div>

            <div style="background: rgba(6, 182, 212, 0.1); border: 1px dashed rgba(6, 182, 212, 0.3); padding: 0.75rem 1rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.8rem; color: var(--primary);">
                💡 <strong>บัญชีทดสอบระบบ Admin:</strong><br>
                Username: <code style="background:rgba(0,0,0,0.3); padding:2px 6px; border-radius:4px;">admin</code> | Password: <code style="background:rgba(0,0,0,0.3); padding:2px 6px; border-radius:4px;">1234</code>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem; border-radius: 12px; margin-bottom: 1rem;">
                🔑 เข้าสู่ระบบ (Sign In)
            </button>
        </form>

        <div style="text-align: center; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            <a href="index.php?page=user_catalog" class="btn btn-secondary" style="width: 100%; padding: 0.75rem; border-radius: 12px; font-size: 0.9rem;">
                🌐 ไปยังหน้าเช่ารถสำหรับลูกค้า / สมัครสมาชิก
            </a>
        </div>

        <div style="text-align: center; margin-top: 1.5rem; font-size: 0.8rem; color: var(--text-muted);">
            &copy; 2026 CarEngine POS System. Premium Car Rental Management.
        </div>
    </div>
</div>

<script>
async function handleLoginSubmit(e) {
    e.preventDefault();
    const errorAlert = document.getElementById('login-error-alert');
    errorAlert.style.display = 'none';

    const username = document.getElementById('login-username').value;
    const password = document.getElementById('login-password').value;

    try {
        const res = await fetch('api/auth.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'login', username, password })
        });

        const json = await res.json();
        if (json.status === 'success') {
            if (json.user.role === 'admin') {
                window.location.href = 'index.php?page=dashboard';
            } else {
                window.location.href = 'index.php?page=user_catalog';
            }
        } else {
            errorAlert.innerText = json.message;
            errorAlert.style.display = 'block';
        }
    } catch (err) {
        errorAlert.innerText = 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์';
        errorAlert.style.display = 'block';
    }
}
</script>
