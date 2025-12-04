<?php
session_start();

// --- Handle avatar upload ---
$upload_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar']) && isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);

    // Basic validation
    $allowed_types = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
    $file = $_FILES['avatar'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $upload_error = 'เกิดข้อผิดพลาดขณะอัปโหลดไฟล์';
    } elseif (!in_array($file['type'], $allowed_types)) {
        $upload_error = 'ไฟล์ต้องเป็นรูปภาพ (jpg, png, gif)';
    } else {
        // Ensure uploads folder exists
        $uploads_dir = __DIR__ . '/uploads';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0755, true);
        }

        // Save with a predictable name: avatar_user_{id}.{ext}
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        $target_filename = 'avatar_user_' . $user_id . '.' . $ext;
        $target_path = $uploads_dir . '/' . $target_filename;

        // Remove any previous avatar variants for this user (different extensions)
        foreach (glob($uploads_dir . '/avatar_user_' . $user_id . '.*') as $old) {
            if ($old !== $target_path) @unlink($old);
        }

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            // uploaded successfully, reload to show new avatar
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $upload_error = 'ไม่สามารถย้ายไฟล์ไปยังโฟลเดอร์ปลายทางได้';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>PetHome</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
    /* Animation เด้งน้องหมา */
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
</style>
</head>

<body class="bg-gradient-to-b from-[#f7d7a3] to-[#efbf82] min-h-screen">

  <nav class="w-full fixed top-0 left-0 bg-white/40 backdrop-blur-md shadow-sm py-4 z-20">
    <div class="flex items-center px-6">

        <!-- LOGO -->
        <h1 class="flex items-center gap-3 text-2xl font-extrabold text-[#2f5d31] tracking-wide">
            <div class="bg-white rounded-full shadow-md p-1 px-2">
                🐾
            </div>
            PetHome
        </h1>

        <!-- MENU (ขยับไปขวา) -->
<ul class="flex items-center space-x-8 text-sm font-semibold text-gray-900 ml-auto mr-4">
            <li><a href="home.php" class="hover:text-green-700">HOME</a></li>
            <li><a href="#" class="hover:text-green-700">FORM</a></li>
            <li><a href="#" class="hover:text-green-700">DONATE</a></li>
            <li><a href="#" class="hover:text-green-700">REQUEST STATUS</a></li>
            <li><a href="#" class="hover:text-green-700">PROFILE</a></li>
              <li>
        <?php if(isset($_SESSION['user_id'])): ?>

        <!-- PROFILE DROPDOWN -->
        <li class="relative group">

            <?php
            // Check for existing avatar file in uploads/
            $avatar_src = null;
            if (isset($_SESSION['user_id'])) {
                $uid = intval($_SESSION['user_id']);
                $matches = glob(__DIR__ . '/uploads/avatar_user_' . $uid . '.*');
                if (!empty($matches)) {
                    $avatar_file = basename($matches[0]);
                    $avatar_src = 'uploads/' . $avatar_file;
                }
            }
            ?>

            <!-- วงกลมโปรไฟล์ (รูปหรืออักษรตัวแรก) -->
            <button class="w-10 h-10 rounded-full bg-green-300 text-white font-bold flex items-center justify-center shadow-md overflow-hidden">
                <?php if ($avatar_src): ?>
                    <img src="<?php echo $avatar_src; ?>" alt="avatar" class="w-full h-full object-cover">
                <?php else:
                    $letter = strtoupper($_SESSION['email'][0]);
                    echo $letter;
                endif; ?>
            </button>

            <!-- DROPDOWN -->
            <div class="absolute right-0 mt-3 w-60 bg-white shadow-xl rounded-xl p-4 text-gray-700 
                        opacity-0 invisible group-hover:opacity-100 group-hover:visible transition">

                <p class="text-sm text-gray-500">เข้าสู่ระบบเป็น</p>
                <p class="font-bold text-gray-900">
                    <?php 
                        echo explode('@', $_SESSION['email'])[0]; 
                    ?>
                </p>
                <p class="text-sm"><?php echo $_SESSION['email']; ?></p>

                <hr class="my-3">

                <!-- Upload form: auto-submit when a file is picked -->
              <!-- Upload Profile Section -->
<div class="mb-3">
    <p class="text-sm font-medium text-gray-700 mb-1">เปลี่ยนรูปโปรไฟล์</p>

    <label class="flex items-center gap-3 cursor-pointer bg-gray-100 p-2 rounded-lg hover:bg-gray-200 transition">
        <div class="w-10 h-10 rounded-full overflow-hidden shadow">
            <?php if ($avatar_src): ?>
                <img src="<?php echo $avatar_src; ?>" class="w-full h-full object-cover">
            <?php else: ?>
                <div class="w-full h-full bg-green-400 flex items-center justify-center text-white font-bold">
                    <?php echo strtoupper($_SESSION['email'][0]); ?>
                </div>
            <?php endif; ?>
        </div>

        <span class="text-sm text-gray-700">เลือกไฟล์ใหม่</span>

        <!-- Hidden File Input -->
        <form method="POST" enctype="multipart/form-data" id="uploadForm">
            <input type="file" name="avatar" accept="image/*" 
                   class="hidden" id="avatarInput"
                   onchange="document.getElementById('uploadForm').submit();">
        </form>
    </label>

    <?php if (!empty($upload_error)): ?>
        <p class="text-sm text-red-600 mt-1"><?php echo $upload_error; ?></p>
    <?php endif; ?>
</div>


                <?php if (!empty($upload_error)): ?>
                    <p class="text-sm text-red-600 mt-2"><?php echo $upload_error; ?></p>
                <?php endif; ?>

                <a class="block py-2 hover:text-green-600">⚖️ น้ำหนักของฉัน</a>
                <a class="block py-2 hover:text-green-600">🏃 การออกกำลังกาย</a>
                <a class="block py-2 hover:text-green-600">📄 บทความ</a>

                <hr class="my-3">

                <a href="index.php" class="text-red-600 font-bold hover:underline">ออกจากระบบ</a>
            </div>
        </li>

    <?php else: ?>

        <!-- ปุ่ม Login เมื่อยังไม่เข้าสู่ระบบ -->
        <li>
            <a href="login.php"
               class="px-5 py-2 rounded-full bg-green-600 text-white font-semibold shadow-md hover:bg-green-700 transition">
               Login
            </a>
        </li>

    <?php endif; ?>

</ul>
    </div>
</nav>


    <!-- CONTENT -->
    <section class="flex flex-col items-center justify-center min-h-screen px-6 pt-24">

        <!-- PET IMAGE -->
        <img 
            src="dog.png" 
            alt="pet" 
            class="w-44 h-44 mt-6 animate-[float_3s_ease-in-out_infinite]"
        >

        <!-- TEXT -->
        <div class="text-center mt-8">
            <h2 class="text-3xl font-black text-gray-900 tracking-wide">
                Make A New Friends
            </h2>
            <p class="text-gray-700 mt-3 text-lg">
                A New Home For Your Four Legged Friend
            </p>
        </div>

        <!-- BUTTON -->
        <div class="mt-8">
            <a href="signup.php"
               class="px-8 py-3 rounded-full bg-green-600 text-white font-semibold shadow-lg hover:bg-green-700 transition">
               Adopt Now
            </a>
        </div>

    </section>

</body>
</html>
