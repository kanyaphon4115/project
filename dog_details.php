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
    <div class="mt-8 flex justify-center">
<a href="request_status.php?add_dog=<?= $dog['id'] ?>"
   class="inline-block mx-auto bg-green-600 text-white px-14 py-3 rounded-xl font-bold shadow-lg hover:bg-green-700 transition">
   🐾 Adopt Me
</a>


</div>

<!-- CHAT FLOATING BUTTON -->
<a href="chat.php"
   class="fixed bottom-6 right-6 bg-blue-600 w-14 h-14 rounded-full shadow-xl
          flex items-center justify-center hover:bg-blue-700 transition duration-300">

    <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" width="30" height="30">
        <path d="M12 2C6.486 2 2 6.033 2 10.993c0 2.835 1.354 5.389 3.598 7.131V22l3.289-1.795c.993.276 2.042.429 3.113.429 
                5.514 0 10-4.033 10-8.993S17.514 2 12 2zm1.066 12.596l-2.648-2.826-4.4 2.826 
                4.84-5.173 2.648 2.826 4.4-2.826-4.84 5.173z"/>
    </svg>

</a>

</body>
</html>
