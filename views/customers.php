<!-- views/customers.php - Real-Time Customer Management & Identity Verification View -->
<div id="customers-view">
    
    <!-- Top Filter Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div class="custom-search-box" style="width: 380px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="search-customer" placeholder="ค้นหาชื่อ/เบอร์โทร/บัตรประชาชน..." oninput="loadCustomers()">
        </div>
    </div>

    <!-- Customers DataTable -->
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ชื่อ-นามสกุล</th>
                    <th>เบอร์โทรศัพท์</th>
                    <th>เลขบัตรประชาชน</th>
                    <th>ใบขับขี่</th>
                    <th>หลักฐานบัตรประชาชน</th>
                    <th>สถานะยืนยันตัวตน</th>
                    <th>การจัดการ (Actions)</th>
                </tr>
            </thead>
            <tbody id="customer-table-body">
                <!-- Loaded dynamically via AJAX -->
            </tbody>
        </table>
    </div>

</div>

<!-- Modal: View Customer Details, ID Card Image & Verification -->
<div class="modal-overlay" id="modal-view-customer">
    <div class="modal-card" style="max-width: 650px;">
        <div class="modal-header">
            <h3> ข้อมูลลูกค้าและเอกสารยืนยันตัวตน</h3>
            <button class="close-btn" onclick="closeModal('modal-view-customer')">&times;</button>
        </div>
        <div>
            <!-- Interactive Verification Switch Bar in Modal -->
            <div style="background: #d1fae5; border: 1px solid #a7f3d0; padding: 1rem 1.25rem; border-radius: 16px; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <strong style="color:#047857; font-size:1.05rem;">☑️ ติ๊กยืนยันตัวตนลูกค้ารายนี้ (Identity Verification Check)</strong>
                    <p style="font-size:0.8rem; color:#065f46; margin-top:2px;">เมื่อตรวจบัตรประชาชนผ่านแล้ว ให้กดติ๊กอนุมัติ สถานะผู้ใช้จะเปลี่ยนเป็นสีเขียวทันที</p>
                </div>
                <div id="cview-verify-btn-container">
                    <!-- Populated via JS -->
                </div>
            </div>

            <!-- Customer Details Card -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem; background: #fff8f5; padding: 1rem 1.25rem; border-radius: 16px; border: 1px solid #fed7aa;">
                <div>
                    <p style="font-size: 0.8rem; color: var(--text-muted);">ชื่อ-นามสกุล:</p>
                    <strong id="cview-name" style="font-size: 1.1rem; color: #0f172a;"></strong>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 6px;">เบอร์โทรศัพท์:</p>
                    <strong id="cview-phone" style="color: var(--primary);"></strong>
                </div>
                <div>
                    <p style="font-size: 0.8rem; color: var(--text-muted);">เลขบัตรประชาชน / Passport:</p>
                    <strong id="cview-natid" style="color: #0f172a;"></strong>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 6px;">เลขที่ใบขับขี่:</p>
                    <strong id="cview-license" style="color: #0f172a;"></strong>
                </div>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <p style="font-size: 0.8rem; color: var(--text-muted);">ที่อยู่:</p>
                <div id="cview-address" style="font-size: 0.9rem; color: #0f172a; font-weight:500;"></div>
            </div>

            <!-- Documents Image Preview Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="text-align: center; background: #fff8f5; padding: 1rem; border-radius: 16px; border: 1px dashed #f97316;">
                    <h4 style="font-size: 0.9rem; color: #0f172a; margin-bottom: 0.75rem;">🪪 บัตรประชาชน</h4>
                    <img id="cview-idcard-img" src="" style="max-width: 100%; max-height: 180px; border-radius: 12px; border: 1px solid var(--border-color); object-fit: cover;" alt="ID Card Image">
                </div>
                <div style="text-align: center; background: #fff8f5; padding: 1rem; border-radius: 16px; border: 1px dashed #f97316;">
                    <h4 style="font-size: 0.9rem; color: #0f172a; margin-bottom: 0.75rem;">🚗 ใบขับขี่</h4>
                    <img id="cview-driver-license-img" src="" style="max-width: 100%; max-height: 180px; border-radius: 12px; border: 1px solid var(--border-color); object-fit: cover;" alt="Driver License Image">
                </div>
            </div>

            <!-- Booking History of Customer -->
            <div style="margin-bottom: 1.5rem;">
                <h4 style="font-size: 0.95rem; color: #0f172a; margin-bottom: 0.5rem;">📋 ประวัติการเช่ารถลูกค้ารายนี้:</h4>
                <div id="cview-history-container" style="max-height: 150px; overflow-y: auto; font-size: 0.85rem;">
                    <!-- Loaded via JS -->
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button class="btn btn-secondary" onclick="closeModal('modal-view-customer')">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Customer Add / Edit Form -->
<div class="modal-overlay" id="modal-customer">
    <div class="modal-card">
        <div class="modal-header">
            <h3 id="cust-modal-title">แก้ไขข้อมูลลูกค้า</h3>
            <button class="close-btn" onclick="closeModal('modal-customer')">&times;</button>
        </div>
        <div>
            <input type="hidden" id="cust-form-action" value="create">
            <input type="hidden" id="cust-id" value="">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label">ชื่อ-นามสกุล *</label>
                    <input type="text" id="cust-name" class="form-control" placeholder="เช่น คุณสมชาย ใจดี">
                </div>
                <div class="form-group">
                    <label class="form-label">เบอร์โทรศัพท์ *</label>
                    <input type="text" id="cust-phone" class="form-control" placeholder="เช่น 081-234-5678">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label">เลขบัตรประชาชน / Passport</label>
                    <input type="text" id="cust-natid" class="form-control" placeholder="เลขบัตรประจำตัว">
                </div>
                <div class="form-group">
                    <label class="form-label">เลขที่ใบขับขี่</label>
                    <input type="text" id="cust-license" class="form-control" placeholder="เลขใบขับขี่">
                </div>
            </div>


            <div class="form-group">
                <label class="form-label">ที่อยู่ตามทะเบียนบ้าน/เอกสาร</label>
                <textarea id="cust-address" class="form-control" rows="2" placeholder="ที่อยู่ลูกค้า..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">บันทึกเพิ่มเติม (Notes)</label>
                <input type="text" id="cust-notes" class="form-control" placeholder="ข้อความหมายเหตุ...">
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem;">
                <button class="btn btn-secondary" onclick="closeModal('modal-customer')">ยกเลิก</button>
                <button class="btn btn-primary" onclick="saveCustomerSubmit()">💾 บันทึกข้อมูลลูกค้า</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('customer-table-body')) {
        loadCustomers();
    }
});

async function loadCustomers() {
    const tbody = document.getElementById('customer-table-body');
    if (!tbody) return;

    const search = document.getElementById('search-customer')?.value || '';
    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:2rem; color:var(--text-muted);">กำลังโหลดข้อมูลลูกค้า...</td></tr>`;

    try {
        const res = await fetch(`api/customers.php?search=${encodeURIComponent(search)}`);
        const json = await res.json();
        if (json.status === 'success') {
            const customers = json.data;
            if (customers.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:3rem; color:var(--text-muted);">ไม่พบข้อมูลลูกค้า</td></tr>`;
                return;
            }

            tbody.innerHTML = customers.map(c => {
                const isVer = parseInt(c.is_verified || 0) === 1;
                return `
                    <tr>
                        <td><strong>#${c.id}</strong></td>
                        <td style="display:flex; align-items:center; gap:0.6rem;">
                            <img src="${c.profile_image || 'assets/images/default-avatar.svg'}" style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:1.5px solid #f97316;" alt="Avatar" onerror="this.src='assets/images/default-avatar.svg'">
                            <strong>${c.name}</strong>
                        </td>
                        <td>${c.phone}</td>
                        <td>${c.national_id || '-'}</td>
                        <td>${c.driver_license || '-'}</td>
                        <td>
                            ${c.id_card_image ? `<a href="${c.id_card_image}" target="_blank" style="color:var(--primary); font-weight:bold; font-size:0.85rem;">🪪 ดูรูปภาพบัตร</a>` : '<span style="color:var(--text-muted); font-size:0.8rem;">ยังไม่อัปโหลด</span>'}
                        </td>
                        <td>
                            ${isVer ? `
                                <label style="display:inline-flex; align-items:center; gap:0.4rem; background:rgba(16,185,129,0.2); border:1px solid rgba(16,185,129,0.4); color:#34d399; padding:0.35rem 0.75rem; border-radius:20px; font-size:0.8rem; cursor:pointer; font-weight:600;">
                                    <input type="checkbox" checked onchange="toggleCustomerVerify(${c.id}, 0)" style="width:16px; height:16px; accent-color:#10b981; cursor:pointer;">
                                    <span>🟢 ยืนยันสำเร็จแล้ว</span>
                                </label>
                            ` : `
                                <label style="display:inline-flex; align-items:center; gap:0.4rem; background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#f87171; padding:0.35rem 0.75rem; border-radius:20px; font-size:0.8rem; cursor:pointer; font-weight:600;">
                                    <input type="checkbox" onchange="toggleCustomerVerify(${c.id}, 1)" style="width:16px; height:16px; cursor:pointer;">
                                    <span>🔴 ยังไม่ได้ยืนยัน (กดติ๊กผ่าน)</span>
                                </label>
                            `}
                        </td>
                        <td>
                            <div style="display:flex; gap:0.35rem; flex-wrap:wrap;">
                                <button class="btn btn-sm btn-secondary" onclick="openViewCustomerModal(${c.id})">ดูข้อมูล/View</button>
                                <button class="btn btn-sm btn-secondary" onclick="editCustomerModal(${c.id})">แก้ไข/Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCustomer(${c.id})">ลบ/Delete</button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }
    } catch (e) {
        console.error("Load customers error", e);
    }
}

async function openViewCustomerModal(id) {
    try {
        const res = await fetch(`api/customers.php?id=${id}`);
        const json = await res.json();
        if (json.status === 'success') {
            const c = json.data;
            document.getElementById('cview-name').innerText = c.name;
            document.getElementById('cview-phone').innerText = c.phone;
            document.getElementById('cview-natid').innerText = c.national_id || 'ไม่ระบุ';
            document.getElementById('cview-license').innerText = c.driver_license || 'ไม่ระบุ';
            document.getElementById('cview-address').innerText = c.address || 'ไม่ระบุ';

            const img = document.getElementById('cview-idcard-img');
            if (c.id_card_image && c.id_card_image !== 'null' && c.id_card_image !== 'undefined') {
                img.src = c.id_card_image;
                img.style.display = 'inline-block';
            } else {
                img.src = '';
                img.style.display = 'none';
            }

            const dlImg = document.getElementById('cview-driver-license-img');
            if (dlImg) {
                if (c.driver_license_image && c.driver_license_image !== 'null' && c.driver_license_image !== 'undefined') {
                    dlImg.src = c.driver_license_image;
                    dlImg.style.display = 'inline-block';
                } else {
                    dlImg.src = '';
                    dlImg.style.display = 'none';
                }
            }

            const isVer = intval(c.is_verified) === 1;
            const btnBox = document.getElementById('cview-verify-btn-container');
            if (btnBox) {
                btnBox.innerHTML = !isVer ? `
                    <button class="btn btn-primary" style="background:linear-gradient(135deg,#10b981,#059669); font-weight:bold; padding:0.75rem 1.25rem;" onclick="toggleCustomerVerify(${c.id}, 1); closeModal('modal-view-customer');">
                        ☑️ คลิกเพื่ออนุมัติยืนยันตัวตน (Approve)
                    </button>
                ` : `
                    <button class="btn btn-danger" style="font-size:0.85rem; padding:0.6rem 1rem;" onclick="toggleCustomerVerify(${c.id}, 0); closeModal('modal-view-customer');">
                        ❎ ยกเลิกการยืนยันตัวตน
                    </button>
                `;
            }

            const histDiv = document.getElementById('cview-history-container');
            if (c.history && c.history.length > 0) {
                histDiv.innerHTML = c.history.map(h => `
                    <div style="background:rgba(255,255,255,0.03); padding:6px 10px; border-radius:6px; margin-bottom:4px; display:flex; justify-content:space-between;">
                        <span><strong>${h.rental_code}</strong>: ${h.brand} ${h.model} (${h.plate_number})</span>
                        <span class="badge badge-${h.status.toLowerCase()}">${h.status}</span>
                    </div>
                `).join('');
            } else {
                histDiv.innerHTML = `<span style="color:var(--text-muted);">ยังไม่มีประวัติการเช่ารถ</span>`;
            }

            openModal('modal-view-customer');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาดในการดึงข้อมูลลูกค้า', 'error');
    }
}

function openAddCustomerModal() {
    document.getElementById('cust-form-action').value = 'create';
    document.getElementById('cust-id').value = '';
    document.getElementById('cust-name').value = '';
    document.getElementById('cust-phone').value = '';
    document.getElementById('cust-natid').value = '';
    document.getElementById('cust-license').value = '';
    document.getElementById('cust-address').value = '';
    document.getElementById('cust-notes').value = '';
    document.getElementById('cust-modal-title').innerText = 'เพิ่มลูกค้าใหม่';
    openModal('modal-customer');
}

async function editCustomerModal(id) {
    try {
        const res = await fetch(`api/customers.php?id=${id}`);
        const json = await res.json();
        if (json.status === 'success') {
            const c = json.data;
            document.getElementById('cust-form-action').value = 'update';
            document.getElementById('cust-id').value = c.id;
            document.getElementById('cust-name').value = c.name;
            document.getElementById('cust-phone').value = c.phone;
            document.getElementById('cust-natid').value = c.national_id || '';
            document.getElementById('cust-license').value = c.driver_license || '';
            document.getElementById('cust-address').value = c.address || '';
            document.getElementById('cust-notes').value = c.notes || '';
            document.getElementById('cust-modal-title').innerText = 'แก้ไขข้อมูลลูกค้า';
            openModal('modal-customer');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาด', 'error');
    }
}

async function saveCustomerSubmit() {
    const action = document.getElementById('cust-form-action').value;
    const payload = {
        action: action,
        id: document.getElementById('cust-id').value,
        name: document.getElementById('cust-name').value,
        phone: document.getElementById('cust-phone').value,
        national_id: document.getElementById('cust-natid').value,
        driver_license: document.getElementById('cust-license').value,
        address: document.getElementById('cust-address').value,
        notes: document.getElementById('cust-notes').value
    };

    try {
        const res = await fetch('api/customers.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
            closeModal('modal-customer');
            loadCustomers();
        } else {
            showToast(json.message, 'error');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาดในการบันทึก', 'error');
    }
}

async function toggleCustomerVerify(id, newStatus) {
    const actionText = newStatus == 1 ? 'อนุมัติยืนยันตัวตน' : 'ยกเลิกการยืนยันตัวตน';
    if (!confirm(`ยืนยันที่จะ ${actionText} ของลูกค้ารายนี้หรือไม่?`)) return;

    try {
        const res = await fetch('api/customers.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'toggle_verify',
                id: id,
                is_verified: newStatus
            })
        });

        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
            loadCustomers();
        } else {
            showToast(json.message, 'error');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาดในการเปลี่ยนสถานะ', 'error');
    }
}

async function deleteCustomer(id) {
    if (!confirm('ยืนยันการลบลูกค้ารายนี้หรือไม่?')) return;

    try {
        const res = await fetch('api/customers.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'delete', id: id })
        });
        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
            loadCustomers();
        } else {
            showToast(json.message, 'error');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาดในการลบ', 'error');
    }
}
</script>
<script src="assets/js/app.js?v=2.0"></script>