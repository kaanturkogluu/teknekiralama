<nav class="navbar">
    <div class="container">
        <a href="/index.php" class="logo">Tekne Kiralama</a>
        <ul class="nav-links">
            <li><a href="/campaigns.php"><?php echo $langArr['campaigns'] ?? 'Kampanyalar'; ?></a></li>
            <li><a href="/blog/index.php"><?php echo $langArr['blog'] ?? 'Blog'; ?></a></li>
            <li><a href="/help.php"><?php echo $langArr['help_center'] ?? 'Yardım Merkezi'; ?></a></li>
            <li><a href="/contact.php"><?php echo $langArr['contact'] ?? 'İletişim'; ?></a></li>
            <li><a href="/rent_your_boat.php"><?php echo $langArr['rent_your_boat'] ?? 'Teknenizi Kiralayın'; ?></a></li>
        </ul>
        <!-- Kullanıcıya göre giriş/çıkış butonları buraya gelecek -->
    </div>
</nav> 