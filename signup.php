<?php
// =======================
// เชื่อมต่อฐานข้อมูล
// =======================
include "database/db.php";

// ถ้าฟอร์มถูกส่งมาให้ประมวลผล
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // ตรวจสอบช่องว่าง
    if (empty($email) || empty($password) || empty($confirm)) {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบ');</script>";
    }

    // ตรวจสอบ password ตรงกันไหม
    elseif ($password != $confirm) {
        echo "<script>alert('รหัสผ่านไม่ตรงกัน');</script>";
    }

    else {
        // เข้ารหัส password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // SQL
        $sql = "INSERT INTO users (email, password) VALUES ('$email', '$hash')";

        if (mysqli_query($con, $sql)) {
            echo "<script>alert('สมัครสมาชิกสำเร็จ!'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('สมัครไม่สำเร็จ อีเมลนี้ถูกใช้แล้ว');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sign Up</title>
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

    <!-- RIGHT IMAGE -->
    <img src="side-img.png" 
         class="absolute right-0 top-0 h-full object-cover opacity-90 pointer-events-none">

    <!-- BACK BUTTON -->
    <a href="index.php" 
       class="absolute top-6 left-6 bg-[#d9c29c] p-3 rounded-full shadow-md text-xl">
       ←
    </a>

    <!-- CENTER CONTAINER -->
    <div class="flex justify-center pt-24 px-6">

        <!-- FORM BOX -->
        <div class="w-full max-w-md bg-[#e8c99a] bg-opacity-60 rounded-3xl shadow-xl p-8 relative">

            <!-- DOG IMAGE -->
            <img src="dog1.png"
                class="w-32 absolute inset-x-0 mx-auto -top-20 z-20 drop-shadow-lg animate-[floatDog_3s_ease-in-out_infinite]">

            <!-- TAB -->
            <div class="relative w-full bg-white rounded-full shadow-inner p-1 flex justify-between items-center mt-14">
                <a href="login.php" 
                    class="flex-1 text-center py-3 rounded-full text-gray-700 font-semibold hover:bg-gray-100 transition">
                    Login
                </a>

                <a href="signup.php" 
                    class="flex-1 text-center py-3 rounded-full bg-[#f6d69e] text-gray-900 font-bold">
                    Sign Up
                </a>
            </div>

            <!-- FORM -->
            <form action="" method="POST" class="mt-10 space-y-5">

                <!-- EMAIL -->
                <div class="relative">
                    <input type="text" name="email"
                        class="w-full py-3 pl-12 pr-4 rounded-full bg-white shadow focus:ring-2 focus:ring-yellow-400"
                        placeholder="E mail / username" required>
                    <span class="absolute left-4 top-3.5 text-gray-500 text-xl">📧</span>
                </div>

                <!-- PASSWORD -->
                <div class="relative">
                    <input type="password" name="password"
                        class="w-full py-3 pl-12 pr-10 rounded-full bg-white shadow focus:ring-2 focus:ring-yellow-400"
                        placeholder="Password" required>
                    <span class="absolute left-4 top-3.5 text-gray-500 text-xl">🔒</span>
                </div>

                <!-- CONFIRM PASSWORD -->
                <div class="relative">
                    <input type="password" name="confirm_password"
                        class="w-full py-3 pl-12 pr-10 rounded-full bg-white shadow focus:ring-2 focus:ring-yellow-400"
                        placeholder="Confirm password" required>
                    <span class="absolute left-4 top-3.5 text-gray-500 text-xl">🔒</span>
                </div>

                <!-- SUBMIT BUTTON -->
                <button type="submit"
                    class="w-full bg-green-600 text-white py-3 rounded-full text-lg font-semibold shadow-lg hover:bg-green-700 transition">
                    Sign Up
                </button>

            </form>
        </div>

    </div>

</body>
</html>
