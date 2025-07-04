<?php
require 'db.php';
require 'config.php';
require_once __DIR__ . '/vendor/autoload.php';

use Iyzipay\Options;
use Iyzipay\Request\RetrieveCheckoutFormRequest;
use Iyzipay\Model\CheckoutForm;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($to, $toName, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to, $toName);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {
        // Hata loglanabilir
    }
}

session_start();
$lang = $_SESSION['lang'] ?? 'tr';
$langFile = __DIR__ . '/languages/' . $lang . '.php';
if (file_exists($langFile)) {
    $langArr = include $langFile;
} else {
    $langArr = include __DIR__ . '/languages/tr.php';
}

$reservation_id = $_GET['reservation_id'] ?? null;
$token = $_POST['token'] ?? null;

if (!$reservation_id || !$token) {
    echo '<div class="container"><p>Geçersiz istek.</p></div>';
    exit;
}

$options = new Options();
$options->setApiKey(IYZICO_API_KEY);
$options->setSecretKey(IYZICO_SECRET_KEY);
$options->setBaseUrl(IYZICO_BASE_URL);

$request = new RetrieveCheckoutFormRequest();
$request->setLocale("tr");
$request->setToken($token);
$request->setConversationId($reservation_id);

$checkoutForm = CheckoutForm::retrieve($request, $options);

if ($checkoutForm->getPaymentStatus() === 'SUCCESS') {
    // Rezervasyon ve ödeme kontrolü
    $stmt = $pdo->prepare('SELECT * FROM reservations WHERE id = ?');
    $stmt->execute([$reservation_id]);
    $reservation = $stmt->fetch();
    if ($reservation) {
        $commission = round($reservation['total_price'] * COMMISSION_RATE / 100, 2);
        $net = $reservation['total_price'] - $commission;
        // Zaten ödeme var mı?
        $stmt = $pdo->prepare('SELECT * FROM payments WHERE reservation_id = ?');
        $stmt->execute([$reservation_id]);
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare('INSERT INTO payments (reservation_id, amount, commission, status) VALUES (?, ?, ?, ?)');
            $stmt->execute([$reservation_id, $net, $commission, 'paid']);
        }
        // Rezervasyon durumunu güncelle (isteğe bağlı)
        $stmt = $pdo->prepare('UPDATE reservations SET status = "confirmed" WHERE id = ?');
        $stmt->execute([$reservation_id]);
        // Müşteri ve tekne sahibi e-posta adreslerini al
        $stmt = $pdo->prepare('SELECT u.email as customer_email, u.name as customer_name, b.owner_id, b.title, o.email as owner_email, o.name as owner_name FROM reservations r JOIN users u ON r.customer_id = u.id JOIN boats b ON r.boat_id = b.id JOIN users o ON b.owner_id = o.id WHERE r.id = ?');
        $stmt->execute([$reservation_id]);
        $info = $stmt->fetch();
        if ($info) {
            // Müşteriye e-posta
            $subject = $langArr['payment_success'] ?? 'Ödeme işleminiz başarıyla tamamlandı.';
            $body = '<p>' . ($langArr['payment_success'] ?? 'Ödeme işleminiz başarıyla tamamlandı.') . '</p>';
            $body .= '<p>' . ($langArr['search_boat'] ?? 'Tekne') . ': <b>' . htmlspecialchars($info['title']) . '</b></p>';
            $body .= '<p>' . ($langArr['total_price'] ?? 'Toplam Tutar') . ': ₺' . $reservation['total_price'] . '</p>';
            sendMail($info['customer_email'], $info['customer_name'], $subject, $body);
            // Tekne sahibine e-posta
            $subject2 = $langArr['new_reservation'] ?? 'Yeni rezervasyon ve ödeme';
            $body2 = '<p>' . ($langArr['new_reservation_info'] ?? 'Teknenize yeni bir rezervasyon ve ödeme yapıldı.') . '</p>';
            $body2 .= '<p>' . ($langArr['search_boat'] ?? 'Tekne') . ': <b>' . htmlspecialchars($info['title']) . '</b></p>';
            $body2 .= '<p>' . ($langArr['total_price'] ?? 'Toplam Tutar') . ': ₺' . $reservation['total_price'] . '</p>';
            sendMail($info['owner_email'], $info['owner_name'], $subject2, $body2);
        }
    }
    echo '<div class="container"><h2>' . ($langArr['paid'] ?? 'Ödendi') . '</h2><p>' . ($langArr['payment_success'] ?? 'Ödeme işleminiz başarıyla tamamlandı.') . '</p><a href="customer/reservations.php"><< ' . ($langArr['make_reservation'] ?? 'Rezervasyonlarım') . '</a></div>';
} else {
    echo '<div class="container"><h2>' . ($langArr['failed'] ?? 'Başarısız') . '</h2><p>' . ($langArr['payment_failed'] ?? 'Ödeme başarısız oldu.') . '</p><a href="customer/reservations.php"><< ' . ($langArr['make_reservation'] ?? 'Rezervasyonlarım') . '</a></div>';
} 