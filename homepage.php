<?php
session_start();

// --- Handle avatar upload ---
$upload_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar']) && isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);

    $allowed_types = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
    $file = $_FILES['avatar'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $upload_error = 'เกิดข้อผิดพลาดขณะอัปโหลดไฟล์';
    } elseif (!in_array($file['type'], $allowed_types)) {
        $upload_error = 'ไฟล์ต้องเป็นรูปภาพ (jpg, png, gif)';
    } else {
        $uploads_dir = __DIR__ . '/uploads';
        if (!is_dir($uploads_dir)) mkdir($uploads_dir, 0755, true);

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $target_filename = "avatar_user_$user_id.$ext";
        $target_path = "$uploads_dir/$target_filename";

        foreach (glob("$uploads_dir/avatar_user_$user_id.*") as $old) {
            if ($old !== $target_path) @unlink($old);
        }

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $upload_error = 'ไม่สามารถย้ายไฟล์ไปยังโฟลเดอร์ปลายทางได้';
        }
    }
}

// Load avatar profile
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
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PetHome</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
}
</style>
</head>

<body class="min-h-screen bg-repeat relative" style="background-image: url('bg_pethome_pattern.jpg'); background-size: 300px;">

<!-- ================= OVERLAY ทำให้พื้นหลังจางลง ================= -->
<div class="absolute inset-0 bg-white/70 backdrop-blur-sm pointer-events-none"></div>

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
            <li><a href="donate.php" class="hover:text-green-700">DONATE</a></li>
            <li><a href="request_status.php" class="hover:text-green-700">REQUEST STATUS</a></li>

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

                    <a href="#" class="block py-1 font-medium hover:text-red-600 transition">⚙️ ตั้งค่า</a>
                    <a href="profile.php" class="block py-1 font-medium hover:text-red-600 transition">👤 โปรไฟล์</a>
                    <a href="#" class="block py-1 font-medium hover:text-red-600 transition">ℹ️ About Us</a>

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

<!-- ================= CONTENT ================= -->
<section class="relative z-10 flex flex-col items-center justify-center min-h-screen px-6 pt-24">

    <img src="dog.png" class="w-44 h-44 mt-6 animate-[float_3s_ease-in-out_infinite]">

    <div class="text-center mt-8">
        <h2 class="text-3xl font-black text-gray-900 tracking-wide">Make A New Friends</h2>
        <p class="text-gray-700 mt-3 text-lg">A New Home For Your Four Legged Friend</p>
    </div>

    <div class="mt-8">
        <a href="homeped.php"
        class="px-8 py-3 rounded-full bg-green-600 text-white font-semibold shadow-lg hover:bg-green-700 transition">
            Adopt Now
        </a>
    </div>

</section>

<!-- ================= JS: CLICK TO OPEN, HOLD TO CLOSE ================= -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const btn = document.getElementById("profileBtn");
    const dropdown = document.getElementById("profileDropdown");

    if (!btn || !dropdown) return;

    let holdTimer = null;
    const holdTime = 350; // 0.35 วินาที เพื่อปิด
    let isHolding = false;

    function openDropdown() {
        dropdown.classList.remove("hidden");
    }

    function closeDropdown() {
        dropdown.classList.add("hidden");
    }

    // Single click to open
    btn.addEventListener("click", function (e) {
        e.stopPropagation();
        if (!isHolding) {
            openDropdown();
        }
    });

    // Hold to close
    function startHold(e) {
        if (e.type === "touchstart") e.preventDefault();
        e.stopPropagation();
        isHolding = true;

        holdTimer = setTimeout(() => {
            closeDropdown();
            isHolding = false;
            holdTimer = null;
        }, holdTime);
    }

    function cancelHold() {
        if (holdTimer) {
            clearTimeout(holdTimer);
            holdTimer = null;
        }
        isHolding = false;
    }

    // Desktop
    btn.addEventListener("mousedown", startHold);
    btn.addEventListener("mouseup", cancelHold);
    btn.addEventListener("mouseleave", cancelHold);

    // Mobile
    btn.addEventListener("touchstart", startHold, { passive: false });
    btn.addEventListener("touchend", cancelHold);
    btn.addEventListener("touchcancel", cancelHold);

    // Keep dropdown open when clicking inside
    dropdown.addEventListener("click", function (e) {
        e.stopPropagation();
    });

    // Close by clicking outside
    document.addEventListener("click", function (e) {
        if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
            closeDropdown();
        }
    });

});
</script>

</body>
</html>
