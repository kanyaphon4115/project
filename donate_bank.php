<?php 
session_start();
$con = new mysqli("localhost", "root", "", "pethome_donate");

$bank_success = false;

if (isset($_POST['donate_bank_submit']) && isset($_FILES['slip'])) {

    $donor = $con->real_escape_string($_POST['donor_name']);
    $file = $_FILES['slip'];

    if ($file['error'] === UPLOAD_ERR_OK) {

        $uploads = __DIR__ . "/slips";
        if (!is_dir($uploads)) mkdir($uploads, 0777, true);

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newname = time() . "_" . rand(1000,9999) . "." . $ext;
        $path = $uploads . "/" . $newname;

        if (move_uploaded_file($file['tmp_name'], $path)) {

            $sql = "INSERT INTO donate_bank(donor_name, slip_path)
                    VALUES('$donor', '$newname')";

            if ($con->query($sql)) {
                $bank_success = true;
            }
        }
    }
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

// โหลดรูปโปรไฟล์
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
<title>Donate Bank - PawHome</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
    body { font-family: 'Prompt', sans-serif; }
</style>
</head>

<body class="bg-[#f7d7a3] min-h-screen">

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

<a href="setting.php" class="block py-1 font-medium hover:text-red-600 transition">
    ⚙️ ตั้งค่า
</a>

<a href="profile.php" class="block py-1 font-medium hover:text-red-600 transition">
    👤 โปรไฟล์
</a>

<a href="about_us.php" class="block py-1 font-medium hover:text-red-600 transition">
    ℹ️ About Us
</a>

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
        <div class="text-5xl">💰</div>
        <p class="text-2xl font-extrabold text-[#2f5d31] mt-1">บริจาคผ่านบัญชีธนาคาร</p>
    </div>

    <p class="mt-6 text-gray-700 text-lg font-medium">
        เงินทุกบาทช่วยชีวิตสัตว์ไร้บ้านได้จริง  
        ขอบคุณที่ร่วมเป็นส่วนหนึ่งของเรา 🐶❤️
    </p>
</div>

<!-- BANK ACCOUNT CARD -->
<div class="max-w-xl mx-auto mt-12 bg-white/80 backdrop-blur-lg shadow-xl rounded-2xl p-8 border border-[#E6C89F]">

    <h3 class="text-xl font-extrabold text-[#2f5d31] mb-4">บัญชีรับบริจาค</h3>

    <div class="flex items-center gap-4">
        <img src="bank.png" class="w-16 h-16 rounded-xl shadow">
        <div>
            <p class="text-lg font-bold text-gray-800">ธนาคารไทยพาณิชย์</p>
            <p class="text-gray-700">ชื่อบัญชี: มูลนิธิช่วยเหลือสัตว์ PawHome</p>
            <p class="text-xl font-bold text-green-700" id="bankNumber">0-538-12387-0</p>
        </div>
    </div>

    <button onclick="copyBank()"
        class="mt-4 bg-green-600 text-white px-6 py-2 rounded-xl shadow hover:bg-green-700 transition">
        คัดลอกเลขบัญชี
    </button>

</div>

<!-- UPLOAD SLIP -->
<div class="max-w-xl mx-auto mt-10 mb-20 bg-white/80 backdrop-blur-lg shadow-xl rounded-2xl p-8 border border-[#E6C89F]">

    <h3 class="text-xl font-extrabold text-[#2f5d31] mb-4">อัปโหลดสลิปการโอนเงิน</h3>

    <form id="bankDonateForm" method="POST" enctype="multipart/form-data" class="space-y-4">

        <input type="file" name="slip"
            class="w-full p-3 bg-[#FAEED1] border rounded-xl">

        <input type="text" name="donor_name"
            class="w-full p-3 bg-[#FAEED1] border rounded-xl"
            placeholder="ชื่อผู้บริจาค">

      <button type="submit" name="donate_bank_submit"
    class="bg-green-600 text-white w-full py-3 rounded-xl font-bold shadow hover:bg-green-700 transition">
    ส่งข้อมูล
</button>

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



<script>
function copyBank() {
    const text = document.getElementById("bankNumber").innerText;
    navigator.clipboard.writeText(text);
    alert("คัดลอกเลขบัญชีแล้ว!");
}
</script>

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
<?php if ($bank_success): ?>
<script> showPopup(); </script>
<?php endif; ?>

</body>
</html>
