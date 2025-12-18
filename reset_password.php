<?php
session_start();
include("backend/db.php");

// ต้องผ่าน OTP มาก่อน
$user_id = $_SESSION['otp_verified_user_id'] ?? null;
$reset_row_id = $_SESSION['otp_reset_row_id'] ?? null;
$email = $_SESSION['reset_email'] ?? '';

if (!$user_id || !$reset_row_id) {
    header("Location: forgot_password.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST["password"] ?? '';
    $confirm  = $_POST["confirm_password"] ?? '';

    if ($password === '' || $confirm === '') {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบ');history.back();</script>";
        exit;
    }

    if ($password !== $confirm) {
        echo "<script>alert('รหัสผ่านไม่ตรงกัน');history.back();</script>";
        exit;
    }

    // แนะนำให้เก็บแบบ hash
    $pass_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $con->prepare("UPDATE form SET pass=? WHERE id=?");
    $stmt->bind_param("si", $pass_hash, $user_id);

    if ($stmt->execute()) {
        // mark otp used
        $mark = $con->prepare("UPDATE password_resets SET used=1 WHERE id=?");
        $mark->bind_param("i", $reset_row_id);
        $mark->execute();

        // ล้าง session ของ reset
        unset($_SESSION['otp_verified_user_id'], $_SESSION['otp_reset_row_id'], $_SESSION['reset_email']);

        echo "<script>alert('เปลี่ยนรหัสผ่านสำเร็จ!');window.location='login.php';</script>";
        exit;
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด กรุณาลองใหม่');history.back();</script>";
        exit;
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
    <a href="login.php"
        class="absolute top-6 left-6 bg-[#d9c29c] p-3 rounded-full shadow-md text-xl">
        ←
    </a>

    <!-- CENTER -->
    <div class="flex justify-center pt-24 px-6">

        <!-- BOX -->
        <div class="w-full max-w-md bg-[#e8c99a] bg-opacity-60 rounded-3xl shadow-xl p-8 relative">

            <!-- DOG IMAGE -->
            <img src="assets/images/dog_popup.png"
                class="w-32 absolute inset-x-0 mx-auto -top-20 drop-shadow-lg animate-[floatDog_3s_ease-in-out_infinite]">

            <!-- TITLE -->
            <h2 class="text-center text-2xl font-extrabold text-gray-800 mt-10">รีเซ็ตรหัสผ่าน</h2>
            <p class="text-center text-gray-700 mb-6">ตั้งรหัสผ่านใหม่เพื่อกลับเข้าสู่ระบบ</p>

            <!-- FORM -->
            <form method="POST" class="space-y-5">

                <!-- EMAIL (readonly) -->
                <div class="relative">
                    <input type="email" name="email"
                        value="<?= htmlspecialchars($email) ?>"
                        class="w-full py-3 pl-12 pr-5 rounded-full bg-white shadow"
                        placeholder="E-mail" required readonly>
                    <span class="absolute left-4 top-2.5 text-xl">📧</span>
                </div>

                <!-- PASSWORD -->
                <div class="relative">
                    <input type="password" name="password"
                        class="password-input w-full py-3 pl-12 pr-12 rounded-full bg-white shadow"
                        placeholder="New password" required>
                    <span class="absolute left-4 top-2.5 text-gray-500 text-xl">🔒</span>

                    <button type="button"
                        class="toggle-password absolute right-4 top-3.5 z-30 pointer-events-auto text-black">
                        <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg class="eye-closed hidden" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" />
                            <circle cx="12" cy="12" r="3" />
                            <path d="M1 1l22 22" />
                        </svg>
                    </button>
                </div>

                <!-- CONFIRM PASSWORD -->
                <div class="relative">
                    <input type="password" name="confirm_password"
                        class="password-input w-full py-3 pl-12 pr-12 rounded-full bg-white shadow"
                        placeholder="Confirm password" required>
                    <span class="absolute left-4 top-2.5 text-gray-500 text-xl">🔒</span>

                    <button type="button"
                        class="toggle-password absolute right-4 top-3.5 z-30 pointer-events-auto text-black">
                        <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg class="eye-closed hidden" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" />
                            <circle cx="12" cy="12" r="3" />
                            <path d="M1 1l22 22" />
                        </svg>
                    </button>
                </div>

                <!-- BUTTON -->
                <button type="submit"
                    class="w-full bg-green-600 text-white py-3 rounded-full text-lg font-semibold shadow-lg hover:bg-green-700 transition">
                    เปลี่ยนรหัสผ่าน
                </button>

            </form>
        </div>
    </div>

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

</body>

</html>