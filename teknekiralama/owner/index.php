<?php
include '../includes/header.php';
include '../includes/navbar.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header('Location: ../login.php');
    exit;
}
?>
<main>
    <div class="container">
        <h2>Tekne Sahibi Paneli</h2>
        <p>Hoşgeldiniz, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
        <ul>
            <li><a href="add_boat.php">Tekne Ekle</a></li>
            <li><a href="my_boats.php">Teknelerim</a></li>
            <li><a href="reservations.php">Rezervasyonlarım</a></li>
            <li><a href="payments.php">Ödemelerim</a></li>
        </ul>
    </div>
</main>
<?php include '../includes/footer.php'; ?> 