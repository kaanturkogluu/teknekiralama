<?php
include '../includes/header.php';
include '../includes/navbar.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header('Location: ../login.php');
    exit;
}
require '../db.php';

// Tekne sahibinin teknelerine ait ödemeleri çek
$stmt = $pdo->prepare('
    SELECT p.*, r.total_price, b.title
    FROM payments p
    JOIN reservations r ON p.reservation_id = r.id
    JOIN boats b ON r.boat_id = b.id
    WHERE b.owner_id = ?
    ORDER BY p.payment_date DESC
');
$stmt->execute([$_SESSION['user_id']]);
$payments = $stmt->fetchAll();
?>
<main>
    <div class="container">
        <h2><?php echo $langArr['payments'] ?? 'Ödemelerim'; ?></h2>
        <table>
            <thead>
                <tr>
                    <th><?php echo $langArr['title'] ?? 'Tekne'; ?></th>
                    <th><?php echo $langArr['total_price'] ?? 'Toplam Tutar'; ?></th>
                    <th><?php echo $langArr['commission'] ?? 'Komisyon'; ?></th>
                    <th><?php echo $langArr['amount'] ?? 'Net Tutar'; ?></th>
                    <th><?php echo $langArr['payment_date'] ?? 'Ödeme Tarihi'; ?></th>
                    <th><?php echo $langArr['status'] ?? 'Durum'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['title']); ?></td>
                        <td>₺<?php echo $p['total_price']; ?></td>
                        <td>₺<?php echo $p['commission']; ?></td>
                        <td>₺<?php echo $p['amount']; ?></td>
                        <td><?php echo $p['payment_date']; ?></td>
                        <td><?php echo ucfirst($p['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($payments)): ?>
                    <tr><td colspan="6"><?php echo $langArr['no_payments'] ?? 'Henüz ödeme yok.'; ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include '../includes/footer.php'; ?> 