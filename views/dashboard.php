<!-- views/dashboard.php - Real-Time Analytics Dashboard -->
<style>
    /* CSS ปรับแต่งการแสดงผล SVG Icon ให้สวยงามสมดุล */
    .stat-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        flex-shrink: 0;
    }
    .stat-icon svg {
        width: 22px;
        height: 22px;
    }
    .stat-icon.icon-cyan {
        background-color: rgba(6, 182, 212, 0.1);
        color: #0891b2;
    }
    .stat-icon.icon-emerald {
        background-color: rgba(16, 185, 129, 0.1);
        color: #059669;
    }
    .stat-icon.icon-amber {
        background-color: rgba(245, 158, 11, 0.1);
        color: #d97706;
    }
    .stat-icon.icon-blue {
        background-color: rgba(59, 130, 246, 0.1);
        color: #2563eb;
    }
</style>

<div id="dashboard-view">
    <!-- Quick Metrics Row -->
    <div class="stats-grid">
        <!-- Card 1: Revenue -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">รายได้รวมสุทธิ (Total Revenue)</span>
                <div class="stat-icon icon-cyan">
                    <!-- Icon: Wallet / Revenue -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m-3-5h6" />
                    </svg>
                </div>
            </div>
            <div class="stat-value" id="dash-revenue">฿0.00</div>
            <p style="font-size: 0.78rem; color: var(--success); margin-top: 0.5rem; font-weight: 600;">+100% รายได้จากการเช่ารถ</p>
        </div>

        <!-- Card 2: Active Rentals -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">กำลังถูกเช่า (Active Rentals)</span>
                <div class="stat-icon icon-emerald">
                    <!-- Icon: Key / Active -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0121 7.5z" />
                    </svg>
                </div>
            </div>
            <div class="stat-value" id="dash-active-rentals">0</div>
            <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.5rem;">จำนวนรถที่กำลังใช้งานนอกสถานที่</p>
        </div>

        <!-- Card 3: Available Cars -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">รถพร้อมเช่า (Available Cars)</span>
                <div class="stat-icon icon-amber">
                    <!-- Icon: Car -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.25 2.25 0 00-1.875-.933H6.875a2.25 2.25 0 00-1.875.933A17.9 17.9 0 001.787 13.126 1.125 1.125 0 002.877 14.25H3.375m15.75 4.5V14.25m0 0H3.375" />
                    </svg>
                </div>
            </div>
            <div class="stat-value" id="dash-available-vehicles">0 / 0</div>
            <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.5rem;">พร้อมปล่อยเช่าทันที</p>
        </div>

        <!-- Card 4: Customers -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">ลูกค้ารวมในระบบ (Customers)</span>
                <div class="stat-icon icon-blue">
                    <!-- Icon: Users -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
            </div>
            <div class="stat-value" id="dash-total-customers">0</div>
            <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.5rem;">ประวัติลูกค้าที่ลงทะเบียน</p>
        </div>
    </div>

    <!-- Table Recent Rentals (Full Width Layout) -->
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 20px; padding: 1.5rem; box-shadow: var(--shadow-card); margin-top: 1.5rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 0.5rem;">
                <!-- Icon: Clipboard List -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#475569" style="width: 22px; height: 22px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm0 5.25h.007v.008H3.75V12zm0 5.25h.007v.008H3.75v-.008z" />
                </svg>
                รายการเช่ารถล่าสุด (Recent Rentals)
            </h3>
            <a href="index.php?page=rentals" class="btn btn-sm btn-secondary">ดูทั้งหมด →</a>
        </div>

        <div class="table-responsive" style="border: none; box-shadow: none;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>รหัสสัญญา</th>
                        <th>ลูกค้า</th>
                        <th>รถเช่า</th>
                        <th>วันที่เริ่ม</th>
                        <th>ยอดรวม</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody id="recent-rentals-list">
                    <!-- Loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>