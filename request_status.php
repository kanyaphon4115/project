<?php
session_start();

// DB ฟอร์ม
$con_forms = new mysqli("localhost", "root", "", "adopt_forms");

// DB สุนัข
$con_dogs = new mysqli("localhost", "root", "", "ped_home");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);


// ===============================
// 1. รับ dog_id จาก Adopt Me
// ===============================
if (isset($_GET['add_dog'])) {

    $dog_id = intval($_GET['add_dog']);

    // ตรวจว่าผู้ใช้มีฟอร์มไหม
    $check = $con_forms->query("SELECT * FROM adopt_forms WHERE user_id=$user_id LIMIT 1");

    if ($check->num_rows == 0) {
        // ถ้ายังไม่มีก็ให้ไปกรอกก่อน
        header("Location: form.php?need_form=1");
        exit;
    }

    // โหลดข้อมูลฟอร์มเดิม
    $form = $check->fetch_assoc();

    $fullname     = $form["fullname"];
    $contact      = $form["contact"];
    $area         = $form["area"];
    $experience   = $form["experience"];
    $time_home    = $form["time_home"];
    $reason       = $form["reason"];
    $family_agree = $form["family_agree"];
    $care_time    = $form["care_time"];
    $check_duplicate = $con_forms->query("
    SELECT id FROM adopt_forms
    WHERE user_id = $user_id AND dog_id = $dog_id
    LIMIT 1
");

if ($check_duplicate->num_rows > 0) {
    // ผู้ใช้เคยเลือกสุนัขตัวนี้แล้ว
    header("Location: request_status.php?duplicate=1");
    exit;
}

    // เพิ่มรายการใหม่ (ไม่ทับรายการเก่า)
    $stmt = $con_forms->prepare("
        INSERT INTO adopt_forms
        (user_id, dog_id, fullname, contact, area, experience, time_home, reason, family_agree, care_time)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "iissssisss",
        $user_id, $dog_id,
        $fullname, $contact, $area, $experience,
        $time_home, $reason, $family_agree, $care_time
    );

    $stmt->execute();

    header("Location: request_status.php?added_success=1");
    exit;
}


// ===============================
// 2. ลบรายการ
// ===============================
if (isset($_GET['delete_id'])) {

    $delete_id = intval($_GET['delete_id']);

    // ทำให้แน่ใจว่าลบของตัวเองเท่านั้น
    $verify = $con_forms->query("SELECT user_id FROM adopt_forms WHERE id=$delete_id");

    if ($verify->num_rows > 0) {
        $row = $verify->fetch_assoc();

        if ($row['user_id'] == $user_id) {
            $con_forms->query("DELETE FROM adopt_forms WHERE id=$delete_id");
        }
    }

    header("Location: request_status.php");
    exit;
}


// ===============================
// 3. โหลด avatar
// ===============================
$avatar_src = null;
$match = glob(__DIR__ . "/uploads/avatar_user_$user_id.*");
if (!empty($match)) {
    $avatar_src = "uploads/" . basename($match[0]);
}


// ===============================
// 4. โหลดรายการสุนัขทั้งหมดที่เลือก
// ===============================
$res_forms = $con_forms->query("
    SELECT * FROM adopt_forms 
    WHERE user_id=$user_id 
    ORDER BY id DESC
");

?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Request Status - PetHome</title>
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
        <h1 class="flex items-center gap-3 text-2xl font-extrabold text-[#2f5d31]">
            <div class="bg-white rounded-full shadow-md p-1 px-2">🐾</div>
            PetHome
        </h1>

        <!-- MENU -->
        <ul class="flex items-center space-x-6 text-gray-900 ml-auto">
            <li><a href="homeped.php" class="hover:text-green-700">HOME</a></li>
            <li><a href="form.php" class="hover:text-green-700">FORM</a></li>
            <li><a href="donate.php" class="hover:text-green-700">DONATE</a></li>
            <li><a href="request_status.php" class="text-green-700 font-bold">REQUEST STATUS</a></li>

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
                    <a href="#" class="block py-1 font-medium hover:text-red-600 transition">⚙️ ตั้งค่า</a>
                    <a href="profile.php" class="block py-1 font-medium hover:text-red-600 transition">👤 โปรไฟล์</a>
                    <a href="#" class="block py-1 font-medium hover:text-red-600 transition">ℹ️ About Us</a>

                    <hr class="my-3">
                    <a href="index.php" class="text-red-600 font-bold">ออกจากระบบ</a>
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

<div class="pt-28"></div>

<!-- HEADER -->
<div class="text-center">
    <div class="bg-white rounded-3xl w-56 mx-auto p-5 shadow">
        <div class="text-4xl">📋</div>
        <p class="text-2xl font-bold text-[#2f5d31]">สถานะคำขอรับเลี้ยง</p>
    </div>
    <p class="text-gray-700 mt-4">ตรวจสอบรายการสุนัขที่คุณขอรับเลี้ยง</p>
</div>

<!-- LIST -->
<div class="max-w-3xl mx-auto mt-10 space-y-6 mb-24">

<?php if ($res_forms->num_rows == 0): ?>

    <div class="text-center bg-white p-6 rounded-2xl shadow text-gray-700">
        ยังไม่มีรายการรับเลี้ยง
    </div>

<?php else: 

// ===============================
// โหลดสถานะจาก JSON (ครั้งเดียว)
// ===============================
$status_file = "status.json";
$status_list = [];

if (file_exists($status_file)) {
    $status_list = json_decode(file_get_contents($status_file), true);
}

// ===============================
// แสดงรายการทีละรายการ
// ===============================

while ($f = $res_forms->fetch_assoc()):

    $dog_id = $f['dog_id'];
    $dog = $con_dogs->query("SELECT * FROM dogs WHERE id=$dog_id")->fetch_assoc();

    if (!$dog) continue;

    // โหลดสถานะ
    $form_id = $f["id"];
    $status = $status_list[$form_id] ?? "Pending";

    $badge_color = [
        "Pending"  => "bg-yellow-200 text-yellow-800",
        "Approved" => "bg-green-200 text-green-800",
        "Rejected" => "bg-red-200 text-red-800"
    ][$status];
?>

<div class="bg-white shadow-md rounded-2xl p-5 flex gap-5 border border-[#e6d5b8] hover:shadow-xl transition duration-300">

    <img src="<?= $dog['image'] ?>" 
         class="w-32 h-32 rounded-2xl object-cover shadow-md border">

    <div class="flex flex-col justify-between w-full">
        
        <div>
            <p class="text-2xl font-bold text-[#2f5d31] flex items-center gap-3">
                <?= $dog['name'] ?>
                <span class="px-3 py-1 text-sm rounded-full <?= $badge_color ?>">
                    <?= $status ?>
                </span>
            </p>

            <p class="text-gray-600 text-sm">
                <?= $dog['gender'] ?> • อายุ <?= $dog['age'] ?>
            </p>
        </div>

        <div class="flex justify-between mt-4">

            <a href="dog_details.php?id=<?= $dog_id ?>" 
               class="text-green-700 font-semibold hover:text-green-900 transition">
                ➜ ดูรายละเอียดสุนัข
            </a>

            <a href="?delete_id=<?= $f['id'] ?>"
               onclick="return confirm('ลบรายการนี้หรือไม่?')"
               class="text-red-600 font-semibold hover:text-red-800 transition">
                🗑️ ลบ
            </a>

        </div>
    </div>

</div>

<?php endwhile; endif; ?>

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
