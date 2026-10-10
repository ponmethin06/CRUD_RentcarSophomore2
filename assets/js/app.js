/* assets/js/app.js - CarEngine POS Real-Time AJAX Engine */

// Common SVG Icons Map
const SVG_ICONS = {
    checkCircle: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`,
    alertTriangle: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>`,
    carSedan: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 1 12.3V16c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>`,
    carSuv: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="8" width="22" height="8" rx="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/><path d="M3 8V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3"/></svg>`,
    carLuxury: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 15h18l-2-5H5l-2 5z"/><circle cx="6.5" cy="17.5" r="1.5"/><circle cx="17.5" cy="17.5" r="1.5"/><path d="M14 10l-1-4H8.5L7.5 10"/></svg>`,
    carVan: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="6" width="22" height="10" rx="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/><line x1="16" y1="6" x2="16" y2="16"/></svg>`,
    motorcycle: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="16" r="3"/><circle cx="19" cy="16" r="3"/><path d="M12 16h3l2-5h-3l-2 5z"/><path d="M7 11l2-4h4"/></svg>`,
    evCharging: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>`,
    eye: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`,
    edit: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>`,
    trash: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>`,
    idCard: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="M7 11h4m-4 4h8"></path><circle cx="17" cy="9" r="1.5"></circle></svg>`,
    statusDotGreen: `<svg width="10" height="10" viewBox="0 0 10 10" fill="#10b981"><circle cx="5" cy="5" r="5"/></svg>`,
    statusDotRed: `<svg width="10" height="10" viewBox="0 0 10 10" fill="#ef4444"><circle cx="5" cy="5" r="5"/></svg>`
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.modal-overlay').forEach(m => {
        m.classList.remove('active');
        m.style.display = 'none';
    });

    if (document.getElementById('dashboard-view')) initDashboard();
    if (document.getElementById('pos-view')) initPOS();
    if (document.getElementById('vehicles-view')) loadVehicles();
    if (document.getElementById('customers-view')) loadCustomers();
    if (document.getElementById('rentals-view')) loadRentals();
});

// Utility: Toast Notification
function showToast(message, type = 'success') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    const icon = type === 'success' ? SVG_ICONS.checkCircle : SVG_ICONS.alertTriangle;
    toast.innerHTML = `
        <span style="display:inline-flex; align-items:center;">${icon}</span>
        <div>${message}</div>
    `;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// Utility: Modal Controls
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('active');
        modal.style.display = 'flex';
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('active');
        modal.style.display = 'none';
    }
}

// Global UI Confirmation Box Modal Controller
let pendingGlobalConfirmCallback = null;

function showConfirmModal(title, message, callback, btnText = 'ยืนยันทำรายการ', btnClass = 'btn-danger') {
    const titleEl = document.getElementById('confirm-modal-title');
    const msgEl = document.getElementById('confirm-modal-message');
    const btnEl = document.getElementById('confirm-modal-btn');

    if (titleEl) titleEl.innerText = title;
    if (msgEl) msgEl.innerText = message;
    if (btnEl) {
        btnEl.innerHTML = `<span style="display:inline-flex; align-items:center; gap:0.4rem;">${SVG_ICONS.checkCircle} ${btnText}</span>`;
        btnEl.className = `btn ${btnClass}`;
    }

    pendingGlobalConfirmCallback = callback;
    openModal('modal-confirm-action');
}

function executeGlobalConfirmAction() {
    closeModal('modal-confirm-action');
    if (typeof pendingGlobalConfirmCallback === 'function') {
        const cb = pendingGlobalConfirmCallback;
        pendingGlobalConfirmCallback = null;
        cb();
    }
}

// Format Currency THB
function formatMoney(amount) {
    return new Intl.NumberFormat('th-TH', { style: 'currency', currency: 'THB' }).format(amount);
}

// ----------------------------------------------------
// 1. DASHBOARD CONTROLLER
// ----------------------------------------------------
async function initDashboard() {
    try {
        const res = await fetch('api/dashboard.php');
        const json = await res.json();
        if (json.status === 'success') {
            const data = json.data;
            document.getElementById('dash-revenue').innerText = formatMoney(data.total_revenue);
            document.getElementById('dash-active-rentals').innerText = data.active_rentals;
            document.getElementById('dash-available-vehicles').innerText = `${data.available_vehicles} / ${data.total_vehicles}`;
            document.getElementById('dash-total-customers').innerText = data.total_customers;

            const tbody = document.getElementById('recent-rentals-list');
            if (tbody) {
                if (data.recent_rentals.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color: var(--text-muted);">ยังไม่มีรายการเช่าล่าสุด</td></tr>`;
                } else {
                    tbody.innerHTML = data.recent_rentals.map(r => `
                        <tr>
                            <td><strong>${r.rental_code}</strong></td>
                            <td>${r.customer_name}</td>
                            <td>${r.brand} ${r.model} (${r.plate_number})</td>
                            <td>${r.start_date.substring(0, 10)}</td>
                            <td>${formatMoney(r.grand_total)}</td>
                            <td>
                                <span class="badge badge-${r.status.toLowerCase()}">${r.status === 'Active' ? 'กำลังเช่า' : 'คืนแล้ว'}</span>
                            </td>
                        </tr>
                    `).join('');
                }
            }
        }
    } catch (e) {
        console.error("Dashboard error", e);
    }
}

// ----------------------------------------------------
// 2. POS RENTAL CHECKOUT CONTROLLER
// ----------------------------------------------------
let selectedVehicle = null;
let selectedCustomer = null;

async function initPOS() {
    loadPOSVehicles();
    loadPOSCustomers();
    
    const startInput = document.getElementById('pos-start-date');
    const endInput = document.getElementById('pos-end-date');
    
    if (startInput && endInput) {
        const now = new Date();
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);

        const pad = (n) => String(n).padStart(2, '0');
        const formatLocal = (d) => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;

        startInput.value = formatLocal(now);
        endInput.value = formatLocal(tomorrow);

        startInput.addEventListener('change', calculatePOSTotal);
        endInput.addEventListener('change', calculatePOSTotal);
    }
}

let posVehiclesCache = [];
let currentPOSCategory = 'All';

function setPOSCategory(cat, btn) {
    currentPOSCategory = cat;
    document.querySelectorAll('.pos-cat-pill').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    const search = document.getElementById('pos-search-input')?.value || '';
    loadPOSVehicles(cat, search);
}

function filterPOSVehicles() {
    const search = document.getElementById('pos-search-input')?.value || '';
    loadPOSVehicles(currentPOSCategory, search);
}

async function loadPOSVehicles(category = 'All', search = '') {
    const grid = document.getElementById('pos-vehicle-grid');
    if (!grid) return;
    grid.innerHTML = `<div style="color:var(--text-muted); padding:2rem; text-align:center;">กำลังโหลดข้อมูลรถ...</div>`;

    try {
        const res = await fetch(`api/vehicles.php?mode=pos&category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}`);
        const json = await res.json();
        if (json.status === 'success') {
            posVehiclesCache = json.data;
            if (posVehiclesCache.length === 0) {
                grid.innerHTML = `<div style="grid-column: 1/-1; padding: 3rem; text-align: center; color: var(--text-muted);">ไม่มีรายการรถในหมวดหมู่นี้</div>`;
                return;
            }

            grid.innerHTML = posVehiclesCache.map(v => {
                const isMoto = v.category === 'Motorcycle' || v.category === 'จักรยานยนต์' || /(wave|dream|dreeam|click|pcx|forza|xmax|nmax|filano|scoopy|rebel|110|cub|vespa|aerox|fazzio|lead|giorno|adv)/i.test(`${v.brand} ${v.model}`);
                const catLabel = isMoto ? 'จักรยานยนต์' : (v.category || 'Sedan');
                const isRented = v.is_currently_rented || v.status === 'Rented';
                return `
                <div class="vehicle-card ${selectedVehicle && selectedVehicle.id === v.id ? 'selected' : ''}" data-veh-id="${v.id}" style="opacity:${isRented ? '0.65' : '1'}; cursor:${isRented ? 'not-allowed' : 'pointer'};" onclick="${isRented ? "showToast('รถคันนี้กำลังติดเช่าอยู่ ไม่สามารถเลือกปล่อยเช่าซ้ำได้', 'error')" : `selectPOSVehicle(${v.id}, this)`}">
                    <img src="${v.image_url || 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=600&auto=format&fit=crop&q=80'}" class="vehicle-img" alt="${v.model}">
                    <div class="vehicle-info">
                        <div class="vehicle-title">${v.brand} ${v.model}</div>
                        <div class="vehicle-plate">${v.plate_number} • ${catLabel}</div>
                        <div style="display:flex; align-items:center; justify-content:space-between;">
                            <span class="vehicle-rate">${formatMoney(v.daily_rate)} <small style="font-size:0.75rem; color:var(--text-muted);">/วัน</small></span>
                            ${isRented ? `<span class="badge badge-rented" style="display:inline-flex; align-items:center; gap:0.35rem;">${SVG_ICONS.statusDotRed} ติดเช่าอยู่</span>` : `<span class="badge badge-available" style="display:inline-flex; align-items:center; gap:0.35rem;">${SVG_ICONS.statusDotGreen} พร้อมเช่า</span>`}
                        </div>
                    </div>
                </div>
                `;
            }).join('');
        }
    } catch (e) {
        console.error("Load POS Vehicles error", e);
    }
}

async function selectPOSVehicle(id, el = null) {
    try {
        let v = posVehiclesCache.find(item => item.id == id);
        if (!v) {
            const res = await fetch(`api/vehicles.php?id=${id}`);
            const json = await res.json();
            if (json.status === 'success') v = json.data;
        }

        if (v) {
            selectedVehicle = v;
            
            document.querySelectorAll('.vehicle-card').forEach(c => c.classList.remove('selected'));
            const targetEl = el || document.querySelector(`.vehicle-card[data-veh-id="${id}"]`);
            if (targetEl) targetEl.classList.add('selected');

            const titleEl = document.getElementById('pos-selected-veh-title');
            const plateEl = document.getElementById('pos-selected-veh-plate');
            const rateEl = document.getElementById('pos-rate-display');
            const depositEl = document.getElementById('pos-deposit-display');

            if (titleEl) titleEl.innerText = `${selectedVehicle.brand} ${selectedVehicle.model}`;
            if (plateEl) plateEl.innerText = selectedVehicle.plate_number;
            if (rateEl) rateEl.innerText = formatMoney(selectedVehicle.daily_rate);
            if (depositEl) depositEl.innerText = formatMoney(selectedVehicle.deposit_amount);

            calculatePOSTotal();
        }
    } catch (e) {
        console.error("Select vehicle error", e);
    }
}

async function loadPOSCustomers() {
    const select = document.getElementById('pos-customer-select');
    if (!select) return;

    try {
        const res = await fetch('api/customers.php');
        const json = await res.json();
        if (json.status === 'success') {
            if (json.data && json.data.length > 0) {
                select.innerHTML = `<option value="">-- กรุณาเลือกข้อมูลลูกค้า --</option>` +
                    json.data.map(c => `<option value="${c.id}">${c.name} (${c.phone})</option>`).join('');
            } else {
                select.innerHTML = `<option value="">-- ยังไม่มีรายชื่อลูกค้าในระบบ --</option>`;
            }
        }
    } catch (e) {
        console.error(e);
    }
}

function calculatePOSTotal() {
    if (!selectedVehicle) return;

    const startVal = document.getElementById('pos-start-date')?.value;
    const endVal = document.getElementById('pos-end-date')?.value;

    if (!startVal || !endVal) return;

    const start = new Date(startVal);
    const end = new Date(endVal);

    const diffTime = Math.max(0, end - start);
    let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    if (diffDays <= 0) diffDays = 1;

    const durEl = document.getElementById('pos-duration-display');
    if (durEl) durEl.innerText = `${diffDays} วัน`;

    const subtotal = selectedVehicle.daily_rate * diffDays;
    const deposit = floatval(selectedVehicle.deposit_amount || 0);
    const discount = floatval(document.getElementById('pos-discount-input')?.value || 0);
    const grandTotal = Math.max(0, subtotal - discount) + deposit;

    const subtotalEl = document.getElementById('pos-subtotal-display');
    if (subtotalEl) subtotalEl.innerText = formatMoney(subtotal);

    const grandtotalEl = document.getElementById('pos-grandtotal-display');
    if (grandtotalEl) grandtotalEl.innerText = formatMoney(grandTotal);
}

function floatval(val) {
    const parsed = parseFloat(val);
    return isNaN(parsed) ? 0 : parsed;
}

async function submitPOSCheckout() {
    if (!selectedVehicle) {
        showToast('กรุณาเลือกรถเช่าก่อนทำรายการ', 'error');
        return;
    }

    const custSelect = document.getElementById('pos-customer-select');
    const customerId = custSelect ? (custSelect.value || 0) : 0;

    const startDate = document.getElementById('pos-start-date').value;
    const endDate = document.getElementById('pos-end-date').value;
    const paymentMethod = document.getElementById('pos-payment-method').value;
    const discount = floatval(document.getElementById('pos-discount-input').value);
    const notes = document.getElementById('pos-notes-input').value;

    const start = new Date(startDate);
    const end = new Date(endDate);
    let totalDays = Math.ceil(Math.max(0, end - start) / (1000 * 60 * 60 * 24));
    if (totalDays <= 0) totalDays = 1;

    const payload = {
        action: 'create',
        vehicle_id: selectedVehicle.id,
        customer_id: customerId,
        start_date: startDate,
        end_date: endDate,
        daily_rate: selectedVehicle.daily_rate,
        total_days: totalDays,
        deposit_amount: selectedVehicle.deposit_amount,
        discount: discount,
        payment_method: paymentMethod,
        notes: notes
    };

    try {
        const res = await fetch('api/rentals.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const json = await res.json();
        if (json.status === 'success') {
            showToast('ปล่อยรถเช่าเรียบร้อยแล้ว!');
            openInvoicePrintModal(json.rental_id);
            selectedVehicle = null;
            loadPOSVehicles();
        } else {
            showToast(json.message, 'error');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
    }
}

async function savePOSQuickCustomer() {
    const name = document.getElementById('cust-quick-name').value;
    const phone = document.getElementById('cust-quick-phone').value;
    const natId = document.getElementById('cust-quick-natid').value;
    const license = document.getElementById('cust-quick-license').value;

    if (!name || !phone) {
        showToast('กรุณากรอกชื่อและเบอร์โทรศัพท์', 'error');
        return;
    }

    try {
        const res = await fetch('api/customers.php', {
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

        const json = await res.json();
        if (json.status === 'success') {
            showToast('เพิ่มลูกค้าเรียบร้อย');
            closeModal('modal-quick-customer');
            await loadPOSCustomers();
            document.getElementById('pos-customer-select').value = json.id;
        } else {
            showToast(json.message, 'error');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาด', 'error');
    }
}

// ----------------------------------------------------
// 3. VEHICLES REAL-TIME CRUD CONTROLLER
// ----------------------------------------------------
function formatVehicleCategoryBadge(cat, brand = '', model = '') {
    const isMoto = cat === 'Motorcycle' || cat === 'จักรยานยนต์' || cat === 'มอเตอร์ไซค์' || /(wave|dream|dreeam|click|pcx|forza|xmax|nmax|filano|scoopy|rebel|110|cub|vespa|aerox|fazzio|lead|giorno|adv)/i.test(`${brand} ${model}`);
    
    if (isMoto) return `<span class="badge" style="background:#ede9fe; color:#6d28d9; border:1px solid #ddd6fe; font-weight:700; padding:0.4rem 0.75rem; border-radius:20px; display:inline-flex; align-items:center; gap:0.35rem;">${SVG_ICONS.motorcycle} จักรยานยนต์</span>`;
    if (cat === 'EV') return `<span class="badge" style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; font-weight:700; padding:0.4rem 0.75rem; border-radius:20px; display:inline-flex; align-items:center; gap:0.35rem;">${SVG_ICONS.evCharging} EV (ไฟฟ้า)</span>`;
    if (cat === 'SUV') return `<span class="badge" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a; font-weight:700; padding:0.4rem 0.75rem; border-radius:20px; display:inline-flex; align-items:center; gap:0.35rem;">${SVG_ICONS.carSuv} SUV (อเนกประสงค์)</span>`;
    if (cat === 'Luxury') return `<span class="badge" style="background:#fae8ff; color:#86198f; border:1px solid #f5d0fe; font-weight:700; padding:0.4rem 0.75rem; border-radius:20px; display:inline-flex; align-items:center; gap:0.35rem;">${SVG_ICONS.carLuxury} Luxury (หรูหรา)</span>`;
    if (cat === 'Van') return `<span class="badge" style="background:#ffedd5; color:#c2410c; border:1px solid #fed7aa; font-weight:700; padding:0.4rem 0.75rem; border-radius:20px; display:inline-flex; align-items:center; gap:0.35rem;">${SVG_ICONS.carVan} Van (ตู้)</span>`;
    
    const label = (cat === 'Sedan' || !cat) ? 'Sedan (เก๋ง)' : cat;
    return `<span class="badge" style="background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; font-weight:700; padding:0.4rem 0.75rem; border-radius:20px; display:inline-flex; align-items:center; gap:0.35rem;">${SVG_ICONS.carSedan} ${label}</span>`;
}

let currentVehicleCategory = 'All';

function setVehicleCategoryFilter(cat, btn) {
    currentVehicleCategory = cat;
    const catInput = document.getElementById('filter-category');
    if (catInput) catInput.value = cat;

    document.querySelectorAll('.veh-cat-pill').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    loadVehicles();
}

async function loadVehicles() {
    const tbody = document.getElementById('vehicle-table-body');
    if (!tbody) return;

    const search = document.getElementById('search-vehicle')?.value || '';
    const category = document.getElementById('filter-category')?.value || currentVehicleCategory || 'All';
    const status = document.getElementById('filter-status')?.value || 'All';

    try {
        const res = await fetch(`api/vehicles.php?search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}&status=${encodeURIComponent(status)}`);
        const json = await res.json();
        if (json.status === 'success') {
            const vehicles = json.data;
            if (vehicles.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:2rem; color:var(--text-muted);">ไม่พบข้อมูลรถในระบบ</td></tr>`;
                return;
            }

            const fallbackImg = 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=600&auto=format&fit=crop&q=80';

            tbody.innerHTML = vehicles.map((v, index) => `
                <tr>
                    <td style="text-align:center; font-weight:700; color:var(--primary);">${index + 1}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:0.75rem;">
                            <img src="${v.image_url || fallbackImg}" onerror="this.onerror=null; this.src='${fallbackImg}'" style="width:54px; height:38px; border-radius:8px; object-fit:cover; border:1px solid #e2e8f0;">
                            <div>
                                <strong>${v.brand ? v.brand + ' ' : ''}${v.model}</strong>
                            </div>
                        </div>
                    </td>
                    <td><strong>${v.plate_number}</strong></td>
                    <td>${formatVehicleCategoryBadge(v.category, v.brand, v.model)}</td>
                    <td>${formatMoney(v.daily_rate)}</td>
                    <td>${formatMoney(v.deposit_amount)}</td>
                    <td><span class="badge badge-${v.status.toLowerCase()}">${v.status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="editVehicleModal(${v.id})" style="display:inline-flex; align-items:center; gap:0.25rem;">${SVG_ICONS.edit} แก้ไข</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteVehicle(${v.id})" style="display:inline-flex; align-items:center; gap:0.25rem;">${SVG_ICONS.trash} ลบ</button>
                    </td>
                </tr>
            `).join('');
        }
    } catch (e) {
        console.error(e);
    }
}

function previewVehicleImage(input) {
    const box = document.getElementById('veh-image-preview-box');
    const img = document.getElementById('veh-image-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            if (img) img.src = e.target.result;
            if (box) box.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function openAddVehicleModal() {
    document.getElementById('veh-form-action').value = 'create';
    document.getElementById('veh-id').value = '';
    document.getElementById('veh-plate').value = '';
    document.getElementById('veh-brand').value = '';
    document.getElementById('veh-model').value = '';
    document.getElementById('veh-category').value = 'Sedan';
    document.getElementById('veh-daily').value = '2000';
    document.getElementById('veh-hourly').value = '250';
    document.getElementById('veh-deposit').value = '5000';
    document.getElementById('veh-status').value = 'Available';
    document.getElementById('veh-image').value = '';
    const fileInput = document.getElementById('veh-image-file');
    if (fileInput) fileInput.value = '';
    const box = document.getElementById('veh-image-preview-box');
    if (box) box.style.display = 'none';
    document.getElementById('veh-modal-title').innerText = 'เพิ่มรถเช่าใหม่';
    openModal('modal-vehicle');
}

async function editVehicleModal(id) {
    try {
        const res = await fetch(`api/vehicles.php?id=${id}`);
        const json = await res.json();
        if (json.status === 'success') {
            const v = json.data;
            document.getElementById('veh-form-action').value = 'update';
            document.getElementById('veh-id').value = v.id;
            document.getElementById('veh-plate').value = v.plate_number;
            document.getElementById('veh-brand').value = v.brand;
            document.getElementById('veh-model').value = v.model;
            let catVal = v.category || 'Sedan';
            const isMoto = catVal === 'Motorcycle' || catVal === 'จักรยานยนต์' || catVal === 'มอเตอร์ไซค์' || /(wave|dream|dreeam|click|pcx|forza|xmax|nmax|filano|scoopy|rebel|110|cub|vespa|aerox|fazzio|lead|giorno|adv)/i.test(`${v.brand} ${v.model}`);
            if (isMoto) catVal = 'Motorcycle';
            const catSelect = document.getElementById('veh-category');
            if (catSelect) {
                catSelect.value = catVal;
                if (!catSelect.value) catSelect.value = 'Motorcycle';
            }
            document.getElementById('veh-daily').value = v.daily_rate;
            document.getElementById('veh-hourly').value = v.hourly_rate;
            document.getElementById('veh-deposit').value = v.deposit_amount;
            document.getElementById('veh-status').value = v.status;
            document.getElementById('veh-image').value = v.image_url || '';
            
            const fileInput = document.getElementById('veh-image-file');
            if (fileInput) fileInput.value = '';
            
            const box = document.getElementById('veh-image-preview-box');
            const img = document.getElementById('veh-image-preview');
            if (v.image_url) {
                if (img) img.src = v.image_url;
                if (box) box.style.display = 'block';
            } else {
                if (box) box.style.display = 'none';
            }

            document.getElementById('veh-modal-title').innerText = 'แก้ไขข้อมูลรถเช่า';
            openModal('modal-vehicle');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาด', 'error');
    }
}

async function saveVehicleSubmit() {
    const action = document.getElementById('veh-form-action').value;
    const formData = new FormData();
    formData.append('action', action);
    formData.append('id', document.getElementById('veh-id').value);
    formData.append('plate_number', document.getElementById('veh-plate').value);
    formData.append('brand', document.getElementById('veh-brand').value);
    formData.append('model', document.getElementById('veh-model').value);
    formData.append('category', document.getElementById('veh-category').value);
    formData.append('daily_rate', document.getElementById('veh-daily').value);
    formData.append('hourly_rate', document.getElementById('veh-hourly').value);
    formData.append('deposit_amount', document.getElementById('veh-deposit').value);
    formData.append('status', document.getElementById('veh-status').value);
    formData.append('image_url', document.getElementById('veh-image').value);

    const fileInput = document.getElementById('veh-image-file');
    if (fileInput && fileInput.files[0]) {
        formData.append('image_file', fileInput.files[0]);
    }

    try {
        const res = await fetch('api/vehicles.php', {
            method: 'POST',
            body: formData
        });

        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
            closeModal('modal-vehicle');
            loadVehicles();
        } else {
            showToast(json.message, 'error');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาดในการบันทึก', 'error');
    }
}

async function deleteVehicle(id) {
    showConfirmModal(
        'ยืนยันลบข้อมูลรถเช่า',
        'คุณแน่ใจหรือไม่ที่จะลบข้อมูลรถคันนี้ออกจากระบบ?',
        async () => {
            try {
                const res = await fetch('api/vehicles.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete', id: id })
                });
                const json = await res.json();
                if (json.status === 'success') {
                    showToast(json.message);
                    loadVehicles();
                } else {
                    showToast(json.message, 'error');
                }
            } catch (e) {
                showToast('เกิดข้อผิดพลาดในการลบ', 'error');
            }
        },
        'ยืนยันลบรถ',
        'btn-danger'
    );
}

// ----------------------------------------------------
// 4. CUSTOMERS REAL-TIME CRUD & IDENTITY VERIFICATION CONTROLLER
// ----------------------------------------------------
async function loadCustomers() {
    const tbody = document.getElementById('customer-table-body');
    if (!tbody) return;

    const search = document.getElementById('search-customer')?.value || '';

    try {
        const res = await fetch(`api/customers.php?search=${encodeURIComponent(search)}`);
        const json = await res.json();
        if (json.status === 'success') {
            const customers = json.data;
            if (customers.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:2rem; color:var(--text-muted);">ไม่พบข้อมูลลูกค้า</td></tr>`;
                return;
            }

            tbody.innerHTML = customers.map(c => {
                const isVer = parseInt(c.is_verified || 0) === 1;
                return `
                    <tr>
                        <td><strong>#${c.id}</strong></td>
                        <td><strong>${c.name}</strong></td>
                        <td>${c.phone}</td>
                        <td>${c.national_id || '-'}</td>
                        <td>${c.driver_license || '-'}</td>
                        <td>
                            ${c.id_card_image ? `<a href="${c.id_card_image}" target="_blank" style="color:var(--primary); font-weight:bold; font-size:0.85rem; display:inline-flex; align-items:center; gap:0.25rem;">${SVG_ICONS.idCard} ดูรูปภาพบัตร</a>` : '<span style="color:var(--text-muted); font-size:0.8rem;">ยังไม่อัปโหลด</span>'}
                        </td>
                        <td>
                            ${isVer ? `
                                <label style="display:inline-flex; align-items:center; gap:0.4rem; background:rgba(16,185,129,0.2); border:1px solid rgba(16,185,129,0.4); color:#34d399; padding:0.35rem 0.75rem; border-radius:20px; font-size:0.8rem; cursor:pointer; font-weight:600;">
                                    <input type="checkbox" checked onchange="toggleCustomerVerify(${c.id}, 0)" style="width:16px; height:16px; accent-color:#10b981; cursor:pointer;">
                                    <span style="display:inline-flex; align-items:center; gap:0.25rem;">${SVG_ICONS.statusDotGreen} ยืนยันสำเร็จแล้ว</span>
                                </label>
                            ` : `
                                <label style="display:inline-flex; align-items:center; gap:0.4rem; background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#f87171; padding:0.35rem 0.75rem; border-radius:20px; font-size:0.8rem; cursor:pointer; font-weight:600;">
                                    <input type="checkbox" onchange="toggleCustomerVerify(${c.id}, 1)" style="width:16px; height:16px; cursor:pointer;">
                                    <span style="display:inline-flex; align-items:center; gap:0.25rem;">${SVG_ICONS.statusDotRed} ยังไม่ได้ยืนยัน</span>
                                </label>
                            `}
                        </td>
                        <td>
                            <div style="display:flex; gap:0.35rem; flex-wrap:wrap;">
                                <button class="btn btn-sm btn-secondary" onclick="openViewCustomerModal(${c.id})" style="display:inline-flex; align-items:center; gap:0.2rem;">${SVG_ICONS.eye} View</button>
                                <button class="btn btn-sm btn-secondary" onclick="editCustomerModal(${c.id})" style="display:inline-flex; align-items:center; gap:0.2rem;">${SVG_ICONS.edit} Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCustomer(${c.id})" style="display:inline-flex; align-items:center; gap:0.2rem;">${SVG_ICONS.trash} ลบ</button>
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
            img.src = (c.id_card_image && c.id_card_image !== 'null' && c.id_card_image !== 'undefined') ? c.id_card_image : 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=600&auto=format&fit=crop&q=80';

            const dlImg = document.getElementById('cview-driver-license-img');
            if (dlImg) {
                dlImg.src = (c.driver_license_image && c.driver_license_image !== 'null' && c.driver_license_image !== 'undefined') ? c.driver_license_image : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=600&auto=format&fit=crop&q=80';
            }

            const isVer = parseInt(c.is_verified || 0) === 1;
            const btnBox = document.getElementById('cview-verify-btn-container');
            if (btnBox) {
                btnBox.innerHTML = !isVer ? `
                    <button class="btn btn-primary" style="background:linear-gradient(135deg,#10b981,#059669); font-weight:bold; padding:0.75rem 1.25rem; display:inline-flex; align-items:center; gap:0.4rem;" onclick="toggleCustomerVerify(${c.id}, 1); closeModal('modal-view-customer');">
                        ${SVG_ICONS.checkCircle} คลิกเพื่ออนุมัติยืนยันตัวตน (Approve)
                    </button>
                ` : `
                    <button class="btn btn-danger" style="font-size:0.85rem; padding:0.6rem 1rem;" onclick="toggleCustomerVerify(${c.id}, 0); closeModal('modal-view-customer');">
                        ยกเลิกการยืนยันตัวตน
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
    const btnColor = newStatus == 1 ? 'btn-primary' : 'btn-danger';
    showConfirmModal(
        'ยืนยันการเปลี่ยนสถานะสมาชิก',
        `คุณแน่ใจหรือไม่ที่จะ ${actionText} ของลูกค้ารายนี้?`,
        async () => {
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
        },
        `ยืนยัน${actionText}`,
        btnColor
    );
}

async function deleteCustomer(id) {
    showConfirmModal(
        'ยืนยันลบข้อมูลลูกค้า',
        'คุณแน่ใจหรือไม่ที่จะลบลูกค้ารายนี้ออกจากระบบ?',
        async () => {
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
        },
        'ยืนยันลบลูกค้า',
        'btn-danger'
    );
}

// ----------------------------------------------------
// 5. RENTALS & RETURN SETTLEMENT CONTROLLER
// ----------------------------------------------------
async function loadRentals() {
    const tbody = document.getElementById('rentals-table-body');
    if (!tbody) return;

    const status = document.getElementById('filter-rental-status')?.value || 'All';
    const search = document.getElementById('search-rental')?.value || '';

    try {
        const res = await fetch(`api/rentals.php?status=${status}&search=${encodeURIComponent(search)}`);
        const json = await res.json();
        if (json.status === 'success') {
            const rentals = json.data;
            if (rentals.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:2rem; color:var(--text-muted);">ไม่พบรายการเช่ารถ</td></tr>`;
                return;
            }

            tbody.innerHTML = rentals.map(r => `
                <tr>
                    <td><strong>${r.rental_code}</strong></td>
                    <td>${r.customer_name}<br><small style="color:var(--text-muted);">${r.customer_phone}</small></td>
                    <td>${r.brand} ${r.model}<br><small style="color:var(--primary);">${r.plate_number}</small></td>
                    <td>${r.start_date.substring(0, 16)}</td>
                    <td>${r.end_date.substring(0, 16)}</td>
                    <td>${formatMoney(parseFloat(r.grand_total) + parseFloat(r.deposit_amount))}</td>
                    <td><span class="badge badge-${r.status.toLowerCase()}">${r.status === 'Active' ? 'กำลังเช่า' : 'คืนเรียบร้อย'}</span></td>
                    <td>
                        ${r.status === 'Active' ? `<button class="btn btn-sm btn-primary" onclick="openReturnModal(${r.id})">รับคืนรถ</button>` : ''}
                        <button class="btn btn-sm btn-secondary" onclick="openInvoicePrintModal(${r.id})">ใบเสร็จ/สัญญา</button>
                    </td>
                </tr>
            `).join('');
        }
    } catch (e) {
        console.error(e);
    }
}

async function openReturnModal(rentalId) {
    try {
        const res = await fetch(`api/rentals.php?id=${rentalId}`);
        const json = await res.json();
        if (json.status === 'success') {
            const r = json.data;
            document.getElementById('return-rental-id').value = r.id;
            document.getElementById('return-code-display').innerText = r.rental_code;
            document.getElementById('return-cust-display').innerText = `${r.customer_name} (${r.customer_phone})`;
            document.getElementById('return-veh-display').innerText = `${r.brand} ${r.model} [${r.plate_number}]`;
            document.getElementById('return-total-display').innerText = formatMoney(r.grand_total);
            document.getElementById('return-deposit-display').innerText = formatMoney(r.deposit_amount);

            document.getElementById('return-extra-charges').value = '0';
            document.getElementById('return-notes').value = '';

            openModal('modal-return-vehicle');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาดในการโหลดข้อมูลสัญญา', 'error');
    }
}

async function submitReturnVehicle() {
    const rentalId = document.getElementById('return-rental-id').value;
    const extraCharges = document.getElementById('return-extra-charges').value;
    const notes = document.getElementById('return-notes').value;
    const method = document.getElementById('return-payment-method').value;

    try {
        const res = await fetch('api/rentals.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'return_vehicle',
                rental_id: rentalId,
                extra_charges: extraCharges,
                damage_notes: notes,
                payment_method: method
            })
        });

        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
            closeModal('modal-return-vehicle');
            loadRentals();
        } else {
            showToast(json.message, 'error');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาดในการรับคืนรถ', 'error');
    }
}

// ----------------------------------------------------
// 6. INVOICE & RENTAL AGREEMENT PRINT MODAL
// ----------------------------------------------------
async function openInvoicePrintModal(rentalId) {
    try {
        const res = await fetch(`api/rentals.php?id=${rentalId}`);
        const json = await res.json();
        if (json.status === 'success') {
            const r = json.data;
            const shop = r.shop;

            const html = `
                <div class="printable-invoice" style="background:#ffffff; color:#0f172a; padding:2rem; border-radius:12px;">
                    <div style="display:flex; justify-content:space-between; border-bottom:2px solid #e2e8f0; padding-bottom:1rem; margin-bottom:1.5rem;">
                        <div>
                            <h2 style="color:#0f172a; margin-bottom:0.25rem;">${shop.shop_name}</h2>
                            <p style="font-size:0.85rem; color:#64748b;">${shop.shop_address}<br>โทร: ${shop.shop_phone}</p>
                        </div>
                        <div style="text-align:right;">
                            <h3 style="color:#ea580c; margin-bottom:0.25rem;">สัญญาเช่า / ใบเสร็จ</h3>
                            <p style="font-size:0.9rem; font-weight:bold;">เลขที่: ${r.rental_code}</p>
                            <p style="font-size:0.8rem; color:#64748b;">วันที่ออก: ${r.created_at}</p>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.5rem; background:#fff7ed; padding:1rem; border-radius:12px; border:1px solid #fed7aa;">
                        <div>
                            <h4 style="color:#475569; margin-bottom:0.5rem;">ข้อมูลผู้เช่า (Customer Info)</h4>
                            <p><strong>ชื่อ-นามสกุล:</strong> ${r.customer_name}</p>
                            <p><strong>เบอร์โทรศัพท์:</strong> ${r.customer_phone}</p>
                            <p><strong>เลขบัตรประชาชน/พาสปอร์ต:</strong> ${r.national_id || '-'}</p>
                            <p><strong>เลขที่ใบขับขี่:</strong> ${r.driver_license || '-'}</p>
                        </div>
                        <div>
                            <h4 style="color:#475569; margin-bottom:0.5rem;">ข้อมูลยานพาหนะ (Vehicle Info)</h4>
                            <p><strong>ยี่ห้อ/รุ่น:</strong> ${r.brand} ${r.model}</p>
                            <p><strong>ทะเบียนรถ:</strong> ${r.plate_number}</p>
                            <p><strong>วันเวลารับรถ:</strong> ${r.start_date}</p>
                            <p><strong>วันเวลาคืนรถ:</strong> ${r.end_date}</p>
                        </div>
                    </div>

                    <table style="width:100%; border-collapse:collapse; margin-bottom:1.5rem;">
                        <thead>
                            <tr style="background:#ffedd5; text-align:left;">
                                <th style="padding:0.75rem; color:#7c2d12;">รายการ (Description)</th>
                                <th style="padding:0.75rem; text-align:center; color:#7c2d12;">จำนวนวัน</th>
                                <th style="padding:0.75rem; text-align:right; color:#7c2d12;">ราคา/วัน</th>
                                <th style="padding:0.75rem; text-align:right; color:#7c2d12;">รวมเงิน (THB)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:0.75rem; border-bottom:1px solid #e2e8f0;">ค่าเช่ารถยนต์ ${r.brand} ${r.model}</td>
                                <td style="padding:0.75rem; border-bottom:1px solid #e2e8f0; text-align:center;">${r.total_days}</td>
                                <td style="padding:0.75rem; border-bottom:1px solid #e2e8f0; text-align:right;">${formatMoney(r.daily_rate)}</td>
                                <td style="padding:0.75rem; border-bottom:1px solid #e2e8f0; text-align:right;">${formatMoney(r.total_amount)}</td>
                            </tr>
                            <tr>
                                <td style="padding:0.75rem; border-bottom:1px solid #e2e8f0;" colspan="3">ค่าประกันความเสียหาย (เงินมัดจำ)</td>
                                <td style="padding:0.75rem; border-bottom:1px solid #e2e8f0; text-align:right;">${formatMoney(r.deposit_amount)}</td>
                            </tr>
                            ${r.extra_charges > 0 ? `
                            <tr>
                                <td style="padding:0.75rem; border-bottom:1px solid #e2e8f0;" colspan="3">ค่าบริการเพิ่มเติม / ค่าปรับ</td>
                                <td style="padding:0.75rem; border-bottom:1px solid #e2e8f0; text-align:right;">${formatMoney(r.extra_charges)}</td>
                            </tr>` : ''}
                            ${r.discount > 0 ? `
                            <tr>
                                <td style="padding:0.75rem; border-bottom:1px solid #e2e8f0;" colspan="3">ส่วนลด (Discount)</td>
                                <td style="padding:0.75rem; border-bottom:1px solid #e2e8f0; text-align:right;">-${formatMoney(r.discount)}</td>
                            </tr>` : ''}
                        </tbody>
                    </table>

                    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:2rem;">
                        <div>
                            <p style="font-size:0.85rem; color:#64748b;">เจ้าหน้าที่ผู้ทำรายการ: ${r.staff_name || 'Admin'}</p>
                            <p style="font-size:0.85rem; color:#64748b;">(เงินมัดจำจะได้รับคืนเมื่อส่งคืนรถในสภาพสมบูรณ์)</p>
                        </div>
                        <div style="text-align:right;">
                            <h3 style="color:#0f172a;">ยอดชำระสุทธิทั้งหมด: <span style="color:#ea580c;">${formatMoney(parseFloat(r.grand_total) + parseFloat(r.deposit_amount))}</span></h3>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem; margin-top:3rem; text-align:center;">
                        <div>
                            <div style="border-bottom:1px solid #cbd5e1; height:40px; margin-bottom:0.5rem;"></div>
                            <p style="font-size:0.85rem;">ลงชื่อผู้เช่า (Customer Signature)</p>
                        </div>
                        <div>
                            <div style="border-bottom:1px solid #cbd5e1; height:40px; margin-bottom:0.5rem;"></div>
                            <p style="font-size:0.85rem;">ลงชื่อผู้รับเช่า (Authorized Officer)</p>
                        </div>
                    </div>
                </div>
            `;

            const targetEl = document.getElementById('invoice-modal-content') || document.getElementById('invoice-modal-body');
            if (targetEl) targetEl.innerHTML = html;
            openModal('modal-invoice-print');
        }
    } catch (e) {
        showToast('เกิดข้อผิดพลาดในการโหลดใบเสร็จ', 'error');
    }
}

function printInvoiceNow() {
    window.print();
}

// ----------------------------------------------------
// 7. SETTINGS & AUTH LOGOUT CONTROLLER

async function logoutUser() {
    showConfirmModal(
        'ยืนยันออกจากระบบ',
        'คุณแน่ใจหรือไม่ที่จะออกจากระบบ?',
        async () => {
            try {
                await fetch('api/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'logout' })
                });
                window.location.href = 'index.php?page=login';
            } catch (e) {
                window.location.href = 'index.php?page=login';
            }
        },
        'ยืนยันออกจากระบบ',
        'btn-danger'
    );
}