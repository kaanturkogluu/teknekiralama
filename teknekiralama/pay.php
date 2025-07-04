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
require_once __DIR__ . '/vendor/autoload.php';

use Iyzipay\Options;
use Iyzipay\Request\CreateCheckoutFormInitializeRequest;
use Iyzipay\Model\CheckoutFormInitialize;

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

// iyzico ile ödeme formu oluştur
$options = new Options();
$options->setApiKey(IYZICO_API_KEY);
$options->setSecretKey(IYZICO_SECRET_KEY);
$options->setBaseUrl(IYZICO_BASE_URL);

$request = new CreateCheckoutFormInitializeRequest();
$request->setLocale("tr");
$request->setConversationId($reservation_id);
$request->setPrice($reservation['total_price']);
$request->setPaidPrice($reservation['total_price']);
$request->setCurrency("TRY");
$request->setBasketId($reservation_id);
$request->setPaymentGroup("PRODUCT");
$request->setCallbackUrl("http://" . $_SERVER['HTTP_HOST'] . "/pay_callback.php?reservation_id=" . $reservation_id);

// Kullanıcı bilgileri (örnek, gerçek projede user tablosundan alınmalı)
$buyer = new \Iyzipay\Model\Buyer();
$buyer->setId($_SESSION['user_id']);
$buyer->setName($_SESSION['name'] ?? 'Müşteri');
$buyer->setSurname('');
$buyer->setGsmNumber('+905555555555');
$buyer->setEmail('test@example.com'); // Gerçek projede user tablosundan alınmalı
$buyer->setIdentityNumber('11111111110');
$buyer->setRegistrationAddress('Adres');
$buyer->setIp($_SERVER['REMOTE_ADDR']);
$buyer->setCity('İstanbul');
$buyer->setCountry('Turkey');
$buyer->setZipCode('34000');
$request->setBuyer($buyer);

// Sepet (tek ürün)
$basketItem = new \Iyzipay\Model\BasketItem();
$basketItem->setId($reservation_id);
$basketItem->setName('Tekne Kiralama');
$basketItem->setCategory1('Tekne');
$basketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
$basketItem->setPrice($reservation['total_price']);
$request->setBasketItems([$basketItem]);

$checkoutForm = CheckoutFormInitialize::create($request, $options);

?>
<main>
    <div class="container">
        <h2><?php echo $langArr['pay_now'] ?? 'Şimdi Öde'; ?></h2>
        <p><?php echo $langArr['total_price'] ?? 'Toplam Tutar'; ?>: <b>₺<?php echo $reservation['total_price']; ?></b></p>
        <p><?php echo $langArr['commission'] ?? 'Komisyon'; ?>: <b>₺<?php echo $commission; ?></b></p>
        <p><?php echo $langArr['amount'] ?? 'Net Tutar'; ?>: <b>₺<?php echo $net; ?></b></p>
        <?php if ($checkoutForm->getStatus() === 'success'): ?>
            <?php echo $checkoutForm->getCheckoutFormContent(); ?>
        <?php else: ?>
            <div class="error">Ödeme başlatılamadı: <?php echo htmlspecialchars($checkoutForm->getErrorMessage()); ?></div>
        <?php endif; ?>
    </div>
</main>
<?php include 'includes/footer.php'; ?> 