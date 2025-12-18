<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);

/* ===== CONNECT DB ===== */
$con = new mysqli("localhost", "root", "", "pethome_donate");
if ($con->connect_error) {
    die("DB Error");
}

/* ===== LOAD DATA ===== */
$bank = $con->query("SELECT * FROM donate_bank ORDER BY created_at DESC");
$items = $con->query("SELECT * FROM donate_items ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>การบริจาค | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#fff7e6] min-h-screen">

<!-- SIDEBAR -->
<aside class="w-64 bg-white shadow-lg fixed h-full">
    <div class="p-6 text-2xl font-black text-orange-600">🐶 Adopt Dog</div>

    <nav class="px-4 space-y-2">
        <a href="adminindex.php"
           class="block p-3 rounded-xl font-semibold
           <?= $currentPage=='adminindex.php' ? 'bg-orange-100 text-orange-600' : 'hover:bg-orange-50 text-gray-700' ?>">
           🏠 Dashboard
        </a>

        <a href="admin_dogs.php"
           class="block p-3 rounded-xl font-semibold hover:bg-orange-50 text-gray-700">
           🐕 จัดการสุนัข
        </a>

        <a href="admin_requests.php"
           class="block p-3 rounded-xl font-semibold hover:bg-orange-50 text-gray-700">
           📄 คำขอรับเลี้ยง
        </a>

        <a href="admin_users.php"
           class="block p-3 rounded-xl font-semibold hover:bg-orange-50 text-gray-700">
           👤 ผู้ใช้งาน
        </a>

        <a href="chat.php"
           class="block p-3 rounded-xl font-semibold hover:bg-orange-50 text-gray-700">
           💬 แชท
        </a>

        <a href="admin_donations.php"
           class="block p-3 rounded-xl font-semibold
           <?= $currentPage=='admin_donations.php' ? 'bg-orange-100 text-orange-600' : 'hover:bg-orange-50 text-gray-700' ?>">
           💰 การบริจาค
        </a>
    </nav>
</aside>

<!-- CONTENT -->
<main class="ml-64 p-10 w-[calc(100vw-16rem)] space-y-10">

<h1 class="text-3xl font-black">💰 การบริจาค</h1>

<!-- ================= DONATE MONEY ================= -->
<section>
<h2 class="text-xl font-bold mb-4">🏦 บริจาคเงิน</h2>

<div class="bg-white rounded-2xl shadow overflow-x-auto">
<table class="w-full min-w-[900px] text-sm text-left">
<thead class="bg-orange-100">
<tr>
    <th class="p-4">ID</th>
    <th>ชื่อผู้บริจาค</th>
    <th>สลิป</th>
    <th>วันที่</th>
</tr>
</thead>

<tbody class="divide-y">
<?php while ($b = $bank->fetch_assoc()): ?>
<tr class="hover:bg-orange-50">
    <td class="p-4 font-semibold"><?= $b['id'] ?></td>
    <td><?= htmlspecialchars($b['donor_name']) ?></td>
    <td>
        <a href="../uploads/donate/<?= htmlspecialchars($b['slip_path']) ?>"
           target="_blank"
           class="text-blue-600 hover:underline">
           ดูสลิป
        </a>
    </td>
    <td><?= $b['created_at'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</section>

<!-- ================= DONATE ITEMS ================= -->
<section>
<h2 class="text-xl font-bold mb-4">📦 บริจาคสิ่งของ</h2>

<div class="bg-white rounded-2xl shadow overflow-x-auto">
<table class="w-full min-w-[1100px] text-sm text-left">
<thead class="bg-orange-100">
<tr>
    <th class="p-4">ID</th>
    <th>ชื่อ</th>
    <th>เบอร์</th>
    <th>สิ่งของ</th>
    <th>วิธีจัดส่ง</th>
    <th>วันที่</th>
</tr>
</thead>

<tbody class="divide-y">
<?php while ($i = $items->fetch_assoc()): ?>
<tr class="hover:bg-orange-50">
    <td class="p-4 font-semibold"><?= $i['id'] ?></td>
    <td><?= htmlspecialchars($i['fullname']) ?></td>
    <td><?= htmlspecialchars($i['contact']) ?></td>
    <td><?= htmlspecialchars($i['items']) ?></td>
    <td>
        <span class="px-3 py-1 rounded text-xs
        <?= $i['send_type']=='จัดส่งเองที่ศูนย์' ? 'bg-green-200' : 'bg-blue-200' ?>">
        <?= htmlspecialchars($i['send_type']) ?>
        </span>
    </td>
    <td><?= $i['created_at'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</section>

</main>
</body>
</html>
