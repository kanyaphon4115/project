<!-- NAVBAR -->
<nav class="w-full fixed top-0 left-0 bg-white/40 backdrop-blur-md shadow-sm py-4 z-20">
    <div class="flex items-center px-6">
        
    <?php if (basename($_SERVER['PHP_SELF']) == 'homeped.php'): ?>
        <button id="openFilter" class="text-2xl mr-4 hover:text-green-700">☰</button>
        <?php endif; ?>

        <!-- ปุ่มสามขีด -->
        <button id="openFilter" class="text-2xl mr-4 hover:text-green-700">☰</button>

        <!-- LOGO -->
        <h1 class="flex items-center gap-3 text-2xl font-extrabold text-[#2f5d31]">
            <div class="bg-white rounded-full shadow-md p-1 px-2">🐾</div>
            PawHome
        </h1>

        <!-- MENU -->
        <ul class="flex items-center space-x-6 font-semibold text-gray-900 ml-auto">
            <li><a href="homeped.php" class="text-green-700 font-bold">HOME</a></li>
            <li><a href="form.php" class="hover:text-green-700">FORM</a></li>
            <li><a href="donate.php" class="hover:text-green-700">DONATE</a></li>
            <li><a href="request_status.php" class="hover:text-green-700">REQUEST STATUS</a></li>

            <!-- PROFILE -->
            <?php if(isset($_SESSION['user_id'])): ?>
            <li class="relative" id="profileMenu">

                <button id="profileBtn" type="button" class="w-10 h-10 rounded-full bg-green-300 shadow-md overflow-hidden flex items-center justify-center">
                    <?php if ($avatar_src): ?>
                        <img src="<?= $avatar_src ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-white font-bold"><?= strtoupper($_SESSION['email'][0]); ?></span>
                    <?php endif; ?>
                </button>

                <!-- DROPDOWN (JS-controlled) -->
                <div id="profileDropdown" class="hidden absolute right-0 mt-3 w-60 bg-white shadow-xl rounded-xl p-4 text-gray-700 transition">

                    <p class="text-sm text-gray-500">เข้าสู่ระบบเป็น</p>
                    <p class="font-bold"><?= explode("@", $_SESSION['email'])[0]; ?></p>
                    <p class="text-sm"><?= $_SESSION['email']; ?></p>


<hr class="my-3">

<a href="setting.php" class="block py-1 font-medium hover:text-red-600 transition">
    ⚙️ ตั้งค่า
</a>

<a href="profile.php" class="block py-1 font-medium hover:text-red-600 transition">
    👤 โปรไฟล์
</a>

<a href="about_us.php" class="block py-1 font-medium hover:text-red-600 transition">
    ℹ️ About Us
</a>

                    <hr class="my-3">
                    <a href="index.php" class="text-red-600 font-bold">ออกจากระบบ</a>
                </div>

            </li>
            <?php endif; ?>
        </ul>

    </div>
</nav>