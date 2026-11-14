<?php
// delete.php
session_start();
require_once 'config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Contact ID missing. <a href='index.php'>Go back</a>");
}

// Lấy thông tin để hiển thị cảnh báo
$stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->execute([$id]);
$contact = $stmt->fetch();

if (!$contact) {
    die("Contact not found. <a href='index.php'>Go back</a>");
}

// Nếu người dùng bấm POST xác nhận xóa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $pdo->prepare("DELETE FROM contacts WHERE id = ?")->execute([$id]);
    
    $_SESSION['flash'] = "Contact deleted successfully!";
    $_SESSION['flash_type'] = "success";
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Delete Contact</title>
    <style>
        body{font-family:Arial;background:#f4f4f9;padding:20px;}
        .container{max-width:500px;margin:auto;background:white;padding:30px;border-radius:8px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        .btn{padding:10px 20px;border:none;border-radius:4px;cursor:pointer;font-weight:bold;font-size:16px;}
        .btn-delete{background:#dc3545;color:white;}
        .btn-cancel{background:#6c757d;color:white;text-decoration:none;margin-left:10px;padding:10px 20px;border-radius:4px;}
        .details { margin: 20px 0; padding: 15px; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; text-align: left;}
    </style>
</head>
<body>
<div class="container">
    <h2 style="color:#dc3545;">Confirm Deletion</h2>
    <p>Are you sure you want to delete this contact?</p>
    
    <div class="details">
        <strong>Name:</strong> <?= htmlspecialchars($contact['name']) ?><br>
        <strong>Email:</strong> <?= htmlspecialchars($contact['email']) ?><br>
        <strong>Group:</strong> <?= htmlspecialchars($contact['group_name']) ?>
    </div>

    <form method="POST">
        <button type="submit" name="confirm" class="btn btn-delete">Yes, Delete Contact</button>
        <a href="index.php" class="btn-cancel">Cancel</a>
    </form>
</div>
</body>
</html>