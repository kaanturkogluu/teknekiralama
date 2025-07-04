<?php
include '../includes/header.php';
include '../includes/navbar.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

// Placeholder: Şehirler, kalkış noktaları ve paketler örnek veriyle
$cities = $pdo->query('SELECT id, name FROM cities')->fetchAll();
$packages = $pdo->query('SELECT id, name FROM packages WHERE is_active=1')->fetchAll();
$departure_points = [];
if (!empty($cities)) {
    $first_city_id = $cities[0]['id'];
    $departure_points = $pdo->prepare('SELECT id, name FROM departure_points WHERE city_id=?');
    $departure_points->execute([$first_city_id]);
    $departure_points = $departure_points->fetchAll();
}

$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $city_id = $_POST['city_id'] ?? '';
    $departure_point_id = $_POST['departure_point_id'] ?? '';
    $price_per_hour = $_POST['price_per_hour'] ?? '';
    $capacity = $_POST['capacity'] ?? '';
    $package_ids = $_POST['packages'] ?? [];
    if (!$title || !$city_id || !$departure_point_id || !$price_per_hour || !$capacity) {
        $errors[] = $langArr['fill_required_fields'] ?? 'Tüm zorunlu alanları doldurun.';
    }
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO boats (owner_id, title, description, city_id, departure_point_id, price_per_hour, capacity) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$_SESSION['user_id'], $title, $description, $city_id, $departure_point_id, $price_per_hour, $capacity]);
        $boat_id = $pdo->lastInsertId();
        // Paketler ile ilişki (ileride boat_packages tablosu eklenebilir)
        // Resim yükleme
        if (!empty($_FILES['images']['name'][0])) {
            $upload_dir = '../assets/images/boats/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            foreach ($_FILES['images']['tmp_name'] as $i => $tmp_name) {
                $name = basename($_FILES['images']['name'][$i]);
                $target = $upload_dir . uniqid() . '_' . $name;
                if (move_uploaded_file($tmp_name, $target)) {
                    $stmt = $pdo->prepare('INSERT INTO boat_images (boat_id, image_path) VALUES (?, ?)');
                    $stmt->execute([$boat_id, str_replace('../', '', $target)]);
                }
            }
        }
        $success = true;
    }
}
?>
<main>
    <div class="container">
        <h2><?php echo $langArr['add_boat'] ?? 'Tekne Ekle'; ?></h2>
        <?php if ($success): ?><div class="success"><?php echo $langArr['boat_added_success'] ?? 'Tekne ilanınız eklendi, admin onayından sonra yayına alınacaktır.'; ?></div><?php endif; ?>
        <?php if ($errors): ?><div class="error"><?php foreach ($errors as $e) echo '<p>'.$e.'</p>'; ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <label><?php echo $langArr['title'] ?? 'Başlık'; ?></label>
            <input type="text" name="title" required>
            <label><?php echo $langArr['description'] ?? 'Açıklama'; ?></label>
            <textarea name="description"></textarea>
            <label><?php echo $langArr['city'] ?? 'Şehir'; ?></label>
            <select name="city_id" required>
                <?php foreach ($cities as $city): ?>
                    <option value="<?php echo $city['id']; ?>"><?php echo htmlspecialchars($city['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <label><?php echo $langArr['departure_point'] ?? 'Kalkış Noktası'; ?></label>
            <select name="departure_point_id" required>
                <?php foreach ($departure_points as $dp): ?>
                    <option value="<?php echo $dp['id']; ?>"><?php echo htmlspecialchars($dp['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <label><?php echo $langArr['per_hour'] ?? 'Saatlik Ücret'; ?> (₺)</label>
            <input type="number" name="price_per_hour" min="0" step="0.01" required>
            <label><?php echo $langArr['capacity'] ?? 'Kapasite'; ?> (<?php echo $langArr['person_count'] ?? 'kişi'; ?>)</label>
            <input type="number" name="capacity" min="1" required>
            <label><?php echo $langArr['images'] ?? 'Resimler'; ?> (<?php echo $langArr['multiple_select'] ?? 'çoklu seçilebilir'; ?>)</label>
            <input type="file" name="images[]" multiple accept="image/*">
            <label><?php echo $langArr['special_packages'] ?? 'Özel Gün Paketleri'; ?></label>
            <select name="packages[]" multiple>
                <?php foreach ($packages as $p): ?>
                    <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit"><?php echo $langArr['add_boat'] ?? 'Ekle'; ?></button>
        </form>
    </div>
</main>
<?php include '../includes/footer.php'; ?> 