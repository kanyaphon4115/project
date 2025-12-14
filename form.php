<?php
session_start();
include("database/db_form.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];

// โหลดรูปโปรไฟล์
$avatar_src = null;
$matches = glob(__DIR__ . "/uploads/avatar_user_$uid.*");
if (!empty($matches)) {
    $avatar_src = "uploads/" . basename($matches[0]);
}

// โหลดฟอร์มเดิม
$form = $con->query("SELECT * FROM adopt_forms WHERE user_id = $uid LIMIT 1")->fetch_assoc();
$edit_mode = $form ? true : false;

// SAVE ฟอร์ม
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullname      = $_POST["fullname"];
    $contact       = $_POST["contact"];
    $area          = $_POST["area"];
    $experience    = $_POST["experience"];
    $time_home     = $_POST["time_home"];
    $reason        = $_POST["reason"];
    $family_agree  = $_POST["family_agree"];
    $care_time     = $_POST["care_time"];

    if ($edit_mode) {
        // UPDATE
        $stmt = $con->prepare("
            UPDATE adopt_forms SET
                fullname=?, contact=?, area=?, experience=?, time_home=?, reason=?, family_agree=?, care_time=?
            WHERE user_id=?
        ");
        $stmt->bind_param("ssssisssi",
            $fullname, $contact, $area, $experience,
            $time_home, $reason, $family_agree, $care_time, $uid
        );
    } else {
        // INSERT
        $stmt = $con->prepare("
            INSERT INTO adopt_forms
            (user_id, fullname, contact, area, experience, time_home, reason, family_agree, care_time)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isssssiss",
            $uid, $fullname, $contact, $area, $experience,
            $time_home, $reason, $family_agree, $care_time
        );
    }

    if ($stmt->execute()) {
        header("Location: homepage.php?form=saved");
        exit;
    } else {
        $error_message = "เกิดข้อผิดพลาด: " . $con->error;
    }
}

function inputValue($form, $field) {
    return $form[$field] ?? "";
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>PawHome - Form</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
    body { font-family: 'Prompt', sans-serif; }
</style>
</head>

<body class="bg-[#f7d7a3] min-h-screen">

<!-- ================= NAVBAR ================= -->
<nav class="w-full fixed top-0 left-0 bg-white/40 backdrop-blur-md shadow-sm py-4 z-20">
    <div class="flex items-center px-6">

        <!-- LOGO -->
        <h1 class="flex items-center gap-3 text-2xl font-extrabold text-[#2f5d31]">
            <div class="bg-white rounded-full shadow p-1 px-2">🐾</div>
            PawHome
        </h1>

        <!-- MENU -->
        <ul class="flex items-center space-x-6 text-gray-900 ml-auto">
            <li><a href="homeped.php" class="hover:text-green-700">HOME</a></li>
            <li><a href="form.php" class="text-green-700 font-bold">FORM</a></li>
            <li><a href="donate.php" class="hover:text-green-700">DONATE</a></li>
            <li><a href="request_status.php" class="hover:text-green-700">REQUEST STATUS</a></li>

            <!-- PROFILE DROPDOWN -->
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
                    class="hidden absolute right-0 mt-3 w-60 bg-white shadow-xl rounded-xl p-4 text-gray-700">

                    <p class="text-sm text-gray-500">เข้าสู่ระบบเป็น</p>
                    <p class="font-bold"><?= explode("@", $_SESSION['email'])[0]; ?></p>
                    <p class="text-sm"><?= $_SESSION['email']; ?></p>

                    <hr class="my-3">

                    <a href="setting.php" class="block py-1 hover:text-green-600">⚙️ ตั้งค่า</a>
                    <a href="profile.php" class="block py-1 hover:text-green-600">👤 โปรไฟล์</a>
                    <a href="about_us.php" class="block py-1 hover:text-green-600">ℹ️ About Us</a>

                    <hr class="my-3">
                    <a href="index.php" class="text-red-600 font-bold">ออกจากระบบ</a>
                </div>

            </li>
            <?php endif; ?>
        </ul>

    </div>
</nav>

<!-- ================= FORM CONTENT ================= -->
<div class="max-w-2xl mx-auto mt-32 mb-16 p-8 bg-white/70 backdrop-blur-lg shadow-xl rounded-2xl">

    <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-[#2f5d31]">
            <?= $edit_mode ? "แก้ไขข้อมูลฟอร์มการรับเลี้ยงสุนัข" : "แบบฟอร์มรับเลี้ยงสุนัข" ?>
        </h2>
        <p class="text-gray-700"><?= $edit_mode ? "คุณสามารถแก้ไขได้ตลอดเวลา" : "กรุณากรอกข้อมูลตามความจริง" ?></p>
    </div>

    <?php if (!empty($error_message)): ?>
    <div class="p-4 bg-red-200 text-red-800 rounded-xl mb-4">
        <?= $error_message ?>
    </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">

        <!-- FULL NAME -->
        <div>
            <label class="font-semibold text-[#2f5d31]">ชื่อ-นามสกุล</label>
            <input type="text" name="fullname" required
                value="<?= inputValue($form,'fullname') ?>"
                class="w-full p-3 rounded-xl border bg-[#FAEED1]">
        </div>

        <!-- CONTACT -->
        <div>
            <label class="font-semibold text-[#2f5d31]">ที่อยู่ / เบอร์ติดต่อ</label>
            <input type="text" name="contact" required
                value="<?= inputValue($form,'contact') ?>"
                class="w-full p-3 rounded-xl border bg-[#FAEED1]">
        </div>

        <!-- AREA -->
        <div>
            <label class="font-semibold text-[#2f5d31]">พื้นที่เลี้ยงดู</label>
            <textarea name="area" required
                class="w-full p-3 rounded-xl border bg-[#FAEED1]"><?= inputValue($form,'area') ?></textarea>
        </div>

        <!-- EXPERIENCE -->
        <div>
            <label class="font-semibold text-[#2f5d31]">ประสบการณ์เลี้ยงสัตว์</label>
            <select name="experience" required
                class="w-full p-3 rounded-xl border bg-[#FAEED1]">
                <option value="">เลือกตัวเลือก</option>
                <option value="ไม่มีประสบการณ์" <?= inputValue($form,'experience')=="ไม่มีประสบการณ์"?"selected":"" ?>>ไม่มีประสบการณ์</option>
                <option value="เคยเลี้ยงมาก่อน" <?= inputValue($form,'experience')=="เคยเลี้ยงมาก่อน"?"selected":"" ?>>เคยเลี้ยงมาก่อน</option>
                <option value="กำลังเลี้ยงอยู่แล้ว" <?= inputValue($form,'experience')=="กำลังเลี้ยงอยู่แล้ว"?"selected":"" ?>>กำลังเลี้ยงอยู่แล้ว</option>
            </select>
        </div>

        <!-- HOURS -->
        <div>
            <label class="font-semibold text-[#2f5d31]">คุณอยู่บ้านกี่ชั่วโมงต่อวัน?</label>
            <input type="number" name="time_home" required
                value="<?= inputValue($form,'time_home') ?>"
                class="w-full p-3 rounded-xl border bg-[#FAEED1]">
        </div>

        <!-- REASON -->
        <div>
            <label class="font-semibold text-[#2f5d31]">เหตุผลที่ต้องการรับเลี้ยง</label>
            <textarea name="reason" required
                class="w-full p-3 rounded-xl border bg-[#FAEED1]"><?= inputValue($form,'reason') ?></textarea>
        </div>

        <!-- FAMILY AGREE -->
        <div>
            <label class="font-semibold text-[#2f5d31]">สมาชิกในบ้านเห็นด้วยหรือไม่?</label>
            <?php $agree = inputValue($form,'family_agree'); ?>

            <div class="flex gap-6">
                <label><input type="radio" name="family_agree" value="Yes" <?= $agree=="Yes"?"checked":"" ?>> เห็นด้วย</label>
                <label><input type="radio" name="family_agree" value="No" <?= $agree=="No"?"checked":"" ?>> ไม่เห็นด้วย</label>
            </div>
        </div>

        <!-- CARE TIME -->
        <div>
            <label class="font-semibold text-[#2f5d31]">เวลาที่ดูแลสุนัข</label>
            <input type="text" name="care_time" required
                value="<?= inputValue($form,'care_time') ?>"
                class="w-full p-3 rounded-xl border bg-[#FAEED1]">
        </div>
<!-- SUBMIT BUTTON -->
<div class="flex justify-center">
    <button type="submit"
        class="bg-green-600 text-white px-12 py-3 rounded-xl font-bold hover:bg-green-700">
        <?= $edit_mode ? "บันทึกการแก้ไข" : "บันทึกข้อมูล" ?>
    </button>
</div>

<!-- ================= DROPDOWN SCRIPT ================= -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const btn = document.getElementById("profileBtn");
    const menu = document.getElementById("profileDropdown");

    btn.addEventListener("click", function () {
        menu.classList.toggle("hidden");
    });

    document.addEventListener("click", function (e) {
        if (!btn.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add("hidden");
        }
    });

});
</script>

</body>
</html>
