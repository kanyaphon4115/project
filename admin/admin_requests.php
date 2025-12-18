<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);


/* ===== CONNECT DB ===== */
$con = new mysqli("localhost", "root", "", "adopt_forms");

/* ===== LOAD STATUS JSON ===== */
$status_file = __DIR__ . "/status.json";
$status_list = file_exists($status_file)
    ? json_decode(file_get_contents($status_file), true)
    : [];

/* ===== UPDATE STATUS ===== */
if (isset($_GET['update_status'])) {
    $id = $_GET['id'];
    $new_status = $_GET['update_status'];

    $status_list[$id] = $new_status;
    file_put_contents(
        $status_file,
        json_encode($status_list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    header("Location: admin_requests.php");
    exit;
}

/* ===== PAGINATION ===== */
$limit = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$count = $con->query("SELECT COUNT(*) AS total FROM adopt_forms")->fetch_assoc()['total'];
$total_pages = ceil($count / $limit);

/* ===== LOAD REQUESTS ===== */
$sql = "SELECT * FROM adopt_forms
        ORDER BY created_at DESC
        LIMIT $limit OFFSET $offset";
$result = $con->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>คำขอรับเลี้ยง | Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#fff7e6] min-h-screen">
<aside class="w-64 bg-white shadow-lg fixed h-full">
    <div class="p-6 text-2xl font-black text-orange-600">
        🐶 Adopt Dog
    </div>

    <nav class="px-4 space-y-2">

        <a href="adminindex.php"
           class="block p-3 rounded-xl font-semibold
           <?= $currentPage == 'adminindex.php'
              ? 'bg-orange-100 text-orange-600'
              : 'hover:bg-orange-50 text-gray-700' ?>">
           🏠 Dashboard
        </a>

        <a href="admin_dogs.php"
           class="block p-3 rounded-xl font-semibold
           <?= $currentPage == 'admin_dogs.php'
              ? 'bg-orange-100 text-orange-600'
              : 'hover:bg-orange-50 text-gray-700' ?>">
           🐕 จัดการสุนัข
        </a>

        <a href="admin_requests.php"
   class="block p-3 rounded-xl font-semibold
   <?= $currentPage == 'admin_requests.php'
      ? 'bg-orange-100 text-orange-600'
      : 'hover:bg-orange-50 text-gray-700' ?>">
   📄 คำขอรับเลี้ยง
</a>


        <a href="users.php"
           class="block p-3 rounded-xl font-semibold
           <?= $currentPage == 'users.php'
              ? 'bg-orange-100 text-orange-600'
              : 'hover:bg-orange-50 text-gray-700' ?>">
           👤 ผู้ใช้งาน
        </a>

        <a href="chat.php"
           class="block p-3 rounded-xl font-semibold
           <?= $currentPage == 'chat.php'
              ? 'bg-orange-100 text-orange-600'
              : 'hover:bg-orange-50 text-gray-700' ?>">
           💬 แชท
        </a>

        <a href="donations.php"
           class="block p-3 rounded-xl font-semibold
           <?= $currentPage == 'donations.php'
              ? 'bg-orange-100 text-orange-600'
              : 'hover:bg-orange-50 text-gray-700' ?>">
           💰 การบริจาค
        </a>

    </nav>
</aside>
</aside>

<main class="ml-64 flex flex-col min-h-screen p-10">

<h1 class="text-3xl font-black mb-6">📄 คำขอรับเลี้ยงสุนัข</h1>

<div class="bg-white rounded-2xl shadow overflow-hidden">
<table class="w-full text-left text-sm">
<thead class="bg-orange-100">
<tr>
  <th class="p-4">ผู้ขอ</th>
  <th>จังหวัด</th>
  <th>พื้นที่</th>
  <th>ประสบการณ์</th>
  <th>ครอบครัวโอเค</th>
  <th>เวลาอยู่บ้าน</th>
  <th>เหตุผล</th>
  <th>สถานะ</th>
  <th>จัดการ</th>
</tr>
</thead>

<tbody class="divide-y">
<?php while ($row = $result->fetch_assoc()):
    $id = $row['id'];
    $status = $status_list[$id] ?? "Pending";
?>
<tr class="hover:bg-orange-50">

<td class="p-4 font-semibold"><?= htmlspecialchars($row['fullname']) ?></td>
<td><?= htmlspecialchars($row['contact']) ?></td>
<td><?= htmlspecialchars($row['area']) ?></td>
<td><?= htmlspecialchars($row['experience']) ?></td>

<td class="<?= $row['family_agree']=='Yes'
        ? 'text-green-600 font-semibold'
        : 'text-red-600 font-semibold' ?>">
    <?= htmlspecialchars($row['family_agree']) ?>
</td>

<td><?= htmlspecialchars($row['time_home']) ?> ชม.</td>

<td class="max-w-xs truncate"><?= htmlspecialchars($row['reason']) ?></td>

<td>
<span class="px-3 py-1 rounded text-xs
<?= $status=='Approved' ? 'bg-green-300' : '' ?>
<?= $status=='Pending'  ? 'bg-yellow-300' : '' ?>
<?= $status=='Rejected' ? 'bg-red-300' : '' ?>">
<?= $status ?>
</span>
</td>

<td class="space-x-1">
<a href="?id=<?= $id ?>&update_status=Approved"
   class="px-2 py-1 bg-green-500 text-white rounded text-xs">อนุมัติ</a>

<a href="?id=<?= $id ?>&update_status=Pending"
   class="px-2 py-1 bg-yellow-500 text-white rounded text-xs">รอ</a>

<a href="?id=<?= $id ?>&update_status=Rejected"
   class="px-2 py-1 bg-red-500 text-white rounded text-xs">ปฏิเสธ</a>
</td>

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
 class="px-3 py-1 rounded
 <?= $page==$i ? 'bg-blue-500 text-white' : 'bg-white border' ?>">
 <?= $i ?>
</a>
<?php endfor; ?>

<?php if ($page < $total_pages): ?>
<a href="?page=<?= $page+1 ?>" class="px-3 py-1 bg-white border rounded">Next</a>
<?php endif; ?>
</div>

</body>
</html>
