<?php
require 'firebase_config.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($email == '' || $password == '') {
    header("Location: index.php?error=Please fill all required fields");
    exit;
}

try {
    $signInResult = $auth->signInWithEmailAndPassword($email, $password);
    $idToken = $signInResult->idToken();

    // false for localhost/http, true only if your site uses https
    setcookie("firebase_token", $idToken, time() + 3600, "/", "", false, true);

    header("Location: index.php?success=Login Success");
    exit;

} catch (Exception $e) {
    header("Location: index.php?error=Failed to login");
    exit;
}
?>