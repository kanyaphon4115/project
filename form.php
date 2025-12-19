<?php
session_start();
include("backend/db_form.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];

// โหลดรูปโปรไฟล์
$avatar_src = null;
$matches = glob(__DIR__ . "/uploads/avatar_user_$uid.*");
if (!empty($matches)) {
    $avatar_src = "uploads/" . basename($matches[0]);
}

// โหลดฟอร์มเดิม
$form = $con->query("SELECT * FROM adopt_forms WHERE user_id = $uid LIMIT 1")->fetch_assoc();
$edit_mode = $form ? true : false;

// SAVE ฟอร์ม
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullname     = trim($_POST['fullname'] ?? '');
    $address      = trim($_POST['address'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $area         = trim($_POST['area'] ?? '');
    $experience   = trim($_POST['experience'] ?? '');
    $time_home    = trim($_POST['time_home'] ?? '');
    $reason       = trim($_POST['reason'] ?? '');
    $family_agree = trim($_POST['family_agree'] ?? '');
    $care_time    = trim($_POST['care_time'] ?? '');

    // สำคัญ: ให้ใช้ตัวเดียวกัน
    $uid = (int)($_SESSION['user_id'] ?? 0);

    // สำคัญ: dog_id ต้องมีค่า (มาจาก GET หรือ hidden input)
    $dog_id = (int)($_POST['dog_id'] ?? ($_GET['add_dog'] ?? 0));

    if ($edit_mode) {
        // UPDATE (10 ตัวแปร = 9 ช่อง + WHERE)
        $stmt = $con->prepare("
            UPDATE adopt_forms SET
                fullname=?, address=?, phone=?, area=?, experience=?, time_home=?, reason=?, family_agree=?, care_time=?
            WHERE user_id=? AND dog_id=?
        ");

        $stmt->bind_param(
            "sssssssssii",
            $fullname,
            $address,
            $phone,
            $area,
            $experience,
            $time_home,
            $reason,
            $family_agree,
            $care_time,
            $uid,
            $dog_id
        );
        // หมายเหตุ: "sssssssssi i" เว้นวรรคไม่ได้ ถ้าเอาไปใช้ให้ลบช่องว่างเป็น:
        // "sssssssssii"
    } else {
        // INSERT (11 ตัวแปร = 11 คอลัมน์)
        $stmt = $con->prepare("
            INSERT INTO adopt_forms
            (user_id, dog_id, fullname, address, phone, area, experience, time_home, reason, family_agree, care_time)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iisssssssss",
            $uid,
            $dog_id,
            $fullname,
            $address,
            $phone,
            $area,
            $experience,
            $time_home,
            $reason,
            $family_agree,
            $care_time
        );
    }

    if ($stmt->execute()) {
        header("Location: index.php?form=saved");
        exit;
    } else {
        $error_message = "เกิดข้อผิดพลาด: " . $stmt->error;
    }
}


function inputValue($form, $field)
{
    return $form[$field] ?? "";
}

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawHome - Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f7d7a3] min-h-screen">

    <!-- BG (เบลเฉพาะพื้นหลัง) -->
    <div class="fixed inset-0 -z-10">
        <div class="absolute inset-0 bg-repeat"
            style="background-image:url('assets/images/bg_pethome_pattern.jpg'); background-size:300px;"></div>

        <!-- ถ้าอยากให้จางลง -->
        <div class="absolute inset-0 bg-white/70"></div>

        <!-- เบลเฉพาะพื้นหลัง -->
        <div class="absolute inset-0 backdrop-blur-sm"></div>
    </div>

    <!-- NAVBAR -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <!-- ================= FORM CONTENT ================= -->
    <div class="w-full mx-auto mt-20 sm:mt-28 mb-10 px-2 sm:px-6">
        <div class="w-full max-w-none p-4 sm:p-8 bg-white/80 backdrop-blur-lg shadow-xl rounded-2xl">

            <div class="text-center mb-6 sm:mb-8">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#2f5d31]">
                    <?= $edit_mode ? "แก้ไขข้อมูลฟอร์มการรับเลี้ยงสุนัข" : "แบบฟอร์มรับเลี้ยงสุนัข" ?>
                </h2>
                <p class="text-gray-700 text-sm sm:text-base">
                    <?= $edit_mode ? "คุณสามารถแก้ไขได้ตลอดเวลา" : "กรุณากรอกข้อมูลตามความจริง" ?>
                </p>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="p-4 bg-red-200 text-red-800 rounded-xl mb-4">
                    <?= $error_message ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5 sm:space-y-6">

                <!-- FULL NAME -->
                <div>
                    <label class="font-semibold text-[#2f5d31]">ชื่อ-นามสกุล</label>
                    <input type="text" name="fullname" required
                        value="<?= inputValue($form, 'fullname') ?>"
                        class="w-full p-3 sm:p-4 text-base rounded-xl border bg-[#FAEED1]">
                </div>

                <!-- ADDRESS -->
                <div>
                    <label class="font-semibold text-[#2f5d31]">ที่อยู่</label>
                    <textarea name="address" required rows="3"
                        class="w-full p-3 sm:p-4 text-base rounded-xl border bg-[#FAEED1]"><?= inputValue($form, 'address') ?></textarea>
                </div>

                <!-- PHONE -->
                <div>
                    <label class="font-semibold text-[#2f5d31]">เบอร์โทร</label>
                    <input type="tel" name="phone" required
                        value="<?= inputValue($form, 'phone') ?>"
                        class="w-full p-3 sm:p-4 text-base rounded-xl border bg-[#FAEED1]">
                </div>

                <!-- AREA -->
                <div>
                    <label class="font-semibold text-[#2f5d31]">พื้นที่เลี้ยงดู</label>
                    <textarea name="area" required rows="2"
                        class="w-full p-3 sm:p-4 text-base rounded-xl border bg-[#FAEED1]"><?= inputValue($form, 'area') ?></textarea>
                </div>

                <!-- EXPERIENCE -->
                <div>
                    <label class="font-semibold text-[#2f5d31]">ประสบการณ์เลี้ยงสัตว์</label>
                    <select name="experience" required
                        class="w-full p-3 sm:p-4 text-base rounded-xl border bg-[#FAEED1]">
                        <option value="">เลือกตัวเลือก</option>
                        <option value="ไม่มีประสบการณ์" <?= inputValue($form, 'experience') == "ไม่มีประสบการณ์" ? "selected" : "" ?>>ไม่มีประสบการณ์</option>
                        <option value="เคยเลี้ยงมาก่อน" <?= inputValue($form, 'experience') == "เคยเลี้ยงมาก่อน" ? "selected" : "" ?>>เคยเลี้ยงมาก่อน</option>
                        <option value="กำลังเลี้ยงอยู่แล้ว" <?= inputValue($form, 'experience') == "กำลังเลี้ยงอยู่แล้ว" ? "selected" : "" ?>>กำลังเลี้ยงอยู่แล้ว</option>
                    </select>
                </div>

                <!-- HOURS -->
                <div>
                    <label class="font-semibold text-[#2f5d31]">คุณอยู่บ้านกี่ชั่วโมงต่อวัน?</label>
                    <input type="number" name="time_home" min="0" required
                        value="<?= inputValue($form, 'time_home') ?>"
                        class="w-full p-3 sm:p-4 text-base rounded-xl border bg-[#FAEED1]">
                </div>

                <!-- REASON -->
                <div>
                    <label class="font-semibold text-[#2f5d31]">เหตุผลที่ต้องการรับเลี้ยง</label>
                    <textarea name="reason" required rows="3"
                        class="w-full p-3 sm:p-4 text-base rounded-xl border bg-[#FAEED1]"><?= inputValue($form, 'reason') ?></textarea>
                </div>

                <!-- FAMILY AGREE -->
                <div>
                    <label class="font-semibold text-[#2f5d31]">สมาชิกในบ้านเห็นด้วยหรือไม่?</label>
                    <?php $agree = inputValue($form, 'family_agree'); ?>

                    <div class="mt-2 flex flex-col sm:flex-row gap-3 sm:gap-6">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="family_agree" value="Yes" <?= $agree == "Yes" ? "checked" : "" ?>>
                            เห็นด้วย
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="family_agree" value="No" <?= $agree == "No" ? "checked" : "" ?>>
                            ไม่เห็นด้วย
                        </label>
                    </div>
                </div>

                <!-- CARE TIME -->
                <div>
                    <label class="font-semibold text-[#2f5d31]">เวลาที่ดูแลสุนัข</label>
                    <input type="text" name="care_time" required
                        value="<?= inputValue($form, 'care_time') ?>"
                        class="w-full p-3 sm:p-4 text-base rounded-xl border bg-[#FAEED1]">
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="flex justify-center">
                    <button type="submit"
                        class="w-full sm:w-auto bg-green-600 text-white px-12 py-3 rounded-xl font-bold hover:bg-green-700">
                        <?= $edit_mode ? "บันทึกการแก้ไข" : "บันทึกข้อมูล" ?>
                    </button>
                </div>

            </form>

        </div>
    </div>

</body>

</html>