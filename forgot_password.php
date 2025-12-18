<?php
session_start();
include("database/db.php");
require_once __DIR__ . "/components/mailer.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('อีเมลไม่ถูกต้อง');history.back();</script>";
        exit;
    }

    // หา user
    $stmt = $con->prepare("SELECT id FROM form WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    // กันคนเดา email: ไม่บอกว่าไม่มีบัญชี
    if ($res->num_rows !== 1) {
        $_SESSION['reset_email'] = $email;
        header("Location: verify_otp.php");
        exit;
    }

    $user_id = (int)$res->fetch_assoc()['id'];

    // สุ่ม OTP
$otp = (string)random_int(100000, 999999);
$otp_hash = password_hash($otp, PASSWORD_DEFAULT);

$ins = $con->prepare(
  "INSERT INTO password_resets (user_id, otp_hash, expires_at)
   VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))"
);
$ins->bind_param("is", $user_id, $otp_hash);
$ins->execute();


    // ส่งเมล
    if (!sendOtpMail($email, $otp)) {
        echo "<script>alert('ส่ง OTP ไม่สำเร็จ (เช็ก App Password)');history.back();</script>";
        exit;
    }

    $_SESSION['reset_email'] = $email;
    header("Location: verify_otp.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes floatDog{0%{transform:translateY(0)}50%{transform:translateY(-8px)}100%{transform:translateY(0)}}
  </style>
</head>

<body class="bg-[#f8d7a0] min-h-screen relative overflow-hidden">
  <a href="login.php" class="absolute top-6 left-6 bg-[#d9c29c] p-3 rounded-full shadow-md text-xl">←</a>

  <div class="flex justify-center pt-24 px-6">
    <div class="w-full max-w-md bg-[#e8c99a] bg-opacity-60 rounded-3xl shadow-xl p-8 relative">
      <img src="assets/images/dog_popup.png"
        class="w-32 absolute inset-x-0 mx-auto -top-20 drop-shadow-lg animate-[floatDog_3s_ease-in-out_infinite]">

      <h2 class="text-center text-2xl font-extrabold text-gray-800 mt-10">ลืมรหัสผ่าน</h2>
      <p class="text-center text-gray-700 mb-6">กรอกอีเมลเพื่อรับ OTP</p>

      <form method="POST" class="space-y-5">
        <div class="relative">
          <input type="email" name="email"
            class="w-full py-3 pl-12 pr-5 rounded-full bg-white shadow"
            placeholder="E-mail" required>
          <span class="absolute left-4 top-2.5 text-xl">📧</span>
        </div>

        <button type="submit"
          class="w-full bg-green-600 text-white py-3 rounded-full text-lg font-semibold shadow-lg hover:bg-green-700 transition">
          ขอ OTP
        </button>
      </form>
    </div>
  </div>
</body>
</html>
