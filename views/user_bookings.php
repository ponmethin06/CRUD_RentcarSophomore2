<!-- views/user_bookings.php - Customer My Bookings History & Invoice View -->
<?php
$currentUser =$_SESSION['user'] ?? null;
?>
<div id="user-bookings-view">
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 24px; padding: 2rem; margin-bottom: 2rem; box-shadow: var(--shadow-card);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a;"> ข้อมูลการเช่า & ประวัติการเช่าของฉัน (My Bookings)</h2>
                <p style="font-size: 0.88rem; color: var(--text-muted); margin-top: 2px;">รายการจอง, บิลการเช่า, สถานะรับ-คืนรถ และประวัติยานพาหนะทั้งหมดที่คุณเคยเช่า</p>
            </div>
            <a href="index.php?page=user_catalog" class="btn btn-primary"> + จองรถคันใหม่</a>
        </div>

        <?php if (!$currentUser): ?>
            <div style="text-align: center; padding: 3.5rem 2rem; background: #fff8f5; border-radius: 20px; border: 1px dashed #fcac84;">
                <div style="font-size: 3rem; margin-bottom: 1rem;"></div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">กรุณาเข้าสู่ระบบสมาชิก</h3>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.9rem;">เข้าสู่ระบบเพื่อดูข้อมูลการจอง บิลการเช่า และจัดการยกเลิกรายการเช่าของคุณ</p>
                <a href="login.php?tab=login" class="btn btn-primary"> เข้าสู่ระบบสมาชิก</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>รหัสการจอง / บิล</th>
                            <th>รถที่เช่า</th>
                            <th>วันเวลารับรถ</th>
                            <th>วันเวลาคืนรถ</th>
                            <th>ยอดรวม (THB)</th>
                            <th>สถานะการเช่า</th>
                            <th>การจัดการ / เอกสาร</th>
                        </tr>
                    </thead>
                    <tbody id="my-bookings-tbody">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>

            <!-- Vehicle History Summary Banner -->
            <div style="margin-top: 2.5rem; background: #fff8f5; border: 1px solid #ffe2d5; padding: 1.5rem; border-radius: 20px;">
                <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                     ยานพาหนะทั้งหมดที่เคยเช่าสำเร็จแล้ว (Rental History)
                </h3>
                <div id="my-vehicles-history-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem;">
                    <!-- Loaded via JS -->
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Box: Custom Confirmation Modal for Cancellation -->
<div class="modal-overlay" id="modal-confirm-cancel-booking">
    <div class="modal-card" style="max-width: 460px; text-align: center; border-top: 5px solid var(--danger);">
        
        <!-- Step 1: Form ยืนยันการยกเลิก -->
        <div id="cancel-step-form">
            <div style="width: 65px; height: 65px; background: #c40007; color: #dc2626; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1rem auto;">
                ⚠️
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem; line-height: 1.4;">
                หากยกเลิกคุณจะไม่ได้เงินมัดจำคืน และระบบจะดำเนินการคืนเงินค่าเช่ารถ
            </h3>
            <p style="color: #64748b; font-size: 0.92rem; margin-bottom: 1rem; line-height: 1.5;">
                คุณแน่ใจหรือไม่ว่าต้องการยกเลิกการจองรถเช่ารหัส <br><strong id="cancel-booking-code-text" style="color: var(--danger); font-size: 1.05rem;"></strong>
            </p>
            <div style="font-size: 0.83rem; color: #991b1b; margin-bottom: 1.5rem; background: #fef2f2; border: 1px solid #fecaca; padding: 0.75rem 0.85rem; border-radius: 12px; line-height: 1.5; text-align: left;">
                ❌ <strong>หมายเหตุเงื่อนไขการยกเลิก:</strong><br>
                • เงินมัดจำการจองจะ<strong>ไม่ได้รับการคืนเงิน</strong>ในทุกกรณี<br>
                • ระบบจะดำเนินการอนุมัติการคืนเฉพาะ <strong>"เงินค่าเช่ารถ"</strong> กลับเข้าสู่บัญชีของคุณ
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: center;">
                <button class="btn btn-secondary" style="flex: 1; padding: 0.75rem; border-radius: 12px;" onclick="closeModal('modal-confirm-cancel-booking')">
                    ย้อนกลับ
                </button>
                <button class="btn btn-danger" style="flex: 1; padding: 0.75rem; border-radius: 12px; font-weight: 700;" onclick="executeCancelBooking()">
                    ยืนยันยกเลิกเช่า
                </button>
            </div>
        </div>

        <!-- Step 2: แสดงสถานะกำลังดำเนินการ (Loading) -->
        <div id="cancel-step-loading" style="display: none; padding: 1.5rem 0;">
            <div style="border: 4px solid #f3f3f3; border-top: 4px solid var(--danger); border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite; margin: 0 auto 1.25rem auto;"></div>
            <h3 style="font-size: 1.2rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">กำลังดำเนินการยกเลิกและคืนเงินค่าเช่า</h3>
            <p style="font-size: 0.88rem; color: #64748b;">กรุณารอซักครู่ ระบบกำลังประมวลผลการยกเลิกและจัดเตรียมข้อมูลการคืนเงิน...</p>
        </div>

        <!-- Step 3: แสดงสถานะยกเลิกสำเร็จ -->
        <div id="cancel-step-success" style="display: none; padding: 1rem 0;">
            <div style="width: 65px; height: 65px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1rem auto;">
                ✅
            </div>
            <h3 style="font-size: 1.3rem; font-weight: 800; color: #16a34a; margin-bottom: 0.75rem;">
                ยืนยันการยกเลิกและคืนเงินค่าเช่าเรียบร้อยแล้ว
            </h3>
            <p style="font-size: 0.9rem; color: #475569; margin-bottom: 1.5rem; line-height: 1.5;">
                ระบบได้ทำการบันทึกการยกเลิกรายการเช่า และสร้างรายการคืนเงินค่าเช่ารถสำเร็จแล้ว
            </p>
            <button class="btn btn-primary" style="width: 100%; padding: 0.75rem; border-radius: 12px; font-weight: 700;" onclick="finishCancelProcess()">
                ตกลง / ปิดหน้าต่าง
            </button>
        </div>

    </div>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
let pendingCancelRentalId = null;

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('my-bookings-tbody')) {
        loadMyBookings();
    }
});

async function loadMyBookings() {
    const tbody = document.getElementById('my-bookings-tbody');
    const histGrid = document.getElementById('my-vehicles-history-grid');
    if (!tbody) return;

    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--text-muted);">กำลังโหลดข้อมูลและบิลการเช่าของคุณ...</td></tr>`;

    try {
        const res = await fetch('api/rentals.php');
        const json = await res.json();
        if (json.status === 'success') {
            const currentName = <?= json_encode($currentUser['fullname'] ?? '') ?>;
            const currentPhone = <?= json_encode($currentUser['phone'] ?? $currentUser['username'] ?? '') ?>;

            // Filter rentals for current logged-in customer
            const myRentals = json.data.filter(r => r.customer_name === currentName || r.customer_phone === currentPhone);

            if (myRentals.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:3rem; color:var(--text-muted);">คุณยังไม่มีประวัติการจองรถในขณะนี้</td></tr>`;
                if (histGrid) histGrid.innerHTML = `<span style="color:var(--text-muted); font-size:0.9rem;">ยังไม่มีประวัติการเช่ารถที่เสร็จสิ้น</span>`;
                return;
            }

            tbody.innerHTML = myRentals.map(r => {
                let badgeHTML = '';
                const comp = r.computed_status || r.status;
                if (comp === 'Cancelled') {
                    badgeHTML = `<span class="badge badge-rented">🔴 ยกเลิกและคืนเงินแล้ว</span>`;
                } else if (comp === 'Completed' || comp === 'Returned') {
                    badgeHTML = `<span class="badge badge-available">🟢 คืนรถเรียบร้อย</span>`;
                } else if (comp === 'Booked') {
                    badgeHTML = `<span class="badge badge-booked">🔵 จองแล้ว (รอรับรถ)</span>`;
                } else {
                    badgeHTML = `<span class="badge badge-completed">🟣 กำลังเช่าอยู่</span>`;
                }

                const canCancel = (comp === 'Booked' || comp === 'Active') && r.status !== 'Cancelled';

                return `
                    <tr>
                        <td>
                            <strong style="color:var(--primary); font-size:0.95rem;">${r.rental_code}</strong>
                        </td>
                        <td>
                            <strong>${r.brand} ${r.model}</strong><br>
                            <small style="color:var(--text-muted);">${r.plate_number} • ${(r.category === 'Motorcycle' || r.category === 'จักรยานยนต์') ? ' จักรยานยนต์' : (r.category || 'Sedan')}</small>
                        </td>
                        <td>${r.start_date.substring(0, 16)}</td>
                        <td>${r.end_date.substring(0, 16)}</td>
                        <td><strong style="color:#0f172a; font-size:1rem;">${formatMoney(parseFloat(r.grand_total) + parseFloat(r.deposit_amount))}</strong></td>
                        <td>${badgeHTML}</td>
                        <td>
                            <div style="display:flex; gap:0.4rem; flex-wrap:wrap;">
                                <button class="btn btn-sm btn-primary" onclick="openInvoicePrintModal(${r.id})">
                                     บิลการเช่า / ใบเสร็จ
                                </button>
                                ${canCancel ? `
                                    <button class="btn btn-sm btn-danger" onclick="cancelMyBooking(${r.id}, '${r.rental_code}')">
                                        ❌ ยกเลิกเช่า
                                    </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');

            // Filter successfully completed rented vehicles
            const completedRentals = myRentals.filter(r => r.status === 'Completed' || r.status === 'Returned');
            if (histGrid) {
                if (completedRentals.length === 0) {
                    histGrid.innerHTML = `<span style="color:var(--text-muted); font-size:0.9rem;">ยังไม่มีประวัติการเช่ารถที่คืนรถเสร็จสมบูรณ์</span>`;
                } else {
                    histGrid.innerHTML = completedRentals.map(h => `
                        <div style="background:#ffffff; border:1px solid var(--border-color); padding:0.85rem 1rem; border-radius:14px; box-shadow:0 2px 8px rgba(0,0,0,0.03);">
                            <div style="font-weight:700; color:#0f172a; font-size:0.95rem;">${h.brand} ${h.model}</div>
                            <div style="font-size:0.8rem; color:var(--primary); font-weight:600;">ทะเบียน: ${h.plate_number}</div>
                            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">คืนเมื่อ: ${h.actual_return_date ? h.actual_return_date.substring(0, 10) : h.end_date.substring(0, 10)}</div>
                        </div>
                    `).join('');
                }
            }
        }
    } catch (e) {
        console.error("Load my bookings error", e);
    }
}

function cancelMyBooking(rentalId, rentalCode) {
    pendingCancelRentalId = rentalId;
    const codeEl = document.getElementById('cancel-booking-code-text');
    if (codeEl) codeEl.innerText = `[${rentalCode}]`;
    
    // รีเซ็ตการแสดงผลของ Modal กลับไปที่ขั้นตอนแรก
    document.getElementById('cancel-step-form').style.display = 'block';
    document.getElementById('cancel-step-loading').style.display = 'none';
    document.getElementById('cancel-step-success').style.display = 'none';

    openModal('modal-confirm-cancel-booking');
}

async function executeCancelBooking() {
    if (!pendingCancelRentalId) return;

    // แสดงหน้าจอดาวน์โหลด/โหลดสถานะการทำงาน
    document.getElementById('cancel-step-form').style.display = 'none';
    document.getElementById('cancel-step-loading').style.display = 'block';

    try {
        const res = await fetch('api/rentals.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'cancel_user_booking',
                rental_id: pendingCancelRentalId
            })
        });

        const json = await res.json();

        // หน่วงเวลาเล็กน้อยเพื่อให้ผู้ใช้เห็นสถานะกำลังดำเนินการ (Loading)
        await new Promise(resolve => setTimeout(resolve, 1200));

        if (json.status === 'success') {
            // ซ่อนหน้าโหลด แสดงหน้าสำเร็จ
            document.getElementById('cancel-step-loading').style.display = 'none';
            document.getElementById('cancel-step-success').style.display = 'block';
        } else {
            // กรณีเกิดความผิดพลาด กลับไปหน้าฟอร์มเดิมพร้อมแจ้งเตือน
            document.getElementById('cancel-step-loading').style.display = 'none';
            document.getElementById('cancel-step-form').style.display = 'block';
            showToast(json.message, 'error');
        }
    } catch (e) {
        document.getElementById('cancel-step-loading').style.display = 'none';
        document.getElementById('cancel-step-form').style.display = 'block';
        showToast('เกิดข้อผิดพลาดในการยกเลิกการจอง', 'error');
    }
}

function finishCancelProcess() {
    closeModal('modal-confirm-cancel-booking');
    pendingCancelRentalId = null;
    loadMyBookings();
}
</script>
<script src="assets/js/app.js?v=2.0"></script>