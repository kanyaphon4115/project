<?php
session_start();
include("database/db_form.php"); // ใช้ $con สำหรับเชื่อมต่อฐานข้อมูล

// โหลดรูปโปรไฟล์
$avatar_src = null;
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $matches = glob(__DIR__ . "/uploads/avatar_user_$uid.*");
    if (!empty($matches)) {
        $avatar_src = "uploads/" . basename($matches[0]);
    }
}

// รับ dog_id จากหน้า Adopt Me
$dog_id = $_GET['dog_id'] ?? 0;

// ------------------ Save Form ------------------
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullname      = $_POST["fullname"];
    $contact       = $_POST["contact"];
    $area          = $_POST["area"];
    $experience    = $_POST["experience"];
    $time_home     = $_POST["time_home"];
    $reason        = $_POST["reason"];
    $family_agree  = $_POST["family_agree"] ?? "";
    $care_time     = $_POST["care_time"];

    // บันทึกข้อมูล + user_id + dog_id
    $stmt = $con->prepare("
        INSERT INTO adopt_forms 
        (user_id, dog_id, fullname, contact, area, experience, time_home, reason, family_agree, care_time)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "iissssisss",
        $_SESSION['user_id'], 
        $dog_id,
        $fullname, $contact, $area, $experience,
        $time_home, $reason, $family_agree, $care_time
    );

    if ($stmt->execute()) {
        $success_message = "🎉 บันทึกข้อมูลสำเร็จ!";
    } else {
        $success_message = "❌ เกิดข้อผิดพลาด: " . $con->error;
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

<!-- NAVBAR -->
<nav class="w-full fixed top-0 left-0 bg-white/40 backdrop-blur-md shadow-sm py-4 z-20">
    <div class="flex items-center px-6">

        <h1 class="flex items-center gap-3 text-2xl font-extrabold text-[#2f5d31]">
            <div class="bg-white rounded-full shadow-md p-1 px-2">🐾</div>
            PetHome
        </h1>

        <ul class="flex items-center space-x-6 text-gray-900 ml-auto">
            <li><a href="homeped.php" class="hover:text-green-700">HOME</a></li>
            <li><a href="form.php" class="text-green-700 font-bold">FORM</a></li>
            <li><a href="#" class="hover:text-green-700">DONATE</a></li>
            <li><a href="#" class="hover:text-green-700">REQUEST STATUS</a></li>

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

                    <a href="#" class="block py-1">⚖️ น้ำหนักของฉัน</a>
                    <a href="#" class="block py-1">🏃 การออกกำลังกาย</a>
                    <a href="#" class="block py-1">📄 บทความ</a>

                    <hr class="my-3">
                    <a href="index.php" class="text-red-600 font-bold">ออกจากระบบ</a>
                </div>

            </li>
            <?php endif; ?>
        </ul>

    </div>
</nav>

<!-- FORM CONTENT -->
<div class="max-w-2xl mx-auto mt-32 mb-16 p-8 bg-white/70 backdrop-blur-lg shadow-xl rounded-2xl border border-[#f2d4a5]">

    <!-- success message -->
    <?php if (!empty($success_message)): ?>
        <div class="p-4 mb-6 bg-green-200 text-green-900 rounded-xl text-center font-bold">
            <?= $success_message ?>
        </div>
    <?php endif; ?>

    <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-[#2f5d31]">แบบฟอร์มสอบถามเพื่อรับเลี้ยงสุนัข</h2>
        <p class="text-gray-700 mt-1">กรุณากรอกข้อมูลให้ครบถ้วนตามความจริง</p>
    </div>

    <form method="POST" class="space-y-6">

<!-- ================= FORM FIELDS ================= -->

<div>
    <label class="block font-semibold text-[#2f5d31] mb-1">ชื่อ-นามสกุล</label>
    <input type="text" name="fullname"
        class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-500 focus:outline-none"
        placeholder="กรอกชื่อ-นามสกุลของคุณ">
</div>

<div>
    <label class="block font-semibold text-[#2f5d31] mb-1">ที่อยู่ / เบอร์ติดต่อ</label>
    <input type="text" name="contact"
        class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-500"
        placeholder="กรอกข้อมูลการติดต่อของคุณ">
</div>

<div>
    <label class="block font-semibold text-[#2f5d31] mb-1">พื้นที่ในการเลี้ยงดู</label>
    <textarea name="area"
        class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-500"
        placeholder="เช่น บ้านพร้อมรั้ว / ห้องเช่ามีพื้นที่จำกัด"></textarea>
</div>

<div>
    <label class="block font-semibold text-[#2f5d31] mb-1">ประสบการณ์ในการเลี้ยงสัตว์</label>
    <select name="experience"
        class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-500">
        <option value="">เลือกตัวเลือก</option>
        <option value="ไม่มีประสบการณ์">ไม่มีประสบการณ์</option>
        <option value="เคยเลี้ยงมาก่อน">เคยเลี้ยงมาก่อน</option>
        <option value="กำลังเลี้ยงอยู่แล้ว">กำลังเลี้ยงสัตว์ในปัจจุบัน</option>
    </select>
</div>

<div>
    <label class="block font-semibold text-[#2f5d31] mb-1">คุณอยู่บ้านเฉลี่ยกี่ชั่วโมงต่อวัน?</label>
    <input type="number" name="time_home"
        class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-500"
        placeholder="กรอกจำนวนชั่วโมง">
</div>

<div>
    <label class="block font-semibold text-[#2f5d31] mb-1">เหตุผลที่ต้องการรับเลี้ยงสุนัข</label>
    <textarea name="reason"
        class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-500"
        placeholder="อธิบายเหตุผลของคุณอย่างชัดเจน"></textarea>
</div>

<div>
    <label class="block font-semibold text-[#2f5d31] mb-1">สมาชิกในบ้านเห็นด้วยหรือไม่?</label>
    <div class="flex gap-6 text-gray-800">
        <label class="flex items-center gap-2">
            <input type="radio" name="family_agree" value="Yes" class="w-4 h-4"> เห็นด้วย
        </label>
        <label class="flex items-center gap-2">
            <input type="radio" name="family_agree" value="No" class="w-4 h-4"> ไม่เห็นด้วย
        </label>
    </div>
</div>

<div>
    <label class="block font-semibold text-[#2f5d31] mb-1">เวลาที่คุณสามารถให้สุนัขได้ต่อวัน</label>
    <input type="text" name="care_time"
        class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-500"
        placeholder="เช่น เช้า–เย็น, วันหยุดมีเวลาเพิ่ม">
</div>

<!-- Submit -->
<div class="flex justify-center">
    <button type="submit" name="save_form"
        class="bg-green-600 text-white px-14 py-3 rounded-xl font-bold shadow-lg hover:bg-green-700 transition">
        save
    </button>
</div>

</form>
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
