<?php
session_start();
include("backend/db_ped.php"); // DB Connection

$breedList = [];
$breedRes = $con->query("
  SELECT DISTINCT TRIM(breed) AS breed
  FROM dogs
  WHERE TRIM(breed) <> ''
  ORDER BY TRIM(breed) ASC
");
while ($r = $breedRes->fetch_assoc()) {
    $breedList[] = $r['breed'];
}

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
$filter_age    = $_GET['age'] ?? [];
$filter_breed  = $_GET['breed'] ?? [];

// ทำเป็น "ฐานเดียว" (ไม่มี SELECT * ก่อน)
$base = "FROM dogs WHERE 1";

// Gender filter
if (!empty($filter_gender)) {
    $g = "'" . implode("','", $filter_gender) . "'";
    $base .= " AND gender IN ($g)";
}

// Age filter
if (!empty($filter_age)) {
    $age_sql = [];
    foreach ($filter_age as $age) {
        if ($age == "less6") {
            $age_sql[] = "(age LIKE '%week%' 
                        OR age = '1 month'
                        OR age = '2 months'
                        OR age = '3 months'
                        OR age = '4 months'
                        OR age = '5 months')";
        }
        if ($age == "6months") $age_sql[] = "age = '6 months'";
        if ($age == "1year")   $age_sql[] = "age = '1 year'";
        if ($age == "more1")   $age_sql[] = "age LIKE '%years%'";
    }
    if (!empty($age_sql)) {
        $base .= " AND (" . implode(" OR ", $age_sql) . ")";
    }
}

// Breed filter
if (!empty($filter_breed)) {
    $b = "'" . implode("','", $filter_breed) . "'";
    $base .= " AND TRIM(breed) IN ($b)";
}

// ---------------- PAGINATION -----------------
$limit = 6;
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;

//  COUNT ต้องใช้เงื่อนไขเดียวกับ filter
$count_result = $con->query("SELECT COUNT(*) AS total $base");
$total_dogs = (int)$count_result->fetch_assoc()['total'];

$total_pages = max(1, (int)ceil($total_dogs / $limit));

//  กันหน้าหลุดตอนลบหมา (เช่นอยู่หน้า 3 แต่เหลือ 2 หน้า)
$page = max(1, min($page, $total_pages));
$start = ($page - 1) * $limit;

//  ดึงข้อมูลตาม filter + pagination
$dogs = $con->query("SELECT * $base ORDER BY id ASC LIMIT $start, $limit");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawHome</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @keyframes float {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-6px);
            }

            100% {
                transform: translateY(0);
            }
        }
    </style>

</head>

<body class="min-h-screen relative overflow-x-hidden">
    <!-- BG (เบลเฉพาะพื้นหลัง) -->
    <div class="fixed inset-0 -z-10">
        <div class="absolute inset-0 bg-repeat"
            style="background-image:url('assets/images/bg_pethome_pattern.jpg'); background-size:300px;"></div>

        <!-- ถ้าอยากให้จางลง -->
        <div class="absolute inset-0 bg-white/70"></div>

        <!-- เบลเฉพาะพื้นหลัง -->
        <div class="absolute inset-0 backdrop-blur-sm"></div>
    </div>

    <!-- NAVBAR -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <!-- DOG LIST -->
    <div class="pt-28 px-6 pb-10">

        <h2 class="text-2xl font-bold text-gray-800 mb-4">🐶 Meet Our Lovely Dogs</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <?php if (!$dogs || $dogs->num_rows === 0): ?>
                <p class="text-center text-gray-700">ไม่พบสุนัขตามตัวกรอง</p>
            <?php else: ?>
                <?php while ($dog = $dogs->fetch_assoc()): ?>
                    <a href="dog_details.php?id=<?= (int)$dog['id'] ?>" class="block">
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:scale-[1.02] transition">

                            <?php
                            $img = ltrim((string)($dog['image'] ?? ''), '/');
                            $src = (str_starts_with($img, 'assets/')) ? $img : 'assets/' . $img;
                            ?>
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:scale-[1.02] transition">
                                <img src="<?= htmlspecialchars($src) ?>" class="w-full h-48 object-contain bg-white" alt="">
                            </div>

                            <div class="p-4">
                                <p class="font-bold text-lg"><?= htmlspecialchars($dog['name'] ?? '') ?></p>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($dog['breed'] ?? '') ?></p>
                                <div class="flex justify-between text-sm text-gray-500 mt-2">
                                    <span><?= htmlspecialchars($dog['age'] ?? '') ?></span>
                                    <span><?= htmlspecialchars($dog['gender'] ?? '') ?></span>
                                </div>
                            </div>

                        </div>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>

        </div>

    </div>

    <?php
    function pageLink($p)
    {
        $qs = $_GET;        // เก็บค่า filter เดิมทั้งหมด
        $qs['page'] = $p;   // เปลี่ยนแค่ page
        return '?' . http_build_query($qs);
    }
    ?>

    <!-- PAGINATION -->
    <div class="flex justify-center mt-10 space-x-2">

        <?php
        // ตั้งจำนวนหน้าที่ต้องการให้แสดง
        $show = 3;

        // คำนวณจุดเริ่ม – จบ
        $start_page = max(1, $page - floor($show / 2));
        $end_page = min($total_pages, $start_page + $show - 1);

        // ถ้าจำนวนหน้ารวมยังไม่ถึง 3 ให้ปรับใหม่
        if ($end_page - $start_page + 1 < $show) {
            $start_page = max(1, $end_page - $show + 1);
        }
        ?>

        <!-- ปุ่ม Prev -->
        <?php if ($page > 1): ?>
            <a href="<?= pageLink($page - 1) ?>"
                class="px-3 py-1 bg-white border rounded-lg hover:bg-gray-200">Previous</a>
        <?php endif; ?>

        <!-- ปุ่มหมายเลข -->
        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="<?= pageLink($i) ?>"
                class="px-3 py-1 rounded <?= $page == $i ? 'bg-blue-500 text-white' : 'bg-white border' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <!-- ปุ่ม Next -->
        <?php if ($page < $total_pages): ?>
            <a href="<?= pageLink($page + 1) ?>"
                class="px-3 py-1 bg-white border rounded-lg hover:bg-gray-200">Next</a>
        <?php endif; ?>


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
                <label class="flex gap-2"><input type="checkbox" name="gender[]" value="Male"> เพศผู้</label>
                <label class="flex gap-2"><input type="checkbox" name="gender[]" value="Female"> เพศเมีย</label>
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

                <?php foreach ($breedList as $b): ?>
                    <label class="flex gap-2">
                        <input
                            type="checkbox"
                            name="breed[]"
                            value="<?= htmlspecialchars($b) ?>"
                            <?= in_array($b, $filter_breed ?? []) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($b) ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="w-full bg-green-600 text-white py-2 rounded-lg shadow hover:bg-green-700">
                    Apply Filter
                </button>

                <a href="<?= basename($_SERVER['PHP_SELF']) ?>"
                    class="w-full text-center bg-gray-200 py-2 rounded-lg shadow hover:bg-gray-300">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- CHAT FLOATING BUTTON -->
    <a href="chat.php"
        class="fixed bottom-6 right-6 bg-blue-600 w-14 h-14 rounded-full shadow-xl
          flex items-center justify-center hover:bg-blue-700 transition duration-300">

        <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" width="30" height="30">
            <path d="M12 2C6.486 2 2 6.033 2 10.993c0 2.835 1.354 5.389 3.598 7.131V22l3.289-1.795c.993.276 2.042.429 3.113.429 
                5.514 0 10-4.033 10-8.993S17.514 2 12 2zm1.066 12.596l-2.648-2.826-4.4 2.826 
                4.84-5.173 2.648 2.826 4.4-2.826-4.84 5.173z" />
        </svg>
    </a>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openBtn = document.getElementById("openFilter");
            const closeBtn = document.getElementById("closeFilter");
            const sidebar = document.getElementById("filterSidebar");

            if (!openBtn || !closeBtn || !sidebar) return;

            openBtn.addEventListener("click", () => {
                sidebar.classList.remove("-translate-x-full");
            });

            closeBtn.addEventListener("click", () => {
                sidebar.classList.add("-translate-x-full");
            });
        });
    </script>
</body>

</html>