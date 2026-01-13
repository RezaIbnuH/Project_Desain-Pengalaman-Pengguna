<?php
session_start();
// Generate CSRF Token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Secure random token
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <title>Login Page</title>
</head>
<body class="login-bg">
    <div class="auth-container">
        <div class="form-container">
            <!-- Display error if login fails -->
            <?php if (isset($_GET['error'])): ?>
    <?php if ($_GET['error'] == 'invalid_credentials'): ?>
        <div class="error">Login gagal. Email atau password salah.</div>
    <?php elseif ($_GET['error'] == 'invalid_email'): ?>
        <div class="error">Format email tidak valid.</div>
    <?php elseif ($_GET['error'] == 'csrf'): ?>
        <div class="error">Sesi tidak valid. Silakan refresh dan coba lagi.</div>
    <?php elseif ($_GET['error'] == 'empty'): ?>
        <div class="error">Email dan password wajib diisi.</div>
    <?php endif; ?>
<?php endif; ?>

            <form method="POST" action="login_process.php">
                <h1>Login</h1>
                <input name="username" type="email" placeholder="Email" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                <input name="password" type="password" placeholder="Password" required>

                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <button type="submit">Login</button>
            </form>
        </div>
        <div class="image-container">
            <img class="ilustrasi-login" src="../css/assets/login.jpg" alt="Login Illustration">
        </div>
    </div>
</body>
</html>