<?php 
session_start();
$con = new mysqli("localhost", "root", "", "pethome_donate");
if ($con->connect_errno) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $con->connect_error);
}

$success = false;

/* ---------------------
   บันทึกข้อมูลบริจาค
---------------------- */
if (isset($_POST['donate_submit'])) {

    $fullname = $con->real_escape_string($_POST['fullname']);
    $contact  = $con->real_escape_string($_POST['contact']);
    $items    = $con->real_escape_string($_POST['items']);
    $send     = $con->real_escape_string($_POST['send_type']);

    $sql = "INSERT INTO donate_items(fullname, contact, items, send_type)
            VALUES('$fullname', '$contact', '$items', '$send')";

    if ($con->query($sql)) {
        $success = true;
    }
}

/* ---------------------
   อัปโหลด Avatar
---------------------- */
$upload_error = '';

if (isset($_FILES['avatar']) && isset($_SESSION['user_id'])) {

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
           header("Location: ".$_SERVER['PHP_SELF']);
exit;

        }

    } else {
        $upload_error = "ไฟล์ต้องเป็นภาพ (jpg, png, gif)";
    }
}

/* ---------------------
   โหลด Avatar
---------------------- */
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
<title>Donate Items - PawHome</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
    body { font-family: 'Prompt', sans-serif; }
</style>
</head>
<body class="bg-[#f7d7a3] min-h-screen">

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

<body class="min-h-screen bg-[#F7E7C1] relative">

<!-- NAVBAR -->
<nav class="w-full fixed top-0 left-0 bg-white/40 backdrop-blur-md shadow-sm py-4 z-20">
    <div class="flex items-center px-6">

        <h1 class="flex items-center gap-3 text-2xl font-extrabold text-[#2f5d31]">
            <div class="bg-white rounded-full shadow-md p-1 px-2">🐾</div>
            PawHome
        </h1>

        <ul class="flex items-center space-x-6 text-gray-900 ml-auto font-medium">
            <li><a href="homeped.php" class="hover:text-green-700">HOME</a></li>
            <li><a href="form.php" class="hover:text-green-700">FORM</a></li>
            <li><a href="donate.php" class="text-green-700 font-bold">DONATE</a></li>
            <li><a href="request_status.php" class="hover:text-green-700">REQUEST STATUS</a></li>

            <?php if(isset($_SESSION['user_id'])): ?>
            <li class="relative">
                <button id="profileBtn"
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
                    <a href="setting.php" class="block py-1 font-medium hover:text-red-600 transition">⚙️ ตั้งค่า</a>
                    <a href="profile.php" class="block py-1 font-medium hover:text-red-600 transition">👤 โปรไฟล์</a>
                    <a href="about_us.php" class="block py-1 font-medium hover:text-red-600 transition">ℹ️ About Us</a>

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

<!-- HEADER -->
<div class="text-center">
    <div class="w-40 h-40 mx-auto bg-white/90 rounded-3xl shadow-xl backdrop-blur-lg flex flex-col justify-center items-center border border-white/50">
        <div class="text-5xl">🎁</div>
        <p class="text-2xl font-extrabold text-[#2f5d31] mt-1">ส่งของบริจาค</p>
    </div>

    <p class="mt-6 text-gray-700 text-lg font-medium">
        บริจาคสิ่งของเพื่อช่วยดูแลสัตว์ไร้บ้าน  
        ทุกชิ้นมีคุณค่าอย่างยิ่ง 🐶❤️
    </p>
</div>

<!-- ITEMS SUGGESTION -->
<div class="max-w-4xl mx-auto mt-12 grid grid-cols-2 md:grid-cols-4 gap-6">

    <div class="bg-white/80 p-5 rounded-2xl shadow-lg text-center hover:scale-105 transition">
        <div class="text-4xl">🥫</div>
        <p class="mt-2 font-semibold text-gray-700">อาหารสุนัข</p>
    </div>

    <div class="bg-white/80 p-5 rounded-2xl shadow-lg text-center hover:scale-105 transition">
        <div class="text-4xl">🧸</div>
        <p class="mt-2 font-semibold text-gray-700">ของเล่น</p>
    </div>

    <div class="bg-white/80 p-5 rounded-2xl shadow-lg text-center hover:scale-105 transition">
        <div class="text-4xl">🛏️</div>
        <p class="mt-2 font-semibold text-gray-700">ผ้าห่ม / ที่นอน</p>
    </div>

    <div class="bg-white/80 p-5 rounded-2xl shadow-lg text-center hover:scale-105 transition">
        <div class="text-4xl">🍼</div>
        <p class="mt-2 font-semibold text-gray-700">นม / อาหารเสริม</p>
    </div>

</div>

<!-- FORM SECTION -->
<div class="max-w-3xl mx-auto mt-16 mb-20 p-8 bg-white/80 backdrop-blur-lg shadow-xl rounded-2xl border border-[#E6C89F]">

    <h3 class="text-2xl font-extrabold text-[#2f5d31] text-center mb-6">กรอกข้อมูลการส่งของบริจาค</h3>

<form id="donateForm" method="POST" class="space-y-6">

        <div>
            <label class="block font-semibold text-[#2f5d31] mb-1">ชื่อ-นามสกุล</label>
            <input type="text" name="fullname"
                class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-600">
        </div>

        <div>
            <label class="block font-semibold text-[#2f5d31] mb-1">เบอร์ติดต่อ</label>
            <input type="text" name="contact"
                class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-600">
        </div>

        <div>
            <label class="block font-semibold text-[#2f5d31] mb-1">รายการของที่ต้องการส่ง</label>
            <textarea name="items"
                class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-600"
                placeholder="เช่น อาหารสุนัข 2 กระสอบ, ผ้าห่ม 3 ผืน"></textarea>
        </div>

        <div>
            <label class="block font-semibold text-[#2f5d31] mb-1">วิธีจัดส่ง</label>
            <select name="send_type"
                class="w-full p-3 rounded-xl border bg-[#FAEED1] focus:ring-2 focus:ring-green-600">
                <option value="">เลือกวิธีจัดส่ง</option>
                <option>ส่งทางไปรษณีย์</option>
                <option>จัดส่งเองที่ศูนย์</option>
                <option>ส่งผ่าน Grab</option>
            </select>
        </div>

        <div>
            <label class="block font-semibold text-[#2f5d31] mb-1">ที่อยู่ศูนย์รับบริจาค</label>
            <input type="text" readonly
                value="ศูนย์ช่วยเหลือสัตว์ PawHome - ถนนสุขใจ กรุงเทพฯ"
                class="w-full p-3 rounded-xl border bg-gray-100 font-semibold">
        </div>

        <div class="flex justify-center">
            <button type="submit" name="donate_submit"
        class="bg-green-600 text-white px-14 py-3 rounded-xl font-bold shadow-lg hover:bg-green-700 transition">
    ส่งข้อมูล
</button>

        </div>

    </form>
</div>
<!-- SUCCESS POPUP -->
<div id="successPopup" class="fixed inset-0 hidden bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white w-80 p-8 rounded-xl text-center shadow-xl">
        <div class="text-5xl text-green-500 mb-4">✅</div>
        <h3 class="text-xl font-bold mb-2">ส่งข้อมูลสำเร็จ!</h3>
        <p class="text-gray-700 mb-4">เราได้รับข้อมูลการบริจาคของคุณแล้ว</p>

        <button onclick="closePopup()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            ปิด
        </button>
    </div>
</div>

<!-- BOTTOM DOG IMAGE -->
<div class="flex justify-center pb-10">
    <img src="familydogs.png" class="w-52 drop-shadow-lg">
</div>
<script>
// เปิด Popup
function showPopup() {
    document.getElementById("successPopup").classList.remove("hidden");
}

// ปิด Popup
function closePopup() {
    document.getElementById("successPopup").classList.add("hidden");
}

</script>
<?php if ($success): ?>
<script>
showPopup();
</script>
<?php endif; ?>

</body>
</html>
