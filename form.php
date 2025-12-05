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
<!-- ============= END NAVBAR ============= -->


<!-- FORM CONTENT -->
<div class="max-w-2xl mx-auto mt-32 mb-16 p-8 bg-white/70 backdrop-blur-lg shadow-xl rounded-2xl border border-[#f2d4a5]">

    <!-- HEADER -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-[#2f5d31]">แบบฟอร์มสอบถามเพื่อรับเลี้ยงสุนัข</h2>
        <p class="text-gray-700 mt-1">กรุณากรอกข้อมูลตามความจริงเพื่อให้เราประเมินความเหมาะสมของคุณ</p>
    </div>

    <form method="POST" action="save_form.php" class="space-y-6">

        <!-- ชื่อ -->
        <div>
            <label class="block font-semibold text-[#2f5d31] mb-1">ชื่อ-นามสกุล</label>
            <input type="text" name="fullname"
                class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-500 focus:outline-none"
                placeholder="กรอกชื่อ-นามสกุลของคุณ">
        </div>

        <!-- ที่อยู่ & เบอร์ติดต่อ -->
        <div>
            <label class="block font-semibold text-[#2f5d31] mb-1">ที่อยู่ / เบอร์ติดต่อ</label>
            <input type="text" name="contact"
                class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-500"
                placeholder="กรอกข้อมูลการติดต่อของคุณ">
        </div>

        <!-- พื้นที่เลี้ยงดู -->
        <div>
            <label class="block font-semibold text-[#2f5d31] mb-1">พื้นที่ในการเลี้ยงดู</label>
            <textarea name="area"
                class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-500"
                placeholder="เช่น บ้านพร้อมรั้ว / ห้องเช่ามีพื้นที่จำกัด"></textarea>
        </div>

        <!-- ประสบการณ์เลี้ยงสัตว์ -->
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

        <!-- ระยะเวลาอยู่บ้าน -->
        <div>
            <label class="block font-semibold text-[#2f5d31] mb-1">คุณอยู่บ้านเฉลี่ยกี่ชั่วโมงต่อวัน?</label>
            <input type="number" name="time_home"
                class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-500"
                placeholder="กรอกจำนวนชั่วโมง">
        </div>

        <!-- เหตุผลที่ต้องการเลี้ยงสุนัข -->
        <div>
            <label class="block font-semibold text-[#2f5d31] mb-1">เหตุผลที่ต้องการรับเลี้ยงสุนัข</label>
            <textarea name="reason"
                class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-500"
                placeholder="อธิบายเหตุผลของคุณอย่างชัดเจน"></textarea>
        </div>

        <!-- สภาพครอบครัว -->
        <div>
            <label class="block font-semibold text-[#2f5d31] mb-1">สมาชิกในบ้านเห็นด้วยกับการรับเลี้ยงหรือไม่?</label>
            <div class="flex gap-6 text-gray-800">
                <label class="flex items-center gap-2">
                    <input type="radio" name="family_agree" value="Yes" class="w-4 h-4"> เห็นด้วย
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="family_agree" value="No" class="w-4 h-4"> ไม่เห็นด้วย
                </label>
            </div>
        </div>

        <!-- เวลาในการดูแล -->
        <div>
            <label class="block font-semibold text-[#2f5d31] mb-1">เวลาที่คุณสามารถให้สุนัขได้ต่อวัน</label>
            <input type="text" name="care_time"
                class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-500"
                placeholder="เช่น เช้า–เย็น, วันหยุดมีเวลาเพิ่ม">
        </div>

        <!-- Submit -->
        <button
            class="w-full bg-green-600 text-white py-3 mt-5 rounded-full font-bold text-lg shadow hover:bg-green-700 transition">
            ส่งแบบฟอร์ม
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
