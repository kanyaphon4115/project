<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>บัญชีธนาคาร - PetHome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* สไตล์เสริมเพื่อให้การ์ดมีพื้นหลังภาพประกอบ (ครอบครัวและหมา) */
        .bank-card-bg {
            background-color: #e3d2b0; 
            background-size: cover;
            background-position: center bottom;
        }
        .file-input-hidden {
            display: none;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'page-bg': '#F9D8A0',          // สีพื้นหลังหลัก
                        'card-bg': '#e3d2b0',          // สีพื้นหลังการ์ดตามรูป
                        'dark-text': '#5d4037',        // สีข้อความและไอคอน (น้ำตาลเข้ม)
                        'accent-red': '#cc0000',       // สีเน้น (อัปโหลดสลิป)
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-page-bg min-h-screen flex flex-col items-center">

    <section class="flex flex-col items-center w-full max-w-sm px-6 pt-32 pb-10">
        <!-- ================= NAVBAR ================= -->
<nav class="w-full fixed top-0 left-0 bg-white/40 backdrop-blur-md shadow-sm py-4 z-20">
    <div class="flex items-center px-6">

        <!-- LOGO -->
        <h1 class="flex items-center gap-3 text-2xl font-extrabold text-[#2f5d31] tracking-wide">
            <div class="bg-white rounded-full shadow-md p-1 px-2">🐾</div>
            PetHome
        </h1>

        <!-- MENU -->
        <ul class="flex items-center space-x-8 text-sm font-semibold text-gray-900 ml-auto mr-4">
            <li><a href="homeped.php" class="hover:text-green-700">HOME</a></li>
            <li><a href="form.php" class="hover:text-green-700">FORM</a></li>
            <li><a href="#" class="hover:text-green-700">DONATE</a></li>
            <li><a href="#" class="hover:text-green-700">REQUEST STATUS</a></li>

            <?php if(isset($_SESSION['user_id'])): ?>

            <!-- PROFILE MENU -->
            <li class="relative">

                <button id="profileBtn"
                    class="w-10 h-10 rounded-full bg-green-300 text-white font-bold flex items-center justify-center shadow-md overflow-hidden">

                    <?php if ($avatar_src): ?>
                        <img src="<?= $avatar_src ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <?= strtoupper($_SESSION['email'][0]); ?>
                    <?php endif; ?>

                </button>

                <div id="profileDropdown"
                    class="absolute right-0 mt-3 w-64 bg-white shadow-xl rounded-xl p-4 text-gray-700 hidden">

                    <p class="text-sm text-gray-500">เข้าสู่ระบบเป็น</p>
                    <p class="font-bold"><?= explode('@', $_SESSION['email'])[0] ?></p>
                    <p class="text-sm"><?= $_SESSION['email'] ?></p>

                    <hr class="my-3">

                    <a class="block py-2 hover:text-green-600">⚖️ น้ำหนักของฉัน</a>
                    <a class="block py-2 hover:text-green-600">🏃 การออกกำลังกาย</a>
                    <a class="block py-2 hover:text-green-600">📄 บทความ</a>

                    <hr class="my-3">

                    <a href="index.php" class="text-red-600 font-bold hover:underline">ออกจากระบบ</a>
                </div>

            </li>

            <?php else: ?>
            <li>
                <a href="login.php"
                    class="px-5 py-2 rounded-full bg-green-600 text-white font-semibold shadow-md hover:bg-green-700 transition">
                    Login
                </a>
            </li>
            <?php endif; ?>
        </ul>

    </div>
</nav>


        <form id="uploadForm" class="bank-card-bg rounded-3xl p-6 text-center shadow-2xl w-full relative">
            
            <div class="text-6xl text-dark-text mb-4 mt-2">🏛</div>
            
            <div class="text-sm text-dark-text space-y-4">
                <div class="text-left">
                    <span class="font-bold">มูลนิธิ</span><br>
                    <span class="break-all block text-sm">xxxxxxxxxxxxxxxxxxxxxxxxxxxx</span>
                </div>
                <div class="text-left">
                    <span class="font-bold">ชื่อบัญชี</span><br>
                    <span class="break-all block text-sm">xxxxxxxxxxxxxxxxxxxxxxxxxxxx</span>
                </div>
                <div class="text-left">
                    <span class="font-bold">ธนาคาร</span><br>
                    <span class="break-all block text-sm">xxxxxxxxxxxxxxxxxxxxxxxxxxxx</span>
                </div>
                <div class="text-left mb-6">
                    <span class="font-bold">เลขบัญชี</span><br>
                    <span class="break-all block text-sm">xxxxxxxxxxxxxxxxxxxxxxxxxxxx</span>
                </div>
            </div>

            <div class="qr-code-area bg-white border border-gray-400 mt-5 mx-auto w-40 h-40 flex justify-center items-center relative overflow-hidden text-gray-500 cursor-pointer group">
                <div id="qrCodePlaceholder" class="absolute text-xs leading-tight p-2 text-center" onclick="document.getElementById('qrCodeInput').click()">คลิกเพื่อใส่รูป<br>QR Code</div>
                
                <img id="qrCodeImage" src="" alt="QR Code" class="w-full h-full object-contain hidden" />
                
                <button 
                    type="button" 
                    id="saveQrButton"
                    class="absolute bottom-0 w-full bg-gray-900/70 text-white text-xs py-1 opacity-0 group-hover:opacity-100 transition duration-300 hidden"
                    onclick="saveQrCode()"
                >
                    บันทึกรูปภาพ
                </button>
            </div>
            <input type="file" id="qrCodeInput" accept="image/*" class="file-input-hidden" onchange="previewQrCode(event)">

            <input type="file" id="slipInput" accept="image/*" class="file-input-hidden" onchange="updateFileName(event)">
            
            <button 
                type="button" 
                onclick="document.getElementById('slipInput').click()"
                class="bg-white text-dark-text border-2 border-card-bg py-3 px-6 rounded-full font-bold cursor-pointer shadow-xl flex items-center justify-center w-full mx-auto mt-8 transition hover:bg-gray-100"
            >
                <span class="text-lg mr-3">☁</span> 
                <span id="uploadButtonText">upload picture</span>
            </button>
            
            <div id="fileNameDisplay" class="text-center text-xs text-gray-600 mt-2 hidden"></div>
            <div class="text-center text-xs text-accent-red mt-1">อัพโหลดสลิปการโอน</div>

            <button 
                type="submit" 
                class="bg-green-600 text-white py-3 px-6 rounded-full font-bold cursor-pointer shadow-xl w-full mx-auto mt-5 transition hover:bg-green-700"
            >
                ส่งสลิปการโอน
            </button>
        </form>

        <div class="mt-10 text-gray-600 text-sm">
             </div>

    </section>
    
    <div id="successPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-8 rounded-xl shadow-2xl text-center w-80">
            <div class="text-5xl text-green-500 mb-4">✅</div>
            <h3 class="text-xl font-bold mb-3">ส่งข้อมูลสำเร็จ!</h3>
            <p class="text-gray-700 mb-6">ระบบได้รับสลิปการโอนของคุณแล้ว จะตรวจสอบและดำเนินการต่อไป</p>
            <button onclick="closePopup()" class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700">ปิด</button>
        </div>
    </div>


    <script>
        // ================= JAVASCRIPT FUNCTIONS =================

        // 1. Function สำหรับแสดงตัวอย่างรูปภาพ QR Code ที่ผู้ใช้เลือก
        function previewQrCode(event) {
            const input = event.target;
            const image = document.getElementById('qrCodeImage');
            const placeholder = document.getElementById('qrCodePlaceholder');
            const saveBtn = document.getElementById('saveQrButton');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    image.src = e.target.result;
                    image.classList.remove('hidden'); // แสดงรูปภาพ
                    placeholder.classList.add('hidden'); // ซ่อนข้อความตัวยึด
                    saveBtn.classList.remove('hidden'); // แสดงปุ่มบันทึก
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                image.classList.add('hidden');
                placeholder.classList.remove('hidden');
                saveBtn.classList.add('hidden');
                image.src = '';
            }
        }

        // 2. Function สำหรับบันทึกรูป QR Code
        function saveQrCode() {
            const image = document.getElementById('qrCodeImage');
            if (image.src) {
                const link = document.createElement('a');
                link.href = image.src;
                link.download = 'QR_Code_PetHome_Donation.png'; // ตั้งชื่อไฟล์
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                alert('กรุณาใส่รูป QR Code ก่อนทำการบันทึก');
            }
        }

        // 3. Function สำหรับอัปเดตชื่อไฟล์สลิปที่เลือก
        function updateFileName(event) {
            const input = event.target;
            const display = document.getElementById('fileNameDisplay');
            const uploadText = document.getElementById('uploadButtonText');

            if (input.files && input.files[0]) {
                display.textContent = `ไฟล์ที่เลือก: ${input.files[0].name}`;
                display.classList.remove('hidden');
                uploadText.textContent = 'เปลี่ยนรูปสลิป';
            } else {
                display.classList.add('hidden');
                uploadText.textContent = 'upload picture';
            }
        }

        // 4. Function สำหรับแสดง Popup แจ้งความสำเร็จ
        function showPopup() {
            document.getElementById('successPopup').classList.remove('hidden');
        }

        // 5. Function สำหรับปิด Popup
        function closePopup() {
            document.getElementById('successPopup').classList.add('hidden');
        }

        // 6. จัดการ Submit Form และแสดง Popup (จำลองการส่งข้อมูล)
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault(); // ป้องกันการ Submit จริงๆ

            const slipInput = document.getElementById('slipInput');
            if (!slipInput.files || slipInput.files.length === 0) {
                alert('กรุณาอัปโหลดสลิปการโอนเงินก่อนส่ง');
                return;
            }

            // ณ จุดนี้จะทำการส่งข้อมูลไปยัง PHP/Backend
            // เมื่อการส่งข้อมูลสำเร็จ (สมมติว่าสำเร็จ):
            
            // 1. แสดง Popup
            showPopup();

            // 2. (ตัวเลือก) รีเซ็ตฟอร์มหลังจากส่ง
            // e.target.reset();
            // document.getElementById('fileNameDisplay').classList.add('hidden');
            // document.getElementById('uploadButtonText').textContent = 'upload picture';
        });
    </script>

</body>
</html>