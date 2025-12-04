<?php
session_start();
include("database/db_chat.php"); // ฐานข้อมูลเดียวกับ project เดิม

// ------------------ CHECK LOGIN ------------------
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_GET['to'] ?? 1; // ค่า default แชทกับ Admin (ID = 1)

// ------------------ SEND MESSAGE ------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $msg = trim($_POST["message"]);

    if ($msg != "") {
        $stmt = $con->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message)
                               VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $receiver_id, $msg);
        $stmt->execute();
    }

    header("Location: chat.php?to=" . $receiver_id);
    exit;
}

// ------------------ LOAD MESSAGES ------------------
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

<!-- CHAT LIST -->
<div class="flex-1 overflow-y-auto p-4 space-y-3">

<?php while($m = $chat->fetch_assoc()): ?>

    <?php if ($m['sender_id'] == $user_id): ?>
        <!-- MY MESSAGE -->
        <div class="flex justify-end">
            <div class="bg-green-500 text-white px-4 py-2 rounded-2xl rounded-br-none max-w-xs">
                <?= htmlspecialchars($m['message']) ?>
            </div>
        </div>
    <?php else: ?>
        <!-- OTHER MESSAGE -->
        <div class="flex justify-start">
            <div class="bg-white px-4 py-2 rounded-2xl rounded-bl-none shadow max-w-xs">
                <?= htmlspecialchars($m['message']) ?>
            </div>
        </div>
    <?php endif; ?>

<?php endwhile; ?>

</div>

<!-- INPUT BOX -->
<form method="POST" class="p-4 bg-white flex gap-2 shadow">
    <input type="text" name="message" 
           class="flex-1 p-3 border rounded-xl"
           placeholder="พิมพ์ข้อความ..."
           required>
    <button class="bg-green-600 text-white px-5 rounded-xl">ส่ง</button>
</form>

</body>
</html>
