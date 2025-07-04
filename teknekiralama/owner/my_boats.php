<?php
include '../includes/header.php';
include '../includes/navbar.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header('Location: ../login.php');
    exit;
}
require '../db.php';
$stmt = $pdo->prepare('SELECT * FROM boats WHERE owner_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$boats = $stmt->fetchAll();
?>
<main>
    <div class="container">
        <h2>Teknelerim</h2>
        <a href="add_boat.php">+ Yeni Tekne Ekle</a>
        <table>
            <thead>
                <tr>
                    <th>Başlık</th>
                    <th>Durum</th>
                    <th>Fiyat (₺/saat)</th>
                    <th>Kapasite</th>
                    <th>Oluşturulma</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($boats as $boat): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($boat['title']); ?></td>
                        <td><?php echo ucfirst($boat['status']); ?></td>
                        <td><?php echo $boat['price_per_hour']; ?></td>
                        <td><?php echo $boat['capacity']; ?></td>
                        <td><?php echo $boat['created_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include '../includes/footer.php'; ?> 