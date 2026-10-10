<!-- views/rentals.php - Rentals & Vehicle Return Settlement View -->
<div id="rentals-view">
    
    <!-- Top Filter Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <div class="custom-search-box" style="width: 260px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="search-rental" placeholder="ค้นหาเลขสัญญา/ชื่อลูกค้า/ทะเบียน..." oninput="loadRentals()">
            </div>
            
            <select id="filter-rental-status" class="form-control" style="width: 170px;" onchange="loadRentals()">
                <option value="All">ทุกสถานะสัญญา</option>
                <option value="Active" selected>Active (กำลังเช่าอยู่)</option>
                <option value="Completed">Completed (คืนรถเรียบร้อย)</option>
            </select>
        </div>

        <a href="index.php?page=pos" class="btn btn-primary">
            + สร้างรายการเช่าใหม่ (POS)
        </a>
    </div>

    <!-- Rentals DataTable -->
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>เลขที่สัญญา</th>
                    <th>ผู้เช่า</th>
                    <th>รถเช่า</th>
                    <th>วันเวลารับรถ</th>
                    <th>กำหนดคืน</th>
                    <th>ยอดรวมเงิน</th>
                    <th>สถานะ</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody id="rentals-table-body">
                <!-- Loaded dynamically via AJAX -->
            </tbody>
        </table>
    </div>

</div>

<!-- Modal: Return Vehicle Settlement -->
<div class="modal-overlay" id="modal-return-vehicle">
    <div class="modal-card">
        <div class="modal-header">
            <h3>รับคืนรถยนต์ & สรุปยอดชำระ</h3>
            <button class="close-btn" onclick="closeModal('modal-return-vehicle')">&times;</button>
        </div>
        <div>
            <input type="hidden" id="return-rental-id" value="">

            <div style="background: rgba(252, 207, 173, 0.6); padding: 1rem; border-radius: 12px; margin-bottom: 1.25rem; border: 1px solid var(--border-color);">
                <div style="font-size: 0.85rem; color: var(--text-muted);">เลขสัญญา: <strong id="return-code-display" style="color:#3B51A1;"></strong></div>
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">ลูกค้า: <strong id="return-cust-display" style="color:#327830;"></strong></div>
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">รถยนต์: <strong id="return-veh-display" style="color:var(--primary);"></strong></div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                <div style="background:rgba(255,255,255,0.03); padding:0.85rem; border-radius:10px;">
                    <div style="font-size:0.75rem; color:var(--text-muted);">ค่าเช่าเดิม</div>
                    <div style="font-size:1.15rem; font-weight:700; color:white;" id="return-total-display">฿0.00</div>
                </div>
                <div style="background:rgba(255,255,255,0.03); padding:0.85rem; border-radius:10px;">
                    <div style="font-size:0.75rem; color:var(--text-muted);">มัดจำประกันรถ</div>
                    <div style="font-size:1.15rem; font-weight:700; color:var(--success);" id="return-deposit-display">฿0.00</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">ค่าบริการเพิ่มเติม / ค่าปรับส่งคืนช้า / ค่าน้ำมัน (บาท)</label>
                <input type="number" id="return-extra-charges" class="form-control" value="0">
            </div>

            <div class="form-group">
                <label class="form-label">วิธีชำระยอดคงเหลือ</label>
                <select id="return-payment-method" class="form-control">
                    <option value="Cash">เงินสด</option>
                    <option value="Transfer">โอนเงิน/Scan QR</option>
                    <option value="CreditCard">บัตรเครดิต</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">หมายเหตุการตรวจสภาพรถรับคืน</label>
                <textarea id="return-notes" class="form-control" rows="2" placeholder="เช่น รถเรียบร้อยดี, เติมน้ำมันเต็มถัง..."></textarea>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem;">
                <button class="btn btn-secondary" onclick="closeModal('modal-return-vehicle')">ยกเลิก</button>
                <button class="btn btn-primary" onclick="submitReturnVehicle()">ยืนยันรับคืนรถ & ปรับรถเป็น Available</button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/app.js?v=2.0"></script>