<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}
$boat_id = $_POST['boat_id'] ?? 0;
$customer_id = $_SESSION['user_id'];
$start_time = $_POST['start_time'] ?? '';
$end_time = $_POST['end_time'] ?? '';
$person_count = $_POST['person_count'] ?? 1;
$error = '';
if ($boat_id && $start_time && $end_time && $person_count) {
    // Tekne kapasitesi kontrolü
    $stmt = $pdo->prepare('SELECT capacity, price_per_hour FROM boats WHERE id = ? AND status = "approved"');
    $stmt->execute([$boat_id]);
    $boat = $stmt->fetch();
    if (!$boat || $person_count > $boat['capacity']) {
        $error = 'Kapasite aşıldı veya tekne bulunamadı.';
    } else {
        // Çakışan rezervasyon var mı?
        $stmt = $pdo->prepare('SELECT id FROM reservations WHERE boat_id = ? AND status IN ("pending","confirmed") AND ((start_time < ? AND end_time > ?) OR (start_time < ? AND end_time > ?) OR (start_time >= ? AND end_time <= ?))');
        $stmt->execute([$boat_id, $end_time, $end_time, $start_time, $start_time, $start_time, $end_time]);
        if ($stmt->fetch()) {
            $error = 'Bu saat aralığında zaten rezervasyon var.';
        } else {
            // Fiyat hesapla
            $start = strtotime($start_time);
            $end = strtotime($end_time);
            $hours = max(1, ceil(($end - $start) / 3600));
            $total_price = $hours * $boat['price_per_hour'];
            $stmt = $pdo->prepare('INSERT INTO reservations (boat_id, customer_id, start_time, end_time, total_price, person_count) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$boat_id, $customer_id, $start_time, $end_time, $total_price, $person_count]);
        }
    }
}
$ref = $_SERVER['HTTP_REFERER'] ?? 'boat_detail.php?id=' . $boat_id;
if ($error) {
    $ref .= (strpos($ref, '?') === false ? '?' : '&') . 'error=' . urlencode($error);
}
header('Location: ' . $ref);
exit; 