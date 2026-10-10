<!-- views/user_invoices.php - Digital Invoices & Payment Receipts View for Customer -->
<?php
$currentUser =$_SESSION['user'] ?? null;
?>
<div id="user-invoices-view">
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 24px; padding: 2rem; margin-bottom: 2rem; box-shadow: var(--shadow-card);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 0.5rem;">
                     ใบเสร็จรับเงิน & เอกสารสัญญาเช่า (My Invoices & Receipts)
                </h2>
                <p style="font-size: 0.88rem; color: var(--text-muted); margin-top: 2px;">ดาวน์โหลดและพิมพ์ใบเสร็จรับเงิน ใบกำกับอย่างย่อ และสัญญาเช่าอิเล็กทรอนิกส์</p>
            </div>
            <a href="index.php?page=user_bookings" class="btn btn-secondary"> ดูประวัติการจองทั้งหมด</a>
        </div>

        <?php if (!$currentUser): ?>
            <div style="text-align: center; padding: 3.5rem 2rem; background: #faf5ff; border-radius: 20px; border: 1px dashed #fca884;">
                <div style="font-size: 3rem; margin-bottom: 1rem;"></div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">กรุณาเข้าสู่ระบบสมาชิก</h3>
                <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.9rem;">เข้าสู่ระบบเพื่อดูบิลและใบเสร็จการเช่าของคุณ</p>
                <a href="login.php?tab=login" class="btn btn-primary"> เข้าสู่ระบบสมาชิก</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>เลขที่บิล / สัญญา</th>
                            <th>รายการยานพาหนะ</th>
                            <th>วันที่เช่า</th>
                            <th>มัดจำ</th>
                            <th>ราคาค่าเช่ารถ</th>
                            <th>เงินรวมสุทธิ</th>
                            <th>สถานะการชำระ</th>
                            <th>การพิมพ์ / ออกบิล</th>
                        </tr>
                    </thead>
                    <tbody id="invoices-tbody">
                        <!-- Loaded via JS -->
                    </tbody>
                </table>
            </div>

            <!-- Notice Banner -->
            <div style="margin-top: 2rem; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 1.25rem 1.5rem; border-radius: 16px; display: flex; align-items: center; gap: 1rem;">
                <div style="font-size: 2rem;"></div>
                <div>
                    <strong style="color: #166534; font-size: 0.95rem;">ต้องการใบกำกับภาษีเต็มรูปแบบ?</strong>
                    <p style="color: #15803d; font-size: 0.85rem; margin-top: 2px;">คุณสามารถนำเลขที่บิลติดต่อแอดมินทาง Line หรือโทร 02-123-4567 เพื่อขอรับใบกำกับภาษีเต็มรูปแบบได้ตลอด 24 ชั่วโมง</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('invoices-tbody')) {
        loadMyInvoices();
    }
});

async function loadMyInvoices() {
    const tbody = document.getElementById('invoices-tbody');
    if (!tbody) return;

    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:2rem; color:var(--text-muted);">กำลังค้นหาใบเสร็จรับเงินของคุณ...</td></tr>`;

    try {
        const res = await fetch('api/rentals.php');
        const json = await res.json();
        if (json.status === 'success') {
            const currentName = <?= json_encode($currentUser['fullname'] ?? '') ?>;
            const currentPhone = <?= json_encode($currentUser['phone'] ?? $currentUser['username'] ?? '') ?>;

            const myRentals = json.data.filter(r => r.customer_name === currentName || r.customer_phone === currentPhone);

            if (myRentals.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:3rem; color:var(--text-muted);">ไม่พบประวัติใบเสร็จรับเงิน</td></tr>`;
                return;
            }

            tbody.innerHTML = myRentals.map(r => {
                const deposit = parseFloat(r.deposit_amount || 0);
                const grandTotal = parseFloat(r.grand_total || 0);
                const netTotal = deposit + grandTotal;

                // ตรวจสอบสถานะการยกเลิก
                const isCancelled = (r.status === 'Cancelled' || r.computed_status === 'Cancelled');

                // ปรับเปลี่ยนสถานะการชำระเงิน
                let paymentBadge = `<span class="badge badge-completed" style="font-size:0.78rem;">ชำระเงินเรียบร้อย</span>`;
                if (isCancelled) {
                    paymentBadge = `<span class="badge" style="font-size:0.78rem; background:#fff7ed; color:#c2410c; border:1px solid #ffedd5;"> คืนเงินเรียบร้อยแล้ว</span>`;
                }

                // ปรับเปลี่ยนปุ่มพิมพ์ใบเสร็จ
                let printButton = `
                    <button class="btn btn-sm btn-primary" onclick="openInvoicePrintModal(${r.id})" style="display:inline-flex; align-items:center; gap:0.3rem;">
                         พิมพ์ใบเสร็จ / PDF
                    </button>
                `;
                if (isCancelled) {
                    printButton = `
                        <button class="btn btn-sm" onclick="openInvoicePrintModal(${r.id})" style="display:inline-flex; align-items:center; gap:0.3rem; background:#ea580c; color:#ffffff; border:none;">
                             ใบเสร็จคืนเงิน / PDF
                        </button>
                    `;
                }

                return `
                    <tr>
                        <td>
                            <strong style="color:var(--primary); font-size:0.95rem;">${r.rental_code}</strong>
                            <div style="font-size:0.75rem; color:var(--text-muted);">${r.created_at.substring(0, 10)}</div>
                        </td>
                        <td>
                            <strong>${r.brand} ${r.model}</strong><br>
                            <small style="color:var(--text-muted);">${r.plate_number}</small>
                        </td>
                        <td>${r.start_date.substring(0, 10)} ถึง ${r.end_date.substring(0, 10)}</td>
                        <td><span style="color:#059669; font-weight:600;">${formatMoney(deposit)}</span></td>
                        <td><span style="color:#2563eb; font-weight:600;">${formatMoney(grandTotal)}</span></td>
                        <td><strong style="color:#0f172a; font-size:1.05rem;">${formatMoney(netTotal)}</strong></td>
                        <td>${paymentBadge}</td>
                        <td>${printButton}</td>
                    </tr>
                `;
            }).join('');
        }
    } catch (e) {
        console.error("Load invoices error", e);
    }
}
</script>