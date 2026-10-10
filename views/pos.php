<!-- views/pos.php - POS Point of Sale Checkout Interface -->
<style>
    /* ปรับแต่งไอคอนและองค์ประกอบให้ธีมสอดคล้องกัน */
    .pos-icon-inline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        vertical-align: middle;
        flex-shrink: 0;
    }
    .pos-cat-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .pos-cat-pill svg {
        width: 15px;
        height: 15px;
    }
</style>

<div id="pos-view">
    <div class="pos-layout">
        
        <!-- Left Side: Vehicle Selection Grid -->
        <div>
            <!-- Category Filters & Search -->
            <div style="display: flex; gap: 1rem; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap;">
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;" id="pos-cat-pill-container">
                    <button class="btn btn-sm pos-cat-pill active" onclick="setPOSCategory('All', this)">
                        ทั้งหมด
                    </button>
                    <button class="btn btn-sm pos-cat-pill" onclick="setPOSCategory('Motorcycle', this)">
                        <svg class="pos-icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M19 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M12 16h3l3-7h-5l-2 3h-3"/></svg>
                        จักรยานยนต์
                    </button>
                    <button class="btn btn-sm pos-cat-pill" onclick="setPOSCategory('Sedan', this)">
                        <svg class="pos-icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.5 2.8C1.4 11.2 1 12.1 1 13v3c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
                        Sedan
                    </button>
                    <button class="btn btn-sm pos-cat-pill" onclick="setPOSCategory('SUV', this)">
                        <svg class="pos-icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="8" width="22" height="8" rx="2"/><path d="M5 8V5a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3"/><circle cx="6" cy="16" r="2"/><circle cx="18" cy="16" r="2"/></svg>
                        SUV
                    </button>
                    <button class="btn btn-sm pos-cat-pill" onclick="setPOSCategory('EV', this)">
                        <svg class="pos-icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        EV (ไฟฟ้า)
                    </button>
                    <button class="btn btn-sm pos-cat-pill" onclick="setPOSCategory('Luxury', this)">
                        <svg class="pos-icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        Luxury
                    </button>
                    <button class="btn btn-sm pos-cat-pill" onclick="setPOSCategory('Van', this)">
                        <svg class="pos-icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="6" width="22" height="12" rx="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/><line x1="10" y1="6" x2="10" y2="12"/></svg>
                        Van (ตู้)
                    </button>
                </div>

                <div class="custom-search-box" style="width: 250px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="pos-search-input" placeholder="ค้นหาทะเบียน/ยี่ห้อ/รุ่น..." oninput="filterPOSVehicles()">
                </div>
            </div>

            <!-- Vehicle Cards Grid -->
            <div class="vehicle-grid" id="pos-vehicle-grid">
                <!-- Loaded Dynamically via JS AJAX -->
            </div>
        </div>

        <!-- Right Side: POS Checkout Sidebar Panel -->
        <div class="pos-summary-panel">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg class="pos-icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; color: var(--primary);"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                สรุปรายการปล่อยเช่า (POS Checkout)
            </h3>

            <!-- 1. Selected Vehicle Box -->
            <div style="background: var(--bg-card, #f8fafc); border: 1px solid var(--border-color); padding: 0.75rem 1rem; border-radius: 12px;">
                <div style="font-size: 0.8rem; color: #64748b; font-weight: 600; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.35rem;">
                    <svg class="pos-icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.5 2.8C1.4 11.2 1 12.1 1 13v3c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
                    รถที่เลือก:
                </div>
                <div style="font-size: 1.1rem; font-weight: 700; color: var(--primary);" id="pos-selected-veh-title">ยังไม่ได้เลือกรถ</div>
                <div style="font-size: 0.85rem; color: #475569;" id="pos-selected-veh-plate">โปรดคลิกเลือกรถจากรายการด้านซ้าย</div>
            </div>

            <!-- 2. Customer Lookup / Selection -->
            <div class="form-group" style="margin: 0;">
                <label class="form-label" style="font-weight: 600; color: #334155; display: flex; align-items: center; gap: 0.35rem;">
                    <svg class="pos-icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 15px; height: 15px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    ข้อมูลลูกค้า (Customer)
                </label>
                <select id="pos-customer-select" class="form-control" style="background: #ffffff; color: #0f172a; border: 1px solid var(--border-color); font-size: 0.9rem;">
                    <!-- Loaded via JS -->
                </select>
            </div>

            <!-- 3. Rental Dates & Time -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; color: #334155; display: flex; align-items: center; gap: 0.35rem;">
                        <svg class="pos-icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        วัน-เวลา รับรถ
                    </label>
                    <input type="datetime-local" id="pos-start-date" class="form-control" style="font-size: 0.85rem; background: #ffffff; color: #0f172a; border: 1px solid var(--border-color);">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; color: #334155; display: flex; align-items: center; gap: 0.35rem;">
                        <svg class="pos-icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                        วัน-เวลา คืนรถ
                    </label>
                    <input type="datetime-local" id="pos-end-date" class="form-control" style="font-size: 0.85rem; background: #ffffff; color: #0f172a; border: 1px solid var(--border-color);">
                </div>
            </div>

            <!-- 4. Price & Duration Breakdown -->
            <div style="border-top: 1px dashed var(--border-color); padding-top: 0.75rem; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.85rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #64748b; font-weight: 500;">อัตราเช่าต่อวัน:</span>
                    <strong id="pos-rate-display" style="color: #0f172a; font-weight: 700;">฿0.00</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #64748b; font-weight: 500;">ระยะเวลาเช่า:</span>
                    <strong id="pos-duration-display" style="color: var(--primary); font-weight: 700;">0 วัน</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #64748b; font-weight: 500;">รวมค่าเช่า (Subtotal):</span>
                    <strong id="pos-subtotal-display" style="color: #0f172a; font-weight: 700;">฿0.00</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #64748b; font-weight: 500;">เงินมัดจำประกันรถ:</span>
                    <strong id="pos-deposit-display" style="color: #0f172a; font-weight: 700;">฿0.00</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #64748b; font-weight: 500;">ส่วนลด (THB):</span>
                    <input type="number" id="pos-discount-input" class="form-control" style="width: 110px; padding: 6px 10px; text-align: right; background: #ffffff; color: #0f172a; border: 1px solid var(--border-color);" value="0" min="0" oninput="calculatePOSTotal()">
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #64748b; font-weight: 500;">ชำระด้วย:</span>
                    <select id="pos-payment-method" class="form-control" style="width: 145px; padding: 6px 10px; font-size: 0.85rem; background: #ffffff; color: #0f172a; border: 1px solid var(--border-color);">
                        <option value="Cash">เงินสด</option>
                        <option value="Transfer">โอนเงิน / Scan QR</option>
                        <option value="CreditCard">บัตรเครดิต</option>
                    </select>
                </div>
            </div>

            <!-- 5. Grand Total Calculation -->
            <div style="background: var(--bg-card, #f8fafc); border: 1px solid var(--border-color); border-radius: 12px; padding: 0.75rem; text-align: center; margin: 0; box-shadow: var(--shadow-card, 0 4px 12px rgba(0,0,0,0.03));">
                <div style="font-size: 0.85rem; color: #64748b; font-weight: 600;">ยอดรวมชำระสุทธิ (ค่าเช่า + มัดจำ)</div>
                <div style="font-size: 1.85rem; font-weight: 800; color: var(--primary);" id="pos-grandtotal-display">฿0.00</div>
            </div>

            <!-- Notes -->
            <div class="form-group" style="margin: 0; position: relative;">
                <input type="text" id="pos-notes-input" class="form-control" placeholder="หมายเหตุเพิ่มเติม (ถ้ามี)..." style="font-size: 0.85rem; background: #ffffff; color: #0f172a; border: 1px solid var(--border-color); padding-left: 2rem;">
                <svg class="pos-icon-inline" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.6rem; top: 50%; transform: translateY(-50%); width: 14px; height: 14px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </div>

            <!-- Submit Checkout Button -->
            <button class="btn btn-primary" style="padding: 0.75rem; font-size: 0.95rem; width: 100%; border-radius: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 0.5rem;" onclick="submitPOSCheckout()">
                <svg class="pos-icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><polyline points="20 6 9 17 4 12"/></svg>
                ยืนยันปล่อยรถเช่า
            </button>
        </div>

    </div>
</div><script src="assets/js/app.js?v=2.0"></script>