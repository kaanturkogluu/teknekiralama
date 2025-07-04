<?php
// Çoklu dil desteği için session başlat
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Dil seçimi
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}
$lang = $_SESSION['lang'] ?? 'tr';
// Dil dosyasını yükle
$langFile = __DIR__ . '/../languages/' . $lang . '.php';
if (file_exists($langFile)) {
    $langArr = include $langFile;
} else {
    $langArr = include __DIR__ . '/../languages/tr.php';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tekne Kiralama</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <!-- Dil seçici placeholder -->
    <!-- <div class="language-switcher"> ... </div> -->
</head>
<body>
<div class="language-switcher" style="text-align:right; padding:8px 16px;">
    <form method="get" style="display:inline;">
        <select name="lang" onchange="this.form.submit()">
            <option value="tr" <?php if($lang=='tr') echo 'selected'; ?>>Türkçe</option>
            <option value="en" <?php if($lang=='en') echo 'selected'; ?>>English</option>
            <option value="ar" <?php if($lang=='ar') echo 'selected'; ?>>العربية</option>
            <option value="ru" <?php if($lang=='ru') echo 'selected'; ?>>Русский</option>
        </select>
    </form>
</div> 