<?php 
session_start();
$con = new mysqli("localhost", "root", "", "pet_home");

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
    header("Location: donate_bank.php?success=1");
    exit;
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Donate Bank - PawHome</title>
<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="min-h-screen relative overflow-x-hidden">

<!-- BG (เบลเฉพาะพื้นหลัง) -->
<div class="fixed inset-0 -z-10">
  <div class="absolute inset-0 bg-repeat"
       style="background-image:url('assets/images/bg_pethome_pattern.jpg'); background-size:300px;"></div>
  <div class="absolute inset-0 bg-white/70"></div>
  <div class="absolute inset-0 backdrop-blur-sm"></div>
</div>

    <!-- NAVBAR -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

<!-- OFFSET -->
<div class="pt-28"></div>

<!-- HEADER -->
<div class="text-center px-4">
  <div class="w-36 h-36 sm:w-44 sm:h-44 mx-auto bg-white/90 rounded-3xl shadow-xl
              backdrop-blur-lg flex flex-col justify-center items-center border border-white/50">
    <div class="text-4xl sm:text-5xl">💰</div>
    <p class="text-xl sm:text-2xl font-extrabold text-[#2f5d31] mt-1 leading-tight">
      บริจาคผ่านบัญชีธนาคาร
    </p>
  </div>

  <p class="mt-4 sm:mt-6 text-gray-900 text-base sm:text-lg md:text-xl font-extrabold leading-relaxed text-center
            drop-shadow-[0_2px_2px_rgba(255,255,255,0.95)] max-w-xl mx-auto">
    เงินทุกบาทช่วยชีวิตสัตว์ไร้บ้านได้จริง  
    ขอบคุณที่ร่วมเป็นส่วนหนึ่งของเรา 🐶❤️
  </p>
</div>


<!-- BANK ACCOUNT CARD -->
<div class="w-full max-w-xl mx-auto mt-10 sm:mt-12 px-4">
  <div class="bg-white/80 backdrop-blur-lg shadow-xl rounded-2xl p-5 sm:p-8 border border-[#E6C89F]">
    <h3 class="text-lg sm:text-xl font-extrabold text-[#2f5d31] mb-4">บัญชีรับบริจาค</h3>

    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
      <img src="assets/images/scb.png" class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl shadow">

      <div class="text-left">
        <p class="text-base sm:text-lg font-bold text-gray-800">ธนาคารไทยพาณิชย์</p>
        <p class="text-sm sm:text-base text-gray-700">ชื่อบัญชี: มูลนิธิช่วยเหลือสัตว์ PawHome</p>
        <p class="text-lg sm:text-xl font-bold text-green-700 break-all" id="bankNumber">0-538-12387-0</p>
      </div>
    </div>

    <button onclick="copyBank()"
      class="mt-4 w-full sm:w-auto bg-green-600 text-white px-6 py-3 rounded-xl shadow hover:bg-green-700 transition font-bold">
      คัดลอกเลขบัญชี
    </button>
  </div>
</div>


<!-- UPLOAD SLIP -->
<div class="w-full max-w-xl mx-auto mt-6 sm:mt-10 mb-20 px-4">
  <div class="bg-white/80 backdrop-blur-lg shadow-xl rounded-2xl p-5 sm:p-8 border border-[#E6C89F]">
    <h3 class="text-lg sm:text-xl font-extrabold text-[#2f5d31] mb-4">อัปโหลดสลิปการโอนเงิน</h3>

    <form id="bankDonateForm" method="POST" enctype="multipart/form-data" class="space-y-4">
      <input type="file" name="slip"
        class="w-full p-3 sm:p-4 bg-[#FAEED1] border rounded-xl text-sm sm:text-base">

      <input type="text" name="donor_name"
        class="w-full p-3 sm:p-4 bg-[#FAEED1] border rounded-xl text-sm sm:text-base"
        placeholder="ชื่อผู้บริจาค">

      <button type="submit" name="donate_bank_submit"
        class="bg-green-600 text-white w-full py-3 sm:py-4 rounded-xl font-bold shadow hover:bg-green-700 transition text-base sm:text-lg">
        ส่งข้อมูล
      </button>
    </form>
  </div>
</div>


<!-- SUCCESS POPUP -->
<div id="successPopup" class="fixed inset-0 hidden bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white w-[92%] max-w-md p-5 sm:p-6 rounded-2xl shadow-xl flex items-center gap-5 animate-pop">
        <div class="text-5xl text-green-500 mb-4">✅</div>
        <h3 class="text-xl font-bold mb-2">ส่งข้อมูลสำเร็จ!</h3>
        <p class="text-gray-700 mb-4">เราได้รับข้อมูลการบริจาคของคุณแล้ว</p>

        <button onclick="closePopup()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            ปิด
        </button>
    </div>
</div>
<!-- COPY SUCCESS POPUP -->
<div id="copyPopup" 
     class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-50">

    <div class="bg-white w-96 p-6 rounded-2xl shadow-xl flex items-center gap-5 animate-pop">
        
        <!-- ICON LEFT -->
        <div class="text-3xl text-green-500">
            ✅
        </div>

        <!-- TEXT RIGHT -->
        <div>
            <h3 class="text-xl font-bold text-gray-800">คัดลอกสำเร็จ!</h3>
            <p class="text-gray-600 mt-1">เลขบัญชีถูกคัดลอกแล้ว ✔️</p>

            <button onclick="closeCopyPopup()"
                class="mt-4 bg-green-600 text-white px-5 py-2 rounded-xl hover:bg-green-700 transition">
                ปิด
            </button>
        </div>

    </div>
</div>

<style>
@keyframes pop {
    from { transform: scale(0.8); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
}
.animate-pop { animation: pop .25s ease-out; }
</style>



<script>
function copyBank() {
    const text = document.getElementById("bankNumber").innerText;
    navigator.clipboard.writeText(text);

    // แสดง Popup
    document.getElementById("copyPopup").classList.remove("hidden");
}
function closeCopyPopup() {
    document.getElementById("copyPopup").classList.add("hidden");
}
</script>

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
