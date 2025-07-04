<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}
$boat_id = $_POST['boat_id'] ?? 0;
$customer_id = $_SESSION['user_id'];
if ($boat_id) {
    // Favoride mi?
    $stmt = $pdo->prepare('SELECT id FROM favorites WHERE customer_id = ? AND boat_id = ?');
    $stmt->execute([$customer_id, $boat_id]);
    if ($stmt->fetch()) {
        // Varsa çıkar
        $pdo->prepare('DELETE FROM favorites WHERE customer_id = ? AND boat_id = ?')->execute([$customer_id, $boat_id]);
    } else {
        // Yoksa ekle
        $pdo->prepare('INSERT INTO favorites (customer_id, boat_id) VALUES (?, ?)')->execute([$customer_id, $boat_id]);
    }
}
// Önceki sayfaya dön
$ref = $_SERVER['HTTP_REFERER'] ?? 'boats.php';
header('Location: ' . $ref);
exit; 