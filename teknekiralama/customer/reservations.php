<?php
include '../includes/header.php';
include '../includes/navbar.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit;
}
require '../db.php';
$stmt = $pdo->prepare('SELECT r.*, b.title, p.status as payment_status, p.id as payment_id FROM reservations r JOIN boats b ON r.boat_id = b.id LEFT JOIN payments p ON p.reservation_id = r.id WHERE r.customer_id = ? ORDER BY r.start_time DESC');
$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll();
?>
<main>
    <div class="container">
        <h2><?php echo $langArr['make_reservation'] ?? 'Rezervasyonlarım'; ?></h2>
        <table>
            <thead>
                <tr>
                    <th><?php echo $langArr['search_boat'] ?? 'Tekne'; ?></th>
                    <th><?php echo $langArr['start_datetime'] ?? 'Başlangıç'; ?></th>
                    <th><?php echo $langArr['end_datetime'] ?? 'Bitiş'; ?></th>
                    <th><?php echo $langArr['person_count'] ?? 'Kişi'; ?></th>
                    <th><?php echo $langArr['total_price'] ?? 'Toplam Fiyat'; ?></th>
                    <th><?php echo $langArr['status'] ?? 'Durum'; ?></th>
                    <th><?php echo $langArr['payments'] ?? 'Ödeme'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $r): ?>
                    <tr>
                        <td><a href="../boat_detail.php?id=<?php echo $r['boat_id']; ?>"><?php echo htmlspecialchars($r['title']); ?></a></td>
                        <td><?php echo $r['start_time']; ?></td>
                        <td><?php echo $r['end_time']; ?></td>
                        <td><?php echo $r['person_count']; ?></td>
                        <td>₺<?php echo $r['total_price']; ?></td>
                        <td><?php echo ucfirst($r['status']); ?></td>
                        <td>
                            <?php if ($r['payment_status'] === null): ?>
                                <a href="../pay.php?reservation_id=<?php echo $r['id']; ?>" class="pay-btn"><?php echo $langArr['pay_now'] ?? 'Şimdi Öde'; ?></a>
                            <?php else: ?>
                                <?php echo $langArr[$r['payment_status']] ?? ucfirst($r['payment_status']); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($reservations)): ?>
                    <tr><td colspan="7"><?php echo $langArr['make_reservation'] ?? 'Henüz rezervasyon yapmadınız.'; ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include '../includes/footer.php'; ?> 