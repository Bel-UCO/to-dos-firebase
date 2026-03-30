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

} catch (\Kreait\Firebase\Exception\Auth\InvalidPassword $e) {
    header("Location: index.php?error=Wrong password");
    exit;

} catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
    header("Location: index.php?error=Account not found");
    exit;

} catch (Exception $e) {
    header("Location: index.php?error=Failed to login");
    exit;
}
?>