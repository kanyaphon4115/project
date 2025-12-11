<?php
// ตรวจค่า
$form_id = $_GET['id'] ?? null;
$new_status = $_GET['status'] ?? null;

if (!$form_id || !$new_status) {
    die("Invalid request");
}

// Path ไฟล์ JSON
$status_file = "status.json";

// โหลดไฟล์เดิม
$status_list = [];

if (file_exists($status_file)) {
    $status_list = json_decode(file_get_contents($status_file), true);
}

// อัปเดตสถานะของฟอร์มนี้
$status_list[$form_id] = $new_status;

// เซฟกลับไป
file_put_contents($status_file, json_encode($status_list, JSON_PRETTY_PRINT));

// กลับไปหน้า Admin พร้อมแจ้งเตือน
header("Location: admin_status_list.php?updated=1");
exit;
?>
