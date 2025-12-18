<?php
session_start();
include("backend/db.php");

$email = $_SESSION['reset_email'] ?? '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? $email);
    $otp_in = trim($_POST["otp"] ?? "");

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('อีเมลไม่ถูกต้อง');history.back();</script>";
        exit;
    }

    // หา user
    $stmt = $con->prepare("SELECT id FROM form WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows !== 1) {
        echo "<script>alert('OTP ไม่ถูกต้องหรือหมดอายุ');history.back();</script>";
        exit;
    }
    $user_id = (int)$res->fetch_assoc()['id'];

    // OTP ล่าสุดที่ยังไม่หมดอายุ/ยังไม่ใช้
    $q = $con->prepare("
      SELECT id, otp_hash, attempts
      FROM password_resets
      WHERE user_id=? AND used=0 AND expires_at > NOW()
      ORDER BY id DESC LIMIT 1
    ");
    $q->bind_param("i", $user_id);
    $q->execute();
    $r = $q->get_result();
    if ($r->num_rows !== 1) {
        echo "<script>alert('OTP หมดอายุ/ไม่มี OTP กรุณาขอใหม่');window.location='forgot_password.php';</script>";
        exit;
    }
    $row = $r->fetch_assoc();

    if ((int)$row['attempts'] >= 5) {
        echo "<script>alert('ใส่ผิดเกิน 5 ครั้ง กรุณาขอ OTP ใหม่');window.location='forgot_password.php';</script>";
        exit;
    }

    if (!password_verify($otp_in, $row['otp_hash'])) {
        $upd = $con->prepare("UPDATE password_resets SET attempts = attempts + 1 WHERE id=?");
        $upd->bind_param("i", $row['id']);
        $upd->execute();

        echo "<script>alert('OTP ไม่ถูกต้อง');history.back();</script>";
        exit;
    }

    // ผ่าน OTP -> ไปตั้งรหัส
    $_SESSION['otp_verified_user_id'] = $user_id;
    $_SESSION['otp_reset_row_id'] = (int)$row['id'];

    header("Location: reset_password.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Verify OTP</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes floatDog{0%{transform:translateY(0)}50%{transform:translateY(-8px)}100%{transform:translateY(0)}}
  </style>
</head>

<body class="bg-[#f8d7a0] min-h-screen relative overflow-hidden">
  <a href="forgot_password.php" class="absolute top-6 left-6 bg-[#d9c29c] p-3 rounded-full shadow-md text-xl">←</a>

  <div class="flex justify-center pt-24 px-6">
    <div class="w-full max-w-md bg-[#e8c99a] bg-opacity-60 rounded-3xl shadow-xl p-8 relative">
      <img src="assets/images/dog_popup.png"
        class="w-32 absolute inset-x-0 mx-auto -top-20 drop-shadow-lg animate-[floatDog_3s_ease-in-out_infinite]">

      <h2 class="text-center text-2xl font-extrabold text-gray-800 mt-10">ยืนยัน OTP</h2>
      <p class="text-center text-gray-700 mb-6">กรอกรหัส OTP ที่ส่งไปทางอีเมล</p>

      <form method="POST" class="space-y-5">
        <!-- เพื่อให้ UI เดิม: โชว์อีเมลได้ แต่ล็อกไม่ให้แก้ -->
        <div class="relative">
          <input type="email" name="email"
            value="<?= htmlspecialchars($email) ?>"
            class="w-full py-3 pl-12 pr-5 rounded-full bg-white shadow"
            placeholder="E-mail" required readonly>
          <span class="absolute left-4 top-2.5 text-xl">📧</span>
        </div>

        <div class="relative">
          <input type="text" name="otp"
            class="w-full py-3 pl-12 pr-5 rounded-full bg-white shadow"
            placeholder="OTP 6 หลัก" required>
          <span class="absolute left-4 top-2.5 text-xl">🔢</span>
        </div>

        <button type="submit"
          class="w-full bg-green-600 text-white py-3 rounded-full text-lg font-semibold shadow-lg hover:bg-green-700 transition">
          ยืนยัน OTP
        </button>
      </form>
    </div>
  </div>
</body>
</html>
