<?php
include '../includes/header.php';
include '../includes/navbar.php';
include '../config.php';
// Komisyon oranı güncelleme işlemi örneği (dosya tabanlı, gerçek uygulamada veritabanı ile yapılmalı)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Burada komisyon oranı ve dil ayarı güncellenebilir
    // ...
    echo '<div class="container"><p>Ayarlar kaydedildi (örnek).</p></div>';
}
?>
<main>
    <div class="container">
        <h2>Site Ayarları</h2>
        <form method="post">
            <label>Komisyon Oranı (%)</label>
            <input type="number" name="commission" value="<?php echo COMMISSION_RATE; ?>" min="0" max="100">
            <br><br>
            <label>Varsayılan Dil</label>
            <select name="default_language">
                <?php foreach ($supported_languages as $lang): ?>
                    <option value="<?php echo $lang; ?>" <?php if ($lang == $default_language) echo 'selected'; ?>><?php echo strtoupper($lang); ?></option>
                <?php endforeach; ?>
            </select>
            <br><br>
            <button type="submit">Kaydet</button>
        </form>
    </div>
</main>
<?php include '../includes/footer.php'; ?> 