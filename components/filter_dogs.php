<?php
include("backend/db_ped.php");

// สร้าง Query เริ่มต้น
$sql = "SELECT * FROM dogs WHERE 1 ";

// ฟิลเตอร์เพศ
if (!empty($_POST['gender'])) {
    $genders = array_map(function($v){ return "'" . $v . "'"; }, $_POST['gender']);
    $sql .= " AND gender IN (" . implode(",", $genders) . ")";
}

// ฟิลเตอร์อายุ
if (!empty($_POST['age'])) {
    $ages = array_map(function($v){ return "'" . $v . "'"; }, $_POST['age']);
    $sql .= " AND age IN (" . implode(",", $ages) . ")";
}

// ฟิลเตอร์สายพันธุ์
if (!empty($_POST['breed'])) {
    $breeds = array_map(function($v){ return "'" . $v . "'"; }, $_POST['breed']);
    $sql .= " AND breed IN (" . implode(",", $breeds) . ")";
}

$result = $con->query($sql);

if ($result->num_rows == 0) {
    echo "<p class='text-center text-gray-700'>ไม่พบสุนัขตามตัวกรอง</p>";
    exit;
}

// ส่ง HTML กลับไปให้หน้า homeped.php
while ($dog = $result->fetch_assoc()):
?>
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <img src="<?= $dog['image']; ?>" class="w-full h-48 object-cover">

        <div class="p-4">
            <p class="font-bold text-gray-900 text-lg"><?= $dog['name']; ?></p>
            <p class="text-sm text-gray-600 -mt-1"><?= $dog['breed']; ?></p>

            <div class="flex justify-between text-sm text-gray-500 mt-2">
                <span><?= $dog['age']; ?></span>
                <span><?= $dog['gender']; ?></span>
            </div>
        </div>
    </div>
<?php endwhile; ?>



