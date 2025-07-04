<?php
include 'includes/header.php';
include 'includes/navbar.php';
require 'db.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$email || !$password) {
        $error = 'Tüm alanları doldurun.';
    } else {
        $stmt = $pdo->prepare('SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && $user['password'] && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role_name'];
            $_SESSION['name'] = $user['name'];
            // Role göre yönlendirme
            if ($user['role_name'] == 'admin') {
                header('Location: admin/index.php');
            } elseif ($user['role_name'] == 'owner') {
                header('Location: owner/index.php');
            } else {
                header('Location: customer/index.php');
            }
            exit;
        } else {
            $error = 'Geçersiz e-posta veya şifre.';
        }
    }
}
?>
<main>
    <div class="container">
        <h2>Giriş Yap</h2>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <form method="post">
            <label>E-posta</label>
            <input type="email" name="email" required>
            <label>Şifre</label>
            <input type="password" name="password" required>
            <button type="submit">Giriş Yap</button>
        </form>
        <p>Hesabınız yok mu? <a href="register.php">Kayıt Ol</a></p>
    </div>
</main>
<?php include 'includes/footer.php'; ?> 