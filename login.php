<?php
session_start();
include("backend/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบ');history.back();</script>";
        exit;
    }

    $stmt = $con->prepare("SELECT id, pass FROM form WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        echo "<script>alert('ไม่พบบัญชีนี้');history.back();</script>";
        exit;
    }

    $row = $result->fetch_assoc();
    $stored = (string)$row['pass'];         // รหัสใน DB
    $storedTrim = trim($stored);            // กันช่องว่าง
    $passTrim = trim($password);

    // เช็กว่าเป็น hash ไหม (ชัวร์)
    $info = password_get_info($storedTrim);
    $isHashed = ($info['algo'] !== 0);

    $ok = false;

    if ($isHashed) {
        // hash -> verify
        if (password_verify($passTrim, $storedTrim)) {
            $ok = true;
        }
    } else {
        // plain -> เทียบตรง ๆ แบบกัน timing attack
        if (hash_equals($storedTrim, $passTrim)) {
            $ok = true;

            // อัปเกรดเป็น hash ทันที
            $newHash = password_hash($passTrim, PASSWORD_DEFAULT);
            $up = $con->prepare("UPDATE form SET pass=? WHERE id=?");
            $up->bind_param("si", $newHash, $row['id']);
            $up->execute();
        }
    }

    if ($ok) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['email'] = $email;
        header("Location: homepage.php");
        exit;
    }

    echo "<script>alert('รหัสผ่านไม่ถูกต้อง');history.back();</script>";
    exit;
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
            <img src="assets/images/dog_popup.png"
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
                    <input type="email" name="email"
                        class="w-full py-3 pl-12 pr-4 rounded-full bg-white shadow"
                        placeholder="E-mail" required>
                    <span class="absolute left-4 top-2.5 text-gray-500 text-xl">📧</span>
                </div>

                <div class="relative">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="w-full py-3 pl-12 pr-12 rounded-full bg-white shadow"
                        placeholder="Password"
                        required>

                    <span class="absolute left-4 top-2.5 text-gray-500 text-xl">🔒</span>

                    <button type="button" id="togglePassword"
                        class="absolute right-4 top-3.5 z-20 pointer-events-auto text-black">

                        <!-- ตาเปิด -->
                        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>

                        <!-- ตาปิด (มีรูปตา + เส้นทับ) -->
                        <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" class="hidden">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" />
                            <circle cx="12" cy="12" r="3" />
                            <path d="M1 1l22 22" />
                        </svg>
                    </button>

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
            </div>

        </div>

    </div>

</body>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeOpen = document.getElementById('eyeOpen');
        const eyeClosed = document.getElementById('eyeClosed');

        toggleBtn.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        });
    });
</script>

</html>