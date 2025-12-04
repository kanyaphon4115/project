<?php
session_start();
include("database/db.php");

// เมื่อกดปุ่ม Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // ตรวจสอบช่องว่าง
    if (empty($email) || empty($password)) {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบ');history.back();</script>";
        exit;
    }

    // ค้นหาผู้ใช้ในฐานข้อมูล
    $stmt = $con->prepare("SELECT id, pass FROM form WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $row = $result->fetch_assoc();

        // ตรวจรหัสผ่าน (ไม่เข้ารหัส)
        if ($password === $row['pass']) {

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $email;

            header('Location: homepage.php');
            exit;

        } else {
            echo "<script>alert('รหัสผ่านไม่ถูกต้อง');history.back();</script>";
            exit;
        }

    } else {
        echo "<script>alert('ไม่พบบัญชีนี้');history.back();</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
@keyframes floatDog {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-8px); }
  100% { transform: translateY(0px); }
}
</style>
</head>

<body class="bg-[#f8d7a0] min-h-screen relative overflow-hidden">

    <!-- BACK BUTTON -->
    <a href="index.php" 
       class="absolute top-6 left-6 bg-[#d9c29c] p-3 rounded-full shadow-md text-xl">
       ←
    </a>

    <!-- CENTER CONTAINER -->
    <div class="flex justify-center pt-24 px-6">

        <!-- FORM BOX -->
<div class="w-full max-w-md bg-[#e8c99a] bg-opacity-60 rounded-3xl shadow-xl p-8 relative">

    <!-- DOG TOP IMAGE -->
<img src="dog1.png"
     class="w-32 absolute inset-x-0 mx-auto -top-20 z-20 drop-shadow-lg animate-[floatDog_3s_ease-in-out_infinite]">

    <!-- TAB -->
    <div class="relative w-full bg-white rounded-full shadow-inner p-1 flex justify-between items-center mt-14">
        <a href="login.php" 
            class="flex-1 text-center py-3 rounded-full bg-[#f6d69e] text-gray-900 font-bold">
            Login
        </a>

        <a href="signup.php" 
            class="flex-1 text-center py-3 rounded-full text-gray-700 font-semibold hover:bg-gray-100 transition">
            Sign Up
        </a>
    </div>

    <!-- LOGIN FORM -->
    <form method="POST" class="mt-10 space-y-5">

        <div class="relative">
            <input type="text" name="email"
                class="w-full py-3 pl-12 pr-4 rounded-full bg-white shadow"
                placeholder="E-mail / username" required>
            <span class="absolute left-4 top-3.5 text-gray-500 text-xl">📧</span>
        </div>

        <div class="relative">
            <input type="password" name="password"
                class="w-full py-3 pl-12 pr-10 rounded-full bg-white shadow"
                placeholder="Password" required>
            <span class="absolute left-4 top-3.5 text-gray-500 text-xl">🔒</span>
        </div>
<div class="flex justify-end -mt-3">
    <a href="forgot_password.php" class="text-green-700 text-sm font-semibold hover:underline">
        ลืมรหัสผ่าน?
    </a>
</div>

        <button type="submit"
            class="w-full bg-green-600 text-white py-3 rounded-full text-lg font-semibold shadow-lg hover:bg-green-700 transition">
            เข้าสู่ระบบ
        </button>
    </form>

    <!-- UNDER FORM LINKS -->
    <div class="text-center mt-5">
        <p class="text-gray-700">
            ยังไม่มีบัญชี?
            <a href="signup.php" class="text-green-700 font-semibold hover:underline">
                สมัครสมาชิก
            </a>
        </p>

        <a href="index.php" class="text-gray-600 hover:text-gray-900 hover:underline mt-2 inline-block">
            ← กลับหน้าแรก
        </a>
    </div>

</div>

</div>

</body>
</html>
