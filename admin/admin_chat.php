<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);

$admin_id = 1; // ID ของ Admin
$con = new mysqli("localhost","root","","pet_home");

/* ===== GET USER LIST ===== */
$users = $con->query("
    SELECT DISTINCT 
        IF(sender_id=$admin_id, receiver_id, sender_id) AS user_id
    FROM chat_messages
    WHERE sender_id=$admin_id OR receiver_id=$admin_id
");

/* ===== CURRENT CHAT ===== */
$to = $_GET['to'] ?? null;

$messages = null;
if ($to) {
    $messages = $con->query("
        SELECT * FROM chat_messages
        WHERE (sender_id=$admin_id AND receiver_id=$to)
           OR (sender_id=$to AND receiver_id=$admin_id)
        ORDER BY created_at ASC
    ");
}

/* ===== SEND MESSAGE ===== */
if ($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['send'])) {
    $msg = $_POST['message'] ?? '';
    $pic = null;

    if (!empty($_FILES['image']['name'])) {
        if (!is_dir("chat_uploads")) mkdir("chat_uploads",0777,true);
        $pic = "chat_uploads/".time()."_".$_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'],$pic);
    }

    $stmt = $con->prepare("
        INSERT INTO chat_messages(sender_id,receiver_id,message,pic)
        VALUES (?,?,?,?)
    ");
    $stmt->bind_param("iiss",$admin_id,$to,$msg,$pic);
    $stmt->execute();

    header("Location: admin_chat.php?to=$to");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Admin Chat</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#fff7e6] min-h-screen flex">

<!-- SIDEBAR USERS -->
<div class="w-72 bg-white shadow-lg p-4">
    <h2 class="text-xl font-black text-orange-600 mb-4">💬 แชทผู้ใช้</h2>

    <?php while($u=$users->fetch_assoc()): ?>
        <a href="?to=<?= $u['user_id'] ?>"
           class="block p-3 rounded-xl mb-2
           <?= $to==$u['user_id']
               ? 'bg-orange-100 text-orange-600'
               : 'hover:bg-orange-50 text-gray-700' ?>">
           👤 User ID <?= $u['user_id'] ?>
        </a>
    <?php endwhile; ?>
</div>

<!-- CHAT AREA -->
<div class="flex-1 flex flex-col p-6">

<?php if(!$to): ?>
    <div class="text-center text-gray-500 mt-20">
        👈 เลือกผู้ใช้เพื่อเริ่มแชท
    </div>
<?php else: ?>

<!-- MESSAGES -->
<div class="flex-1 bg-white rounded-2xl shadow p-4 overflow-y-auto space-y-3">

<?php while($m=$messages->fetch_assoc()): ?>
<div class="flex <?= $m['sender_id']==$admin_id ? 'justify-end' : 'justify-start' ?>">
    <div class="max-w-xs p-3 rounded-xl
        <?= $m['sender_id']==$admin_id
            ? 'bg-orange-500 text-white'
            : 'bg-gray-200 text-gray-800' ?>">
        
        <?php if($m['message']): ?>
            <div><?= htmlspecialchars($m['message']) ?></div>
        <?php endif; ?>

        <?php if($m['pic']): ?>
            <img src="<?= $m['pic'] ?>" class="mt-2 rounded-lg max-h-40">
        <?php endif; ?>

        <div class="text-xs mt-1 opacity-70">
            <?= $m['created_at'] ?>
        </div>
    </div>
</div>
<?php endwhile; ?>

</div>

<!-- SEND FORM -->
<form method="POST" enctype="multipart/form-data"
      class="mt-4 flex gap-2">

<input name="message"
 class="flex-1 border rounded-xl px-4 py-2"
 placeholder="พิมพ์ข้อความ...">

<input type="file" name="image" class="border rounded-xl px-2">

<button name="send"
 class="bg-orange-500 hover:bg-orange-600 text-white px-6 rounded-xl">
 ส่ง
</button>

</form>

<?php endif; ?>
</div>

</body>
</html>
