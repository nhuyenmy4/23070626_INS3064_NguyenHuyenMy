<?php
// edit.php
session_start();
require_once 'config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Contact ID missing. <a href='index.php'>Go back</a>");
}

$stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->execute([$id]);
$contact = $stmt->fetch();

if (!$contact) {
    die("Contact not found. <a href='index.php'>Go back</a>");
}

$name = $contact['name'];
$email = $contact['email'];
$phone = $contact['phone'];
$group_name = $contact['group_name'];
$notes = $contact['notes'];
$errors = [];
$groups = ['Family', 'Friends', 'Work', 'Other', 'General'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $group_name = $_POST['group_name'];
    $notes = trim($_POST['notes']);

    if (empty($name)) $errors[] = "Name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid Email is required.";
    else {
        // Kiểm tra email trùng (ngoại trừ chính nó)
        $chk = $pdo->prepare("SELECT id FROM contacts WHERE email = ? AND id != ?");
        $chk->execute([$email, $id]);
        if ($chk->fetch()) $errors[] = "Email already exists.";
    }

    if (empty($errors)) {
        $sql = "UPDATE contacts SET name=?, email=?, phone=?, group_name=?, notes=? WHERE id=?";
        $pdo->prepare($sql)->execute([$name, $email, $phone, $group_name, $notes, $id]);
        
        $_SESSION['flash'] = "Contact updated successfully!";
        $_SESSION['flash_type'] = "success";
        header("Location: index.php");
        exit;
    }
}
?>
<!-- Phần HTML tương tự y hệt file add.php ở trên, bạn chỉ cần thay thẻ h2 thành "Edit Contact" và copy form xuống -->
<!DOCTYPE html>
<html lang="vi">
<head><title>Edit Contact</title><style>body{font-family:Arial;background:#f4f4f9;padding:20px;}.container{max-width:500px;margin:auto;background:white;padding:20px;border-radius:8px;}.form-group{margin-bottom:15px;}.form-group label{display:block;margin-bottom:5px;font-weight:bold;}.form-group input,.form-group select,.form-group textarea{width:100%;padding:8px;box-sizing:border-box;}.error-box{background:#f8d7da;color:#721c24;padding:10px;margin-bottom:15px;border:1px solid #f5c6cb;}.btn{padding:10px 15px;background:#ffc107;color:black;border:none;border-radius:4px;cursor:pointer;font-weight:bold;}</style></head>
<body>
<div class="container">
    <h2>Edit Contact</h2>
    <?php if(!empty($errors)): ?><div class="error-box"><ul><?php foreach($errors as $e) echo "<li>$e</li>"; ?></ul></div><?php endif; ?>
    <form method="POST">
        <div class="form-group"><label>Name *</label><input type="text" name="name" value="<?= htmlspecialchars($name) ?>"></div>
        <div class="form-group"><label>Email *</label><input type="text" name="email" value="<?= htmlspecialchars($email) ?>"></div>
        <div class="form-group"><label>Phone</label><input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>"></div>
        <div class="form-group"><label>Group</label>
            <select name="group_name">
                <?php foreach($groups as $g): ?>
                    <option value="<?= $g ?>" <?= ($group_name == $g) ? 'selected' : '' ?>><?= $g ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Notes</label><textarea name="notes" rows="4"><?= htmlspecialchars($notes) ?></textarea></div>
        <button type="submit" class="btn">Update Contact</button>
        <a href="index.php" style="margin-left: 10px;">Cancel</a>
    </form>
</div>
</body></html>