<?php
session_start();

$form_id = intval($_GET['id'] ?? 0);
$new_status = $_GET['status'] ?? null;

// ตรวจสอบความถูกต้อง
if (!$form_id || !$new_status) {
    die("Invalid request");
}

// ใช้ path ที่ถูกต้องในโฟลเดอร์ admin
$status_file = __DIR__ . "/status.json";

// โหลดข้อมูลสถานะเก่า
if (file_exists($status_file)) {
    $status_list = json_decode(file_get_contents($status_file), true);
    if (!is_array($status_list)) {
        $status_list = [];
    }
} else {
    $status_list = [];
}

// อัปเดตสถานะใหม่
$status_list[$form_id] = $new_status;

// บันทึกลง JSON
file_put_contents($status_file, json_encode($status_list, JSON_PRETTY_PRINT));

// redirect กลับไปหน้า admin
header("Location: admin_status_list.php?updated=1");
exit;
?>
