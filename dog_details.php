<?php
session_start();
include("database/db_ped.php");

// ตรวจสอบว่ามี id ถูกส่งมาหรือไม่
if (!isset($_GET['id'])) {
    die("ไม่พบสุนัขที่เลือก");
}

$dog_id = intval($_GET['id']);
$query = $con->prepare("SELECT * FROM dogs WHERE id = ?");
$query->bind_param("i", $dog_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    die("ไม่พบข้อมูลสุนัข");
}

$dog = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo $dog['name']; ?> - Pet Details</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f7d7a3] min-h-screen">

<!-- BACK BUTTON -->
<a href="homeped.php"
   class="absolute top-6 left-6 bg-white px-4 py-2 rounded-full shadow-md hover:bg-gray-100 transition">
   ← Back
</a>

<!-- CONTENT -->
<div class="max-w-4xl mx-auto bg-white shadow-xl rounded-3xl mt-24 p-8">

    <!-- IMAGE -->
    <img src="<?php echo $dog['image']; ?>" 
         class="w-full h-80 object-cover rounded-2xl shadow-lg">

    <!-- INFO SECTION -->
    <div class="mt-6">
        <h1 class="text-3xl font-extrabold text-gray-900">
            <?php echo $dog['name']; ?>
        </h1>

        <p class="text-gray-600 text-lg -mt-1">
            Breed: <?php echo $dog['breed']; ?>
        </p>

        <div class="grid grid-cols-2 gap-4 mt-4 text-lg">

            <div class="bg-gray-100 p-4 rounded-xl">
                <p class="font-semibold text-gray-700">Age</p>
                <p><?php echo $dog['age']; ?></p>
            </div>

            <div class="bg-gray-100 p-4 rounded-xl">
                <p class="font-semibold text-gray-700">Gender</p>
                <p><?php echo $dog['gender']; ?></p>
            </div>

            <div class="bg-gray-100 p-4 rounded-xl col-span-2">
                <p class="font-semibold text-gray-700">Description</p>
                <p><?php echo $dog['description']; ?></p>
            </div>

        </div>
    </div>

    <!-- BUTTONS -->
    <div class="mt-8 flex gap-4">

        <a href="adopt_form.php?dog_id=<?php echo $dog['id']; ?>"
           class="flex-1 text-center bg-green-600 text-white py-3 rounded-xl text-lg font-semibold shadow-lg hover:bg-green-700 transition">
           🐾 Adopt Me
        </a>

        <a href="homeped.php"
           class="flex-1 text-center bg-gray-300 py-3 rounded-xl text-lg font-semibold hover:bg-gray-400 transition">
           Back to Home
        </a>

    </div>

</div>

</body>
</html>
