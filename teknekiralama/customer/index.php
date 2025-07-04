<?php
include '../includes/header.php';
include '../includes/navbar.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit;
}
?>
<main>
    <div class="container">
        <h2>Müşteri Paneli</h2>
        <p>Hoşgeldiniz, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
        <ul>
            <li><a href="favorites.php">Favorilerim</a></li>
            <li><a href="reservations.php">Rezervasyonlarım</a></li>
            <li><a href="profile.php">Profilim</a></li>
        </ul>
    </div>
</main>
<?php include '../includes/footer.php'; ?> 