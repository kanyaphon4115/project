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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Donate - PawHome</title>
<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="min-h-screen bg-repeat relative overflow-x-hidden"
      style="background-image: url('assets/images/bg_pethome_pattern.jpg'); background-size: 300px;">

  <!-- OVERLAY -->
  <div class="absolute inset-0 bg-white/70 backdrop-blur-sm pointer-events-none"></div>

  <!-- NAVBAR -->
  <?php include __DIR__ . '/components/navbar.php'; ?>

  <!-- OFFSET -->
  <div class="pt-20 sm:pt-28"></div>

  <!-- ============= HEADING DONATE ============= -->
  <div class="flex flex-col items-center relative z-10 px-4 sm:px-6">
    <div class="w-32 h-32 sm:w-44 sm:h-44 bg-white/80 backdrop-blur-2xl rounded-3xl
                flex flex-col justify-center items-center shadow-2xl border border-white/30">
      <div class="text-4xl sm:text-5xl mb-1">💖</div>
      <p class="font-extrabold text-xl sm:text-2xl text-[#2f5d31] tracking-wide">DONATE</p>
    </div>

    <p class="mt-4 sm:mt-6 text-gray-700 text-base sm:text-lg max-w-md text-center font-medium">
      ทุกการบริจาคของคุณมีคุณค่าต่อชีวิตสัตว์ไร้บ้าน 🐶❤️
      ขอบคุณที่ช่วยทำให้โลกนี้อ่อนโยนขึ้น
    </p>
  </div>

  <!-- ============= MENU BUTTONS ============= -->
  <div class="relative z-10 mt-8 sm:mt-14 px-4 sm:px-6">
    <div class="grid grid-cols-2 gap-4 sm:gap-8 max-w-md mx-auto">
      <!-- บัญชีธนาคาร -->
      <a href="donate_bank.php"
         class="w-full h-28 sm:h-32 bg-white/80 backdrop-blur-xl rounded-2xl
                flex flex-col items-center justify-center shadow-lg border border-white/30
                hover:scale-105 sm:hover:scale-110 hover:-translate-y-0.5 transition-all">
        <div class="text-4xl sm:text-5xl">🏦</div>
        <p class="text-xs sm:text-sm font-semibold mt-2">บัญชีธนาคาร</p>
      </a>

      <!-- ส่งของ -->
      <a href="donate_items.php"
         class="w-full h-28 sm:h-32 bg-white/80 backdrop-blur-xl rounded-2xl
                flex flex-col items-center justify-center shadow-lg border border-white/30
                hover:scale-105 sm:hover:scale-110 hover:-translate-y-0.5 transition-all">
        <div class="text-4xl sm:text-5xl">🎁</div>
        <p class="text-xs sm:text-sm font-semibold mt-2">ส่งของ</p>
      </a>
    </div>
  </div>

  <!-- ============= DOG ILLUSTRATION ============= -->
  <div class="flex justify-center mt-10 sm:mt-16 relative z-10 px-4 sm:px-6 pb-10">
    <img src="assets/images/donate_dog.png"
         class="w-40 sm:w-52 drop-shadow-xl hover:scale-105 transition"
         alt="donate dog">
  </div>

</body>

</html>
