<?php
session_start();
include("database/db_ped.php"); // DB Connection

// โหลดรูปโปรไฟล์
$avatar_src = null;
if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
    $matches = glob(__DIR__ . "/uploads/avatar_user_$uid.*");
    if (!empty($matches)) {
        $avatar_src = "uploads/" . basename($matches[0]);
    }
}

// ---------------- FILTER -----------------
$filter_gender = $_GET['gender'] ?? [];
$filter_age = $_GET['age'] ?? [];
$filter_breed = $_GET['breed'] ?? [];

$sql = "SELECT * FROM dogs WHERE 1";

// Gender filter
if (!empty($filter_gender)) {
    $g = "'" . implode("','", $filter_gender) . "'";
    $sql .= " AND gender IN ($g)";
}

// Age filter + อายุน้อยกว่า 6 เดือน (NEW)
if (!empty($filter_age)) {
    $age_sql = [];
    
    foreach ($filter_age as $age) {

        // ⭐ เพิ่มเงื่อนไขใหม่: น้อยกว่า 6 เดือน
        if ($age == "less6") {
            $age_sql[] = "(age LIKE '%week%' 
                        OR age LIKE '1 month'
                        OR age LIKE '2 months'
                        OR age LIKE '3 months'
                        OR age LIKE '4 months'
                        OR age LIKE '5 months')";
        }

        if ($age == "6months") $age_sql[] = "age = '6 months'";
        if ($age == "1year") $age_sql[] = "age = '1 year'";
        if ($age == "more1") $age_sql[] = "age LIKE '%years%'";
    }

    if (!empty($age_sql)) {
        $sql .= " AND (" . implode(" OR ", $age_sql) . ")";
    }
}

// Breed filter
if (!empty($filter_breed)) {
    $b = "'" . implode("','", $filter_breed) . "'";
    $sql .= " AND breed IN ($b)";
}

$dogs = $con->query($sql);
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

        <!-- MENU -->
        <ul class="flex items-center space-x-6 font-semibold text-gray-900 ml-auto">
            <li><a href="homeped.php" class="text-green-700 font-bold">HOME</a></li>
            <li><a href="form.php" class="hover:text-green-700">FORM</a></li>
            <li><a href="donate.php" class="hover:text-green-700">DONATE</a></li>
            <li><a href="request_status.php" class="hover:text-green-700">REQUEST STATUS</a></li>

            <!-- PROFILE -->
            <?php if(isset($_SESSION['user_id'])): ?>
            <li class="relative" id="profileMenu">

                <button id="profileBtn" type="button" class="w-10 h-10 rounded-full bg-green-300 shadow-md overflow-hidden flex items-center justify-center">
                    <?php if ($avatar_src): ?>
                        <img src="<?= $avatar_src ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-white font-bold"><?= strtoupper($_SESSION['email'][0]); ?></span>
                    <?php endif; ?>
                </button>

                <!-- DROPDOWN (JS-controlled) -->
                <div id="profileDropdown" class="hidden absolute right-0 mt-3 w-60 bg-white shadow-xl rounded-xl p-4 text-gray-700 transition">

                    <p class="text-sm text-gray-500">เข้าสู่ระบบเป็น</p>
                    <p class="font-bold"><?= explode("@", $_SESSION['email'])[0]; ?></p>
                    <p class="text-sm"><?= $_SESSION['email']; ?></p>


<hr class="my-3">

<a href="setting.php" class="block py-1 font-medium hover:text-red-600 transition">
    ⚙️ ตั้งค่า
</a>

<a href="profile.php" class="block py-1 font-medium hover:text-red-600 transition">
    👤 โปรไฟล์
</a>

<a href="about_us.php" class="block py-1 font-medium hover:text-red-600 transition">
    ℹ️ About Us
</a>


                    <hr class="my-3">
                    <a href="index.php" class="text-red-600 font-bold">ออกจากระบบ</a>
                </div>

            </li>
            <?php endif; ?>
        </ul>

    </div>
</nav>

<!-- DOG LIST -->
<div class="pt-28 px-6 pb-10">

    <h2 class="text-2xl font-bold text-gray-800 mb-4">🐶 Meet Our Lovely Dogs</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

    <?php while($dog = $dogs->fetch_assoc()): ?>
<a href="dog_details.php?id=<?= $dog['id'] ?>" class="block">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:scale-[1.02] transition">

        <img src="<?= $dog['image'] ?>" class="w-full h-48 object-cover">

        <div class="p-4">
            <p class="font-bold text-lg"><?= $dog['name'] ?></p>
            <p class="text-sm text-gray-600"><?= $dog['breed'] ?></p>

            <div class="flex justify-between text-sm text-gray-500 mt-2">
                <span><?= $dog['age'] ?></span>
                <span><?= $dog['gender'] ?></span>
            </div>
        </div>

    </div>
</a>
<?php endwhile; ?>


    </div>

</div>

<!-- FILTER SIDEBAR -->
<div id="filterSidebar"
     class="fixed top-0 left-0 w-80 h-full bg-white shadow-2xl transform -translate-x-full
            transition-all duration-300 z-50">

    <div class="p-5 border-b flex justify-between">
        <h2 class="font-bold">Filter Dogs</h2>
        <button id="closeFilter" class="text-xl">✕</button>
    </div>

    <form method="GET" class="p-5 space-y-6">

        <!-- Gender -->
        <div>
            <p class="font-semibold mb-2">เพศ</p>
            <label class="flex gap-2"><input type="checkbox" name="gender[]" value="Male"> ชาย</label>
            <label class="flex gap-2"><input type="checkbox" name="gender[]" value="Female"> หญิง</label>
        </div>

        <!-- Age -->
        <div>
            <p class="font-semibold mb-2">อายุ</p>
            <label class="flex gap-2"><input type="checkbox" name="age[]" value="less6"> น้อยกว่า 6 เดือน</label>
            <label class="flex gap-2"><input type="checkbox" name="age[]" value="6months"> 6 เดือน</label>
            <label class="flex gap-2"><input type="checkbox" name="age[]" value="1year"> 1 ปี</label>
            <label class="flex gap-2"><input type="checkbox" name="age[]" value="more1"> มากกว่า 1 ปี</label>
        </div>

        <!-- Breed -->
        <div>
            <p class="font-semibold mb-2">สายพันธุ์</p>
            <label class="flex gap-2"><input type="checkbox" name="breed[]" value="Shih Tzu"> Shih Tzu</label>
            <label class="flex gap-2"><input type="checkbox" name="breed[]" value="Samoyed"> Samoyed</label>
            <label class="flex gap-2"><input type="checkbox" name="breed[]" value="Yorkshire Terrier"> Yorkshire Terrier</label>
            <label class="flex gap-2"><input type="checkbox" name="breed[]" value="Jack Russell"> Jack Russell</label>
        </div>

        <button type="submit"
                class="w-full bg-green-600 text-white py-2 rounded-lg shadow hover:bg-green-700">
            Apply Filter
        </button>

    </form>
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

<!-- JS CONTROL -->
<script>
document.getElementById("openFilter").onclick = function() {
    document.getElementById("filterSidebar").classList.remove("-translate-x-full");
};
document.getElementById("closeFilter").onclick = function() {
    document.getElementById("filterSidebar").classList.add("-translate-x-full");
};

// Profile dropdown: single-click open, hold to close
document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('profileBtn');
    var dropdown = document.getElementById('profileDropdown');
    if (!btn || !dropdown) return;

    var holdTimer = null;
    var holdTime = 350; // ms
    var isHolding = false;

    function openDropdown(){
        dropdown.classList.remove('hidden');
    }
    function closeDropdown(){
        dropdown.classList.add('hidden');
    }

    // Single click to open
    btn.addEventListener('click', function(e){
        e.stopPropagation();
        if (!isHolding) {
            openDropdown();
        }
    });

    // Hold to close
    function startHold(e){
        e.stopPropagation();
        isHolding = true;
        if (holdTimer) clearTimeout(holdTimer);
        holdTimer = setTimeout(function(){
            closeDropdown();
            isHolding = false;
            holdTimer = null;
        }, holdTime);
    }

    function cancelHold(e){
        if (holdTimer) { clearTimeout(holdTimer); holdTimer = null; }
        isHolding = false;
    }

    // Mouse events
    btn.addEventListener('mousedown', startHold);
    btn.addEventListener('mouseup', cancelHold);
    btn.addEventListener('mouseleave', cancelHold);

    // Touch events
    btn.addEventListener('touchstart', startHold, {passive: false});
    btn.addEventListener('touchend', cancelHold);
    btn.addEventListener('touchcancel', cancelHold);

    // Keep dropdown open when interacting inside
    dropdown.addEventListener('click', function(e){ e.stopPropagation(); });

    // Close when clicking elsewhere
    document.addEventListener('click', function(e){
        if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
            closeDropdown();
        }
    });
});
</script>

</body>
</html>
