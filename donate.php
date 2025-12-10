<?php 
session_start();

// โหลดโปรไฟล์ถ้ามี
$avatar_src = null;
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $match = glob(__DIR__ . "/uploads/avatar_user_$uid.*");
    if (!empty($match)) {
        $avatar_src = "uploads/" . basename($match[0]);
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Donate - PetHome</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
    body { font-family: 'Prompt', sans-serif; }
</style>
</head>

<body class="min-h-screen bg-repeat relative"
      style="background-image: url('bg_pethome_pattern.jpg'); background-size: 300px;">

    <!-- ================= OVERLAY ทำให้พื้นหลังจางลง ================= -->
    <div class="absolute inset-0 bg-white/70 backdrop-blur-sm pointer-events-none"></div>

<!-- NAVBAR -->
<nav class="w-full fixed top-0 left-0 bg-white/40 backdrop-blur-md shadow-sm py-4 z-20">
    <div class="flex items-center px-6">

        <h1 class="flex items-center gap-3 text-2xl font-extrabold text-[#2f5d31]">
            <div class="bg-white rounded-full shadow-md p-1 px-2">🐾</div>
            PetHome
        </h1>

        <ul class="flex items-center space-x-6 text-gray-900 ml-auto">
            <li><a href="homeped.php" class="hover:text-green-700">HOME</a></li>
            <li><a href="form.php" class="hover:text-green-700">FORM</a></li>
            <li><a href="donate.php" class="text-green-700 font-bold">DONATE</a></li>
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

<!-- OFFSET -->
<div class="pt-28"></div>

<!-- ============= HEADING DONATE ============= -->
<div class="flex flex-col items-center relative z-10">
    <div class="w-44 h-44 bg-white/80 backdrop-blur-2xl rounded-3xl flex flex-col justify-center items-center shadow-2xl border border-white/30">
        <div class="text-5xl mb-1">💖</div>
        <p class="font-extrabold text-2xl text-[#2f5d31] tracking-wide">DONATE</p>
    </div>

    <p class="mt-6 text-gray-700 text-lg max-w-md text-center font-medium">
        ทุกการบริจาคของคุณมีคุณค่าต่อชีวิตสัตว์ไร้บ้าน 🐶❤️  
        ขอบคุณที่ช่วยทำให้โลกนี้อ่อนโยนขึ้น
    </p>
</div>

<!-- ============= MENU BUTTONS ============= -->
<div class="flex justify-center gap-12 mt-14 relative z-10">

    <!-- บัญชีธนาคาร -->
    <a href="donate_bank.php" 
       class="w-32 h-32 bg-white/80 backdrop-blur-xl rounded-2xl flex flex-col items-center justify-center 
              shadow-lg border border-white/30 hover:scale-110 hover:-translate-y-1 transition-all">
        <div class="text-5xl">🏦</div>
        <p class="text-sm font-semibold mt-2">บัญชีธนาคาร</p>
    </a>

    <!-- ส่งของ -->
    <a href="donate_items.php" 
       class="w-32 h-32 bg-white/80 backdrop-blur-xl rounded-2xl flex flex-col items-center justify-center 
              shadow-lg border border-white/30 hover:scale-110 hover:-translate-y-1 transition-all">
        <div class="text-5xl">🎁</div>
        <p class="text-sm font-semibold mt-2">ส่งของ</p>
    </a>

</div>

<!-- ============= DOG ILLUSTRATION ============= -->
<div class="flex justify-center mt-16 relative z-10">
    <img src="dog.png" class="w-52 drop-shadow-xl hover:scale-105 transition">
</div>

<script>
// CLICK = เปิด, กดค้าง = ปิด
document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('profileBtn');
    var dropdown = document.getElementById('profileDropdown');
    if (!btn || !dropdown) return;

    var holdTimer = null;
    var holdTime = 350;
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
