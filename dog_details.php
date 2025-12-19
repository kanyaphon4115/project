<?php
session_start();
include("backend/db_ped.php");

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $dog['name']; ?> - Pet Details</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen relative overflow-x-hidden">

  <!-- BG + OVERLAY (เบลอทั้งหน้า แม้เลื่อน) -->
  <div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-repeat"
         style="background-image:url('assets/images/bg_pethome_pattern.jpg'); background-size:300px;"></div>

    <!-- ทำให้พื้นหลังสว่างขึ้น ไม่กลืน -->
    <div class="absolute inset-0 bg-white/80"></div>

    <!-- เบลอพื้นหลัง -->
    <div class="absolute inset-0 backdrop-blur-md"></div>
  </div>

  <!-- BACK BUTTON (responsive) -->
  <a href="homeped.php"
    class="fixed top-4 left-4 sm:top-6 sm:left-6 z-50 bg-white/90 backdrop-blur px-4 py-2 rounded-full shadow-md hover:bg-gray-100 transition text-sm sm:text-base">
    ← Back
  </a>

  <!-- CONTENT (responsive padding + margin) -->
  <div class="max-w-4xl mx-auto bg-white shadow-xl rounded-2xl sm:rounded-3xl mt-20 sm:mt-24
            p-4 sm:p-8 mx-4 sm:mx-auto">

    <!-- IMAGE (responsive height) -->
    <?php
    $img = $dog['image'] ?? '';
    $img = ltrim($img, '/');
    $src = (str_starts_with($img, 'assets/')) ? $img : 'assets/' . $img;
    ?>

    <div class="relative w-full h-64 sm:h-80 md:h-[420px] overflow-hidden rounded-2xl shadow-lg bg-gray-100">
      <!-- background blur -->
      <img src="<?= htmlspecialchars($src) ?>"
        class="absolute inset-0 w-full h-full object-cover blur-xl scale-110 opacity-60"
        alt="">

      <!-- real image -->
      <img src="<?= htmlspecialchars($src) ?>"
        class="relative z-10 w-full h-full object-contain"
        alt="">
    </div>

    <!-- INFO SECTION -->
    <div class="mt-6">
      <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">
        <?php echo $dog['name']; ?>
      </h1>

      <p class="text-gray-600 text-base sm:text-lg mt-1">
        Breed: <?php echo $dog['breed']; ?>
      </p>

      <!-- responsive grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-4 text-base sm:text-lg">

        <div class="bg-gray-100 p-4 rounded-xl">
          <p class="font-semibold text-gray-700">Age</p>
          <p><?php echo $dog['age']; ?></p>
        </div>

        <div class="bg-gray-100 p-4 rounded-xl">
          <p class="font-semibold text-gray-700">Gender</p>
          <p><?php echo $dog['gender']; ?></p>
        </div>

        <div class="bg-gray-100 p-4 rounded-xl sm:col-span-2">
          <p class="font-semibold text-gray-700">Description</p>
          <p class="leading-relaxed"><?php echo $dog['description']; ?></p>
        </div>

      </div>
    </div>

    <!-- BUTTONS (responsive full width on mobile) -->
    <div class="mt-7 sm:mt-8 flex justify-center">
      <a href="request_status.php?add_dog=<?= $dog['id'] ?>"
        class="w-full sm:w-auto inline-flex justify-center items-center gap-2
              bg-green-600 text-white px-6 sm:px-14 py-3 rounded-xl font-bold shadow-lg
              hover:bg-green-700 transition">
        🐾 Adopt Me
      </a>
    </div>
  </div>

  <!-- CHAT FLOATING BUTTON -->
  <a href="chat.php"
    class="fixed bottom-5 right-5 sm:bottom-6 sm:right-6 bg-blue-600 w-12 h-12 sm:w-14 sm:h-14 rounded-full shadow-xl
          flex items-center justify-center hover:bg-blue-700 transition duration-300">

    <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" width="26" height="26" class="sm:w-[30px] sm:h-[30px]">
      <path d="M12 2C6.486 2 2 6.033 2 10.993c0 2.835 1.354 5.389 3.598 7.131V22l3.289-1.795c.993.276 2.042.429 3.113.429 
            5.514 0 10-4.033 10-8.993S17.514 2 12 2zm1.066 12.596l-2.648-2.826-4.4 2.826 
            4.84-5.173 2.648 2.826 4.4-2.826-4.84 5.173z" />
    </svg>
  </a>

</body>

</html>