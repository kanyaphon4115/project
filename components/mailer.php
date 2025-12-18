<?php
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/../vendor/autoload.php';

function sendOtpMail(string $toEmail, string $otp): bool {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // ✅ ใส่เมลผู้ส่ง
        $mail->Username = 'wefer6381@gmail.com';
        // ✅ ใส่ App Password 16 ตัว (ไม่มีเว้นวรรค)
        $mail->Password = 'lbpcyuxhjysoujls';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->CharSet = 'UTF-8';
        $mail->setFrom($mail->Username, 'PawHome');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = 'OTP รีเซ็ตรหัสผ่าน PawHome';
        $mail->Body = "
          <p>รหัส OTP ของคุณคือ</p>
          <div style='font-size:28px;font-weight:800;letter-spacing:3px'>{$otp}</div>
          <p>รหัสหมดอายุใน 10 นาที</p>
        ";

        return $mail->send();
    } catch (\Throwable $e) {
        return false;
    }
}
