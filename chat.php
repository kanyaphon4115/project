<?php
session_start();
include("database/db_chat.php");

// ------------------ CHECK LOGIN ------------------
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_GET['to'] ?? 1;  // admin id

// ------------------ CREATE UPLOAD FOLDER ------------------
if (!is_dir("chat_uploads")) {
    mkdir("chat_uploads", 0777, true);
}

// ------------------ SEND MESSAGE ------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $msg = trim($_POST["message"]);
    $image_path = null;

    // ------------------ HANDLE IMAGE UPLOAD ------------------
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {

        $file = $_FILES["image"];
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png", "gif"];

        if (in_array($ext, $allowed)) {
            $filename = time() . "_" . rand(1000, 9999) . "." . $ext;
            $image_path = "chat_uploads/" . $filename;

            if (!move_uploaded_file($file["tmp_name"], $image_path)) {
                $image_path = null; // upload fail
            }
        }
    }

    // ------------------ INSERT INTO DATABASE ------------------
    $stmt = $con->prepare("
        INSERT INTO chat_messages (sender_id, receiver_id, message, pic)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("iiss", $user_id, $receiver_id, $msg, $image_path);
    $stmt->execute();

    header("Location: chat.php?to=" . $receiver_id);
    exit;
}

// ------------------ LOAD CHAT HISTORY ------------------
$chat = $con->query("
    SELECT * FROM chat_messages
    WHERE (sender_id=$user_id AND receiver_id=$receiver_id)
       OR (sender_id=$receiver_id AND receiver_id=$user_id)
    ORDER BY created_at ASC
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Chat</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 h-screen flex flex-col">

<!-- HEADER -->
<div class="bg-green-600 text-white p-4 flex items-center gap-3 shadow">
    <a href="homeped.php" class="text-2xl">←</a>
    <h2 class="text-xl font-bold">Chat with Admin</h2>
</div>

<!-- CHAT WINDOW -->
<div class="flex-1 overflow-y-auto p-4 space-y-4">

<?php while($m = $chat->fetch_assoc()): ?>

    <?php if ($m['sender_id'] == $user_id): ?>
        <!-- MY MESSAGE -->
        <div class="flex justify-end">
            <div class="max-w-xs bg-green-500 text-white p-3 rounded-2xl rounded-br-none shadow">

                <?php if (!empty($m['message'])): ?>
                    <p class="mb-2"><?= htmlspecialchars($m['message']) ?></p>
                <?php endif; ?>

                <?php if (!empty($m['pic']) && $m['pic'] !== "0"): ?>
                    <img src="<?= $m['pic'] ?>" 
                         class="rounded-lg max-w-[180px] mt-1 object-cover shadow-md">
                <?php endif; ?>

            </div>
        </div>

    <?php else: ?>
        <!-- ADMIN MESSAGE -->
        <div class="flex justify-start">
            <div class="max-w-xs bg-white p-3 rounded-2xl rounded-bl-none shadow">

                <?php if (!empty($m['message'])): ?>
                    <p class="mb-2"><?= htmlspecialchars($m['message']) ?></p>
                <?php endif; ?>

                <?php if (!empty($m['pic']) && $m['pic'] !== "0"): ?>
                    <img src="<?= $m['pic'] ?>" 
                         class="rounded-lg max-w-[180px] mt-1 object-cover shadow-md">
                <?php endif; ?>

            </div>
        </div>

    <?php endif; ?>

<?php endwhile; ?>

</div>

<!-- INPUT AREA -->
<form method="POST" enctype="multipart/form-data" class="p-4 bg-white flex flex-col gap-3 shadow">

    <!-- IMAGE PREVIEW -->
    <div id="imagePreviewBox"
         class="hidden bg-gray-200 p-3 rounded-xl flex items-center gap-3 relative">

        <img id="previewImage" src="" class="w-14 h-14 rounded-lg object-cover">

        <span class="text-gray-800">รูปภาพที่เลือก</span>

        <button type="button" id="removeImage"
                class="absolute right-3 top-3 text-xl font-bold text-gray-500 hover:text-red-500">✕</button>
    </div>

    <div class="flex items-center gap-3">

        <!-- IMAGE BUTTON -->
        <label class="cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="gray" width="28" height="28"
                viewBox="0 0 24 24">
                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 
                         0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 
                         0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 
                         12l4.5 6H5l3.5-4.5z"/>
            </svg>
            <input type="file" id="imageInput" name="image" class="hidden" accept="image/*">
        </label>

        <!-- TEXT INPUT -->
        <input type="text" name="message"
               class="flex-1 p-3 border rounded-xl"
               placeholder="พิมพ์ข้อความ...">

        <!-- SEND BUTTON -->
        <button class="bg-green-600 text-white px-6 py-2 rounded-xl">ส่ง</button>
    </div>
</form>

<!-- SCRIPT FOR IMAGE PREVIEW -->
<script>
const imageInput = document.getElementById("imageInput");
const previewBox = document.getElementById("imagePreviewBox");
const previewImage = document.getElementById("previewImage");
const removeImage = document.getElementById("removeImage");

// SHOW PREVIEW
imageInput.addEventListener("change", () => {
    const file = imageInput.files[0];
    if (!file) return;

    previewImage.src = URL.createObjectURL(file);
    previewBox.classList.remove("hidden");
});

// REMOVE IMAGE
removeImage.addEventListener("click", () => {
    imageInput.value = "";
    previewImage.src = "";
    previewBox.classList.add("hidden");
});
</script>

</body>
</html>
