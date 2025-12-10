<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Donate</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#F9D8A0] min-h-screen flex flex-col">

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

    <!-- CONTENT -->
    <section class="flex flex-col items-center pt-32 flex-grow">

        <!-- ปุ่มบัญชีธนาคาร -->
        <a href="donate_money.php" class="flex flex-col items-center mb-10">
            <div class="w-20 h-20 bg-white rounded-2xl shadow flex items-center justify-center">
                <!-- ไอคอนธนาคาร -->
                <img src="icon_bank.png" class="w-10 h-10" alt="">
            </div>
            <p class="mt-3 text-lg font-semibold text-black">บัญชีธนาคาร</p>
        </a>

        <!-- ปุ่มสิ่งของ -->
        <a href="donate_item.php" class="flex flex-col items-center">
            <div class="w-20 h-20 bg-white rounded-2xl shadow flex items-center justify-center">
                <!-- ไอคอนสิ่งของ -->
                <img src="icon_item.png" class="w-10 h-10" alt="">
            </div>
            <p class="mt-3 text-lg font-semibold text-black">บริจาคสิ่งของ</p>
        </a>

        <!-- รูปหมา -->
        <div class="mt-14">
            <img src="dog.png" class="w-40 mx-auto" alt="">
        </div>

    </section>

</body>
</html>
