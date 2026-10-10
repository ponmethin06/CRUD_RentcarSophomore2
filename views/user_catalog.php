<!-- views/user_catalog.php - Customer Public Vehicle Catalog & Online Booking with Member Guard -->
<?php
$currentUser =$_SESSION['user'] ?? null;
?>
<style>
    /* CSS สำหรับไอคอนและปุ่มหมวดหมู่สไตล์เรียบหรูสีส้ม */
    .user-cat-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    
    /* บังคับเส้นไอคอน SVG ให้เป็นสีส้ม */
    .user-cat-pill svg, 
    .icon-inline-svg {
        width: 20px;
        height: 20px;
        vertical-align: middle;
        flex-shrink: 0;
        stroke: #ff5500 !important; /* กำหนดสีเส้นเป็นสีส้ม */
        transition: stroke 0.2s ease;
    }

    /* เมื่อปุ่มถูกเลือก (Active) ให้ไอคอนเปลี่ยนเป็นสีขาวเพื่อความชัดเจน */
    .user-cat-pill.active svg {
        stroke: #ffffff !important;
    }

    /* เมื่อนำเมาส์ไปชี้ที่ปุ่ม */
    .user-cat-pill:hover svg {
        stroke: #e64a00 !important;
    }
</style>

<div id="user-catalog-view">
    
    <!-- Top Member Header Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; background: #ffffff; border: 1px solid var(--border-color); padding: 1rem 1.75rem; border-radius: 20px; box-shadow: var(--shadow-card);">
        <div style="display: flex; align-items: center; gap: 0.85rem;">
            <img src="ล้อหมุน.png" style="width: 44px; height: 44px; object-fit: contain; border-radius: 12px;" alt="CarEngine Logo">
            <div>
                <strong style="color: #0f172a; font-size: 1.05rem;">CarEngine Luxury Rental</strong>
                <div style="font-size: 0.8rem; color: var(--text-muted);">ระบบบริการเช่ารถยนต์ & จักรยานยนต์ ออนไลน์</div>
            </div>
        </div>

        <div id="user-auth-header-status">
            <?php if ($currentUser): ?>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <img src="<?= htmlspecialchars(!empty($currentUser['profile_image']) ?$currentUser['profile_image'] : 'assets/images/default-avatar.svg') ?>" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid #ff7f30; box-shadow: 0 3px 10px rgba(124, 58, 237, 0.15);" alt="User Avatar" onerror="this.src='assets/images/default-avatar.svg'">
                    <div style="text-align: right;">
                        <div style="font-size: 0.9rem; font-weight: 700; color: #0f172a;">สวัสดี <?= htmlspecialchars($currentUser['fullname']) ?></div>
                        <div style="font-size: 0.78rem; color: var(--primary); font-weight: 600;">สถานะ: <?= htmlspecialchars($currentUser['role'] === 'admin' ? 'ผู้ดูแลระบบ (Admin)' : 'สมาชิก (Member)') ?></div>
                    </div>
                    <?php if ($currentUser['role'] === 'admin'): ?>
                        <a href="index.php?page=dashboard" class="btn btn-sm btn-primary">เข้าสู่ระบบ Admin</a>
                    <?php endif; ?>
                    <button onclick="logoutUser()" class="btn btn-sm btn-danger">ออกจากระบบ</button>
                </div>
            <?php else: ?>
                <div style="display: flex; gap: 0.6rem;">
                    <a href="login.php?tab=login" class="btn btn-sm btn-secondary">เข้าสู่ระบบ</a>
                    <a href="login.php?tab=register" class="btn btn-sm btn-primary">สมัครสมาชิกฟรี</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Hero Banner -->
    <div style="background: linear-gradient(135deg, #fcddcc 0%, #f69c69 100%); border: 1px solid #ff893b; border-radius: 24px; padding: 2.5rem 2rem; margin-bottom: 2rem; text-align: center; position: relative; overflow: hidden; box-shadow: 0 10px 30px -10px rgba(124, 58, 237, 0.15);">
        <div style="position: absolute; top: -50px; right: -50px; font-size: 10rem; opacity: 0.08; pointer-events: none;"></div>
        <span class="badge badge-completed" style="margin-bottom: 0.75rem; font-size: 0.85rem; padding: 0.45rem 1.1rem; background: #ffffff; color: var(--primary); font-weight: 700;">บริการเช่ารถยนต์ & จักรยานยนต์ ออนไลน์ 24 ชั่วโมง</span>
        <h1 style="font-size: 2.2rem; font-weight: 800; color: #ff5500; margin-bottom: 0.5rem; letter-spacing: -0.5px;">
            เลือกรถเช่าที่ใช่ สำหรับทุกการเดินทางของคุณ
        </h1>
        <p style="color: #fdfcfc; font-size: 1rem; max-width: 620px; margin: 0 auto 1.75rem auto; font-weight: 500;">
            เช่ารถยนต์และจักรยานยนต์ การันตีรถใหม่ สภาพดี พร้อมประกันภัย มัดจำต่ำ อนุมัติไว
        </p>

        <!-- Quick Filter / Search Bar in Hero -->
        <div style="max-width: 680px; margin: 0 auto; display: flex; gap: 0.75rem; background: #ffffff; padding: 0.5rem; border-radius: 20px; border: 1px solid var(--border-color); box-shadow: 0 10px 25px rgba(124, 58, 237, 0.1);">
            <div class="custom-search-box" style="flex: 1; border: none; background: transparent;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="user-search-input" placeholder="ค้นหาทะเบียน/ยี่ห้อ/รุ่น..." oninput="loadUserCatalogVehicles()">
            </div>
            <select id="user-category-filter" class="form-control" style="width: 180px; border: none; background: #f8fafc; font-weight: 600;" onchange="loadUserCatalogVehicles()">
                <option value="All">ทุกประเภทรถ</option>
                <option value="Motorcycle">จักรยานยนต์</option>
                <option value="Sedan">Sedan (เก๋ง)</option>
                <option value="SUV">SUV (อเนกประสงค์)</option>
                <option value="EV">EV (ไฟฟ้า)</option>
                <option value="Luxury">Luxury (หรูหรา)</option>
                <option value="Van">Van (ตู้)</option>
            </select>
        </div>
    </div>

    <!-- Category Pills Navigation -->
    <div style="display: flex; gap: 0.75rem; margin-bottom: 2rem; overflow-x: auto; padding-bottom: 0.5rem; align-items: center;">
        <button class="btn btn-secondary user-cat-pill active" onclick="setUserCategory('All', this)">
            ทั้งหมด
        </button>
        
        <button class="btn btn-secondary user-cat-pill" onclick="setUserCategory('Motorcycle', this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M19 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M12 16h3l3-7h-5l-2 3h-3"/></svg>
            จักรยานยนต์
        </button>
        
        <button class="btn btn-secondary user-cat-pill" onclick="setUserCategory('Sedan', this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.5 2.8C1.4 11.2 1 12.1 1 13v3c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
            เก๋ง (Sedan)
        </button>
        
        <button class="btn btn-secondary user-cat-pill" onclick="setUserCategory('SUV', this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="8" width="22" height="8" rx="2"/><path d="M5 8V5a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3"/><circle cx="6" cy="16" r="2"/><circle cx="18" cy="16" r="2"/></svg>
            SUV
        </button>
        
        <button class="btn btn-secondary user-cat-pill" onclick="setUserCategory('EV', this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            รถไฟฟ้า EV
        </button>
        
        <button class="btn btn-secondary user-cat-pill" onclick="setUserCategory('Luxury', this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Luxury
        </button>
        
        <button class="btn btn-secondary user-cat-pill" onclick="setUserCategory('Van', this)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="6" width="22" height="12" rx="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/><line x1="10" y1="6" x2="10" y2="12"/></svg>
            รถตู้ (Van)
        </button>
    </div>

    <!-- Vehicle Cards Grid -->
    <div class="vehicle-grid" id="user-vehicles-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.75rem;">
        <!-- Loaded via AJAX JS -->
    </div>

</div>

<!-- Modal BOX: Custom Identity Verification Required Warning Box -->
<div class="modal-overlay" id="modal-verification-box">
    <div class="modal-card" style="max-width: 480px; text-align: center; border-top: 5px solid var(--danger);">
        <div style="width: 70px; height: 70px; background: #fee2e2; color: #dc2626; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem auto;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 36px; height: 36px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <h3 style="font-size: 1.35rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;">ยังไม่ได้ยืนยันตัวตน</h3>
        <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 1.5rem; line-height: 1.6;">
            ระบบต้องการให้คุณอัปโหลดรูปถ่ายบัตรประชาชนและข้อมูลส่วนตัวเพื่อยืนยันตัวตนก่อนทำการจองรถเช่า
        </p>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="index.php?page=user_profile" class="btn btn-primary" style="padding: 0.85rem; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                ไปที่หน้าอัปโหลดเอกสารยืนยันตัวตน
            </a>
            <button class="btn btn-secondary" onclick="closeModal('modal-verification-box')">
                ปิดหน้าต่าง
            </button>
        </div>
    </div>
</div>

<!-- Modal: User Booking Form Modal -->
<div class="modal-overlay" id="modal-user-booking">
    <div class="modal-card" style="max-width: 620px;">
        <div class="modal-header">
            <h3>ฟอร์มจองเช่ารถออนไลน์</h3>
            <button class="close-btn" onclick="closeModal('modal-user-booking')">&times;</button>
        </div>
        <div>
            <!-- Selected Vehicle Summary Header -->
            <div style="display: flex; gap: 1rem; align-items: center; background: #fff8f5; border: 1px solid #ff9365; padding: 1rem; border-radius: 16px; margin-bottom: 1.25rem;">
                <img id="ubook-veh-img" src="" style="width: 100px; height: 70px; object-fit: cover; border-radius: 10px;">
                <div>
                    <h4 id="ubook-veh-title" style="color: #0f172a; font-size: 1.15rem; font-weight: 700;"></h4>
                    <div style="font-size: 0.85rem; color: var(--text-muted);" id="ubook-veh-plate"></div>
                    <div style="font-size: 1.1rem; font-weight: 800; color: var(--primary); margin-top: 2px;" id="ubook-veh-rate"></div>
                </div>
            </div>

            <!-- Customer Details Form -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label">ชื่อ-นามสกุล ผู้เช่า *</label>
                    <input type="text" id="ubook-cust-name" class="form-control" placeholder="เช่น คุณสมชาย ใจดี" required>
                </div>
                <div class="form-group">
                    <label class="form-label">เบอร์โทรศัพท์ติดต่อ *</label>
                    <input type="text" id="ubook-cust-phone" class="form-control" placeholder="เช่น 081-234-5678" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label">เลขบัตรประชาชน / Passport</label>
                    <input type="text" id="ubook-cust-natid" class="form-control" placeholder="เลข 13 หลัก">
                </div>
                <div class="form-group">
                    <label class="form-label">เลขที่ใบขับขี่</label>
                    <input type="text" id="ubook-cust-license" class="form-control" placeholder="เลขใบขับขี่">
                </div>
            </div>

            <!-- Booking Dates -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label">วัน-เวลารับรถ *</label>
                    <input type="datetime-local" id="ubook-start-date" class="form-control" style="font-size: 0.85rem;" onchange="calculateUserBookingPrice()">
                </div>
                <div class="form-group">
                    <label class="form-label">วัน-เวลาคืนรถ *</label>
                    <input type="datetime-local" id="ubook-end-date" class="form-control" style="font-size: 0.85rem;" onchange="calculateUserBookingPrice()">
                </div>
            </div>

            <!-- Live Date Conflict Alert Banner -->
            <div id="ubook-date-conflict-notice" style="display:none; background: #fee2e2; border: 1px solid #fca5a5; color: #dc2626; padding: 0.85rem 1rem; border-radius: 12px; font-size: 0.85rem; margin-bottom: 1rem; font-weight: 600;"></div>

            <!-- Payment Method -->
            <div class="form-group">
                <label class="form-label">ช่องทางชำระเงินมัดจำ/ค่าเช่า</label>
                <select id="ubook-payment-method" class="form-control">
                    <option value="Transfer">โอนเงินผ่าน PromptPay / QR Code</option>
                    <option value="Cash">ชำระเงินสดวันรับรถ</option>
                    <option value="CreditCard">บัตรเครดิต</option>
                </select>
            </div>

            <!-- Price Breakdown Box -->
            <div style="background: #fff8f5; border: 1px solid #ff9365; border-radius: 16px; padding: 1.15rem; margin-top: 1rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.88rem; color: #64748b; margin-bottom: 6px;">
                    <span>จำนวนวันเช่า:</span>
                    <strong id="ubook-duration-display" style="color:#0f172a;">1 วัน</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.88rem; color: #64748b; margin-bottom: 6px;">
                    <span>ค่าเช่ารถ:</span>
                    <strong id="ubook-rent-total-display" style="color:#2563eb;">฿0.00</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.88rem; color: #64748b; margin-bottom: 6px;">
                    <span>เงินมัดจำเช่ารถ:</span>
                    <strong id="ubook-deposit-display" style="color:var(--success);">฿0.00</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 800; color: #0f172a; border-top: 1px dashed #cbd5e1; padding-top: 8px; margin-top: 8px;">
                    <span>ยอดชำระสุทธิ:</span>
                    <span id="ubook-total-display" style="color: var(--primary);">฿0.00</span>
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem;">
                <button class="btn btn-secondary" onclick="closeModal('modal-user-booking')">ยกเลิก</button>
                <button class="btn btn-primary" id="ubook-submit-btn" onclick="submitUserBooking()">ยืนยันการจองเช่ารถ</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: User Booking Success Confirmation -->
<div class="modal-overlay" id="modal-user-booking-success">
    <div class="modal-card" style="text-align: center; max-width: 500px;">
        <div style="width: 76px; height: 76px; background: #d1fae5; color: var(--success); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; border: 2px solid #a7f3d0;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width: 38px; height: 38px;"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h2 style="font-size: 1.6rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;">การจองรถสำเร็จเรียบร้อย!</h2>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1.25rem;">
            ขอบคุณที่ไว้วางใจใช้บริการเช่ารถกับเรา รหัสการจองของคุณคือ:
        </p>
        
        <div style="background: #faf5ff; border: 2px dashed var(--primary); padding: 1.15rem; border-radius: 16px; font-size: 1.6rem; font-weight: 800; color: var(--primary); letter-spacing: 1px; margin-bottom: 1.5rem;" id="user-success-booking-code">
            RENT-2026-0000
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <button class="btn btn-primary" style="padding: 0.85rem;" id="user-btn-view-invoice-success">
                ดูใบเสร็จการเช่ารถ
            </button>
            <a href="index.php?page=user_bookings" class="btn btn-secondary" style="padding: 0.85rem;">
                ไปยังหน้าประวัติการเช่าของฉัน
            </a>
            <button class="btn btn-secondary" onclick="closeModal('modal-user-booking-success')">
                ปิดหน้าต่าง
            </button>
        </div>
    </div>
</div>

<script>
let userSelectedVeh = null;
let currentCategoryFilter = 'All';
let currentUserSession = <?= json_encode($currentUser, JSON_UNESCAPED_UNICODE) ?>;
let createdRentalId = null;

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('user-catalog-view')) {
        loadUserCatalogVehicles();
        initUserDates();
    }
});

function initUserDates() {
    const sInput = document.getElementById('ubook-start-date');
    const eInput = document.getElementById('ubook-end-date');
    if (sInput && eInput) {
        const now = new Date();
        const tmr = new Date();
        tmr.setDate(tmr.getDate() + 1);
        sInput.value = now.toISOString().slice(0, 16);
        eInput.value = tmr.toISOString().slice(0, 16);
    }
}

function setUserCategory(cat, btn) {
    currentCategoryFilter = cat;
    const select = document.getElementById('user-category-filter');
    if (select) select.value = cat;
    document.querySelectorAll('.user-cat-pill').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    loadUserCatalogVehicles();
}

async function loadUserCatalogVehicles() {
    const grid = document.getElementById('user-vehicles-grid');
    if (!grid) return;

    const select = document.getElementById('user-category-filter');
    if (select && select.value) {
        currentCategoryFilter = select.value;
    }
    const cat = currentCategoryFilter || 'All';

    // Synchronize active category pill
    document.querySelectorAll('.user-cat-pill').forEach(b => {
        const onclickAttr = b.getAttribute('onclick') || '';
        if (onclickAttr.includes(`'${cat}'`)) {
            b.classList.add('active');
        } else {
            b.classList.remove('active');
        }
    });

    const search = document.getElementById('user-search-input')?.value || '';
    grid.innerHTML = `<div style="color:var(--text-muted); padding:2rem; text-align:center;">กำลังดึงรายการรถเช่า...</div>`;

    try {
        const res = await fetch(`api/vehicles.php?mode=catalog&category=${encodeURIComponent(cat)}&search=${encodeURIComponent(search)}`);
        const json = await res.json();
        if (json.status === 'success') {
            const vehicles = json.data;
            if (vehicles.length === 0) {
                grid.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:4rem; color:var(--text-muted);">ขออภัย ไม่พบรายการรถตรงตามเงื่อนไข</div>`;
                return;
            }

            grid.innerHTML = vehicles.map(v => {
                const isRented = v.is_currently_rented || v.status === 'Rented';
                return `
                    <div class="vehicle-card" style="display:flex; flex-direction:column; justify-content:space-between; opacity:${isRented ? '0.85' : '1'};">
                        <div>
                            <div style="position:relative;">
                                <img src="${v.image_url || 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=600&auto=format&fit=crop&q=80'}" class="vehicle-img" style="height:180px;" alt="${v.model}">
                                ${isRented ? `
                                    <span class="badge badge-rented" style="position:absolute; top:12px; right:12px; font-weight:bold; font-size:0.8rem; padding:0.35rem 0.75rem; backdrop-filter:blur(8px);">ไม่ว่าง</span>
                                ` : `
                                    <span class="badge badge-available" style="position:absolute; top:12px; right:12px; font-weight:bold; font-size:0.8rem; padding:0.35rem 0.75rem; backdrop-filter:blur(8px);">ว่างพร้อมเช่า</span>
                                `}
                            </div>
                            <div class="vehicle-info">
                                <div class="vehicle-title" style="font-size:1.15rem; margin-bottom:2px;">${v.brand} ${v.model}</div>
                                <div class="vehicle-plate" style="margin-bottom:0.85rem;">ทะเบียน: ${v.plate_number}</div>
                                
                                <div style="background:#fff8f5; padding:0.85rem; border-radius:14px; display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; border:1px solid #fedbca;">
                                    <div>
                                        <div style="font-size:0.75rem; color:var(--text-muted);">ค่าเช่าต่อวัน</div>
                                        <div style="font-size:1.3rem; font-weight:800; color:var(--primary);">${formatMoney(v.daily_rate)}</div>
                                    </div>
                                    <div style="text-align:right;">
                                        <div style="font-size:0.75rem; color:var(--text-muted);">เงินมัดจำประกัน</div>
                                        <div style="font-size:0.95rem; font-weight:700; color:var(--success);">${formatMoney(v.deposit_amount)}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="padding:0 1.15rem 1.15rem 1.15rem;">
                            ${isRented ? `
                                <button class="btn btn-secondary" style="width:100%; padding:0.75rem; border-radius:12px; font-weight:600; cursor:not-allowed; opacity:0.7;" disabled>
                                    ไม่ว่าง (ติดสัญญาเช่าขณะนี้)
                                </button>
                            ` : `
                                <button class="btn btn-primary" style="width:100%; padding:0.75rem; border-radius:12px; font-weight:600;" onclick="openUserBookingModal(${v.id})">
                                     จองเช่ารถคันนี้
                                </button>
                            `}
                        </div>
                    </div>
                `;
            }).join('');
        }
    } catch (e) {
        console.error("Load user vehicles error", e);
    }
}

async function openUserBookingModal(vehId) {
    if (!currentUserSession) {
        showToast('กรุณาเข้าสู่ระบบหรือสมัครสมาชิกก่อนทำการจองรถ', 'error');
        setTimeout(() => window.location.href = 'login.php?tab=login', 800);
        return;
    }

    const isVer = parseInt(currentUserSession.is_verified || 0);
    if (isVer !== 1) {
        openModal('modal-verification-box');
        return;
    }

    try {
        const res = await fetch(`api/vehicles.php?id=${vehId}`);
        const json = await res.json();
        if (json.status === 'success') {
            userSelectedVeh = json.data;
            document.getElementById('ubook-veh-title').innerText = `${userSelectedVeh.brand} ${userSelectedVeh.model}`;
            document.getElementById('ubook-veh-plate').innerText = `ทะเบียน: ${userSelectedVeh.plate_number} • ประเภท: ${userSelectedVeh.category}`;
            document.getElementById('ubook-veh-rate').innerText = `${formatMoney(userSelectedVeh.daily_rate)} /วัน`;
            document.getElementById('ubook-veh-img').src = userSelectedVeh.image_url || 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=600&auto=format&fit=crop&q=80';

            if (currentUserSession) {
                document.getElementById('ubook-cust-name').value = currentUserSession.fullname || '';
                document.getElementById('ubook-cust-phone').value = currentUserSession.phone || currentUserSession.username || '';
                document.getElementById('ubook-cust-natid').value = currentUserSession.national_id || '';
                document.getElementById('ubook-cust-license').value = currentUserSession.driver_license || '';
            }

            calculateUserBookingPrice();
            openModal('modal-user-booking');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาดในการโหลดข้อมูลรถ', 'error');
    }
}

async function calculateUserBookingPrice() {
    if (!userSelectedVeh) return;

    const sVal = document.getElementById('ubook-start-date').value;
    const eVal = document.getElementById('ubook-end-date').value;
    const notice = document.getElementById('ubook-date-conflict-notice');
    const submitBtn = document.getElementById('ubook-submit-btn');

    if (!sVal || !eVal) return;

    const start = new Date(sVal);
    const end = new Date(eVal);
    let diffDays = Math.ceil(Math.max(0, end - start) / (1000 * 60 * 60 * 24));
    if (diffDays <= 0) diffDays = 1;

    const rentTotal = parseFloat(userSelectedVeh.daily_rate || 0) * diffDays;
    const depositTotal = parseFloat(userSelectedVeh.deposit_amount || 0);
    const grandTotalWithDeposit = rentTotal + depositTotal;

    document.getElementById('ubook-duration-display').innerText = `${diffDays} วัน`;
    const rentEl = document.getElementById('ubook-rent-total-display');
    if (rentEl) rentEl.innerText = formatMoney(rentTotal);
    document.getElementById('ubook-deposit-display').innerText = formatMoney(depositTotal);
    document.getElementById('ubook-total-display').innerText = formatMoney(grandTotalWithDeposit);

    try {
        const res = await fetch('api/rentals.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'check_availability',
                vehicle_id: userSelectedVeh.id,
                start_date: sVal,
                end_date: eVal
            })
        });

        const json = await res.json();
        if (json.status === 'error' && json.conflict) {
            if (notice) {
                notice.innerText = json.message;
                notice.style.display = 'block';
            }
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.6';
                submitBtn.style.cursor = 'not-allowed';
            }
        } else {
            if (notice) notice.style.display = 'none';
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
            }
        }
    } catch (e) {
        console.error("Check availability error", e);
    }
}

async function submitUserBooking() {
    if (!userSelectedVeh) return;

    if (!currentUserSession) {
        showToast('กรุณาเข้าสู่ระบบสมาชิกก่อนทำรายการ', 'error');
        return;
    }

    const name = document.getElementById('ubook-cust-name').value.trim();
    const phone = document.getElementById('ubook-cust-phone').value.trim();
    const natId = document.getElementById('ubook-cust-natid').value.trim();
    const license = document.getElementById('ubook-cust-license').value.trim();
    const startDate = document.getElementById('ubook-start-date').value;
    const endDate = document.getElementById('ubook-end-date').value;
    const payMethod = document.getElementById('ubook-payment-method').value;

    if (!name || !phone || !startDate || !endDate) {
        showToast('กรุณากรอกชื่อ เบอร์โทรศัพท์ และวันเวลารับ-คืนรถให้ครบถ้วน', 'error');
        return;
    }

    try {
        const custRes = await fetch('api/customers.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'create',
                name: name,
                phone: phone,
                national_id: natId,
                driver_license: license
            })
        });
        const custJson = await custRes.json();
        
        let customerId = custJson.id;
        if (!customerId && custJson.customer) {
            customerId = custJson.customer.id;
        }

        const start = new Date(startDate);
        const end = new Date(endDate);
        let totalDays = Math.ceil(Math.max(0, end - start) / (1000 * 60 * 60 * 24));
        if (totalDays <= 0) totalDays = 1;

        const rentalRes = await fetch('api/rentals.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'create',
                vehicle_id: userSelectedVeh.id,
                customer_id: customerId || currentUserSession.customer_id || 1,
                start_date: startDate,
                end_date: endDate,
                daily_rate: userSelectedVeh.daily_rate,
                total_days: totalDays,
                deposit_amount: userSelectedVeh.deposit_amount,
                discount: 0,
                payment_method: payMethod,
                notes: `จองผ่านระบบสมาชิกออนไลน์ (ผู้จอง: ${currentUserSession.username})`
            })
        });

        const rentalJson = await rentalRes.json();
        if (rentalJson.status === 'success') {
            closeModal('modal-user-booking');
            createdRentalId = rentalJson.rental_id;
            document.getElementById('user-success-booking-code').innerText = rentalJson.rental_code;
            
            const viewBtn = document.getElementById('user-btn-view-invoice-success');
            if (viewBtn) {
                viewBtn.onclick = () => {
                    closeModal('modal-user-booking-success');
                    openInvoicePrintModal(createdRentalId);
                };
            }

            openModal('modal-user-booking-success');
            loadUserCatalogVehicles();
        } else {
            showToast(rentalJson.message, 'error');
        }

    } catch (e) {
        showToast('เกิดข้อผิดพลาดในการทำรายการจอง', 'error');
    }
}
</script>