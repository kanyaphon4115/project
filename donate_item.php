<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>บริจาคสิ่งของ - PetHome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // กำหนดค่าสีแบบกำหนดเองใน theme.extend เพื่อให้ Tailwind รับรู้ชื่อสี
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'page-bg': '#F9D8A0',          // สีพื้นหลังหลัก
                        'info-box-bg': '#e3d2b0',      // สีกล่องข้อมูลตามรูป
                        'input-bg': '#c9b48a',         // สีพื้นหลัง Input ตามรูป
                        'dark-text': '#5d4037',        // สีข้อความและไอคอน (น้ำตาลเข้ม)
                        'accent-red': '#cc0000',       // สีเน้น (อัปโหลดใบเสร็จ)
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

        <div class="bg-info-box-bg rounded-xl p-6 shadow-md w-full mb-8">
            <div class="text-lg font-bold text-dark-text mb-2">ที่อยู่</div>
            <p class="break-all text-sm mb-4 text-dark-text leading-relaxed">
                xxxxxxxxxxxxxxxxxxxxxxxxxxxx<br>
                xxxxxxxxxxxxxxxxxxxxxxxxxxxx<br>
                xxxxxxxxxxxxxxxxxxxxxxxxxxxx
            </p>

            <div class="text-lg font-bold text-dark-text mb-2">เบอร์โทรศัพท์</div>
            <p class="text-sm text-dark-text">+66xxxxxxxxx</p>
        </div>

        <form id="donationForm" class="w-full space-y-6">

            <div>
                <label for="addressName" class="text-lg font-bold text-black block mb-2">ชื่อ-ที่อยู่</label>
                <input 
                    type="text" 
                    id="addressName" 
                    placeholder="type here...." 
                    required
                    class="w-full bg-input-bg rounded-lg py-3 px-4 text-gray-800 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-dark-text/50 border border-transparent transition duration-200"
                >
            </div>

            <div>
                <label for="phoneNumber" class="text-lg font-bold text-black block mb-2">เบอร์โทรศัพท์</label>
                <input 
                    type="tel" 
                    id="phoneNumber" 
                    placeholder="type here...." 
                    required
                    class="w-full bg-input-bg rounded-lg py-3 px-4 text-gray-800 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-dark-text/50 border border-transparent transition duration-200"
                >
            </div>

            <div>
                <label for="itemDescription" class="text-lg font-bold text-black block mb-2">สิ่งของที่คุณต้องการบริจาค</label>
                <textarea
                    id="itemDescription" 
                    placeholder="type here...." 
                    rows="3"
                    required
                    class="w-full bg-input-bg rounded-lg py-3 px-4 text-gray-800 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-dark-text/50 border border-transparent transition duration-200 resize-none"
                ></textarea>
            </div>

            <input type="file" id="receiptInput" accept="image/*, application/pdf" class="hidden" onchange="updateReceiptFileName(event)">
            
            <button 
                type="button" 
                onclick="document.getElementById('receiptInput').click()"
                class="bg-white text-dark-text border-2 border-transparent py-3 px-6 rounded-full font-bold cursor-pointer shadow-xl flex items-center justify-center w-full mx-auto mt-8 transition hover:bg-gray-100"
            >
                <span class="text-lg mr-3">☁</span> 
                <span id="uploadButtonText">upload picture</span>
            </button>
            
            <div id="receiptFileNameDisplay" class="text-center text-xs text-gray-600 mt-2 hidden"></div>
            <div class="text-center text-xs text-accent-red mt-1">อัพโหลดใบเสร็จพัสดุ (ใบเสร็จเลขพัสดุ)</div>

            <button 
                type="submit" 
                class="bg-green-600 text-white py-3 px-6 rounded-full font-bold cursor-pointer shadow-xl w-full mx-auto mt-5 transition hover:bg-green-700"
            >
                ส่งข้อมูลการบริจาค
            </button>

        </form>
    </section>

    <div id="successPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-8 rounded-xl shadow-2xl text-center w-80">
            <div class="text-5xl text-green-500 mb-4">✅</div>
            <h3 class="text-xl font-bold mb-3">ส่งข้อมูลสำเร็จ!</h3>
            <p class="text-gray-700 mb-6">เราได้รับข้อมูลการบริจาคสิ่งของของคุณแล้ว</p>
            <button onclick="closeReceiptPopup()" class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700">ปิด</button>
        </div>
    </div>


    <script>
        // ================= JAVASCRIPT FUNCTIONS =================

        // 1. Function สำหรับอัปเดตชื่อไฟล์ใบเสร็จที่เลือก
        function updateReceiptFileName(event) {
            const input = event.target;
            const display = document.getElementById('receiptFileNameDisplay');
            const uploadText = document.getElementById('uploadButtonText');

            if (input.files && input.files[0]) {
                display.textContent = `ไฟล์ที่เลือก: ${input.files[0].name}`;
                display.classList.remove('hidden');
                uploadText.textContent = 'เปลี่ยนรูปใบเสร็จ';
            } else {
                display.classList.add('hidden');
                uploadText.textContent = 'upload picture';
            }
        }

        // 2. Function สำหรับแสดง Popup แจ้งความสำเร็จ
        function showReceiptPopup() {
            document.getElementById('successPopup').classList.remove('hidden');
        }

        // 3. Function สำหรับปิด Popup
        function closeReceiptPopup() {
            document.getElementById('successPopup').classList.add('hidden');
            // สามารถเพิ่มโค้ดให้รีเซ็ตฟอร์ม หรือ redirect ไปหน้าอื่นที่นี่
        }

        // 4. จัดการ Submit Form และแสดง Popup (จำลองการส่งข้อมูล)
        document.getElementById('donationForm').addEventListener('submit', function(e) {
            e.preventDefault(); // ป้องกันการ Submit จริงๆ

            const itemDescription = document.getElementById('itemDescription').value;
            if (itemDescription.trim() === "") {
                alert('กรุณากรอกสิ่งของที่คุณต้องการบริจาค');
                return;
            }
            
            // ณ จุดนี้จะทำการส่งข้อมูลไปยัง PHP/Backend
            // เมื่อการส่งข้อมูลสำเร็จ (สมมติว่าสำเร็จ):
            
            // 1. แสดง Popup
            showReceiptPopup();

            // 2. (ตัวเลือก) รีเซ็ตฟอร์มหลังจากส่ง
            // e.target.reset();
            // document.getElementById('receiptFileNameDisplay').classList.add('hidden');
            // document.getElementById('uploadButtonText').textContent = 'upload picture';
        });
    </script>

</body>
</html>