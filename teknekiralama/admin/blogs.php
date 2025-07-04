<?php
include '../includes/header.php';
include '../includes/navbar.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

// Blog ekleme işlemi
$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $image_path = '';
    if (!$title || !$content) {
        $errors[] = $langArr['fill_required_fields'] ?? 'Tüm zorunlu alanları doldurun.';
    }
    if (empty($errors)) {
        // Resim yükleme
        if (!empty($_FILES['image']['name'])) {
            $upload_dir = '../assets/images/blogs/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $name = basename($_FILES['image']['name']);
            $target = $upload_dir . uniqid() . '_' . $name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image_path = str_replace('../', '', $target);
            }
        }
        $stmt = $pdo->prepare('INSERT INTO blogs (admin_id, title, content, image_path) VALUES (?, ?, ?, ?)');
        $stmt->execute([$_SESSION['user_id'], $title, $content, $image_path]);
        $success = true;
    }
}
// Blogları çek
$blogs = $pdo->query('SELECT * FROM blogs ORDER BY created_at DESC')->fetchAll();
?>
<main>
    <div class="container">
        <h2><?php echo $langArr['blog'] ?? 'Blog'; ?></h2>
        <h3><?php echo $langArr['add_blog'] ?? 'Yeni Blog Ekle'; ?></h3>
        <?php if ($success): ?><div class="success"><?php echo $langArr['blog_added_success'] ?? 'Blog eklendi.'; ?></div><?php endif; ?>
        <?php if ($errors): ?><div class="error"><?php foreach ($errors as $e) echo '<p>'.$e.'</p>'; ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <label><?php echo $langArr['title'] ?? 'Başlık'; ?></label>
            <input type="text" name="title" required>
            <label><?php echo $langArr['description'] ?? 'İçerik'; ?></label>
            <textarea name="content" required></textarea>
            <label><?php echo $langArr['images'] ?? 'Resim'; ?></label>
            <input type="file" name="image" accept="image/*">
            <button type="submit"><?php echo $langArr['add_blog'] ?? 'Ekle'; ?></button>
        </form>
        <h3><?php echo $langArr['blog_list'] ?? 'Blog Listesi'; ?></h3>
        <table>
            <thead>
                <tr>
                    <th><?php echo $langArr['title'] ?? 'Başlık'; ?></th>
                    <th><?php echo $langArr['description'] ?? 'İçerik'; ?></th>
                    <th><?php echo $langArr['images'] ?? 'Resim'; ?></th>
                    <th><?php echo $langArr['created_at'] ?? 'Tarih'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($blogs as $blog): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($blog['title']); ?></td>
                        <td><?php echo mb_substr(strip_tags($blog['content']), 0, 60) . '...'; ?></td>
                        <td><?php if ($blog['image_path']): ?><img src="../<?php echo $blog['image_path']; ?>" alt="" width="60"><?php endif; ?></td>
                        <td><?php echo $blog['created_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include '../includes/footer.php'; ?> 