<?php
session_start();
include("database/db_ped.php"); // DB Connection

// --- Handle avatar upload ---
$upload_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar']) && isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
    $allowed_types = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
    $file = $_FILES['avatar'];

    if ($file['error'] === UPLOAD_ERR_OK && in_array($file['type'], $allowed_types)) {

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
        }

    } else {
        $upload_error = "ไฟล์ต้องเป็นภาพ (jpg, png, gif)";
    }
}

// โหลดรูปโปรไฟล์
$avatar_src = null;
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $matches = glob(__DIR__ . "/uploads/avatar_user_$uid.*");
    if (!empty($matches)) {
        $avatar_src = "uploads/" . basename($matches[0]);
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>PetHome - Form</title>
<script src="https://cdn.tailwindcss.com"></script>
<style> body { font-family: 'Prompt', sans-serif; } </style>
</head>

<body class="bg-[#f7d7a3] min-h-screen">

<!-- ============= NAVBAR ============= -->
<nav class="w-full fixed top-0 left-0 bg-white/40 backdrop-blur-md shadow-sm py-4 z-20">
    <div class="flex items-center px-6">

        <!-- LOGO -->
        <h1 class="flex items-center gap-3 text-2xl font-extrabold text-[#2f5d31]">
            <div class="bg-white rounded-full shadow-md p-1 px-2">🐾</div>
            PetHome
        </h1>

        <!-- MENU -->
<ul class="flex items-center space-x-6 text-gray-900 ml-auto">
            <li><a href="homeped.php" class="hover:text-green-700">HOME</a></li>
            <li><a href="form.php" class="text-green-700 font-bold">FORM</a></li>
            <li><a href="#" class="hover:text-green-700">DONATE</a></li>
            <li><a href="#" class="hover:text-green-700">REQUEST STATUS</a></li>

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

                <!-- DROPDOWN -->
                <div id="profileDropdown" class="hidden absolute right-0 mt-3 w-60 bg-white shadow-xl rounded-xl p-4 text-gray-700 transition">

                    <p class="text-sm text-gray-500">เข้าสู่ระบบเป็น</p>
                    <p class="font-bold"><?= explode("@", $_SESSION['email'])[0]; ?></p>
                    <p class="text-sm"><?= $_SESSION['email']; ?></p>

                    <hr class="my-3">

                    <form method="POST" enctype="multipart/form-data">
                        <label class="flex items-center gap-3 cursor-pointer bg-gray-100 p-2 rounded-lg hover:bg-gray-200">
                            <div class="w-10 h-10 rounded-full overflow-hidden shadow">
                                <?php if ($avatar_src): ?>
                                    <img src="<?= $avatar_src ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full bg-green-400 flex items-center justify-center text-white font-bold">
                                        <?= strtoupper($_SESSION['email'][0]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <span class="text-sm">เปลี่ยนรูปโปรไฟล์</span>
                            <input type="file" name="avatar" class="hidden" onchange="this.form.submit()" accept="image/*">
                        </label>
                    </form>

                    <hr class="my-3">
                    <a href="#" class="block py-1">⚖️ น้ำหนักของฉัน</a>
                    <a href="#" class="block py-1">🏃 การออกกำลังกาย</a>
                    <a href="#" class="block py-1">📄 บทความ</a>

                    <hr class="my-3">
                    <a href="logout.php" class="text-red-600 font-bold">ออกจากระบบ</a>
                </div>

            </li>
            <?php else: ?>

            <li>
                <a href="login.php" class="px-5 py-2 rounded-full bg-green-600 text-white font-semibold shadow-md hover:bg-green-700 transition">
                   Login
                </a>
            </li>

            <?php endif; ?>
        </ul>
    </div>
</nav>
<!-- ============= END NAVBAR ============= -->


<!-- FORM CONTENT -->
<div class="max-w-lg mx-auto pt-28 px-4 pb-10">

    <h2 class="text-xl font-bold text-center text-gray-900">ฟอร์มประเมินความเหมาะสมของคุณ</h2>
    <p class="text-center text-sm text-gray-700 mb-6">กรอกข้อมูลให้ครบถ้วน</p>

    <form method="POST" action="save_form.php" class="space-y-5">

        <div>
            <label class="font-semibold text-sm text-gray-900">ชื่อ-นามสกุลของคุณ</label>
            <input type="text" name="fullname"
                   class="w-full p-3 rounded-lg border mt-1" placeholder="ใส่คำตอบของคุณ">
        </div>

        <div>
            <label class="font-semibold text-sm text-gray-900">ที่อยู่-เบอร์ติดต่อ</label>
            <input type="text" name="contact"
                   class="w-full p-3 rounded-lg border mt-1" placeholder="ใส่คำตอบของคุณ">
        </div>

        <div>
            <label class="font-semibold text-sm text-gray-900">พื้นที่การเลี้ยงดู</label>
            <input type="text" name="area"
                   class="w-full p-3 rounded-lg border mt-1" placeholder="ใส่คำตอบของคุณ">
        </div>

        <div>
            <label class="font-semibold text-sm text-gray-900">สถานะการเป็นคนรับเลี้ยง</label>
            <input type="text" name="status"
                   class="w-full p-3 rounded-lg border mt-1" placeholder="ใส่คำตอบของคุณ">
        </div>

        <div>
            <label class="font-semibold text-sm text-gray-900">อายุของคุณ</label>
            <input type="text" name="age"
                   class="w-full p-3 rounded-lg border mt-1" placeholder="ใส่คำตอบของคุณ">
        </div>

        <div>
            <label class="font-semibold text-sm text-gray-900">เวลาในการเลี้ยงดู</label>
            <input type="text" name="time"
                   class="w-full p-3 rounded-lg border mt-1" placeholder="ใส่คำตอบของคุณ">
        </div>

        <button class="w-full bg-green-600 text-white py-3 rounded-full mt-6 hover:bg-green-700 transition font-semibold">
            ยืนยัน
        </button>

    </form>

</div>

<script>
// Click to open, hold to close
document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('profileBtn');
    var dropdown = document.getElementById('profileDropdown');
    if (!btn || !dropdown) return;

    var holdTimer = null;
    var holdTime = 350; // ms
    var isHolding = false;

    function openDropdown(){
        dropdown.classList.remove('hidden');
    }
    function closeDropdown(){
        dropdown.classList.add('hidden');
    }

    // Single click to open
    btn.addEventListener('click', function(e){
        e.stopPropagation();
        if (!isHolding) {
            openDropdown();
        }
    });

    // Hold to close
    function startHold(e){
        e.stopPropagation();
        isHolding = true;
        if (holdTimer) clearTimeout(holdTimer);
        holdTimer = setTimeout(function(){
            closeDropdown();
            isHolding = false;
            holdTimer = null;
        }, holdTime);
    }

    function cancelHold(e){
        if (holdTimer) { clearTimeout(holdTimer); holdTimer = null; }
        isHolding = false;
    }

    // Mouse events
    btn.addEventListener('mousedown', startHold);
    btn.addEventListener('mouseup', cancelHold);
    btn.addEventListener('mouseleave', cancelHold);

    // Touch events
    btn.addEventListener('touchstart', startHold, {passive: false});
    btn.addEventListener('touchend', cancelHold);
    btn.addEventListener('touchcancel', cancelHold);

    // Keep dropdown open when interacting inside
    dropdown.addEventListener('click', function(e){ e.stopPropagation(); });

    // Close when clicking elsewhere
    document.addEventListener('click', function(e){
        if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
            closeDropdown();
        }
    });
});
</script>

</body>
</html>
