<!-- views/user_support.php - 24/7 Support, FAQ & Emergency Assistance View -->
<div id="user-support-view">
    
    <!-- Hero Banner -->
    <div style="background: linear-gradient(135deg, #7b8083, #73787d); border-radius: 24px; padding: 2.5rem 2rem; color: white; margin-bottom: 2rem; box-shadow: var(--shadow-card); position: relative; overflow: hidden;">
        <div style="position: absolute; right: -20px; bottom: -20px; opacity: 0.08; pointer-events: none;">
        </div>
        <span class="badge badge-completed" style="background: #0284c7; color: white; margin-bottom: 0.75rem; font-weight: 700;">SUPPORT 24/7</span>
        <h2 style="font-size: 1.85rem; font-weight: 800; margin-bottom: 0.5rem; color: #f8fafc; display: flex; align-items: center; gap: 0.5rem;">
            <img src="https://cdn-icons-png.flaticon.com/512/3771/3771518.png" style="width: 32px; height: 32px; vertical-align: middle;" alt="Chat Icon">
            ศูนย์ช่วยเหลือ & ความช่วยเหลือฉุกเฉิน 24 ชั่วโมง
        </h2>
        <p style="color: #fdfeff; max-width: 600px; font-size: 0.95rem; line-height: 1.6;">
            พบปัญหาขณะเดินทาง รถขัดข้อง อุบัติเหตุ หรือมีข้อสงสัยเกี่ยวกับการเช่ารถ ทีมงาน ล้อหมุน พร้อมดูแลและช่วยเหลือคุณตลอด 24 ชั่วโมง
        </p>
    </div>

    <!-- Quick Emergency Contact Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
        
        <!-- Emergency Phone Card -->
        <div style="background: #fff1f2; border: 1px solid #fecdd3; padding: 1.75rem; border-radius: 20px; box-shadow: var(--shadow-card);">
            <div style="margin-bottom: 0.75rem;">
                <img src="https://cdn-icons-png.flaticon.com/512/1022/1022331.png" style="width: 48px; height: 48px;" alt="Emergency Siren">
            </div>
            <h3 style="font-size: 1.2rem; font-weight: 700; color: #9f1239; margin-bottom: 0.35rem;">สายด่วนฉุกเฉิน 24 ชม.</h3>
            <p style="font-size: 0.85rem; color: #be123c; margin-bottom: 1.25rem;">กรณีเกิดอุบัติเหตุ รถเสีย หรือต้องการความช่วยเหลือด่วนบนท้องถนน</p>
            <a href="tel:1669" class="btn btn-danger" style="width: 100%; text-align: center; font-weight: 700; padding: 0.85rem; font-size: 1.05rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <img src="https://images.icon-icons.com/317/PNG/512/phone-icon_34382.png" style="width: 20px; height: 20px;" alt="Phone Call">
                โทรออก 1669
            </a>
        </div>

        <!-- Line Official Card -->
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 1.75rem; border-radius: 20px; box-shadow: var(--shadow-card);">
            <div style="margin-bottom: 0.75rem;">
                <img src="https://cdn-icons-png.flaticon.com/512/3670/3670089.png" style="width: 48px; height: 48px;" alt="Line App">
            </div>
            <h3 style="font-size: 1.2rem; font-weight: 700; color: #166534; margin-bottom: 0.35rem;">แชทสอบถามผ่าน Line Official</h3>
            <p style="font-size: 0.85rem; color: #15803d; margin-bottom: 1.25rem;">ส่งรูปภาพตำแหน่ง แจ้งเปลี่ยนเวลารับคืนรถ หรือสอบถามโปรโมชั่น</p>
            <a href="https://line.me" target="_blank" class="btn btn-primary" style="background: #16a34a; border-color: #16a34a; width: 100%; text-align: center; font-weight: 700; padding: 0.85rem; font-size: 1.05rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <img src="https://cdn-icons-png.flaticon.com/512/3670/3670089.png" style="width: 20px; height: 20px;" alt="Add Friend Line">
                เพิ่มเพื่อน LINE @RENTCAR67
            </a>
        </div>

      <!-- Location Office Card -->
    <div style="background: #f0f9ff; border: 1px solid #bae6fd; padding: 1.75rem; border-radius: 20px; box-shadow: var(--shadow-card); display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="margin-bottom: 0.75rem;">
                <img src="https://cdn-icons-png.flaticon.com/512/684/684908.png" style="width: 48px; height: 48px;" alt="Map Location Pin">
            </div>
            <h3 style="font-size: 1.2rem; font-weight: 700; color: #075985; margin-bottom: 0.35rem;">จุดให้บริการ</h3>
            <p style="font-size: 0.85rem; color: #0369a1; margin-bottom: 1.25rem;">88/9 ถนนท่าโพธิ์ นเรศวร</p>
        </div>
        <button class="btn btn-secondary" onclick="openModal('modal-office-map')" style="width: 100%; font-weight: 700; padding: 0.75rem 1rem; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; min-height: 48px; box-sizing: border-box;">
            <img src="https://cdn-icons-png.flaticon.com/512/854/854878.png" style="width: 18px; height: 18px;" alt="Map Icon">
            ดูแผนที่บริษัท
        </button>
    </div>

</div>

    <!-- Modal Box: Office Map & Google Maps Location -->
    <div class="modal-overlay" id="modal-office-map">
        <div class="modal-card" style="max-width: 620px;">
            <div class="modal-header">
                <h3 style="display: flex; align-items: center; gap: 0.5rem;">
                    <img src="https://cdn-icons-png.flaticon.com/512/684/684908.png" style="width: 24px; height: 24px;" alt="Pin">
                    พิกัดสำนักงานใหญ่ (Google Maps Location)
                </h3>
                <button class="close-btn" onclick="closeModal('modal-office-map')">&times;</button>
            </div>
            <div>
                <div style="background: #f0f9ff; border: 1px solid #bae6fd; padding: 1rem 1.25rem; border-radius: 16px; margin-bottom: 1.25rem;">
                    <strong style="color: #0369a1; font-size: 1.05rem; display: flex; align-items: center; gap: 0.4rem;">
                        <img src="https://cdn-icons-png.flaticon.com/512/25/25694.png" style="width: 18px; height: 18px;" alt="Home Icon">
                        ที่อยู่และเวลาทำการ:
                    </strong>
                    <p style="font-size: 0.9rem; color: #075985; margin-top: 6px; line-height: 1.6;">
                        <strong>CarEngine Luxury Rental (สำนักงานใหญ่)</strong><br>
                        88/9 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพมหานคร 10110<br>
                        <span style="display: inline-flex; align-items: center; gap: 0.3rem;">
                            <img src="https://cdn-icons-png.flaticon.com/512/2088/2088617.png" style="width: 16px; height: 16px;" alt="Clock Icon">
                            <strong>เวลาเปิดบริการ:</strong> รับ-คืนรถได้ตั้งแต่ 08:00 - 22:00 น. ทุกวัน
                        </span>
                    </p>
                </div>

                <!-- Embedded Interactive Map Frame -->
                <div style="border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color); margin-bottom: 1.25rem; height: 260px; background: #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <iframe src="https://maps.google.com/maps?q=13.7246,100.5524&z=15&output=embed" width="100%" height="260" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>

                <div style="display: flex; gap: 0.75rem; justify-content: flex-end; flex-wrap: wrap;">
                    <a href="https://maps.app.goo.gl/xyqU9ZtgPL1xr2qM7" target="_blank" class="btn btn-primary" style="flex: 1; text-align: center; font-weight: 700; padding: 0.85rem; font-size: 0.95rem; background: linear-gradient(135deg, #0284c7, #0369a1); border: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <img src="https://cdn-icons-png.flaticon.com/512/2991/2991231.png" style="width: 20px; height: 20px; filter: brightness(0) invert(1);" alt="Navigation">
                        นำทางด้วย Google Maps 
                    </a>
                    <button class="btn btn-secondary" style="padding: 0.85rem 1.25rem;" onclick="closeModal('modal-office-map')">
                        ปิดหน้าต่าง
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Accordion Container -->
    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 24px; padding: 2rem; box-shadow: var(--shadow-card);">
        <h3 style="font-size: 1.3rem; font-weight: 800; color: #0f172a; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <img src="https://cdn-icons-png.flaticon.com/512/471/471664.png" style="width: 28px; height: 28px;" alt="FAQ Icon">
            คำถามที่พบบ่อย (Frequently Asked Questions)
        </h3>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            
            <details style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 1rem 1.25rem; border-radius: 14px; cursor: pointer;">
                <summary style="font-weight: 700; color: #0f172a; font-size: 1rem;"> เอกสารที่ต้องใช้ในวันรับรถเช่ามีอะไรบ้าง?</summary>
                <p style="font-size: 0.9rem; color: #475569; margin-top: 0.75rem; line-height: 1.6;">
                    1. บัตรประชาชนตัวจริง (หรือ พาสปอร์ตสำหรับชาวต่างชาติ)<br>
                    2. ใบอนุญาตขับขี่รถยนต์/มอเตอร์ไซค์ ที่ยังไม่หมดอายุ<br>
                    3. สลิปโอนเงินค่าเช่าและเงินมัดจำประกันรถ
                </p>
            </details>

            <details style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 1rem 1.25rem; border-radius: 14px; cursor: pointer;">
                <summary style="font-weight: 700; color: #0f172a; font-size: 1rem;"> เงินมัดจำประกันรถ ได้รับคืนเมื่อไหร่?</summary>
                <p style="font-size: 0.9rem; color: #475569; margin-top: 0.75rem; line-height: 1.6;">
                    เงินมัดจำประกันรถจะได้รับการโอนคืนเข้าบัญชีธนาคารของคุณทันทีภายใน 15-30 นาที หลังจากพนักงานทำการตรวจเช็กสภาพรถยนต์และระดับน้ำมันเรียบร้อยแล้ว
                </p>
            </details>

            <details style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 1rem 1.25rem; border-radius: 14px; cursor: pointer;">
                <summary style="font-weight: 700; color: #0f172a; font-size: 1rem;"> หากคืนรถช้ากว่าเวลาที่กำหนด มีค่าบริการอย่างไร?</summary>
                <p style="font-size: 0.9rem; color: #475569; margin-top: 0.75rem; line-height: 1.6;">
                    อนุญาตให้คืนช้าฟรี 30 นาทีแรก หากเกิน 30 นาทีจะคิดค่าบริการชั่วโมงละ 200 บาท หรือหากเกิน 4 ชั่วโมงจะคิดคำนวณเป็นราคาเช่าเพิ่ม 1 วัน
                </p>
            </details>

            <details style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 1rem 1.25rem; border-radius: 14px; cursor: pointer;">
                <summary style="font-weight: 700; color: #0f172a; font-size: 1rem;"> เกิดอุบัติเหตุหรือรถเสียระหว่างใช้งาน ต้องทำอย่างไร?</summary>
                <p style="font-size: 0.9rem; color: #475569; margin-top: 0.75rem; line-height: 1.6;">
                    กรุณาโทรแจ้งสายด่วน 02-123-4567 ทันที ทางเรามีบริการรถสไลด์ฉุกเฉินและรถสำรองเปลี่ยนให้ใช้งานฟรีตลอด 24 ชั่วโมง
                </p>
            </details>

        </div>
    </div>
</div>