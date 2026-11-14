<?php
// add.php
session_start();
require_once 'config.php';

$name = $email = $phone = $group_name = $notes = '';
$errors = [];
$groups = ['Family', 'Friends', 'Work', 'Other', 'General'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $group_name = $_POST['group_name'];
    $notes = trim($_POST['notes']);

    // Validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid Email is required.";
    } else {
        // Kiểm tra Email trùng
        $stmt = $pdo->prepare("SELECT id FROM contacts WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email already exists in the database.";
        }
    }

    if (empty($errors)) {
        $sql = "INSERT INTO contacts (name, email, phone, group_name, notes) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $email, $phone, $group_name, $notes]);
        
        $_SESSION['flash'] = "Contact added successfully!";
        $_SESSION['flash_type'] = "success";
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Add Contact</title>
    <style> /* Bạn có thể gộp chung vào style.css, ở đây viết gọn để dễ test */
        body { font-family: Arial; background: #f4f4f9; padding: 20px; }
        .container { max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        .error-box { background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border: 1px solid #f5c6cb; border-radius: 4px;}
        .btn { padding: 10px 15px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
<div class="container">
    <h2>Add New Contact</h2>
    <?php if(!empty($errors)): ?>
        <div class="error-box">
            <ul><?php foreach($errors as $e) echo "<li>$e</li>"; ?></ul>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group"><label>Name *</label>
            <input type="text" name="name" value="<?= htmlspecialchars($name) ?>">
        </div>
        <div class="form-group"><label>Email *</label>
            <input type="text" name="email" value="<?= htmlspecialchars($email) ?>">
        </div>
        <div class="form-group"><label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>">
        </div>
        <div class="form-group"><label>Group</label>
            <select name="group_name">
                <?php foreach($groups as $g): ?>
                    <option value="<?= $g ?>" <?= ($group_name == $g) ? 'selected' : '' ?>><?= $g ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Notes</label>
            <textarea name="notes" rows="4"><?= htmlspecialchars($notes) ?></textarea>
        </div>
        <button type="submit" class="btn">Save Contact</button>
        <a href="index.php" style="margin-left: 10px;">Cancel</a>
    </form>
</div>
</body>
</html>