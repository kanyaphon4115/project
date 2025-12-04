<?php
session_start();
include("database/db.php");

// เมื่อผู้ใช้ส่งฟอร์มรีเซ็ตรหัสผ่าน
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm  = $_POST["confirm_password"];

    // ตรวจสอบรหัสผ่านตรงกัน
    if ($password !== $confirm) {
        echo "<script>alert('รหัสผ่านไม่ตรงกัน');history.back();</script>";
        exit;
    }

    // อัปเดตรหัสผ่านใหม่
    $stmt = $con->prepare("UPDATE form SET pass=? WHERE email=?");
    $stmt->bind_param("ss", $password, $email);

    if ($stmt->execute()) {
        echo "<script>alert('เปลี่ยนรหัสผ่านสำเร็จ!');window.location='login.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด กรุณาลองใหม่');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Reset Password</title>
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
<a href="login.php" 
   class="absolute top-6 left-6 bg-[#d9c29c] p-3 rounded-full shadow-md text-xl">
   ←
</a>

<!-- CENTER -->
<div class="flex justify-center pt-24 px-6">

    <!-- BOX -->
    <div class="w-full max-w-md bg-[#e8c99a] bg-opacity-60 rounded-3xl shadow-xl p-8 relative">

        <!-- DOG IMAGE -->
        <img src="dog1.png" 
             class="w-32 absolute inset-x-0 mx-auto -top-20 drop-shadow-lg animate-[floatDog_3s_ease-in-out_infinite]">

        <!-- TITLE -->
        <h2 class="text-center text-2xl font-extrabold text-gray-800 mt-10">รีเซ็ตรหัสผ่าน</h2>
        <p class="text-center text-gray-700 mb-6">ตั้งรหัสผ่านใหม่เพื่อกลับเข้าสู่ระบบ</p>

        <!-- FORM -->
        <form method="POST" class="space-y-5">

            <!-- EMAIL -->
            <div class="relative">
                <input type="email" name="email"
                    class="w-full py-3 pl-12 pr-5 rounded-full bg-white shadow"
                    placeholder="อีเมลที่ใช้สมัคร" required>
                <span class="absolute left-4 top-3.5 text-xl">📧</span>
            </div>

            <!-- PASSWORD -->
            <div class="relative">
                <input type="password" name="password" 
                    class="w-full py-3 pl-12 pr-5 rounded-full bg-white shadow"
                    placeholder="รหัสผ่านใหม่" required>
                <span class="absolute left-4 top-3.5 text-xl">🔒</span>
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="relative">
                <input type="password" name="confirm_password" 
                    class="w-full py-3 pl-12 pr-5 rounded-full bg-white shadow"
                    placeholder="ยืนยันรหัสผ่านใหม่" required>
                <span class="absolute left-4 top-3.5 text-xl">🔒</span>
            </div>

            <!-- BUTTON -->
            <button type="submit"
                class="w-full bg-green-600 text-white py-3 rounded-full text-lg font-semibold shadow-lg hover:bg-green-700 transition">
                เปลี่ยนรหัสผ่าน
            </button>

        </form>

        <!-- LINKS BELOW -->
        <div class="text-center mt-5">
            <a href="login.php" class="text-gray-700 hover:underline">
                ← กลับเข้าสู่ระบบ
            </a>
        </div>

    </div>
</div>

</body>
</html>
