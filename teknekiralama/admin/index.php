<?php
include '../includes/header.php';
include '../includes/navbar.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
?>
<main>
    <div class="container">
        <h2>Admin Paneli</h2>
        <p>Hoşgeldiniz, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
        <ul>
            <li><a href="boats.php">Tekne Onayları</a></li>
            <li><a href="packages.php">Özel Gün Paketleri</a></li>
            <li><a href="blogs.php">Blog Yönetimi</a></li>
            <li><a href="settings.php">Site Ayarları</a></li>
        </ul>
    </div>
</main>
<?php include '../includes/footer.php'; ?> 