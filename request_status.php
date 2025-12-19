<?php
session_start();

// DB ฟอร์ม
$con_forms = new mysqli("localhost", "root", "", "pet_home");

// DB สุนัข
$con_dogs = new mysqli("localhost", "root", "", "pet_home");

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

// 3. โหลด avatar

$avatar_src = null;
$match = glob(__DIR__ . "/uploads/avatar_user_$user_id.*");
if (!empty($match)) {
    $avatar_src = "uploads/" . basename($match[0]);
}
// ===============================
// 4. Pagination (แสดง 5 รายการต่อหน้า)
// ===============================

// จำนวนที่ต้องการให้แสดงต่อหน้า
$limit = 5;

// หน้านี้คือหน้าอะไร เช่น page=2
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;

// ตำแหน่งเริ่มดึงข้อมูล
$start = ($page - 1) * $limit;

// นับจำนวนข้อมูลทั้งหมดของผู้ใช้คนนี้
$count_res = $con_forms->query("
    SELECT COUNT(*) AS total 
    FROM adopt_forms 
    WHERE user_id = $user_id
");
$total_rows = $count_res->fetch_assoc()['total'];

// คำนวณจำนวนหน้าทั้งหมด
$total_pages = ceil($total_rows / $limit);

// Query ดึงข้อมูลแบบแบ่งหน้า
$res_forms = $con_forms->query("
    SELECT * FROM adopt_forms 
    WHERE user_id=$user_id 
    ORDER BY id DESC
    LIMIT $start, $limit
");


?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Request Status - PawHome</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f7d7a3] min-h-screen">

<!-- BG (เบลเฉพาะพื้นหลัง) -->
    <div class="fixed inset-0 -z-10">
        <div class="absolute inset-0 bg-repeat"
            style="background-image:url('assets/images/bg_pethome_pattern.jpg'); background-size:300px;"></div>

        <!-- ถ้าอยากให้จางลง -->
        <div class="absolute inset-0 bg-white/70"></div>

        <!-- เบลเฉพาะพื้นหลัง -->
        <div class="absolute inset-0 backdrop-blur-sm"></div>
    </div>

    <!-- NAVBAR -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

<div class="pt-24 sm:pt-28 px-4 sm:px-6">
  <div class="text-center">
    <div class="bg-white/85 backdrop-blur rounded-3xl w-[92%] max-w-md mx-auto p-4 sm:p-6 shadow">
      <div class="text-3xl sm:text-4xl">📋</div>

      <p class="text-xl sm:text-3xl font-extrabold text-[#2f5d31]">
        สถานะคำขอรับเลี้ยง
      </p>
    </div>

    <p class="text-gray-700 mt-3 sm:mt-4 text-base sm:text-xl">
      ตรวจสอบรายการสุนัขที่คุณขอรับเลี้ยง
    </p>
  </div>
</div>


<!-- LIST -->
<div class="max-w-4xl mx-auto mt-8 sm:mt-10 px-4 sm:px-6 space-y-4 sm:space-y-6 pb-24">

<?php if ($res_forms->num_rows == 0): ?>

  <div class="text-center bg-white/80 backdrop-blur p-6 rounded-2xl shadow text-gray-700">
      ยังไม่มีรายการรับเลี้ยง
  </div>

<?php else:

  // โหลดสถานะจาก JSON (ทำครั้งเดียวพอ)
  $status_file = "admin/status.json";
  $status_list = [];
  if (file_exists($status_file)) {
      $status_list = json_decode(file_get_contents($status_file), true) ?: [];
  }
?>

  <?php while ($f = $res_forms->fetch_assoc()): ?>

    <?php
      $dog_id = (int)$f['dog_id'];
      $dog = $con_dogs->query("SELECT * FROM dogs WHERE id=$dog_id")->fetch_assoc();
      if (!$dog) continue;

      $form_id = (int)$f['id'];
      $status  = $status_list[$form_id] ?? "Pending";

      $badge_color = [
          "Pending"  => "bg-yellow-200 text-yellow-800",
          "Approved" => "bg-green-200 text-green-800",
          "Rejected" => "bg-red-200 text-red-800"
      ][$status] ?? "bg-gray-200 text-gray-800";

      $img = $dog['image'] ?? '';
      $img = ltrim($img, '/');
      $src = (str_starts_with($img, 'assets/')) ? $img : 'assets/' . $img;
    ?>

    <div class="bg-white shadow-md rounded-2xl p-4 sm:p-5 border border-[#e6d5b8] hover:shadow-xl transition duration-300
                flex flex-col sm:flex-row gap-4 sm:gap-5 w-full">

      <img src="<?= htmlspecialchars($src) ?>"
           alt="<?= htmlspecialchars($dog['name'] ?? 'dog') ?>"
           class="w-full sm:w-32 h-52 sm:h-32 rounded-2xl object-cover shadow-md border">

      <div class="flex flex-col justify-between w-full">
        <div>
          <p class="text-lg sm:text-2xl font-bold text-[#2f5d31] flex flex-wrap items-center gap-2 sm:gap-3">
            <?= htmlspecialchars($dog['name']) ?>
            <span class="px-3 py-1 text-xs sm:text-sm rounded-full <?= $badge_color ?>">
              <?= htmlspecialchars($status) ?>
            </span>
          </p>

          <p class="text-gray-600 text-sm mt-1">
            <?= htmlspecialchars($dog['gender']) ?> • อายุ <?= htmlspecialchars($dog['age']) ?>
          </p>
        </div>

        <div class="flex justify-between items-center mt-4">
          <a href="dog_details.php?id=<?= $dog_id ?>"
             class="text-green-700 font-semibold hover:text-green-900 transition">
            ➜ ดูรายละเอียดสุนัข
          </a>

          <a href="#"
             onclick="openDeletePopup(<?= $form_id ?>)"
             class="text-red-600 font-semibold hover:text-red-800 transition">
            🗑️ ลบ
          </a>
        </div>
      </div>
    </div>

  <?php endwhile; ?>

<?php endif; ?>

</div>


<!-- DELETE CONFIRM POPUP -->
<div id="deletePopup" 
     class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">

    <div class="bg-white w-[92vw] max-w-[380px] p-5 sm:p-6 rounded-2xl shadow-xl flex items-center gap-4 animate-pop">

        <!-- ICON LEFT -->
        <div class="text-5xl text-red-500">
            🗑️
        </div>

        <!-- TEXT -->
        <div class="flex-1">
            <h3 class="text-xl font-bold text-gray-800">ยืนยันการลบ?</h3>
            <p class="text-gray-600 text-sm mt-1">คุณต้องการลบรายการรับเลี้ยงนี้หรือไม่?</p>

            <div class="flex justify-end gap-3 mt-4">
                <button onclick="closeDeletePopup()"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                    ยกเลิก
                </button>

                <a id="confirmDeleteBtn"
                   class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    ลบ
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes pop {
    from { transform: scale(0.9); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
}
.animate-pop { animation: pop .25s ease-out; }
</style>

<script>
function openDeletePopup(id) {
    document.getElementById("confirmDeleteBtn").href = "?delete_id=" + id;
    document.getElementById("deletePopup").classList.remove("hidden");
}

function closeDeletePopup() {
    document.getElementById("deletePopup").classList.add("hidden");
}
</script>

</body>
</html>
