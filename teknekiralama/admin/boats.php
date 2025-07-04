<?php
include '../includes/header.php';
include '../includes/navbar.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

// Onaylama veya reddetme işlemi
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'approve') {
        $stmt = $pdo->prepare('UPDATE boats SET status = "approved" WHERE id = ?');
        $stmt->execute([$id]);
    } elseif ($_GET['action'] === 'reject') {
        $stmt = $pdo->prepare('UPDATE boats SET status = "rejected" WHERE id = ?');
        $stmt->execute([$id]);
    }
    header('Location: boats.php');
    exit;
}

// Tüm tekneleri çek
$stmt = $pdo->query('SELECT b.*, u.name as owner_name FROM boats b JOIN users u ON b.owner_id = u.id ORDER BY b.created_at DESC');
$boats = $stmt->fetchAll();
?>
<main>
    <div class="container">
        <h2>Tekne İlanları Yönetimi</h2>
        <table>
            <thead>
                <tr>
                    <th>Başlık</th>
                    <th>Sahibi</th>
                    <th>Durum</th>
                    <th>Fiyat (₺/saat)</th>
                    <th>Kapasite</th>
                    <th>Oluşturulma</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($boats as $boat): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($boat['title']); ?></td>
                        <td><?php echo htmlspecialchars($boat['owner_name']); ?></td>
                        <td><?php echo ucfirst($boat['status']); ?></td>
                        <td><?php echo $boat['price_per_hour']; ?></td>
                        <td><?php echo $boat['capacity']; ?></td>
                        <td><?php echo $boat['created_at']; ?></td>
                        <td>
                            <?php if ($boat['status'] === 'pending'): ?>
                                <a href="?action=approve&id=<?php echo $boat['id']; ?>">Onayla</a> |
                                <a href="?action=reject&id=<?php echo $boat['id']; ?>">Reddet</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include '../includes/footer.php'; ?> 