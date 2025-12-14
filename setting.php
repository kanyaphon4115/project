<?php
session_start();
include("database/db.php");

// ถ้าไม่ล็อกอิน → เด้งออก
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['email'];
$user_name = explode("@", $user_email)[0]; 

// ====================================================================
// ลบบัญชีเมื่อกด "delete_account"
// ====================================================================
if (isset($_POST['delete_account'])) {

    // 1) ลบ adopt_forms
    $adopt = new mysqli("localhost", "root", "", "adopt_forms");
    $adopt->query("DELETE FROM adopt_forms WHERE user_id = $user_id");
    $adopt->close();

    // 2) ลบข้อความแชท
    $chat = new mysqli("localhost", "root", "", "chat");
    $chat->query("DELETE FROM chat_messages 
                  WHERE sender_id = $user_id OR receiver_id = $user_id");
    $chat->close();

    // 3) ลบการบริจาค
    $donate = new mysqli("localhost", "root", "", "pethome_donate");
    $donate->query("DELETE FROM donate_bank WHERE donor_name = '$user_name'");
    $donate->close();

    // 4) ลบ Avatar
    foreach (glob("uploads/avatar_user_$user_id.*") as $file) {
        unlink($file);
    }

    // 5) ลบข้อมูลบัญชี (register.form)
$reg = new mysqli("localhost", "root", "", "register");
$reg->query("SET FOREIGN_KEY_CHECKS = 0");
$reg->query("DELETE FROM form WHERE id = $user_id");
$reg->query("SET FOREIGN_KEY_CHECKS = 1");
$reg->close();

    // ออกจากระบบ
    session_destroy();
    header("Location: index.php?delete_success=1");
    exit;
}

// ====================================================================
// ส่วนแก้ไขบัญชี
// ====================================================================

$account_message = '';
$password_message = '';

// โหลด username จาก DB
$con_reg = new mysqli("localhost", "root", "", "register");
$stmt = $con_reg->prepare("SELECT username FROM form WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    if (!empty($row['username'])) {
        $user_name = $row['username'];
    }
}

$stmt->close();
$con_reg->close();
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
<button onclick="openDeleteAccountPopup()" 
        class="px-6 py-3 bg-red-600 text-white rounded-xl font-semibold hover:bg-red-700">
    ลบบัญชีผู้ใช้
</button>

    </div>

</div>
<!-- DELETE ACCOUNT POPUP -->
<div id="deleteAccountPopup"
     class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden z-50 flex justify-center items-center">

    <div class="bg-white w-[380px] rounded-2xl p-6 shadow-xl animate-pop">

        <div class="text-5xl text-red-500 mb-3 text-center">⚠️</div>

        <h3 class="text-xl font-bold text-center text-gray-800">ต้องการลบบัญชีจริงหรือไม่?</h3>
        <p class="text-center text-gray-600 mt-1">การลบนี้ไม่สามารถกู้คืนได้</p>

        <div class="flex justify-center gap-3 mt-5">
            <button onclick="closeDeleteAccountPopup()"
                    class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                ยกเลิก
            </button>

            <form method="POST">
                <button name="delete_account"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    ลบเลย
                </button>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes pop {
    from { transform: scale(0.9); opacity: 0; }
    to   { transform: scale(1); opacity: 1; }
}
.animate-pop { animation: pop .25s ease-out; }
</style>
<script>
function openDeleteAccountPopup() {
    document.getElementById("deleteAccountPopup").classList.remove("hidden");
}
function closeDeleteAccountPopup() {
    document.getElementById("deleteAccountPopup").classList.add("hidden");
}
</script>

</body>
</html>
