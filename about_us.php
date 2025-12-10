<?php
session_start();
include("database/db_ped.php");

if (!isset($_SESSION['user_id'])) {
    // allow viewing but profile actions require login; keep simple
}

// load avatar if logged in
$avatar_src = null;
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $matches = glob(__DIR__ . "/uploads/avatar_user_$uid.*");
    if (!empty($matches)) $avatar_src = "uploads/" . basename($matches[0]);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>About Us - PetHome</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body{font-family:'Prompt',sans-serif}</style>
</head>
<body class="bg-[#f7d7a3] min-h-screen">

<!-- Navbar -->
<nav class="w-full fixed top-0 left-0 bg-white/40 backdrop-blur-md shadow-sm py-4 z-20">
    <div class="flex items-center px-6">
        <h1 class="flex items-center gap-3 text-2xl font-extrabold text-[#2f5d31]"><div class="bg-white rounded-full shadow-md p-1 px-2">🐾</div>PetHome</h1>
        <ul class="flex items-center space-x-6 text-gray-900 ml-auto">
            <li><a href="homeped.php" class="hover:text-green-700">HOME</a></li>
            <li><a href="form.php" class="hover:text-green-700">FORM</a></li>
            <li><a href="donate.php" class="hover:text-green-700">DONATE</a></li>
            <li><a href="request_status.php" class="hover:text-green-700">REQUEST STATUS</a></li>

            <?php if(isset($_SESSION['user_id'])): ?>
            <li class="relative">
                <button id="profileBtn" type="button"
                        class="w-10 h-10 rounded-full bg-green-300 shadow-md overflow-hidden flex items-center justify-center">

                    <?php if ($avatar_src): ?>
                        <img src="<?= $avatar_src ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-white font-bold"><?= strtoupper($_SESSION['email'][0]); ?></span>
                    <?php endif; ?>
                </button>

                <div id="profileDropdown"
                    class="hidden absolute right-0 mt-3 w-60 bg-white shadow-xl rounded-xl p-4 text-gray-700 transition">

                    <p class="text-sm text-gray-500">เข้าสู่ระบบเป็น</p>
                    <p class="font-bold"><?= explode("@", $_SESSION['email'])[0]; ?></p>
                    <p class="text-sm"><?= $_SESSION['email']; ?></p>

                    <hr class="my-3">

                    <a href="#" class="block py-1 font-medium hover:text-red-600 transition">⚙️ ตั้งค่า</a>
                    <a href="profile.php" class="block py-1 font-medium hover:text-red-600 transition">👤 โปรไฟล์</a>
                    <a href="#" class="block py-1 font-medium hover:text-red-600 transition">ℹ️ About Us</a>
                    
                    <hr class="my-3">
                    <a href="index.php" class="text-red-600 font-bold">ออกจากระบบ</a>
                </div>

            </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="pt-28"></div>

<main class="max-w-5xl mx-auto px-6 pb-16">
    <section class="bg-white rounded-3xl shadow-2xl p-8 mb-8">
        <div class="md:flex gap-8 items-center">
            <div class="md:flex-1">
                <h2 class="text-4xl font-extrabold text-[#2f5d31] mb-3">เกี่ยวกับ PetHome</h2>
                <p class="text-gray-700 leading-relaxed">PetHome ตั้งขึ้นเพื่อเชื่อมโยงบ้านที่รักสัตว์กับสุนัขที่ต้องการบ้านใหม่ เราช่วยให้การรับเลี้ยงเป็นเรื่องง่าย ปลอดภัย และยั่งยืน — โดยให้ข้อมูลสุนัข โปรไฟล์ผู้ขอ และระบบติดตามสถานะคำขอ</p>

                <div class="mt-6 flex gap-4">
                    <a href="homeped.php" class="px-6 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700">ค้นหาสุนัข</a>
                    <a href="request_status.php" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700">ตรวจสอบคำขอ</a>
                </div>
            </div>

            <div class="md:w-1/3 mt-6 md:mt-0">
                <img src="familydogs.png" alt="dog" class="w-full rounded-xl shadow-lg">
            </div>
        </div>
    </section>

    <section class="grid md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="font-bold text-xl text-[#2f5d31] mb-2">ภารกิจของเรา</h3>
            <p class="text-gray-700">ให้บ้านใหม่กับสัตว์ที่ต้องการ พร้อมคำแนะนำและการตรวจสอบความพร้อมของผู้รับเลี้ยง</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="font-bold text-xl text-[#2f5d31] mb-2">การร่วมมือ</h3>
            <p class="text-gray-700">ร่วมมือกับมูลนิธิและผู้ช่วยเหลือท้องถิ่น เพื่อเพิ่มโอกาสให้สัตว์ได้พบบ้านที่เหมาะสม</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="font-bold text-xl text-[#2f5d31] mb-2">สนับสนุน</h3>
            <p class="text-gray-700">คุณสามารถสนับสนุนผ่านการบริจาค หรือการเป็นอาสาสมัครเพื่อช่วยเหลือสัตว์</p>
        </div>
    </section>

    
</main>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('profileBtn');
    var dropdown = document.getElementById('profileDropdown');
    if (!btn || !dropdown) return;

    var holdTimer = null;
    var holdTime = 350; // ms
    var isHolding = false;

    function openDropdown(){ dropdown.classList.remove('hidden'); }
    function closeDropdown(){ dropdown.classList.add('hidden'); }

    btn.addEventListener('click', function(e){
        e.stopPropagation();
        if (!isHolding) openDropdown();
    });

    function startHold(e){
        e.stopPropagation();
        isHolding = true;
        holdTimer = setTimeout(function(){
            closeDropdown();
            isHolding = false;
        }, holdTime);
    }

    function cancelHold(){
        if (holdTimer) clearTimeout(holdTimer);
        isHolding = false;
    }

    btn.addEventListener('mousedown', startHold);
    btn.addEventListener('mouseup', cancelHold);
    btn.addEventListener('mouseleave', cancelHold);
    btn.addEventListener('touchstart', startHold, {passive:false});
    btn.addEventListener('touchend', cancelHold);

    document.addEventListener('click', function(e){
        if (!btn.contains(e.target) && !dropdown.contains(e.target)) closeDropdown();
    });
});
</script>

</body>
</html>
