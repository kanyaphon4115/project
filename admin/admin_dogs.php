<?php
session_start();
include(__DIR__ . "/../database/db_ped.php");
$currentPage = basename($_SERVER['PHP_SELF']);
// ===== PAGINATION =====
$limit = 5; // จำนวนย่อยต่อหน้า
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;
// ===== TOTAL PAGES =====
$count_q = mysqli_query($con, "SELECT COUNT(*) AS total FROM dogs");
$total_rows = mysqli_fetch_assoc($count_q)['total'];
$total_pages = ceil($total_rows / $limit);

// ================= DELETE DOG =================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_dog'])) {

    $id = (int)$_POST['id'];

    // ดึงรูปก่อนลบ
    $q = mysqli_query($con, "SELECT image FROM dogs WHERE id=$id");
    if ($q && mysqli_num_rows($q) > 0) {
        $dog = mysqli_fetch_assoc($q);
        if (!empty($dog['image']) && file_exists("../".$dog['image'])) {
            unlink("../".$dog['image']); // ลบไฟล์รูป
        }
    }

    // ลบข้อมูล
    mysqli_query($con, "DELETE FROM dogs WHERE id=$id");

    header("Location: admin_dogs.php");
    exit;
}


// ================= ADD DOG =================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_dog'])) {

    $name   = $_POST['name'];
    $breed  = $_POST['breed'];
    $age    = $_POST['age'];
    $gender = $_POST['gender'];
    $desc   = $_POST['description'] ?? '';

    // อัปโหลดรูป
    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        $folder = "../uploads/dogs/";
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $filename = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $folder . $filename);

        $image_path = "uploads/dogs/" . $filename;
    }

    $sql = "INSERT INTO dogs (name, breed, age, gender, description, image)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss",
        $name, $breed, $age, $gender, $desc, $image_path
    );
    mysqli_stmt_execute($stmt);

    header("Location: admin_dogs.php");
    exit;
}

$dog_sql = "SELECT * FROM dogs 
            ORDER BY created_at DESC
            LIMIT $limit OFFSET $offset";
$dog_result = mysqli_query($con, $dog_sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการสุนัข | Admin</title>
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
           <?= $currentPage == 'adopt_requests.php'
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

<!-- TOAST -->
<?php if (!empty($_SESSION['success'])): ?>
<div id="toast"
     class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-50">
    <?= $_SESSION['success'] ?>
</div>
<script>
setTimeout(() => document.getElementById('toast').remove(), 3000);
</script>
<?php unset($_SESSION['success']); endif; ?>

<div class="ml-64 p-10 relative">

<!-- HEADER -->
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-black">🐕 จัดการสุนัข</h1>
    <button onclick="openAddModal()"
 class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-xl font-semibold">
 ➕ เพิ่มสุนัข
</button>

</div>

<!-- TABLE -->
<div class="bg-white rounded-2xl shadow overflow-hidden">
<table class="w-full text-left">
<thead class="bg-orange-100">
<tr>
    <th class="p-4">รูป</th>
    <th>ชื่อ</th>
    <th>สายพันธุ์</th>
    <th>อายุ</th>
    <th>เพศ</th>
    <th>จัดการ</th>
</tr>
</thead>
<tbody class="divide-y">

<?php while ($dog = mysqli_fetch_assoc($dog_result)): ?>
<tr class="hover:bg-orange-50">
<td class="p-4">
<img src="../<?= htmlspecialchars($dog['image']) ?>"
     class="w-20 h-20 object-cover rounded-xl border">
</td>
<td class="font-semibold"><?= htmlspecialchars($dog['name']) ?></td>
<td><?= htmlspecialchars($dog['breed']) ?></td>
<td><?= htmlspecialchars($dog['age']) ?></td>
<td><?= htmlspecialchars($dog['gender']) ?></td>
<td class="space-x-2">

<a href="../dog_details.php?id=<?= $dog['id'] ?>"
   class="text-blue-600 hover:underline">ดู</a>

<button
onclick="openEditModal(
<?= $dog['id'] ?>,
'<?= htmlspecialchars($dog['name'], ENT_QUOTES) ?>',
'<?= htmlspecialchars($dog['breed'], ENT_QUOTES) ?>',
'<?= htmlspecialchars($dog['age'], ENT_QUOTES) ?>',
'<?= htmlspecialchars($dog['gender'], ENT_QUOTES) ?>',
'<?= htmlspecialchars($dog['description'], ENT_QUOTES) ?>',
'<?= htmlspecialchars($dog['image'], ENT_QUOTES) ?>'
)"
class="text-green-600 hover:underline font-semibold">
แก้ไข
</button>

<button
 onclick="openDeleteModal(<?= $dog['id'] ?>,'<?= htmlspecialchars($dog['name'],ENT_QUOTES) ?>')"
 class="text-red-600 hover:underline font-semibold">
 ลบ
</button>


</td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>
</div>

<!-- EDIT MODAL -->
<div id="editModal"
     class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">

<div class="bg-white rounded-2xl shadow-xl w-[420px] relative">

<button onclick="closeEditModal()"
class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">✕</button>

<div class="bg-orange-100 p-6 text-center rounded-t-2xl">
<div class="text-4xl mb-2">🐶</div>
<h2 class="text-xl font-bold">แก้ไขข้อมูลสุนัข</h2>
</div>

<form method="POST" enctype="multipart/form-data" class="p-6 space-y-4">

<input type="hidden" name="update_dog" value="1">
<input type="hidden" name="id" id="edit_id">
<input type="hidden" name="old_image" id="edit_old_image">

<img id="previewImage"
     class="w-full h-48 object-cover rounded-xl border hidden">

<input type="text" name="name" id="edit_name"
class="w-full border rounded-xl px-3 py-2" required>

<input type="text" name="breed" id="edit_breed"
class="w-full border rounded-xl px-3 py-2">

<input type="text" name="age" id="edit_age"
class="w-full border rounded-xl px-3 py-2">

<select name="gender" id="edit_gender"
class="w-full border rounded-xl px-3 py-2">
<option value="Male">ผู้</option>
<option value="Female">เมีย</option>
</select>

<textarea name="description" id="edit_description"
class="w-full border rounded-xl px-3 py-2"
rows="3"></textarea>

<input type="file" name="image"
onchange="previewDogImage(event)"
class="w-full border rounded-xl px-3 py-2">

<div class="flex justify-end gap-3 pt-4">
<button type="button" onclick="closeEditModal()"
class="px-5 py-2 rounded-xl border">ยกเลิก</button>

<button type="submit"
class="px-5 py-2 rounded-xl bg-orange-500 text-white font-semibold">
บันทึก
</button>
</div>

</form>
</div>
</div>
<?php
$show = 3; // จำนวนเลขที่แสดงต่อชุด

$start_page = max(1, $page - floor($show / 2));
$end_page   = min($total_pages, $start_page + $show - 1);

// ปรับกรณีท้าย ๆ
if ($end_page - $start_page + 1 < $show) {
    $start_page = max(1, $end_page - $show + 1);
}
?>

<div class="flex justify-center mt-6 space-x-2">

<!-- Previous -->
<?php if ($page > 1): ?>
    <a href="?page=<?= $page - 1 ?>"
       class="px-3 py-1 bg-white border rounded-lg hover:bg-gray-200">
       Previous
    </a>
<?php else: ?>
    <span class="px-3 py-1 bg-gray-100 border rounded-lg text-gray-400">
       Previous
    </span>
<?php endif; ?>

<!-- Numbers -->
<?php for ($i = $start_page; $i <= $end_page; $i++): ?>
    <a href="?page=<?= $i ?>"
       class="px-3 py-1 rounded-lg
       <?= $page == $i
            ? 'bg-blue-500 text-white'
            : 'bg-white border hover:bg-gray-200' ?>">
       <?= $i ?>
    </a>
<?php endfor; ?>

<!-- Next -->
<?php if ($page < $total_pages): ?>
    <a href="?page=<?= $page + 1 ?>"
       class="px-3 py-1 bg-white border rounded-lg hover:bg-gray-200">
       Next
    </a>
<?php else: ?>
    <span class="px-3 py-1 bg-gray-100 border rounded-lg text-gray-400">
       Next
    </span>
<?php endif; ?>

</div>


<!-- ADD MODAL -->
<div id="addModal"
 class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">

  <div class="bg-white rounded-2xl shadow-xl w-[420px] relative">

    <button onclick="closeAddModal()"
      class="absolute top-3 right-3 text-gray-400">✕</button>

    <div class="bg-orange-100 rounded-t-2xl p-6 text-center">
      <div class="text-4xl mb-2">🐕</div>
      <h2 class="text-xl font-bold">เพิ่มสุนัขใหม่</h2>
    </div>

    <form method="POST" enctype="multipart/form-data" class="p-6 space-y-4">

      <input type="hidden" name="add_dog" value="1">

      <div>
        <label>ชื่อ</label>
        <input name="name" required class="w-full border rounded-xl px-3 py-2">
      </div>

      <div>
        <label>สายพันธุ์</label>
        <input name="breed" class="w-full border rounded-xl px-3 py-2">
      </div>

      <div>
        <label>อายุ</label>
        <input name="age" class="w-full border rounded-xl px-3 py-2">
      </div>

      <div>
        <label>เพศ</label>
        <select name="gender" class="w-full border rounded-xl px-3 py-2">
          <option value="Male">ผู้</option>
          <option value="Female">เมีย</option>
        </select>
      </div>

      <div>
        <label>รายละเอียด</label>
        <textarea name="description"
         class="w-full border rounded-xl px-3 py-2"></textarea>
      </div>

      <div>
        <label>รูป</label>
        <input type="file" name="image"
         class="w-full border rounded-xl px-3 py-2">
      </div>

      <div class="flex justify-end gap-3 pt-4">
        <button type="button" onclick="closeAddModal()"
         class="px-5 py-2 border rounded-xl">ยกเลิก</button>

        <button type="submit"
         class="px-5 py-2 bg-orange-500 text-white rounded-xl">
         บันทึก
        </button>
      </div>

    </form>
  </div>
</div>
<!-- DELETE MODAL -->
<div id="deleteModal"
 class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">

  <div class="bg-white rounded-2xl shadow-xl w-[380px] relative">

    <button onclick="closeDeleteModal()"
     class="absolute top-3 right-3 text-gray-400">✕</button>

    <div class="bg-red-100 rounded-t-2xl p-6 text-center">
      <div class="text-4xl mb-2">⚠️</div>
      <h2 class="text-xl font-bold text-red-700">ยืนยันการลบ</h2>
      <p class="text-gray-600 text-sm mt-1">
        คุณต้องการลบ <span id="deleteDogName" class="font-semibold"></span> ใช่หรือไม่?
      </p>
    </div>

    <form method="POST" class="p-6 flex justify-center gap-4">
      <input type="hidden" name="delete_dog" value="1">
      <input type="hidden" name="id" id="delete_id">

      <button type="button" onclick="closeDeleteModal()"
       class="px-5 py-2 border rounded-xl">
       ยกเลิก
      </button>

      <button type="submit"
       class="px-5 py-2 bg-red-500 text-white rounded-xl font-semibold">
       ลบ
      </button>
    </form>

  </div>
</div>

<script>
function openEditModal(id,name,breed,age,gender,desc,image){
editModal.classList.remove('hidden');
edit_id.value=id;
edit_name.value=name;
edit_breed.value=breed;
edit_age.value=age;
edit_gender.value=gender;
edit_description.value=desc;
edit_old_image.value=image;
previewImage.src="../"+image;
previewImage.classList.remove('hidden');
}
function closeEditModal(){
editModal.classList.add('hidden');
}
function previewDogImage(e){
previewImage.src=URL.createObjectURL(e.target.files[0]);
previewImage.classList.remove('hidden');
}
</script>
<script>
function openAddModal() {
  document.getElementById('addModal').classList.remove('hidden');
}
function closeAddModal() {
  document.getElementById('addModal').classList.add('hidden');
}
</script>
<script>
function openDeleteModal(id, name) {
  document.getElementById('deleteModal').classList.remove('hidden');
  document.getElementById('delete_id').value = id;
  document.getElementById('deleteDogName').innerText = name;
}

function closeDeleteModal() {
  document.getElementById('deleteModal').classList.add('hidden');
}
</script>

</body>
</html>
