<?php
include 'includes/header.php';
include 'includes/navbar.php';
require 'db.php';
session_start();

// Filtreler
$city_id = $_GET['city_id'] ?? '';
$departure_point_id = $_GET['departure_point_id'] ?? '';
$order = $_GET['order'] ?? 'recommended';

// Şehirler ve kalkış noktaları
$cities = $pdo->query('SELECT id, name FROM cities')->fetchAll();
$departure_points = [];
if ($city_id) {
    $stmt = $pdo->prepare('SELECT id, name FROM departure_points WHERE city_id=?');
    $stmt->execute([$city_id]);
    $departure_points = $stmt->fetchAll();
}

// Sorgu oluştur
$query = 'SELECT b.*, (SELECT image_path FROM boat_images WHERE boat_id = b.id LIMIT 1) as image FROM boats b WHERE b.status = "approved"';
$params = [];
if ($city_id) {
    $query .= ' AND b.city_id = ?';
    $params[] = $city_id;
}
if ($departure_point_id) {
    $query .= ' AND b.departure_point_id = ?';
    $params[] = $departure_point_id;
}
if ($order === 'price') {
    $query .= ' ORDER BY b.price_per_hour ASC';
} else {
    $query .= ' ORDER BY b.featured DESC, b.created_at DESC';
}
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$boats = $stmt->fetchAll();

// Favoriler
$favorites = [];
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer') {
    $stmt = $pdo->prepare('SELECT boat_id FROM favorites WHERE customer_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $favorites = array_column($stmt->fetchAll(), 'boat_id');
}
?>
<main>
    <div class="container">
        <h2><?php echo $langArr['search_boat']; ?></h2>
        <form method="get" class="filters">
            <label><?php echo $langArr['city'] ?? 'Şehir'; ?></label>
            <select name="city_id" onchange="this.form.submit()">
                <option value=""><?php echo $langArr['all'] ?? 'Tümü'; ?></option>
                <?php foreach ($cities as $city): ?>
                    <option value="<?php echo $city['id']; ?>" <?php if($city_id==$city['id']) echo 'selected'; ?>><?php echo htmlspecialchars($city['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <label><?php echo $langArr['departure_point'] ?? 'Kalkış Noktası'; ?></label>
            <select name="departure_point_id" onchange="this.form.submit()">
                <option value=""><?php echo $langArr['all'] ?? 'Tümü'; ?></option>
                <?php foreach ($departure_points as $dp): ?>
                    <option value="<?php echo $dp['id']; ?>" <?php if($departure_point_id==$dp['id']) echo 'selected'; ?>><?php echo htmlspecialchars($dp['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <label><?php echo $langArr['sort'] ?? 'Sırala'; ?></label>
            <select name="order" onchange="this.form.submit()">
                <option value="recommended" <?php if($order=='recommended') echo 'selected'; ?>><?php echo $langArr['recommended'] ?? 'Önerilen'; ?></option>
                <option value="price" <?php if($order=='price') echo 'selected'; ?>><?php echo $langArr['by_price'] ?? 'Fiyata Göre'; ?></option>
            </select>
        </form>
        <div class="boat-list">
            <?php foreach ($boats as $boat): ?>
                <div class="boat-card">
                    <a href="boat_detail.php?id=<?php echo $boat['id']; ?>">
                        <?php if ($boat['image']): ?>
                            <img src="<?php echo $boat['image']; ?>" alt="<?php echo htmlspecialchars($boat['title']); ?>" loading="lazy">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($boat['title']); ?></h3>
                    </a>
                    <div class="badges">
                        <?php if ($boat['featured']): ?><span class="badge"><?php echo $langArr['featured'] ?? 'Öne Çıkan'; ?></span><?php endif; ?>
                        <?php if ($boat['instant_booking']): ?><span class="badge badge-instant"><?php echo $langArr['instant_booking'] ?? 'Anında Rezerve'; ?></span><?php endif; ?>
                    </div>
                    <div class="info">
                        <span>₺<?php echo $boat['price_per_hour']; ?>/<?php echo $langArr['per_hour'] ?? 'saat'; ?></span>
                        <span><?php echo $langArr['capacity'] ?? 'Kapasite'; ?>: <?php echo $boat['capacity']; ?></span>
                    </div>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'customer'): ?>
                        <form method="post" action="favorite.php" class="favorite-form">
                            <input type="hidden" name="boat_id" value="<?php echo $boat['id']; ?>">
                            <button type="submit" class="favorite-btn <?php if(in_array($boat['id'], $favorites)) echo 'active'; ?>">
                                <?php echo in_array($boat['id'], $favorites) ? ($langArr['in_favorites'] ?? 'Favoride') : ($langArr['add_to_favorites'] ?? 'Favorilere Ekle'); ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<?php include 'includes/footer.php'; ?> 