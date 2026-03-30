<?php
require 'firebase_config.php';

if (!empty($_COOKIE['firebase_token'])) {
    try {
        $verifiedIdToken = $auth->verifyIdToken($_COOKIE['firebase_token']);
        $uid = $verifiedIdToken->claims()->get('sub');
        $user = $auth->getUser($uid);

        if ($user->emailVerified) {
            header("Location: index.php");
            exit;
        }

        setcookie("firebase_token", "", time() - 3600, "/");
    } catch (Exception $e) {
        setcookie("firebase_token", "", time() - 3600, "/");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div style="max-width:400px; margin:50px auto;">
        <h2 style="text-align:center;">Login</h2>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert error">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert success">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" style="display:flex; flex-direction:column; gap:12px;">
            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <div style="text-align:center; margin-top:16px;">
            <a href="index.php">Back to Home</a>
        </div>
    </div>
</body>
</html>