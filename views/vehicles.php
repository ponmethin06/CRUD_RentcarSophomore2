<!-- views/vehicles.php - Real-Time Vehicle Management View -->
<style>
    /* CSS สำหรับไอคอน Inline ในปุ่มและองค์ประกอบต่างๆ */
    .btn-with-icon {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-with-icon svg,
    .icon-inline-svg {
        width: 18px;
        height: 18px;
        vertical-align: middle;
        flex-shrink: 0;
    }
    /* สีส้มหลักของระบบสำหรับไอคอน */
    .icon-orange {
        stroke: #ff5500 !important;
    }
    .btn-primary .icon-svg {
        stroke: #ffffff !important;
    }
</style>

<div id="vehicles-view">
    
    <!-- Top Filter Bar & Add Button -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <div class="custom-search-box" style="width: 250px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="search-vehicle" placeholder="ค้นหาทะเบียน/ยี่ห้อ/รุ่น..." oninput="loadVehicles()">
            </div>
            
            <select id="filter-category" class="form-control" style="width: 170px;" onchange="loadVehicles()">
                <option value="All">ทุกประเภทรถ</option>
                <option value="Motorcycle">จักรยานยนต์</option>
                <option value="Sedan">Sedan (เก๋ง)</option>
                <option value="SUV">SUV (อเนกประสงค์)</option>
                <option value="EV">EV (ไฟฟ้า)</option>
                <option value="Luxury">Luxury (หรูหรา)</option>
                <option value="Van">Van (ตู้)</option>
            </select>

            <select id="filter-status" class="form-control" style="width: 150px;" onchange="loadVehicles()">
                <option value="All">ทุกสถานะ</option>
                <option value="Available">Available (ว่าง)</option>
                <option value="Rented">Rented (กำลังเช่า)</option>
                <option value="Maintenance">Maintenance (ซ่อม)</option>
            </select>
        </div>

        <button class="btn btn-primary btn-with-icon" onclick="openAddVehicleModal()">
            <svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            เพิ่มรถเช่าคันใหม่
        </button>
    </div>

    <!-- Vehicles DataTable -->
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 60px; text-align: center;">ลำดับ</th>
                    <th>รูป & ข้อมูลรถ</th>
                    <th>เลขทะเบียน</th>
                    <th>ประเภท</th>
                    <th>ค่าเช่า/วัน</th>
                    <th>ค่ามัดจำ</th>
                    <th>สถานะ</th>
                    <th>จัดการ (Actions)</th>
                </tr>
            </thead>
            <tbody id="vehicle-table-body">
                <!-- Loaded dynamically via AJAX -->
            </tbody>
        </table>
    </div>

</div>

<!-- Modal: Vehicle Add / Edit Form -->
<div class="modal-overlay" id="modal-vehicle">
    <div class="modal-card">
        <div class="modal-header">
            <h3 id="veh-modal-title">เพิ่มรถเช่าใหม่</h3>
            <button class="close-btn" onclick="closeModal('modal-vehicle')">&times;</button>
        </div>
        <div>
            <input type="hidden" id="veh-form-action" value="create">
            <input type="hidden" id="veh-id" value="">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label">เลขทะเบียนรถ *</label>
                    <input type="text" id="veh-plate" class="form-control" placeholder="เช่น กข 1234 กทม">
                </div>
                <div class="form-group">
                    <label class="form-label">ประเภทรถ *</label>
                    <select id="veh-category" class="form-control">
                        <option value="Motorcycle">จักรยานยนต์</option>
                        <option value="Sedan">Sedan (เก๋ง)</option>
                        <option value="SUV">SUV (อเนกประสงค์)</option>
                        <option value="EV">EV (ไฟฟ้า)</option>
                        <option value="Luxury">Luxury (หรูหรา)</option>
                        <option value="Van">Van (ตู้)</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label">ยี่ห้อ (Brand) *</label>
                    <input type="text" id="veh-brand" class="form-control" placeholder="เช่น Toyota, Honda, BYD">
                </div>
                <div class="form-group">
                    <label class="form-label">รุ่น (Model) *</label>
                    <input type="text" id="veh-model" class="form-control" placeholder="เช่น Camry 2.5 HEV">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label">ค่าเช่าต่อวัน (บาท) *</label>
                    <input type="number" id="veh-daily" class="form-control" value="2000">
                </div>
                <div class="form-group">
                    <label class="form-label">ค่าเช่าต่อ ชม. (บาท)</label>
                    <input type="number" id="veh-hourly" class="form-control" value="250">
                </div>
                <div class="form-group">
                    <label class="form-label">ค่ามัดจำ (บาท) *</label>
                    <input type="number" id="veh-deposit" class="form-control" value="5000">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">สถานะรถ</label>
                <select id="veh-status" class="form-control">
                    <option value="Available">Available (ว่างพร้อมเช่า)</option>
                    <option value="Rented">Rented (กำลังถูกเช่าอยู่)</option>
                    <option value="Maintenance">Maintenance (ซ่อมบำรุง)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" style="display: flex; align-items: center; gap: 0.4rem;">
                    <svg class="icon-inline-svg icon-orange" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    เลือกรูปภาพรถ (อัปโหลดจากเครื่องคอมพิวเตอร์)
                </label>
                <input type="file" id="veh-image-file" class="form-control" accept="image/*" onchange="previewVehicleImage(this)">
                <input type="hidden" id="veh-image" value="">
                <div id="veh-image-preview-box" style="margin-top: 0.75rem; display: none; text-align: center; background: #faf5ff; padding: 0.5rem; border-radius: 12px; border: 1px dashed var(--border-color);">
                    <img id="veh-image-preview" src="" style="max-height: 140px; max-width: 100%; border-radius: 8px; object-fit: contain;">
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem;">
                <button class="btn btn-secondary" onclick="closeModal('modal-vehicle')">ยกเลิก</button>
                <button class="btn btn-primary btn-with-icon" onclick="saveVehicleSubmit()">
                    <svg class="icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    บันทึกข้อมูลรถ
                </button>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/app.js?v=1.1"></script>