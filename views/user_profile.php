<!-- views/user_profile.php - Customer Profile & Identity Verification Page -->
<?php
$sessionUser = $_SESSION['user'] ?? null;
$currentUser = $sessionUser;

if ($sessionUser && !empty($sessionUser['id'])) {
    try {
        $dbConn = Database::getInstance()->getConnection();
        $stmtFresh = $dbConn->prepare("
            SELECT u.*, c.id as customer_id, c.id_card_image as cust_idcard, c.driver_license_image as cust_license, c.address as cust_addr, c.national_id as cust_natid, c.driver_license as cust_lic, c.phone as cust_phone
            FROM users u
            LEFT JOIN customers c ON (c.user_id = u.id OR (c.phone != '' AND c.phone = u.phone) OR (c.phone != '' AND c.phone = u.username))
            WHERE u.id = ? ORDER BY c.id DESC LIMIT 1
        ");
        $stmtFresh->execute([$sessionUser['id']]);
        $row = $stmtFresh->fetch();
        if ($row) {
            if (empty($row['id_card_image']) && !empty($row['cust_idcard'])) {
                $row['id_card_image'] = $row['cust_idcard'];
            }
            if (empty($row['driver_license_image']) && !empty($row['cust_license'])) {
                $row['driver_license_image'] = $row['cust_license'];
            }
            if (empty($row['address']) && !empty($row['cust_addr'])) {
                $row['address'] = $row['cust_addr'];
            }
            if (empty($row['national_id']) && !empty($row['cust_natid'])) {
                $row['national_id'] = $row['cust_natid'];
            }
            if (empty($row['driver_license']) && !empty($row['cust_lic'])) {
                $row['driver_license'] = $row['cust_lic'];
            }
            if (empty($row['phone']) && !empty($row['cust_phone'])) {
                $row['phone'] = $row['cust_phone'];
            }
            $currentUser = $row;
            $_SESSION['user'] = array_merge($_SESSION['user'], $row);
        }
    } catch (Exception $e) {}
}
?>
<div id="user-profile-view" style="max-width: 800px; margin: 0 auto;">
    
    <!-- Profile & Verification Status Card -->
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 24px; padding: 2.25rem; margin-bottom: 1.5rem; box-shadow: var(--shadow-card);">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="position: relative;">
                    <img id="uprofile-header-avatar-preview" src="<?= htmlspecialchars(!empty($currentUser['profile_image']) ? $currentUser['profile_image'] : 'assets/images/default-avatar.svg') ?>" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 3px solid #fca684; box-shadow: 0 4px 15px rgba(237, 97, 58, 0.2);" alt="Profile Avatar" onerror="this.src='assets/images/default-avatar.svg'">
                </div>
                <div>
                    <h2 style="font-size: 1.45rem; font-weight: 800; color: #0f172a;"> แก้ไขข้อมูลส่วนตัว & ยืนยันตัวตน</h2>
                    <p style="font-size: 0.88rem; color: var(--text-muted); margin-top: 2px;">จัดการข้อมูลส่วนตัว รูปโปรไฟล์ และอัปโหลดหลักฐานเพื่อเปิดสิทธิ์จองรถเช่า</p>
                </div>
            </div>

            <!-- Identity Verification Status Badge -->
            <div id="uprofile-verify-badge-container">
                <?php if (($currentUser['is_verified'] ?? 0) == 1): ?>
                    <span class="badge badge-available" style="font-size: 0.95rem; padding: 0.55rem 1.25rem;">
                        🟢 ยืนยันตัวตนแล้ว (Verified)
                    </span>
                <?php else: ?>
                    <span class="badge badge-rented" style="font-size: 0.95rem; padding: 0.55rem 1.25rem;">
                        🔴 ยังไม่ได้ยืนยันตัวตน (Unverified)
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <?php if (($currentUser['is_verified'] ?? 0) != 1): ?>
            <div style="background: #fef3c7; border: 1px solid #fde68a; color: #92400e; padding: 1rem 1.25rem; border-radius: 16px; margin-bottom: 1.75rem; font-size: 0.88rem; line-height: 1.6; display: flex; gap: 0.75rem; align-items: flex-start;">
                <div style="font-size: 1.25rem;"></div>
                <div>
                    <strong>คำแนะนำในการยืนยันตัวตน:</strong> กรุณากรอกข้อมูลและอัปโหลดไฟล์รูปถ่ายบัตรประชาชน/เอกสารประจำตัวให้ครบถ้วน จากนั้นเจ้าหน้าที่จะทำการตรวจอนุมัติยืนยันตัวตน (เปลี่ยนสถานะเป็นสีเขียว) เพื่อเปิดสิทธิ์การจองรถเช่าให้คุณทันที
                </div>
            </div>
        <?php endif; ?>

        <!-- Profile Update Form -->
        <form id="user-profile-form" onsubmit="handleUserProfileSubmit(event)">
            
            <!-- 1. Profile Picture & Email Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">รูปโปรไฟล์ (Profile Avatar)</label>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <img id="uprofile-avatar-preview" src="<?= htmlspecialchars(!empty($currentUser['profile_image']) ? $currentUser['profile_image'] : 'assets/images/default-avatar.svg') ?>" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #ea580c; flex-shrink: 0;" alt="Avatar" onerror="this.src='assets/images/default-avatar.svg'">
                        <input type="file" id="uprofile-avatar-file" class="form-control" accept="image/*" onchange="previewFile(this, 'uprofile-avatar-preview')">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">อีเมล (Email) *</label>
                    <input type="email" id="uprofile-email" class="form-control" placeholder="user@example.com" value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>">
                </div>
            </div>

            <!-- 2. Full Name & Phone Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">ชื่อ-นามสกุล *</label>
                    <input type="text" id="uprofile-fullname" class="form-control" value="<?= htmlspecialchars($currentUser['fullname'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">เบอร์โทรศัพท์ *</label>
                    <input type="text" id="uprofile-phone" class="form-control" value="<?= htmlspecialchars($currentUser['phone'] ?? $currentUser['username'] ?? '') ?>" required>
                </div>
            </div>

            <!-- 3. National ID & Driver License Numbers -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">เลขบัตรประชาชน / Passport</label>
                    <input type="text" id="uprofile-natid" class="form-control" placeholder="เลขประจำตัว 13 หลัก" value="<?= htmlspecialchars($currentUser['national_id'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">เลขที่ใบขับขี่</label>
                    <input type="text" id="uprofile-license" class="form-control" placeholder="เลขใบขับขี่" value="<?= htmlspecialchars($currentUser['driver_license'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">ที่อยู่ตามบัตรประชาชน / เอกสาร</label>
                <textarea id="uprofile-address" class="form-control" rows="2" placeholder="กรอกที่อยู่..."><?= htmlspecialchars($currentUser['address'] ?? '') ?></textarea>
            </div>

            <!-- Documents Image Upload Boxes Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.75rem;">
                
                <!-- National ID Card Photo Box -->
                <div style="background: #fff8f5; border: 1px dashed #f97316; border-radius: 16px; padding: 1.25rem;">
                    <label class="form-label" style="font-size: 0.9rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; display: block;">
                         รูปภาพบัตรประชาชน (ID Card)
                    </label>
                    <input type="file" id="uprofile-idcard-file" class="form-control" style="font-size: 0.82rem; margin-bottom: 0.75rem;" accept="image/*" onchange="previewFile(this, 'uprofile-idcard-img-preview')">
                    
                    <div style="text-align: center;">
                        <?php $hasIdCard = !empty($currentUser['id_card_image']); ?>
                        <img id="uprofile-idcard-img-preview" src="<?= $hasIdCard ? htmlspecialchars($currentUser['id_card_image']) : '' ?>" style="max-width: 100%; height: 130px; border-radius: 10px; border: 1px solid var(--border-color); object-fit: cover; <?= $hasIdCard ? '' : 'display: none;' ?>">
                        <div id="uprofile-idcard-placeholder" style="<?= $hasIdCard ? 'display: none;' : '' ?> padding: 1.5rem 0.5rem; color: #ea580c; font-size: 0.85rem; font-weight: 500;">
                            ยังไม่มีรูปภาพบัตรประชาชน (กรุณาเลือกไฟล์เพื่ออัปโหลด)
                        </div>
                    </div>
                </div>

                <!-- Driver License Photo Box -->
                <div style="background: #fff8f5; border: 1px dashed #f97316; border-radius: 16px; padding: 1.25rem;">
                    <label class="form-label" style="font-size: 0.9rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; display: block;">
                         รูปภาพใบขับขี่ (Driver License)
                    </label>
                    <input type="file" id="uprofile-license-file" class="form-control" style="font-size: 0.82rem; margin-bottom: 0.75rem;" accept="image/*" onchange="previewFile(this, 'uprofile-license-img-preview')">
                    
                    <div style="text-align: center;">
                        <?php $hasLicense = !empty($currentUser['driver_license_image']); ?>
                        <img id="uprofile-license-img-preview" src="<?= $hasLicense ? htmlspecialchars($currentUser['driver_license_image']) : '' ?>" style="max-width: 100%; height: 130px; border-radius: 10px; border: 1px solid var(--border-color); object-fit: cover; <?= $hasLicense ? '' : 'display: none;' ?>">
                        <div id="uprofile-license-placeholder" style="<?= $hasLicense ? 'display: none;' : '' ?> padding: 1.5rem 0.5rem; color: #ea580c; font-size: 0.85rem; font-weight: 500;">
                            ยังไม่มีรูปภาพใบขับขี่ (กรุณาเลือกไฟล์เพื่ออัปโหลด)
                        </div>
                    </div>
                </div>

            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.9rem; font-size: 1.05rem; border-radius: 14px;">
                 บันทึกการแก้ไขข้อมูล & อัปเดตเอกสาร
            </button>
        </form>

    </div>
</div>

<script>
const base64Data = {
    profile: '',
    idcard: '',
    license: ''
};

// ฟังก์ชันสำหรับย่อขนาดรูปภาพในเครื่องก่อนอัปโหลด ป้องกันไฟล์ใหญ่เกินขีดจำกัด PHP
function compressAndPreviewImage(file, previewId, base64Key) {
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            let width = img.width;
            let height = img.height;
            const maxDim = 1600; // ขนาดสูงสุด 1600px คมชัดระดับ HD แต่ไฟล์เบาเพียง 200-400KB

            if (width > maxDim || height > maxDim) {
                if (width > height) {
                    height = Math.round((height * maxDim) / width);
                    width = maxDim;
                } else {
                    width = Math.round((width * maxDim) / height);
                    height = maxDim;
                }
            }

            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            const compressedBase64 = canvas.toDataURL('image/jpeg', 0.85);
            base64Data[base64Key] = compressedBase64;

            const targetImg = document.getElementById(previewId);
            if (targetImg) {
                targetImg.src = compressedBase64;
                targetImg.style.display = 'inline-block';
            }
            if (previewId === 'uprofile-avatar-preview') {
                const headerAvatar = document.getElementById('uprofile-header-avatar-preview');
                if (headerAvatar) headerAvatar.src = compressedBase64;
            } else if (previewId === 'uprofile-idcard-img-preview') {
                const ph = document.getElementById('uprofile-idcard-placeholder');
                if (ph) ph.style.display = 'none';
            } else if (previewId === 'uprofile-license-img-preview') {
                const ph = document.getElementById('uprofile-license-placeholder');
                if (ph) ph.style.display = 'none';
            }
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function previewFile(input, previewId) {
    const file = input.files[0];
    if (!file) return;
    let key = 'profile';
    if (previewId.includes('idcard')) key = 'idcard';
    if (previewId.includes('license')) key = 'license';
    compressAndPreviewImage(file, previewId, key);
}

async function handleUserProfileSubmit(e) {
    e.preventDefault();

    const submitBtn = e.target.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerText = 'กำลังบันทึกข้อมูลและอัปโหลดเอกสาร...';
    }

    const formData = new FormData();
    formData.append('action', 'update_profile');
    formData.append('fullname', document.getElementById('uprofile-fullname').value.trim());
    formData.append('phone', document.getElementById('uprofile-phone').value.trim());
    formData.append('email', document.getElementById('uprofile-email').value.trim());
    formData.append('national_id', document.getElementById('uprofile-natid').value.trim());
    formData.append('driver_license', document.getElementById('uprofile-license').value.trim());
    formData.append('address', document.getElementById('uprofile-address').value.trim());

    // ดึงไฟล์จากช่อง Input
    const profileImageFile = document.getElementById('uprofile-avatar-file').files[0];
    const idCardImageFile = document.getElementById('uprofile-idcard-file').files[0];
    const driverLicenseImageFile = document.getElementById('uprofile-license-file').files[0];

    if (profileImageFile) formData.append('profile_image', profileImageFile);
    if (idCardImageFile) formData.append('id_card_image', idCardImageFile);
    if (driverLicenseImageFile) formData.append('driver_license_image', driverLicenseImageFile);

    // ส่ง Base64 สำรองไปด้วยเสมอ ป้องกัน PHP ตัดไฟล์ทิ้งหากขนาดเกิน upload_max_filesize
    if (base64Data.profile) formData.append('profile_image_base64', base64Data.profile);
    if (base64Data.idcard) formData.append('id_card_image_base64', base64Data.idcard);
    if (base64Data.license) formData.append('driver_license_image_base64', base64Data.license);

    try {
        const res = await fetch('api/users.php', {
            method: 'POST',
            body: formData
        });

        const json = await res.json();
        if (json.status === 'success') {
            showToast(json.message);
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(json.message, 'error');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerText = 'บันทึกการแก้ไขข้อมูล & อัปเดตเอกสาร';
            }
        }
    } catch (err) {
        showToast('เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerText = 'บันทึกการแก้ไขข้อมูล & อัปเดตเอกสาร';
        }
    }
}
</script>