<?php
include '../includes/header.php';
include '../includes/navbar.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

// Paket ekleme işlemi
$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? null;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    if (!$name) {
        $errors[] = $langArr['fill_required_fields'] ?? 'Tüm zorunlu alanları doldurun.';
    }
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO packages (name, description, price, is_active) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $description, $price, $is_active]);
        $success = true;
    }
}
// Paketleri çek
$packages = $pdo->query('SELECT * FROM packages ORDER BY id DESC')->fetchAll();
?>
<main>
    <div class="container">
        <h2><?php echo $langArr['special_packages'] ?? 'Özel Gün Paketleri'; ?></h2>
        <h3><?php echo $langArr['add_package'] ?? 'Yeni Paket Ekle'; ?></h3>
        <?php if ($success): ?><div class="success"><?php echo $langArr['package_added_success'] ?? 'Paket eklendi.'; ?></div><?php endif; ?>
        <?php if ($errors): ?><div class="error"><?php foreach ($errors as $e) echo '<p>'.$e.'</p>'; ?></div><?php endif; ?>
        <form method="post">
            <label><?php echo $langArr['title'] ?? 'Başlık'; ?></label>
            <input type="text" name="name" required>
            <label><?php echo $langArr['description'] ?? 'Açıklama'; ?></label>
            <textarea name="description"></textarea>
            <label><?php echo $langArr['price'] ?? 'Fiyat'; ?> (₺)</label>
            <input type="number" name="price" min="0" step="0.01">
            <label><input type="checkbox" name="is_active" checked> <?php echo $langArr['active'] ?? 'Aktif'; ?></label>
            <button type="submit"><?php echo $langArr['add_package'] ?? 'Ekle'; ?></button>
        </form>
        <h3><?php echo $langArr['package_list'] ?? 'Paket Listesi'; ?></h3>
        <table>
            <thead>
                <tr>
                    <th><?php echo $langArr['title'] ?? 'Başlık'; ?></th>
                    <th><?php echo $langArr['description'] ?? 'Açıklama'; ?></th>
                    <th><?php echo $langArr['price'] ?? 'Fiyat'; ?></th>
                    <th><?php echo $langArr['active'] ?? 'Aktif'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($packages as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['name']); ?></td>
                        <td><?php echo htmlspecialchars($p['description']); ?></td>
                        <td><?php echo $p['price']; ?></td>
                        <td><?php echo $p['is_active'] ? ($langArr['active'] ?? 'Aktif') : ($langArr['inactive'] ?? 'Pasif'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include '../includes/footer.php'; ?> 