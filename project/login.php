<?php
require 'firebase_config.php';

use Kreait\Firebase\Auth\SignIn\FailedToSignIn;

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($email == '' || $password == '') {
    header("Location: index.php?error=Please fill all required fields");
    exit;
}

try {
    $signInResult = $auth->signInWithEmailAndPassword($email, $password);
    $idToken = $signInResult->idToken();

    $verifiedIdToken = $auth->verifyIdToken($idToken);
    $uid = $verifiedIdToken->claims()->get('sub');
    $user = $auth->getUser($uid);

    if (!$user->emailVerified) {
        setcookie("firebase_token", "", time() - 3600, "/");
        header("Location: index.php?error=Please verify the account");
        exit;
    }

    setcookie("firebase_token", $idToken, time() + 3600, "/", "", false, true);

    header("Location: index.php?success=Login Success");
    exit;

} catch (FailedToSignIn $e) {
    $message = $e->getMessage();

    if (
        stripos($message, 'INVALID_LOGIN_CREDENTIALS') !== false ||
        stripos($message, 'INVALID_PASSWORD') !== false ||
        stripos($message, 'EMAIL_NOT_FOUND') !== false ||
        stripos($message, 'INVALID_EMAIL') !== false
    ) {
        header("Location: index.php?error=Wrong email or password");
        exit;
    }

    header("Location: index.php?error=" . urlencode($message));
    exit;

} catch (Exception $e) {
    header("Location: index.php?error=" . urlencode($e->getMessage()));
    exit;
}
?>