<?php
include 'includes/header.php';
include 'includes/navbar.php';
require 'db.php';
session_start();
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare('SELECT b.*, c.name as city_name, d.name as departure_name FROM boats b JOIN cities c ON b.city_id = c.id JOIN departure_points d ON b.departure_point_id = d.id WHERE b.id = ? AND b.status = "approved"');
$stmt->execute([$id]);
$boat = $stmt->fetch();
if (!$boat) {
    echo '<div class="container"><p>' . ($langArr['boat_not_found'] ?? 'Tekne bulunamadı.') . '</p></div>';
    include 'includes/footer.php';
    exit;
}
$images = $pdo->prepare('SELECT image_path FROM boat_images WHERE boat_id = ?');
$images->execute([$id]);
$images = $images->fetchAll();
// Favori kontrolü
$is_favorite = false;
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer') {
    $stmt = $pdo->prepare('SELECT id FROM favorites WHERE customer_id = ? AND boat_id = ?');
    $stmt->execute([$_SESSION['user_id'], $id]);
    $is_favorite = $stmt->fetch() ? true : false;
}
?>
<main>
    <div class="container boat-detail">
        <h2><?php echo htmlspecialchars($boat['title']); ?></h2>
        <div class="boat-images">
            <?php foreach ($images as $img): ?>
                <img src="<?php echo $img['image_path']; ?>" alt="<?php echo htmlspecialchars($boat['title']); ?>" loading="lazy">
            <?php endforeach; ?>
        </div>
        <div class="badges">
            <?php if ($boat['featured']): ?><span class="badge"><?php echo $langArr['featured'] ?? 'Öne Çıkan'; ?></span><?php endif; ?>
            <?php if ($boat['instant_booking']): ?><span class="badge badge-instant"><?php echo $langArr['instant_booking'] ?? 'Anında Rezerve'; ?></span><?php endif; ?>
        </div>
        <div class="info">
            <span><?php echo $langArr['city'] ?? 'Şehir'; ?>: <?php echo htmlspecialchars($boat['city_name']); ?></span>
            <span><?php echo $langArr['departure_point'] ?? 'Kalkış Noktası'; ?>: <?php echo htmlspecialchars($boat['departure_name']); ?></span>
            <span>₺<?php echo $boat['price_per_hour']; ?>/<?php echo $langArr['per_hour'] ?? 'saat'; ?></span>
            <span><?php echo $langArr['capacity'] ?? 'Kapasite'; ?>: <?php echo $boat['capacity']; ?></span>
        </div>
        <p><?php echo nl2br(htmlspecialchars($boat['description'])); ?></p>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer'): ?>
            <form method="post" action="favorite.php" class="favorite-form">
                <input type="hidden" name="boat_id" value="<?php echo $boat['id']; ?>">
                <button type="submit" class="favorite-btn <?php if($is_favorite) echo 'active'; ?>">
                    <?php echo $is_favorite ? ($langArr['in_favorites'] ?? 'Favoride') : ($langArr['add_to_favorites'] ?? 'Favorilere Ekle'); ?>
                </button>
            </form>
            <form method="post" action="reserve.php" class="reserve-form">
                <h3><?php echo $langArr['make_reservation'] ?? 'Rezervasyon Yap'; ?></h3>
                <label><?php echo $langArr['start_datetime'] ?? 'Tarih ve Saat'; ?></label>
                <input type="datetime-local" name="start_time" required>
                <label><?php echo $langArr['end_datetime'] ?? 'Bitiş Tarih ve Saat'; ?></label>
                <input type="datetime-local" name="end_time" required>
                <label><?php echo $langArr['person_count'] ?? 'Kişi Sayısı'; ?></label>
                <input type="number" name="person_count" min="1" max="<?php echo $boat['capacity']; ?>" required>
                <input type="hidden" name="boat_id" value="<?php echo $boat['id']; ?>">
                <button type="submit"><?php echo $langArr['make_reservation'] ?? 'Rezervasyon Yap'; ?></button>
            </form>
        <?php endif; ?>
    </div>
</main>
<?php include 'includes/footer.php'; ?> 