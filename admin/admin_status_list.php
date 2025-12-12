<?php
$con = new mysqli("localhost", "root", "", "adopt_forms");

// ดึงรายการทั้งหมด
$res = $con->query("SELECT * FROM adopt_forms ORDER BY id DESC");

// โหลดสถานะจาก JSON
$status_file = __DIR__ . "/status.json";
$status_list = file_exists($status_file) ? json_decode(file_get_contents($status_file), true) : [];
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Admin – จัดการสถานะ</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-10">

<h1 class="text-3xl font-bold mb-5">รายการคำขอรับเลี้ยงสุนัข</h1>

<?php if (isset($_GET['updated'])): ?>
<div class="p-3 bg-green-200 text-green-800 rounded mb-4">
    ✔ อัปเดตสถานะสำเร็จ!
</div>
<?php endif; ?>

<table class="w-full bg-white shadow rounded-lg overflow-hidden">
    <tr class="bg-gray-200">
        <th class="p-3">ID</th>
        <th class="p-3">User</th>
        <th class="p-3">Dog ID</th>
        <th class="p-3">Status</th>
        <th class="p-3">Actions</th>
    </tr>

<?php while ($f = $res->fetch_assoc()): 
    $form_id = $f['id'];
    $current_status = $status_list[$form_id] ?? "Pending";
?>
<tr class="border-b">
    <td class="p-3"><?= $form_id ?></td>
    <td class="p-3"><?= $f['user_id'] ?></td>
    <td class="p-3"><?= $f['dog_id'] ?></td>

    <td class="p-3">
        <span class="px-3 py-1 rounded 
        <?= $current_status=='Approved' ? 'bg-green-300' : '' ?>
        <?= $current_status=='Pending'  ? 'bg-yellow-300' : '' ?>
        <?= $current_status=='Rejected' ? 'bg-red-300' : '' ?>">
        <?= $current_status ?>
        </span>
    </td>

    <td class="p-3">
        <a href="admin_update_status.php?id=<?= $form_id ?>&status=Approved"
           class="px-3 py-1 bg-green-500 text-white rounded">อนุมัติ</a>

        <a href="admin_update_status.php?id=<?= $form_id ?>&status=Pending"
           class="px-3 py-1 bg-yellow-500 text-white rounded">รอดำเนินการ</a>

        <a href="admin_update_status.php?id=<?= $form_id ?>&status=Rejected"
           class="px-3 py-1 bg-red-500 text-white rounded">ปฏิเสธ</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
