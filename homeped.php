<?php
session_start();
include("database/db_ped.php"); // เชื่อมฐานข้อมูล

// โหลดรูปโปรไฟล์
$avatar_src = null;
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $matches = glob(__DIR__ . "/uploads/avatar_user_$uid.*");
    if (!empty($matches)) {
        $avatar_src = "uploads/" . basename($matches[0]);
    }
}

// โหลดข้อมูลสุนัขทั้งหมด (ค่าเริ่มต้น)
$dogs = $con->query("SELECT * FROM dogs ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PetHome</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
@keyframes float {
    0% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
    100% { transform: translateY(0); }
}
</style>
</head>

<body class="bg-[#f7d7a3] min-h-screen">

<!-- NAVBAR -->
<nav class="w-full fixed top-0 left-0 bg-white/40 backdrop-blur-md shadow-sm py-4 z-20">
    <div class="flex items-center px-6">

        <!-- ปุ่มสามขีด -->
        <button id="openFilter" class="text-2xl mr-4 hover:text-green-700">☰</button>

        <!-- LOGO -->
        <h1 class="flex items-center gap-3 text-2xl font-extrabold text-[#2f5d31]">
            <div class="bg-white rounded-full shadow-md p-1 px-2">🐾</div>
            PetHome
        </h1>

        <!-- MENU ขวาสุด -->
        <ul class="flex items-center space-x-8 font-semibold text-gray-900 ml-auto">
            <li><a href="homeped.php" class="text-green-700 font-bold">HOME</a></li>
            <li><a href="#" class="hover:text-green-700">FORM</a></li>
            <li><a href="#" class="hover:text-green-700">DONATE</a></li>
            <li><a href="#" class="hover:text-green-700">REQUEST STATUS</a></li>

            <!-- PROFILE -->
            <?php if(isset($_SESSION['user_id'])): ?>
            <li class="relative group">

                <!-- รูปโปรไฟล์ -->
                <button class="w-10 h-10 rounded-full bg-green-300 shadow-md overflow-hidden flex items-center justify-center">
                    <?php if ($avatar_src): ?>
                        <img src="<?php echo $avatar_src; ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-white font-bold">
                            <?php echo strtoupper($_SESSION['email'][0]); ?>
                        </span>
                    <?php endif; ?>
                </button>

                <!-- DROPDOWN -->
                <div class="absolute right-0 mt-3 w-60 bg-white shadow-xl rounded-xl p-4 text-gray-700
                            opacity-0 invisible group-hover:opacity-100 group-hover:visible transition">

                    <p class="text-sm text-gray-500">เข้าสู่ระบบเป็น</p>
                    <p class="font-bold"><?php echo explode("@", $_SESSION['email'])[0]; ?></p>
                    <p class="text-sm mb-2"><?php echo $_SESSION['email']; ?></p>

                    <hr class="my-2">

                    <!-- Upload Profile -->
                    <form method="POST" enctype="multipart/form-data">
                        <label class="flex items-center gap-3 cursor-pointer bg-gray-100 p-2 rounded-lg hover:bg-gray-200">
                            <div class="w-10 h-10 rounded-full overflow-hidden shadow">
                                <?php if ($avatar_src): ?>
                                    <img src="<?php echo $avatar_src; ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full bg-green-400 text-white flex items-center justify-center font-bold">
                                        <?php echo strtoupper($_SESSION['email'][0]); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <span class="text-sm text-gray-700">เปลี่ยนรูปโปรไฟล์</span>
                            <input type="file" name="avatar" class="hidden" onchange="this.form.submit()" accept="image/*">
                        </label>
                    </form>

                    <hr class="my-3">
                    <a href="#" class="block py-1 hover:text-green-600">⚖️ น้ำหนักของฉัน</a>
                    <a href="#" class="block py-1 hover:text-green-600">🏃 การออกกำลังกาย</a>
                    <a href="#" class="block py-1 hover:text-green-600">📄 บทความ</a>

                    <hr class="my-3">
                    <a href="logout.php" class="text-red-600 font-bold">ออกจากระบบ</a>
                </div>

            </li>

            <?php else: ?>
            <li><a href="login.php" class="px-5 py-2 bg-green-600 text-white rounded-full shadow-md">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>


<!-- DOG LIST -->
<div class="pt-28 px-6 pb-10">

    <h2 class="text-2xl font-bold text-gray-800 mb-4">🐶 Meet Our Lovely Dogs</h2>

    <div id="dogList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <?php while($dog = $dogs->fetch_assoc()): ?>
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">

            <img src="<?php echo $dog['image']; ?>" class="w-full h-48 object-cover">

            <div class="p-4">
                <p class="font-bold text-gray-900 text-lg"><?php echo $dog['name']; ?></p>
                <p class="text-sm text-gray-600 -mt-1"><?php echo $dog['breed']; ?></p>

                <div class="flex justify-between text-sm text-gray-500 mt-2">
                    <span><?php echo $dog['age']; ?></span>
                    <span><?php echo $dog['gender']; ?></span>
                </div>
            </div>

        </div>
        <?php endwhile; ?>

    </div>
</div>


<!-- FILTER SIDEBAR -->
<div id="filterSidebar"
     class="fixed top-0 left-0 w-80 max-w-full h-full bg-white shadow-2xl
            transform -translate-x-full transition-all duration-300 z-50">

    <div class="p-5 border-b flex justify-between items-center">
        <h2 class="text-lg font-bold">Filter Dogs</h2>
        <button id="closeFilter" class="text-xl font-bold">✕</button>
    </div>

    <!-- FILTER OPTIONS -->
    <div class="p-5 space-y-6 overflow-y-auto h-[90%]">

        <div>
            <p class="font-semibold mb-2">เพศ</p>
            <label class="flex gap-2"><input type="checkbox" class="filter" name="gender[]" value="Male"> ชาย</label>
            <label class="flex gap-2"><input type="checkbox" class="filter" name="gender[]" value="Female"> หญิง</label>
        </div>

        <div>
            <p class="font-semibold mb-2">อายุ</p>
            <label class="flex gap-2"><input type="checkbox" class="filter" name="age[]" value="6 months"> 6 เดือน</label>
            <label class="flex gap-2"><input type="checkbox" class="filter" name="age[]" value="1 year"> 1 ปี</label>
            <label class="flex gap-2"><input type="checkbox" class="filter" name="age[]" value="over 1 year"> มากกว่า 1 ปี</label>
        </div>

        <div>
            <p class="font-semibold mb-2">สายพันธุ์</p>
            <label class="flex gap-2"><input type="checkbox" class="filter" name="breed[]" value="Shih Tzu"> Shih Tzu</label>
            <label class="flex gap-2"><input type="checkbox" class="filter" name="breed[]" value="Samoyed"> Samoyed</label>
            <label class="flex gap-2"><input type="checkbox" class="filter" name="breed[]" value="Jack Russell"> Jack Russell</label>
        </div>

    </div>
</div>


<!-- JAVASCRIPT -->
<script>
document.getElementById("openFilter").onclick = () =>
    document.getElementById("filterSidebar").classList.remove("-translate-x-full");

document.getElementById("closeFilter").onclick = () =>
    document.getElementById("filterSidebar").classList.add("-translate-x-full");

document.querySelectorAll(".filter").forEach(chk =>
    chk.addEventListener("change", loadFilteredDogs)
);

function loadFilteredDogs() {
    let formData = new FormData();

    document.querySelectorAll(".filter:checked").forEach(chk =>
        formData.append(chk.name, chk.value)
    );

    fetch("filter_dogs.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(html => document.getElementById("dogList").innerHTML = html);
}
</script>

</body>
</html>
