<?php
session_start();

// ===== INCLUDE DB ทีละตัว แล้วเก็บ connection =====

// register (form)
include(__DIR__ . "/../database/db.php");
$con_register = $con;

// ped_home (dogs)
include(__DIR__ . "/../database/db_ped.php");
$con_ped = $con;

// pethome_donations (donate)
include(__DIR__ . "/../database/db_donate.php");
$con_donate = $con;

// adopt_forms (ยังไม่ใช้ตอนนี้ แต่เตรียมไว้)
include(__DIR__ . "/../database/db_form.php");
$con_form = $con;
?>
<?php
// ---------- USERS (register.form) ----------
$user_sql = "SELECT COUNT(*) AS total FROM form";
$user_result = mysqli_query($con_register, $user_sql);
$total_users = mysqli_fetch_assoc($user_result)['total'];

// ---------- DOGS (ped_home.dogs) ----------
$dog_sql = "SELECT COUNT(*) AS total FROM dogs";
$dog_result = mysqli_query($con_ped, $dog_sql);
$total_dogs = mysqli_fetch_assoc($dog_result)['total'];

// ---------- DONATE ITEMS ----------
$item_sql = "SELECT COUNT(*) AS total FROM donate_items";
$item_result = mysqli_query($con_donate, $item_sql);
$total_items = mysqli_fetch_assoc($item_result)['total'];

// ---------- DONATE MONEY ----------
$money_sql = "SELECT COUNT(*) AS total FROM donate_bank";
$money_result = mysqli_query($con_donate, $money_sql);
$total_money = mysqli_fetch_assoc($money_result)['total'];

$new_request_sql = "SELECT COUNT(*) AS total FROM adopt_forms";
$new_request_result = mysqli_query($con_form, $new_request_sql);
$total_requests = mysqli_fetch_assoc($new_request_result)['total'];

// ---------- LATEST ADOPT REQUESTS ----------
$latest_sql = "
    SELECT id, user_id, dog_id, created_at
    FROM adopt_forms
    ORDER BY created_at DESC
    LIMIT 5
";
$latest_result = mysqli_query($con_form, $latest_sql);

?>


<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | Adopt Dog</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#fff7e6] min-h-screen flex">

<!-- SIDEBAR -->
<aside class="w-64 bg-white shadow-lg fixed h-full">
    <div class="p-6 text-2xl font-black text-orange-600">
        🐶 Adopt Dog
    </div>
    <nav class="px-4 space-y-3">
        <a href="adminindex.php" class="block p-3 rounded-xl bg-orange-100 font-semibold">🏠 Dashboard</a>
        <a href="admin_dogs.php" class="block p-3 rounded-xl hover:bg-orange-50">🐕 จัดการสุนัข</a>
        <a href="admin_requests.php" class="block p-3 rounded-xl hover:bg-orange-50">📄 คำขอรับเลี้ยง</a>
        <a href="admin_user.php" class="block p-3 rounded-xl hover:bg-orange-50">👤 ผู้ใช้งาน</a>
        <a href="admin_chat.php" class="block p-3 rounded-xl hover:bg-orange-50">💬 แชท</a>
        <a href="donations.php" class="block p-3 rounded-xl hover:bg-orange-50">💰 การบริจาค</a>
    </nav>
</aside>

<!-- MAIN -->
<main class="ml-64 p-10 w-full">

    <h1 class="text-3xl font-black mb-8">📊 Dashboard</h1>

    <!-- SUMMARY -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">

    <div class="bg-white p-6 rounded-2xl shadow">
        <p class="text-gray-500">สุนัขทั้งหมด</p>
        <h2 class="text-3xl font-bold">🐕 <?= $total_dogs ?></h2>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow">
        <p class="text-gray-500">คำขอใหม่</p>
<h2 class="text-3xl font-bold">📄 <?= $total_requests ?></h2> 
    </div>

    <div class="bg-white p-6 rounded-2xl shadow">
        <p class="text-gray-500">ผู้ใช้</p>
        <h2 class="text-3xl font-bold">👤 <?= $total_users ?></h2>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow">
        <p class="text-gray-500">บริจาค</p>
        <h2 class="text-3xl font-bold">💰 <?= $total_money ?></h2>
    </div>

</div>


    <!-- TABLE -->
    <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-xl font-bold mb-4">📄 คำขอรับเลี้ยงล่าสุด</h2>
        <table class="w-full text-left">
            <thead>
                <tr class="text-gray-500 border-b">
                    <th class="py-2">ผู้ขอ</th>
                    <th>สุนัข</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
      <tbody>
<?php if ($latest_result && mysqli_num_rows($latest_result) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($latest_result)): ?>

        <?php
        // ---------- USERNAME ----------
        $username = "-";
        $user_id = (int)$row['user_id'];

        $user_q = mysqli_query(
            $con_register,
            "SELECT username FROM form WHERE id = $user_id LIMIT 1"
        );

        if ($user_q && mysqli_num_rows($user_q) > 0) {
            $user = mysqli_fetch_assoc($user_q);
            $username = $user['username'] ?? "-";
        }

        // ---------- DOG NAME ----------
        $dog_name = "-";
        $dog_id = (int)$row['dog_id'];

        $dog_q = mysqli_query(
            $con_ped,
            "SELECT name FROM dogs WHERE id = $dog_id LIMIT 1"
        );

        if ($dog_q && mysqli_num_rows($dog_q) > 0) {
            $dog = mysqli_fetch_assoc($dog_q);
            $dog_name = $dog['name'];
        }
        ?>

        <tr class="border-b">
            <td class="py-2"><?= htmlspecialchars($username) ?></td>
            <td><?= htmlspecialchars($dog_name) ?></td>
            <td class="text-orange-600 font-semibold">รอดำเนินการ</td>
            <td>
                <a href="/project/dog_details.php?id=<?= $row['dog_id'] ?>"
   class="text-blue-600 hover:underline">
   ดูรายละเอียด
</a>

            </td>
        </tr>

    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="4" class="py-4 text-center text-gray-500">
            ยังไม่มีคำขอรับเลี้ยง
        </td>
    </tr>
<?php endif; ?>
</tbody>
    </div>

</main>
</body>
</html>
