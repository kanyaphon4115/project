<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);

/* ===== CONNECT DB ===== */
$con = new mysqli("localhost", "root", "", "pet_home");
if ($con->connect_error) {
    die("DB Error");
}

/* ===== PAGINATION ===== */
$limit = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

/* ===== COUNT USERS ===== */
$count = $con->query("SELECT COUNT(*) AS total FROM form")->fetch_assoc()['total'];
$total_pages = ceil($count / $limit);

/* ===== LOAD USERS ===== */
$sql = "SELECT * FROM form
        ORDER BY id DESC
        LIMIT $limit OFFSET $offset";
$result = $con->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ผู้ใช้งาน | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#fff7e6] min-h-screen">

<!-- SIDEBAR -->
<aside class="w-64 bg-white shadow-lg fixed h-full">
    <div class="p-6 text-2xl font-black text-orange-600">🐶 Adopt Dog</div>

    <nav class="px-4 space-y-2">
        <?php
        function menu($file, $icon, $label, $currentPage) {
            $active = $currentPage == $file
                ? 'bg-orange-100 text-orange-600'
                : 'hover:bg-orange-50 text-gray-700';
            echo "<a href='$file' class='block p-3 rounded-xl font-semibold $active'>$icon $label</a>";
        }

        menu('adminindex.php','🏠','Dashboard',$currentPage);
        menu('admin_dogs.php','🐕','จัดการสุนัข',$currentPage);
        menu('admin_requests.php','📄','คำขอรับเลี้ยง',$currentPage);
        menu('admin_user.php','👤','ผู้ใช้งาน',$currentPage);
        menu('admin_chat.php','💬','แชท',$currentPage);
        menu('admin_donations.php','💰','การบริจาค',$currentPage);
        ?>
    </nav>
</aside>

<!-- CONTENT -->
<main class="ml-64 p-10 w-[calc(100vw-16rem)]">
<h1 class="text-3xl font-black mb-6">👤 ผู้ใช้งานในระบบ</h1>

<div class="bg-white rounded-2xl shadow overflow-x-auto">
<table class="w-full min-w-[1200px] text-sm text-left">
<thead class="bg-orange-100">
<tr>
    <th class="p-4">ID</th>
    <th>Email</th>
    <th>Username</th>
    <th>เบอร์โทร</th>
    <th>ที่อยู่</th>
    <th>ประวัติ</th>
    <th>วันเกิด</th>
</tr>
</thead>

<tbody class="divide-y">
<?php while ($u = $result->fetch_assoc()): ?>
<tr class="hover:bg-orange-50">
<td class="p-4 font-semibold"><?= $u['id'] ?></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td><?= htmlspecialchars($u['username'] ?? '-') ?></td>
<td><?= htmlspecialchars($u['contact'] ?? '-') ?></td>
<td><?= htmlspecialchars($u['address'] ?? '-') ?></td>
<td class="max-w-xs break-words"><?= htmlspecialchars($u['bio'] ?? '-') ?></td>
<td><?= $u['birthdate'] ?? '-' ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<!-- PAGINATION -->
<div class="flex justify-center mt-6 space-x-2">
<?php if ($page > 1): ?>
<a href="?page=<?= $page-1 ?>" class="px-3 py-1 bg-white border rounded">Previous</a>
<?php endif; ?>

<?php
$show = 3;
$start = max(1, $page - 1);
$end = min($total_pages, $start + $show - 1);
for ($i=$start; $i<=$end; $i++):
?>
<a href="?page=<?= $i ?>"
 class="px-3 py-1 rounded <?= $page==$i ? 'bg-blue-500 text-white' : 'bg-white border' ?>">
 <?= $i ?>
</a>
<?php endfor; ?>

<?php if ($page < $total_pages): ?>
<a href="?page=<?= $page+1 ?>" class="px-3 py-1 bg-white border rounded">Next</a>
<?php endif; ?>
</div>

</main>
</body>
</html>
