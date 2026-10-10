<!-- views/users_admin.php - Admin User Search, Verification & Member Management -->
<div id="users-admin-view">
    
    <!-- Top Search & Actions Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div class="custom-search-box" style="width: 320px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="admin-search-user" placeholder="ค้นหาชื่อสมาชิก/เบอร์โทร/บัตรประชาชน..." oninput="loadAdminUsersList()">
        </div>

        <div style="font-size: 0.9rem; color: var(--text-muted);">
            ตารางจัดการข้อมูลสมาชิกและการอนุมัติยืนยันตัวตน (Identity Verification)
        </div>
    </div>

    <!-- Users DataTable -->
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ชื่อ-นามสกุล</th>
                    <th>Username / เบอร์โทร</th>
                    <th>เลขบัตรประชาชน</th>
                    <th>ใบขับขี่</th>
                    <th>หลักฐานบัตรประชาชน</th>
                    <th>สถานะยืนยันตัวตน</th>
                    <th>จัดการ (Actions)</th>
                </tr>
            </thead>
            <tbody id="admin-users-tbody">
                <!-- Loaded dynamically via AJAX -->
            </tbody>
        </table>
    </div>

</div>

<!-- Modal: Admin View User Details & ID Card Image -->
<div class="modal-overlay" id="modal-admin-view-user">
    <div class="modal-card" style="max-width: 650px;">
        <div class="modal-header">
            <h3> ข้อมูลสมาชิกและเอกสารยืนยันตัวตน</h3>
            <button class="close-btn" onclick="closeModal('modal-admin-view-user')">&times;</button>
        </div>
        <div>
            <!-- Admin Interactive Tick Approval Box -->
            <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); padding: 1rem 1.25rem; border-radius: 14px; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <strong style="color:white; font-size:1.05rem;"> ติ๊กยืนยันตัวตนผู้ใช้คนนี้ (Identity Verification Check)</strong>
                    <p style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">เมื่อตรวจบัตรประชาชนผ่านแล้ว ให้กดติ๊กอนุมัติ สถานะผู้ใช้จะเปลี่ยนเป็นสีเขียวทันที</p>
                </div>
                <div id="aview-verify-btn-container">
                    <!-- Populated via JS -->
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem; background: rgba(15, 23, 42, 0.6); padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color);">
                <div>
                    <p style="font-size: 0.8rem; color: var(--text-muted);">ชื่อ-นามสกุล:</p>
                    <strong id="aview-fullname" style="font-size: 1.1rem; color: white;"></strong>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 6px;">เบอร์โทรศัพท์ / Username:</p>
                    <strong id="aview-phone" style="color: var(--primary);"></strong>
                </div>
                <div>
                    <p style="font-size: 0.8rem; color: var(--text-muted);">เลขบัตรประชาชน / Passport:</p>
                    <strong id="aview-natid" style="color: white;"></strong>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 6px;">เลขที่ใบขับขี่:</p>
                    <strong id="aview-license" style="color: white;"></strong>
                </div>
            </div>

            <!-- Documents Image Box (ID Card & Driver License) -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="text-align: center; background: rgba(0,0,0,0.3); padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color);">
                    <h4 style="font-size: 0.88rem; color: #f97316; margin-bottom: 0.5rem; font-weight: 700;"> ภาพบัตรประชาชน (ID Card)</h4>
                    <img id="aview-idcard-img" src="" style="max-width: 100%; max-height: 180px; border-radius: 8px; border: 1px solid var(--border-color); object-fit: cover; display: none;" alt="ID Card">
                    <div id="aview-idcard-placeholder" style="padding: 2rem 0.5rem; color: var(--text-muted); font-size: 0.82rem;">ยังไม่มีรูปภาพบัตรประชาชน</div>
                </div>
                <div style="text-align: center; background: rgba(0,0,0,0.3); padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color);">
                    <h4 style="font-size: 0.88rem; color: #f97316; margin-bottom: 0.5rem; font-weight: 700;"> ภาพใบขับขี่ (Driver License)</h4>
                    <img id="aview-license-img" src="" style="max-width: 100%; max-height: 180px; border-radius: 8px; border: 1px solid var(--border-color); object-fit: cover; display: none;" alt="Driver License">
                    <div id="aview-license-placeholder" style="padding: 2rem 0.5rem; color: var(--text-muted); font-size: 0.82rem;">ยังไม่มีรูปภาพใบขับขี่</div>
                </div>
            </div>

            <!-- Booking History of User -->
            <div style="margin-bottom: 1.5rem;">
                <h4 style="font-size: 0.95rem; color: white; margin-bottom: 0.5rem;"> ประวัติการเช่ารถของผู้ใช้งานรายนี้:</h4>
                <div id="aview-history-container" style="max-height: 150px; overflow-y: auto; font-size: 0.85rem;">
                    <!-- Loaded via JS -->
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button class="btn btn-secondary" onclick="closeModal('modal-admin-view-user')">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Admin Edit User Details -->
<div class="modal-overlay" id="modal-admin-edit-user">
    <div class="modal-card">
        <div class="modal-header">
            <h3>แก้ไขข้อมูลสมาชิก</h3>
            <button class="close-btn" onclick="closeModal('modal-admin-edit-user')">&times;</button>
        </div>
        <div>
            <input type="hidden" id="aedit-user-id" value="">
            <div class="form-group">
                <label class="form-label">ชื่อ-นามสกุล *</label>
                <input type="text" id="aedit-fullname" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">เบอร์โทรศัพท์ / Username *</label>
                <input type="text" id="aedit-phone" class="form-control">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label">เลขบัตรประชาชน</label>
                    <input type="text" id="aedit-natid" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">เลขใบขับขี่</label>
                    <input type="text" id="aedit-license" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">URL รูปภาพบัตรประชาชน</label>
                <input type="text" id="aedit-idcard-url" class="form-control">
            </div>
            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem;">
                <button class="btn btn-secondary" onclick="closeModal('modal-admin-edit-user')">ยกเลิก</button>
                <button class="btn btn-primary" onclick="saveAdminEditUserSubmit()"> บันทึกการแก้ไข</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('admin-users-tbody')) {
        loadAdminUsersList();
    }
});

async function loadAdminUsersList() {
    const tbody = document.getElementById('admin-users-tbody');
    if (!tbody) return;

    const search = document.getElementById('admin-search-user')?.value || '';
    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:2rem; color:var(--text-muted);">กำลังโหลดข้อมูลสมาชิก...</td></tr>`;

    try {
        const res = await fetch(`api/users.php?search=${encodeURIComponent(search)}`);
        const json = await res.json();
        if (json.status === 'success') {
            const users = json.data;
            if (users.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:3rem; color:var(--text-muted);">ไม่พบข้อมูลสมาชิกในระบบ</td></tr>`;
                return;
            }

            tbody.innerHTML = users.map(u => {
                const isVer = parseInt(u.is_verified || 0) === 1;
                return `
                    <tr>
                        <td><strong>#${u.id}</strong></td>
                        <td style="display:flex; align-items:center; gap:0.6rem;">
                            <img src="${u.profile_image || 'assets/images/default-avatar.svg'}" style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:1.5px solid #c084fc;" alt="Avatar" onerror="this.src='assets/images/default-avatar.svg'">
                            <div>
                                <strong>${u.fullname}</strong> ${u.role === 'admin' ? '<span class="badge" style="background:rgba(6,182,212,0.2); color:var(--primary);">Admin</span>' : ''}
                            </div>
                        </td>
                        <td>${u.username} ${u.phone ? '<br><small style="color:var(--text-muted);">' + u.phone + '</small>' : ''}</td>
                        <td>${u.national_id || '-'}</td>
                        <td>${u.driver_license || '-'}</td>
                        <td>
                            ${u.id_card_image ? `<a href="${u.id_card_image}" target="_blank" style="color:var(--primary); font-weight:bold; font-size:0.85rem;">🪪 รูปภาพบัตร</a>` : '<span style="color:var(--text-muted); font-size:0.8rem;">ยังไม่อัปโหลด</span>'}
                        </td>
                        <td>
                            ${isVer ? `
                                <label style="display:inline-flex; align-items:center; gap:0.4rem; background:rgba(16,185,129,0.2); border:1px solid rgba(16,185,129,0.4); color:#34d399; padding:0.35rem 0.75rem; border-radius:20px; font-size:0.8rem; cursor:pointer; font-weight:600;">
                                    <input type="checkbox" checked onchange="toggleAdminUserVerify(${u.id}, 0)" style="width:16px; height:16px; accent-color:#10b981; cursor:pointer;">
                                    <span>🟢 ยืนยันสำเร็จแล้ว</span>
                                </label>
                            ` : `
                                <label style="display:inline-flex; align-items:center; gap:0.4rem; background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#f87171; padding:0.35rem 0.75rem; border-radius:20px; font-size:0.8rem; cursor:pointer; font-weight:600;">
                                    <input type="checkbox" onchange="toggleAdminUserVerify(${u.id}, 1)" style="width:16px; height:16px; cursor:pointer;">
                                    <span>🔴 ยังไม่ได้ยืนยัน (กดติ๊กผ่าน)</span>
                                </label>
                            `}
                        </td>
                        <td>
                            <div style="display:flex; gap:0.35rem; flex-wrap:wrap;">
                                <button class="btn btn-sm btn-secondary" onclick="openAdminViewUserModal(${u.id})"> View</button>
                                <button class="btn btn-sm btn-secondary" onclick="openAdminEditUserModal(${u.id})"> Edit</button>
                                ${!isVer ? `
                                    <button class="btn btn-sm btn-primary" style="background:linear-gradient(135deg,#10b981,#059669); padding:4px 10px; font-size:0.8rem;" onclick="toggleAdminUserVerify(${u.id}, 1)">
                                        ☑️ ติ๊กยืนยันผ่าน
                                    </button>
                                ` : `
                                    <button class="btn btn-sm btn-danger" style="padding:4px 8px; font-size:0.75rem;" onclick="toggleAdminUserVerify(${u.id}, 0)">
                                        ❎ ยกเลิก
                                    </button>
                                `}
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }
    } catch (e) {
        console.error("Load admin users error", e);
    }
}

async function openAdminViewUserModal(id) {
    try {
        const res = await fetch(`api/users.php?id=${id}`);
        const json = await res.json();
        if (json.status === 'success') {
            const u = json.data;
            document.getElementById('aview-fullname').innerText = u.fullname;
            document.getElementById('aview-phone').innerText = u.username + (u.phone ? ` (${u.phone})` : '');
            document.getElementById('aview-natid').innerText = u.national_id || 'ไม่ระบุ';
            document.getElementById('aview-license').innerText = u.driver_license || 'ไม่ระบุ';

            const idImg = document.getElementById('aview-idcard-img');
            const idPlaceholder = document.getElementById('aview-idcard-placeholder');
            if (u.id_card_image) {
                idImg.src = u.id_card_image;
                idImg.style.display = 'inline-block';
                if (idPlaceholder) idPlaceholder.style.display = 'none';
            } else {
                idImg.src = '';
                idImg.style.display = 'none';
                if (idPlaceholder) idPlaceholder.style.display = 'block';
            }

            const licImg = document.getElementById('aview-license-img');
            const licPlaceholder = document.getElementById('aview-license-placeholder');
            if (u.driver_license_image) {
                licImg.src = u.driver_license_image;
                licImg.style.display = 'inline-block';
                if (licPlaceholder) licPlaceholder.style.display = 'none';
            } else {
                licImg.src = '';
                licImg.style.display = 'none';
                if (licPlaceholder) licPlaceholder.style.display = 'block';
            }

            const isVer = parseInt(u.is_verified || 0) === 1;
            const btnBox = document.getElementById('aview-verify-btn-container');
            if (btnBox) {
                btnBox.innerHTML = !isVer ? `
                    <button class="btn btn-primary" style="background:linear-gradient(135deg,#10b981,#059669); font-weight:bold; padding:0.75rem 1.25rem;" onclick="toggleAdminUserVerify(${u.id}, 1); closeModal('modal-admin-view-user');">
                        กดติ๊กอนุมัติยืนยันสำเร็จแล้ว
                    </button>
                ` : `
                    <button class="btn btn-danger" style="font-size:0.85rem; padding:0.6rem 1rem;" onclick="toggleAdminUserVerify(${u.id}, 0); closeModal('modal-admin-view-user');">
                        ❎ ยกเลิกการยืนยันตัวตน
                    </button>
                `;
            }

            const histDiv = document.getElementById('aview-history-container');
            if (u.history && u.history.length > 0) {
                histDiv.innerHTML = u.history.map(h => `
                    <div style="background:rgba(255,255,255,0.03); padding:6px 10px; border-radius:6px; margin-bottom:4px; display:flex; justify-content:space-between;">
                        <span><strong>${h.rental_code}</strong>: ${h.brand} ${h.model} (${h.plate_number})</span>
                        <span class="badge badge-${h.status.toLowerCase()}">${h.status}</span>
                    </div>
                `).join('');
            } else {
                histDiv.innerHTML = `<span style="color:var(--text-muted);">ยังไม่มีประวัติการเช่ารถ</span>`;
            }

            openModal('modal-admin-view-user');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาดในการดึงข้อมูล', 'error');
    }
}

async function openAdminEditUserModal(id) {
    try {
        const res = await fetch(`api/users.php?id=${id}`);
        const json = await res.json();
        if (json.status === 'success') {
            const u = json.data;
            document.getElementById('aedit-user-id').value = u.id;
            document.getElementById('aedit-fullname').value = u.fullname;
            document.getElementById('aedit-phone').value = u.phone || u.username;
            document.getElementById('aedit-natid').value = u.national_id || '';
            document.getElementById('aedit-license').value = u.driver_license || '';
            document.getElementById('aedit-idcard-url').value = u.id_card_image || '';

            openModal('modal-admin-edit-user');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาด', 'error');
    }
}

async function saveAdminEditUserSubmit() {
    const userId = document.getElementById('aedit-user-id').value;
    const payload = {
        action: 'update_profile',
        user_id: userId,
        fullname: document.getElementById('aedit-fullname').value,
        phone: document.getElementById('aedit-phone').value,
        national_id: document.getElementById('aedit-natid').value,
        driver_license: document.getElementById('aedit-license').value,
        id_card_image: document.getElementById('aedit-idcard-url').value
    };

    try {
        const res = await fetch('api/users.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const json = await res.json();
        if (json.status === 'success') {
            showToast('บันทึกการแก้ไขเรียบร้อย');
            closeModal('modal-admin-edit-user');
            loadAdminUsersList();
        } else {
            showToast(json.message, 'error');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาดในการแก้ไข', 'error');
    }
}

async function toggleAdminUserVerify(userId, newStatus) {
    const actionText = newStatus == 1 ? 'อนุมัติผ่านการยืนยันตัวตน' : 'ยกเลิกการยืนยันตัวตน';
    if (!confirm(`ยืนยันที่จะ ${actionText} ของสมาชิกรายนี้หรือไม่?`)) return;

    try {
        const res = await fetch('api/users.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'toggle_verify',
                user_id: userId,
                is_verified: newStatus
            })
        });

        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
            loadAdminUsersList();
        } else {
            showToast(json.message, 'error');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาดในการเปลี่ยนสถานะ', 'error');
    }
}
</script>
<script src="assets/js/app.js?v=2.0"></script>