<?php
include '../includes/header.php';
include '../includes/navbar.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit;
}
require '../db.php';
$stmt = $pdo->prepare('SELECT b.*, (SELECT image_path FROM boat_images WHERE boat_id = b.id LIMIT 1) as image FROM favorites f JOIN boats b ON f.boat_id = b.id WHERE f.customer_id = ? AND b.status = "approved"');
$stmt->execute([$_SESSION['user_id']]);
$boats = $stmt->fetchAll();
?>
<main>
    <div class="container">
        <h2><?php echo $langArr['in_favorites'] ?? 'Favorilerim'; ?></h2>
        <div class="boat-list">
            <?php foreach ($boats as $boat): ?>
                <div class="boat-card">
                    <a href="../boat_detail.php?id=<?php echo $boat['id']; ?>">
                        <?php if ($boat['image']): ?>
                            <img src="../<?php echo $boat['image']; ?>" alt="<?php echo htmlspecialchars($boat['title']); ?>" loading="lazy">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($boat['title']); ?></h3>
                    </a>
                    <div class="info">
                        <span>₺<?php echo $boat['price_per_hour']; ?>/<?php echo $langArr['per_hour'] ?? 'saat'; ?></span>
                        <span><?php echo $langArr['capacity'] ?? 'Kapasite'; ?>: <?php echo $boat['capacity']; ?></span>
                    </div>
                    <form method="post" action="../favorite.php" class="favorite-form">
                        <input type="hidden" name="boat_id" value="<?php echo $boat['id']; ?>">
                        <button type="submit" class="favorite-btn active"><?php echo $langArr['in_favorites'] ?? 'Favoriden Çıkar'; ?></button>
                    </form>
                </div>
            <?php endforeach; ?>
            <?php if (empty($boats)): ?>
                <p><?php echo $langArr['add_to_favorites'] ?? 'Henüz favori eklemediniz.'; ?></p>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?> 