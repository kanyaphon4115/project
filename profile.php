<?php
session_start();
include("database/db_ped.php");

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

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

// Load avatar
$avatar_src = null;
$uid = intval($_SESSION['user_id']);
$matches = glob(__DIR__ . "/uploads/avatar_user_$uid.*");
if (!empty($matches)) {
    $avatar_src = "uploads/" . basename($matches[0]);
}

// --- Load / Save profile fields (username, contact, bio, address, birthdate) ---
$profile_message = '';
$con_register = new mysqli("localhost", "root", "", "register");
if ($con_register->connect_errno) {
    // ignore - will show blank fields
} else {
    // Fetch existing row (select * to tolerate missing columns)
    $stmt_prof = $con_register->prepare("SELECT * FROM form WHERE id = ? LIMIT 1");
    $stmt_prof->bind_param('i', $uid);
    $stmt_prof->execute();
    $res_prof = $stmt_prof->get_result();
    $profile_row = $res_prof->fetch_assoc() ?: [];
    $stmt_prof->close();

    // Prefill values (if columns exist they'll be present)
    $username = $profile_row['username'] ?? '';
    $contact  = $profile_row['contact'] ?? '';
    $bio      = $profile_row['bio'] ?? '';
    $address  = $profile_row['address'] ?? '';
    $birthdate= $profile_row['birthdate'] ?? '';

    // Handle profile save (separate from avatar upload)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
        $new_username = trim($_POST['username'] ?? '');
        $new_contact  = trim($_POST['contact'] ?? '');
        $new_bio      = trim($_POST['bio'] ?? '');
        $new_address  = trim($_POST['address'] ?? '');
        $new_birth    = trim($_POST['birthdate'] ?? '');

        // Ensure columns exist, if not add them
        $needed = [
            'username' => "VARCHAR(120)",
            'contact'  => "VARCHAR(80)",
            'bio'      => "TEXT",
            'address'  => "TEXT",
            'birthdate'=> "DATE"
        ];

        foreach ($needed as $col=>$type) {
            $check = $con_register->query("SHOW COLUMNS FROM form LIKE '$col'");
            if ($check->num_rows === 0) {
                $con_register->query("ALTER TABLE form ADD COLUMN $col $type NULL");
            }
        }

        // Now update with prepared statement
        $upd = $con_register->prepare("UPDATE form SET username=?, contact=?, bio=?, address=?, birthdate=? WHERE id=?");
        $birth_param = $new_birth !== '' ? $new_birth : null;
        $upd->bind_param('sssssi', $new_username, $new_contact, $new_bio, $new_address, $birth_param, $uid);
        if ($upd->execute()) {
            // Use PRG pattern: set flash message then redirect to view mode
            $_SESSION['profile_message'] = 'บันทึกข้อมูลโปรไฟล์เรียบร้อยแล้ว';
            $upd->close();
            header('Location: profile.php');
            exit;
        } else {
            $profile_message = 'เกิดข้อผิดพลาด: ' . $con_register->error;
            $upd->close();
        }
    }

    // Get adoption requests count
    $con_forms = new mysqli("localhost", "root", "", "adopt_forms");
    $user_id = $_SESSION['user_id'];
    $res_count = $con_forms->query("SELECT COUNT(*) as cnt FROM adopt_forms WHERE user_id = $user_id");
    $count_row = $res_count->fetch_assoc();
    $request_count = $count_row['cnt'];
    $con_forms->close();

}

// Read flash message if present
if (isset($_SESSION['profile_message'])) {
    $profile_message = $_SESSION['profile_message'];
    unset($_SESSION['profile_message']);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>โปรไฟล์ - PawHome</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
    body { font-family: 'Prompt', sans-serif; }
</style>
</head>

<body class="bg-[#f7d7a3] min-h-screen">

<!-- NAVBAR -->
<nav class="w-full fixed top-0 left-0 bg-white/40 backdrop-blur-md shadow-sm py-4 z-20">
    <div class="flex items-center px-6">

        <!-- LOGO -->
        <h1 class="text-2xl font-extrabold text-[#2f5d31] flex items-center gap-2">
            <span class="bg-white rounded-full shadow px-2">🐾</span> PawHome
        </h1>

        <!-- MENU -->
        <ul class="flex items-center space-x-6 text-gray-900 ml-auto">
            <li><a href="homeped.php" class="hover:text-green-600">HOME</a></li>
            <li><a href="form.php" class="hover:text-green-600">FORM</a></li>
            <li><a href="donate.php" class="hover:text-green-600">DONATE</a></li>
            <li><a href="request_status.php" class="hover:text-green-600">REQUEST STATUS</a></li>

            <!-- PROFILE -->
            <li class="relative" id="profileMenu">

                <button id="profileBtn" type="button" class="w-10 h-10 rounded-full bg-blue-300 shadow-md overflow-hidden flex items-center justify-center">
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
                    <a href="#" class="block py-1 font-medium hover:text-red-600 transition">⚙️ ตั้งค่า</a>
                    <a href="profile.php" class="block py-1 font-medium hover:text-red-600 transition">👤 โปรไฟล์</a>
                    <a href="about_us.php" class="block py-1 font-medium hover:text-red-600 transition">ℹ️ About Us</a>
                    <hr class="my-3">
                    <a href="index.php" class="text-red-600 font-bold">ออกจากระบบ</a>
                </div>

            </li>
        </ul>
    </div>
</nav>

<!-- OFFSET -->
<div class="pt-28"></div>

<!-- PROFILE HEADER -->
<div class="max-w-5xl mx-auto">
    
    <!-- PROFILE CARD -->
    <div class="bg-white shadow-2xl rounded-3xl p-8 mb-8">

        <div class="flex flex-col md:flex-row gap-8 items-start">

            <!-- AVATAR SECTION -->
            <div class="flex flex-col items-center">
                <div class="w-40 h-40 rounded-full bg-gradient-to-br from-green-300 to-green-500 shadow-xl overflow-hidden flex items-center justify-center">
                    <?php if ($avatar_src): ?>
                        <img src="<?= $avatar_src ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-6xl text-white font-bold"><?= strtoupper($_SESSION['email'][0]); ?></span>
                    <?php endif; ?>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="mt-4">
                    <label class="flex items-center gap-2 px-6 py-2 bg-green-600 text-white rounded-xl font-semibold shadow-lg hover:bg-green-700 transition cursor-pointer">
                        📷 เปลี่ยนรูป
                        <input type="file" name="avatar" class="hidden" onchange="this.form.submit()" accept="image/*">
                    </label>
                </form>
            </div>

            <!-- USER INFO -->
            <div class="flex-1">
                <h2 class="text-4xl font-extrabold text-[#2f5d31] mb-2">
                    <?= explode("@", $_SESSION['email'])[0]; ?>
                </h2>
                
                <div class="space-y-3 text-gray-700">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">📧</span>
                        <p class="text-lg"><?= $_SESSION['email']; ?></p>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <span class="text-xl">📋</span>
                        <p class="text-lg">คำขอรับเลี้ยง: <span class="font-bold text-green-600"><?= $request_count; ?></span> รายการ</p>
                    </div>
                </div>

                <?php $is_edit = (isset($_GET['edit']) && $_GET['edit'] == '1'); ?>

                <?php if (!empty($profile_message)): ?>
                    <div class="p-3 mb-3 rounded-md bg-green-100 text-green-800"><?= htmlspecialchars($profile_message) ?></div>
                <?php endif; ?>

                <?php if ($is_edit): ?>
                    <!-- PROFILE EDIT FORM -->
                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <form method="POST" class="space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="text-sm text-gray-600">ชื่อผู้ใช้งาน (username)</label>
                                    <input name="username" value="<?= htmlspecialchars($username ?? '') ?>" class="w-full mt-1 p-2 rounded-lg border" placeholder="ชื่อผู้ใช้งาน">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">ติดต่อ (contact)</label>
                                    <input name="contact" value="<?= htmlspecialchars($contact ?? '') ?>" class="w-full mt-1 p-2 rounded-lg border" placeholder="เบอร์โทร / ช่องทางติดต่อ">
                                </div>
                            </div>

                            <div>
                                <label class="text-sm text-gray-600">ประวัติสั้น ๆ (bio)</label>
                                <textarea name="bio" class="w-full mt-1 p-2 rounded-lg border" rows="3" placeholder="บอกเล่าเกี่ยวกับคุณหรือบ้านของคุณ"><?= htmlspecialchars($bio ?? '') ?></textarea>
                            </div>

                            <div>
                                <label class="text-sm text-gray-600">ที่อยู่ (address)</label>
                                <input name="address" value="<?= htmlspecialchars($address ?? '') ?>" class="w-full mt-1 p-2 rounded-lg border" placeholder="ที่อยู่">
                            </div>

                            <div>
                                <label class="text-sm text-gray-600">วันเกิด (birthdate)</label>
                                <input type="date" name="birthdate" value="<?= htmlspecialchars($birthdate ?? '') ?>" class="w-full mt-1 p-2 rounded-lg border">
                            </div>

                            <div class="flex items-center justify-end">
                                <button type="submit" name="save_profile" class="px-5 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700">บันทึกข้อมูล</button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- PROFILE VIEW -->
                    <div class="mt-6 bg-gray-50 p-6 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">ข้อมูลโปรไฟล์</h4>
                        <div class="space-y-2 text-gray-700">
                            <div><strong>ชื่อผู้ใช้งาน:</strong> <?= htmlspecialchars($username ?: explode('@', $_SESSION['email'])[0]) ?></div>
                            <div><strong>ติดต่อ:</strong> <?= htmlspecialchars($contact ?: '-') ?></div>
                            <div><strong>ประวัติ:</strong> <?= nl2br(htmlspecialchars($bio ?: '-')) ?></div>
                            <div><strong>ที่อยู่:</strong> <?= nl2br(htmlspecialchars($address ?: '-')) ?></div>
                            <div><strong>วันเกิด:</strong> <?= htmlspecialchars($birthdate ?: '-') ?></div>
                        </div>

                        <div class="flex justify-end mt-4">
                            <a href="profile.php?edit=1" class="px-4 py-2 bg-yellow-500 text-white rounded-lg font-semibold hover:bg-yellow-600">แก้ไข</a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- ACTION BUTTONS -->
                <div class="flex gap-4 mt-6">
                    <a href="homeped.php" class="flex items-center gap-2 px-6 py-2 bg-green-600 text-white rounded-xl font-semibold shadow-lg hover:bg-green-700 transition">
                        🐶 ดูสุนัข
                    </a>
                    <a href="request_status.php" class="flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-xl font-semibold shadow-lg hover:bg-blue-700 transition">
                        📋 สถานะการขอ
                    </a>
                </div>
            </div>

        </div>

    </div>

    <!-- RECENT REQUESTS -->
    <div class="bg-white shadow-xl rounded-2xl p-8 mb-8">
        <h3 class="text-2xl font-extrabold text-[#2f5d31] mb-6">📋 คำขอล่าสุด</h3>
        
        <div class="text-center text-gray-600 py-8">
            <p>ไปที่หน้า <a href="request_status.php" class="text-green-600 font-semibold hover:underline">สถานะการรับเลี้ยง</a> เพื่อดูรายละเอียดคำขอของคุณ</p>
        </div>
    </div>

</div>

<!-- FOOTER DOG IMAGE -->
<div class="flex justify-center pb-10">
    <img src="dog3.png" class="w-52 drop-shadow-lg">
</div>

<script>
// Click to open, hold to close for profile dropdown
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
