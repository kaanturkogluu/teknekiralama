<?php
include 'includes/header.php';
include 'includes/navbar.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}
require 'db.php';
require 'config.php';

$reservation_id = $_GET['reservation_id'] ?? null;
if (!$reservation_id) {
    echo '<div class="container"><p>Geçersiz rezervasyon.</p></div>';
    include 'includes/footer.php';
    exit;
}
// Rezervasyon ve ödeme kontrolü
$stmt = $pdo->prepare('SELECT * FROM reservations WHERE id = ? AND customer_id = ?');
$stmt->execute([$reservation_id, $_SESSION['user_id']]);
$reservation = $stmt->fetch();
if (!$reservation) {
    echo '<div class="container"><p>Rezervasyon bulunamadı.</p></div>';
    include 'includes/footer.php';
    exit;
}
// Zaten ödeme var mı?
$stmt = $pdo->prepare('SELECT * FROM payments WHERE reservation_id = ?');
$stmt->execute([$reservation_id]);
$payment = $stmt->fetch();
if ($payment) {
    echo '<div class="container"><p>' . ($langArr[$payment['status']] ?? 'Ödeme zaten yapılmış.') . '</p></div>';
    include 'includes/footer.php';
    exit;
}
// Komisyon ve net tutar hesapla
$commission = round($reservation['total_price'] * COMMISSION_RATE / 100, 2);
$net = $reservation['total_price'] - $commission;
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gerçek ödeme entegrasyonu yerine simülasyon
    $stmt = $pdo->prepare('INSERT INTO payments (reservation_id, amount, commission, status) VALUES (?, ?, ?, ?)');
    $stmt->execute([$reservation_id, $net, $commission, 'paid']);
    $success = true;
}
?>
<main>
    <div class="container">
        <h2><?php echo $langArr['pay_now'] ?? 'Şimdi Öde'; ?></h2>
        <?php if ($success): ?>
            <div class="success"><?php echo $langArr['paid'] ?? 'Ödendi'; ?>. <a href="customer/reservations.php"><< <?php echo $langArr['make_reservation'] ?? 'Rezervasyonlarım'; ?></a></div>
        <?php else: ?>
            <form method="post">
                <p><?php echo $langArr['total_price'] ?? 'Toplam Tutar'; ?>: <b>₺<?php echo $reservation['total_price']; ?></b></p>
                <p><?php echo $langArr['commission'] ?? 'Komisyon'; ?>: <b>₺<?php echo $commission; ?></b></p>
                <p><?php echo $langArr['amount'] ?? 'Net Tutar'; ?>: <b>₺<?php echo $net; ?></b></p>
                <button type="submit"><?php echo $langArr['pay_now'] ?? 'Şimdi Öde'; ?></button>
            </form>
        <?php endif; ?>
    </div>
</main>
<?php include 'includes/footer.php'; ?> 