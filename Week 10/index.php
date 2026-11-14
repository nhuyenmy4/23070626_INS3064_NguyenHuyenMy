<?php
// index.php
session_start();
require_once 'config.php';

// Xử lý tìm kiếm
$search = $_GET['search'] ?? '';
$search_query = "";
$params = [];

if ($search) {
    $search_query = "WHERE name LIKE :search OR email LIKE :search";
    $params['search'] = "%$search%";
}

// Lấy danh sách liên hệ
$stmt = $pdo->prepare("SELECT * FROM contacts $search_query ORDER BY created_at DESC");
$stmt->execute($params);
$contacts = $stmt->fetchAll();
$total_contacts = count($contacts);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Contact Manager</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .flash { padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .flash.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .flash.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #007bff; color: white; }
        tr:nth-child(even) { background: #f9f9f9; } /* Zebra striping */
        .btn { padding: 8px 12px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; display: inline-block; }
        .btn-delete { background: #dc3545; }
        .btn-edit { background: #ffc107; color: black; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        input[type="text"] { padding: 8px; width: 250px; border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Contact Manager</h2>

    <!-- Hiển thị Flash Message -->
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="flash <?= $_SESSION['flash_type'] ?>">
            <?= htmlspecialchars($_SESSION['flash']) ?>
        </div>
        <?php unset($_SESSION['flash']); unset($_SESSION['flash_type']); ?>
    <?php endif; ?>

    <div class="header-actions">
        <a href="add.php" class="btn">Add New Contact</a>
        <form method="GET" action="index.php">
            <input type="text" name="search" placeholder="Search by name or email..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn">Search</button>
            <?php if($search): ?> <a href="index.php" class="btn btn-delete">Clear</a> <?php endif; ?>
        </form>
    </div>
    
    <p>Total Contacts: <strong><?= $total_contacts ?></strong></p>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Group</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contacts as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['phone']) ?></td>
                <td><?= htmlspecialchars($c['group_name']) ?></td>
                <td><?= htmlspecialchars($c['created_at']) ?></td>
                <td>
                    <a href="edit.php?id=<?= $c['id'] ?>" class="btn btn-edit">Edit</a>
                    <a href="delete.php?id=<?= $c['id'] ?>" class="btn btn-delete">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($contacts)): ?>
                <tr><td colspan="6" style="text-align:center;">No contacts found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>