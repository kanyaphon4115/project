<?php
session_start();
include("database/db.php");

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_email = $_SESSION['email'] ?? "guest@example.com";
$user_name = explode("@", $user_email)[0];
$account_message = '';
$password_message = '';

// Load saved username from DB
$con_reg = new mysqli("localhost", "root", "", "register");
if (!$con_reg->connect_errno) {
    $stmt = $con_reg->prepare("SELECT username FROM form WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    if ($row && $row['username']) {
        $user_name = $row['username'];
    }
    $stmt->close();
    $con_reg->close();
}

// Handle username save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_account'])) {
    $new_username = trim($_POST['username'] ?? '');
    
    if (!empty($new_username)) {
        $con_reg = new mysqli("localhost", "root", "", "register");
        if (!$con_reg->connect_errno) {
            // Ensure username column exists
            $check = $con_reg->query("SHOW COLUMNS FROM form LIKE 'username'");
            if ($check->num_rows === 0) {
                $con_reg->query("ALTER TABLE form ADD COLUMN username VARCHAR(120) NULL");
            }
            
            $upd = $con_reg->prepare("UPDATE form SET username = ? WHERE id = ?");
            $upd->bind_param('si', $new_username, $_SESSION['user_id']);
            if ($upd->execute()) {
                $account_message = '✅ บันทึกชื่อผู้ใช้เรียบร้อยแล้ว';
                $user_name = $new_username;
            } else {
                $account_message = '❌ เกิดข้อผิดพลาด: ' . $con_reg->error;
            }
            $upd->close();
            $con_reg->close();
        }
    } else {
        $account_message = '❌ กรุณากรอกชื่อผู้ใช้';
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_pass = $_POST['old_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    $con_reg = new mysqli("localhost", "root", "", "register");
    if (!$con_reg->connect_errno) {
        // Validate current password
        $check_pass = $con_reg->query("SELECT pass FROM form WHERE id = " . $_SESSION['user_id'] . " LIMIT 1");
        $pass_row = $check_pass->fetch_assoc();

        if ($pass_row && $pass_row['pass'] === $old_pass) {
            if ($new_pass === $confirm_pass && !empty($new_pass)) {
                $pwd_upd = $con_reg->prepare("UPDATE form SET pass=? WHERE id=?");
                $pwd_upd->bind_param('si', $new_pass, $_SESSION['user_id']);
                if ($pwd_upd->execute()) {
                    $password_message = '✅ เปลี่ยนรหัสผ่านเรียบร้อยแล้ว';
                } else {
                    $password_message = '❌ เกิดข้อผิดพลาด: ' . $con_reg->error;
                }
                $pwd_upd->close();
            } else {
                $password_message = '❌ รหัสผ่านใหม่ไม่ตรงกัน หรือไม่ได้กรอก';
            }
        } else {
            $password_message = '❌ รหัสผ่านปัจจุบันไม่ถูกต้อง';
        }
        $con_reg->close();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Settings - PawHome</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>
<style> body { font-family: 'Prompt', sans-serif; } </style>
</head>

<body class="bg-[#f7d7a3] min-h-screen">

<!-- NAVBAR -->
<nav class="fixed top-0 left-0 w-full bg-white/40 backdrop-blur-md shadow-md py-4 z-20">
    <div class="flex items-center justify-between px-6">
        <h1 class="text-2xl font-extrabold text-[#2f5d31] flex items-center gap-2">
            <span class="bg-white rounded-full shadow px-2">🐾</span> PawHome
        </h1>

        <a href="homeped.php" class="text-gray-800 hover:text-green-700 font-medium">กลับหน้าแรก</a>
    </div>
</nav>

<div class="pt-28"></div>

<!-- MAIN CONTENT -->
<div class="max-w-5xl mx-auto px-6 pb-20">

    <!-- HEADER -->
    <h2 class="text-4xl font-extrabold text-[#2f5d31] mb-6">การตั้งค่า</h2>
    <p class="text-gray-700 text-lg mb-8">ปรับแต่งบัญชีและประสบการณ์การใช้งานของคุณ</p>

    <?php if (!empty($account_message)): ?>
        <div class="p-3 mb-4 rounded-lg <?= strpos($account_message, '✅') === 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
            <?= htmlspecialchars($account_message) ?>
        </div>
    <?php endif; ?>

    <!-- SECTION: ACCOUNT -->
    <div class="bg-white/70 backdrop-blur-lg shadow-xl rounded-3xl p-8 mb-10">
        <h3 class="text-2xl font-bold text-[#2f5d31] mb-6">ข้อมูลบัญชี</h3>

        <form method="POST" class="space-y-4">
            <div>
                <label class="text-gray-700 font-medium">ชื่อผู้ใช้</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user_name) ?>" 
                    class="w-full mt-1 p-3 rounded-xl shadow border border-gray-300 focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="text-gray-700 font-medium">อีเมล</label>
                <input type="text" value="<?= htmlspecialchars($user_email) ?>" 
                    class="w-full mt-1 p-3 rounded-xl shadow border border-gray-300 bg-gray-100 cursor-not-allowed" disabled>
            </div>

            <button type="submit" name="save_account" class="px-6 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 mt-3">บันทึกข้อมูล</button>
        </form>
    </div>

    <!-- SECTION: PASSWORD -->
    <div class="bg-white/70 backdrop-blur-lg shadow-xl rounded-3xl p-8 mb-10">
        <h3 class="text-2xl font-bold text-[#2f5d31] mb-6">ความปลอดภัย</h3>

        <?php if (!empty($password_message)): ?>
            <div class="p-3 mb-4 rounded-lg <?= strpos($password_message, '✅') === 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                <?= htmlspecialchars($password_message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="text-gray-700 font-medium">รหัสผ่านปัจจุบัน</label>
                <input type="password" name="old_password" class="w-full mt-1 p-3 rounded-xl shadow border border-gray-300" placeholder="กรอกรหัสผ่านปัจจุบัน" required>
            </div>

            <div>
                <label class="text-gray-700 font-medium">รหัสผ่านใหม่</label>
                <input type="password" name="new_password" class="w-full mt-1 p-3 rounded-xl shadow border border-gray-300" placeholder="กรอกรหัสผ่านใหม่" required>
            </div>

            <div>
                <label class="text-gray-700 font-medium">ยืนยันรหัสผ่านใหม่</label>
                <input type="password" name="confirm_password" class="w-full mt-1 p-3 rounded-xl shadow border border-gray-300" placeholder="ยืนยันรหัสผ่านใหม่" required>
            </div>

            <button type="submit" name="change_password" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 mt-3">เปลี่ยนรหัสผ่าน</button>
        </form>
    </div>

    <!-- SECTION: DANGER ZONE -->
    <div class="bg-red-50 border border-red-300 shadow-xl rounded-3xl p-8 mb-10">
        <h3 class="text-2xl font-bold text-red-700 mb-3">Danger Zone</h3>
        <p class="text-gray-700 mb-4">การลบบัญชีเป็นการดำเนินการถาวรและไม่สามารถกู้คืนได้</p>

        <button class="px-6 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700">
            ลบบัญชีผู้ใช้
        </button>
    </div>

</div>

</body>
</html>
