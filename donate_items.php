<?php 
session_start();
$con = new mysqli("localhost", "root", "", "pet_home");
if ($con->connect_errno) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $con->connect_error);
}

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donate_submit'])) {

    $fullname = $con->real_escape_string($_POST['fullname']);
    $contact  = $con->real_escape_string($_POST['contact']);
    $items    = $con->real_escape_string($_POST['items']);
    $send     = $con->real_escape_string($_POST['send_type']);

    $sql = "INSERT INTO donate_items(fullname, contact, items, send_type)
            VALUES('$fullname', '$contact', '$items', '$send')";

    if ($con->query($sql)) {
        $_SESSION['donate_items_success'] = 1;   // ✅ เก็บไว้ชั่วคราว
        header("Location: donate_items.php");    // ✅ ไม่มี success=1 ค้างใน URL
        exit;
    }
}

// ✅ โชว์ได้ครั้งเดียว แล้วลบทิ้ง
$success = !empty($_SESSION['donate_items_success']);
unset($_SESSION['donate_items_success']);



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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Donate Items - PawHome</title>
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
<div class="text-center px-4 sm:px-6">
  <div class="w-40 h-40 sm:w-48 sm:h-48 mx-auto bg-white/90 rounded-3xl shadow-xl backdrop-blur-lg
              flex flex-col justify-center items-center border border-white/50">
      <div class="text-5xl sm:text-6xl">🎁</div>
      <p class="text-xl sm:text-2xl font-extrabold text-[#2f5d31] mt-2">ส่งของบริจาค</p>
  </div>

  <p class="mt-5 sm:mt-6 text-gray-800 text-base sm:text-lg md:text-xl font-semibold leading-relaxed max-w-2xl mx-auto">
      บริจาคสิ่งของเพื่อช่วยดูแลสัตว์ไร้บ้าน  
      ทุกชิ้นมีคุณค่าอย่างยิ่ง 🐶❤️
  </p>
</div>


<!-- ITEMS SUGGESTION -->
<div class="max-w-4xl mx-auto mt-10 sm:mt-12 px-4 sm:px-6 grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6">

  <div class="bg-white/80 p-4 sm:p-5 rounded-2xl shadow-lg text-center hover:scale-105 transition">
      <div class="text-4xl sm:text-5xl">🥫</div>
      <p class="mt-2 font-semibold text-gray-700 text-sm sm:text-base">อาหารสุนัข</p>
  </div>

  <div class="bg-white/80 p-4 sm:p-5 rounded-2xl shadow-lg text-center hover:scale-105 transition">
      <div class="text-4xl sm:text-5xl">🧸</div>
      <p class="mt-2 font-semibold text-gray-700 text-sm sm:text-base">ของเล่น</p>
  </div>

  <div class="bg-white/80 p-4 sm:p-5 rounded-2xl shadow-lg text-center hover:scale-105 transition">
      <div class="text-4xl sm:text-5xl">🛏️</div>
      <p class="mt-2 font-semibold text-gray-700 text-sm sm:text-base">ผ้าห่ม / ที่นอน</p>
  </div>

  <div class="bg-white/80 p-4 sm:p-5 rounded-2xl shadow-lg text-center hover:scale-105 transition">
      <div class="text-4xl sm:text-5xl">📦</div>
      <p class="mt-2 font-semibold text-gray-700 text-sm sm:text-base">ของอำนวยความสะดวก</p>
  </div>

</div>

<!-- FORM SECTION -->
<div class="max-w-3xl mx-auto mt-12 sm:mt-16 mb-20 px-4 sm:px-6">
  <div class="p-5 sm:p-8 bg-white/80 backdrop-blur-lg shadow-xl rounded-2xl border border-[#E6C89F]">

    <h3 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-[#2f5d31] text-center mb-5 sm:mb-6">
      กรอกข้อมูลการส่งของบริจาค
    </h3>

    <form id="donateForm" method="POST" class="space-y-5 sm:space-y-6">

      <div>
        <label class="block font-semibold text-[#2f5d31] mb-1 text-base sm:text-lg">ชื่อ-นามสกุล</label>
        <input type="text" name="fullname"
          class="w-full p-3 sm:p-4 rounded-xl border bg-[#FAEED1] text-base sm:text-lg focus:ring-2 focus:ring-green-600">
      </div>

      <div>
        <label class="block font-semibold text-[#2f5d31] mb-1 text-base sm:text-lg">เบอร์ติดต่อ</label>
        <input type="text" name="contact"
          class="w-full p-3 sm:p-4 rounded-xl border bg-[#FAEED1] text-base sm:text-lg focus:ring-2 focus:ring-green-600">
      </div>

      <div>
        <label class="block font-semibold text-[#2f5d31] mb-1 text-base sm:text-lg">รายการของที่ต้องการส่ง</label>
        <textarea name="items"
          class="w-full p-3 sm:p-4 rounded-xl border bg-[#FAEED1] text-base sm:text-lg focus:ring-2 focus:ring-green-600"
          rows="4"
          placeholder="เช่น อาหารสุนัข 2 กระสอบ, ผ้าห่ม 3 ผืน"></textarea>
      </div>

      <div>
        <label class="block font-semibold text-[#2f5d31] mb-1 text-base sm:text-lg">วิธีจัดส่ง</label>
        <select name="send_type"
          class="w-full p-3 sm:p-4 rounded-xl border bg-[#FAEED1] text-base sm:text-lg focus:ring-2 focus:ring-green-600">
          <option value="">เลือกวิธีจัดส่ง</option>
          <option>ส่งทางไปรษณีย์</option>
          <option>จัดส่งเองที่ศูนย์</option>
          <option>ส่งผ่าน Grab</option>
        </select>
      </div>

      <div>
        <label class="block font-semibold text-[#2f5d31] mb-1 text-base sm:text-lg">ที่อยู่ศูนย์รับบริจาค</label>
        <input type="text" readonly
          value="ศูนย์ช่วยเหลือสัตว์ PawHome - ถนนสุขใจ กรุงเทพฯ"
          class="w-full p-3 sm:p-4 rounded-xl border bg-gray-100 font-semibold text-base sm:text-lg">
      </div>

      <div class="flex justify-center">
        <button type="submit" name="donate_submit"
          class="bg-green-600 text-white w-full sm:w-auto px-8 sm:px-14 py-3 sm:py-4 rounded-xl font-bold text-base sm:text-lg shadow-lg hover:bg-green-700 transition">
          ส่งข้อมูล
        </button>
      </div>

    </form>
  </div>
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
