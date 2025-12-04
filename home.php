<?php
session_start();

// โหลดรูปโปรไฟล์
$avatar_src = null;
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $matches = glob(__DIR__ . '/uploads/avatar_user_' . $uid . '.*');
    if (!empty($matches)) {
        $avatar_src = 'uploads/' . basename($matches[0]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PetHome - Home</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-6px); }
    100% { transform: translateY(0px); }
}
</style>
</head>

<body class="bg-[#f8d7a0] min-h-screen">

<!-- NAVBAR -->
<nav class="w-full bg-white/40 backdrop-blur-md shadow-sm fixed top-0 left-0 p-4 z-20">
    <div class="flex items-center justify-between">

        <!-- BACK BUTTON -->
        <a href="index.php" class="text-2xl bg-white/60 p-2 rounded-full shadow">
            ←
        </a>

        <h1 class="font-bold text-xl">PetHome</h1>

        <!-- PROFILE ICON -->
        <a href="homepage.php" class="w-10 h-10 rounded-full overflow-hidden shadow-lg">
            <?php if ($avatar_src): ?>
                <img src="<?= $avatar_src ?>" class="w-full h-full object-cover">
            <?php else: ?>
                <div class="w-full h-full bg-green-500 flex items-center justify-center text-white font-bold">
                    <?= strtoupper($_SESSION['email'][0]) ?>
                </div>
            <?php endif ?>
        </a>

    </div>
</nav>

<!-- CONTENT -->
<div class="pt-24 px-6">

    <!-- TITLE -->
    <h2 class="text-2xl font-bold mb-4">สัตว์เลี้ยงที่พร้อมรับเลี้ยง</h2>

    <!-- GRID OF PET CARDS -->
    <div class="grid grid-cols-2 gap-4">

        <!-- PET CARD (EXAMPLE 1) -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <img src="pets/dog1.jpg" class="w-full h-32 object-cover">
            <div class="p-3">
                <p class="font-bold text-sm">Buggy</p>
                <p class="text-xs text-gray-600">Jack Russell</p>
                <p class="text-xs text-gray-400">6 months • Male</p>
            </div>
        </div>

        <!-- PET CARD 2 -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <img src="pets/dog2.jpg" class="w-full h-32 object-cover">
            <div class="p-3">
                <p class="font-bold text-sm">Peach</p>
                <p class="text-xs text-gray-600">Shih Tzu</p>
                <p class="text-xs text-gray-400">4 months • Female</p>
            </div>
        </div>

        <!-- PET CARD 3 -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <img src="pets/dog3.jpg" class="w-full h-32 object-cover">
            <div class="p-3">
                <p class="font-bold text-sm">Gary</p>
                <p class="text-xs text-gray-600">Yorkshire Terrier</p>
                <p class="text-xs text-gray-400">3 years • Female</p>
            </div>
        </div>

        <!-- PET CARD 4 -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <img src="pets/dog4.jpg" class="w-full h-32 object-cover">
            <div class="p-3">
                <p class="font-bold text-sm">Willie</p>
                <p class="text-xs text-gray-600">Samoyed</p>
                <p class="text-xs text-gray-400">1.5 years • Male</p>
            </div>
        </div>

        <!-- PET CARD 5 -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden col-span-2">
            <img src="pets/dog5.jpg" class="w-full h-32 object-cover">
            <div class="p-3">
                <p class="font-bold text-sm">Kiwi</p>
                <p class="text-xs text-gray-600">Yorkshire Terrier</p>
                <p class="text-xs text-gray-400">1 year • Male</p>
            </div>
        </div>

    </div>

</div>

<!-- FLOAT BUTTON -->
<a href="chat.php"
   class="fixed bottom-6 right-6 bg-blue-600 p-4 rounded-full shadow-xl">
    <span class="text-white text-2xl">💬</span>
</a>

</body>
</html>
