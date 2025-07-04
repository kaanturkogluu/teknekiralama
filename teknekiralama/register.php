<?php
include 'includes/header.php';
include 'includes/navbar.php';
require 'db.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'customer';
    if (!$name || !$email || !$password) {
        $errors[] = 'Tüm alanları doldurun.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Geçerli bir e-posta girin.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Şifre en az 6 karakter olmalı.';
    }
    if (empty($errors)) {
        // Rol id'sini bul
        $stmt = $pdo->prepare('SELECT id FROM roles WHERE name = ?');
        $stmt->execute([$role]);
        $roleRow = $stmt->fetch();
        if (!$roleRow) {
            $errors[] = 'Geçersiz rol.';
        } else {
            // E-posta benzersiz mi?
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Bu e-posta zaten kayıtlı.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (role_id, name, email, password) VALUES (?, ?, ?, ?)');
                $stmt->execute([$roleRow['id'], $name, $email, $hash]);
                $success = true;
            }
        }
    }
}
?>
<main>
    <div class="container">
        <h2>Kayıt Ol</h2>
        <?php if ($success): ?>
            <div class="success">Kayıt başarılı! <a href="login.php">Giriş yap</a></div>
        <?php else: ?>
            <?php if ($errors): ?>
                <div class="error">
                    <?php foreach ($errors as $e) echo '<p>'.$e.'</p>'; ?>
                </div>
            <?php endif; ?>
            <form method="post">
                <label>Ad Soyad</label>
                <input type="text" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                <label>E-posta</label>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                <label>Şifre</label>
                <input type="password" name="password" required>
                <label>Rol</label>
                <select name="role">
                    <option value="customer" <?php if(($_POST['role'] ?? '')=='customer') echo 'selected'; ?>>Müşteri</option>
                    <option value="owner" <?php if(($_POST['role'] ?? '')=='owner') echo 'selected'; ?>>Tekne Sahibi</option>
                </select>
                <button type="submit">Kayıt Ol</button>
            </form>
        <?php endif; ?>
    </div>
</main>
<?php include 'includes/footer.php'; ?> 