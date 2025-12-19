<?php
session_start();
include("backend/db.php");  // ← ตรงตามที่คุณขอ

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- รับค่าจากฟอร์ม ---
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? ''; // ← แก้ให้ตรงชื่อฟอร์ม 100%

    // --- ตรวจสอบรหัสผ่าน ---
    if ($password !== $confirm) {
        echo "<script>alert('❌ รหัสผ่านไม่ตรงกัน');history.back();</script>";
        exit;
    }

    // --- ตรวจสอบว่าอีเมลซ้ำหรือไม่ ---
    $check = $con->prepare("SELECT id FROM form WHERE email=? LIMIT 1");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "<script>alert('❌ อีเมลนี้ถูกใช้แล้ว');history.back();</script>";
        exit;
    }
    $check->close();

    // --- บันทึกข้อมูล (ตรงกับตาราง form: email, pass, gender) ---
    $stmt = $con->prepare("INSERT INTO form (email, pass) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $password);

    if ($stmt->execute()) {
        echo "<script>alert('✅ สมัครสมาชิกสำเร็จ!');window.location='login.php';</script>";
    } else {
        echo "<script>alert('❌ บันทึกไม่สำเร็จ: {$con->error}');history.back();</script>";
    }

    $stmt->close();
    $con->close();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @keyframes floatDog {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }

            100% {
                transform: translateY(0px);
            }
        }
    </style>
</head>

<body class="bg-[#f8d7a0] min-h-screen relative overflow-hidden flex items-center justify-center px-3 sm:px-6 py-8 sm:py-12">


    <a href="index.php"
        class="absolute top-4 left-4 sm:top-6 sm:left-6 bg-[#d9c29c] p-2.5 sm:p-3 rounded-full shadow-md text-lg sm:text-xl">
        ←
    </a>

    <!-- CENTER CONTAINER -->
    <div class="w-full max-w-[420px] sm:max-w-md relative">

        <!-- FORM BOX -->
        <div class="w-full bg-[#e8c99a] bg-opacity-60 rounded-3xl shadow-xl p-6 sm:p-8 relative">


            <!-- DOG ON TOP -->
            <img src="assets/images/dog_popup.png"
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
            <form method="POST" class="mt-8 sm:mt-10 space-y-6">

    <!-- EMAIL -->
    <div class="relative">
        <input type="email" name="email"
            class="w-full py-3 pl-12 pr-4 rounded-full bg-white shadow"
            placeholder="E-mail" required>
        <span class="absolute left-4 top-2.5 text-gray-500 text-xl">📧</span>
    </div>

    <!-- PASSWORD -->
    <div class="relative">
        <input
            type="password"
            name="password"
            id="password"
            class="w-full py-3 pl-12 pr-12 rounded-full bg-white shadow"
            placeholder="Password"
            required>

        <span class="absolute left-4 top-2.5 text-gray-500 text-xl">🔒</span>

        <!-- ลืมรหัสผ่าน (ขวาล่างของช่องรหัส) -->
        <a href="forgot_password.php"
           class="absolute right-4 -bottom-6 text-green-700 text-sm font-semibold hover:underline">
            ลืมรหัสผ่าน?
        </a>
    </div>


                      <button type="submit"
    class="w-[70%] mx-auto mt-8 block bg-green-600 text-white py-3 rounded-full text-lg font-semibold shadow-lg hover:bg-green-700 transition">
    Sign Up
</button>


            </form>

            <!-- LINKS UNDER BUTTON -->
            <div class="text-center mt-5">
                <p class="text-gray-700">
                    มีบัญชีแล้ว?
                    <a href="login.php" class="text-green-700 font-semibold hover:underline">
                        เข้าสู่ระบบ
                    </a>
                </p>
            </div>

        </div>

    </div>

</body>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.toggle-password').forEach(btn => {
            const wrapper = btn.closest('.relative');
            const input = wrapper.querySelector('.password-input');
            const eyeOpen = btn.querySelector('.eye-open');
            const eyeClosed = btn.querySelector('.eye-closed');

            btn.addEventListener('click', () => {
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                eyeOpen.classList.toggle('hidden', show);
                eyeClosed.classList.toggle('hidden', !show);
            });
        });
    });
</script>


</html>
