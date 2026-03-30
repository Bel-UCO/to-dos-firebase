<?php
    require 'firebase_config.php';
    $userProperties = [
        'email' => $_POST['email'],
        'password' => $_POST['password'],
        'displayName' => $_POST['displayName'],
    ];

    if ($userProperties['email'] == '' || $userProperties['password'] == '' || $userProperties['displayName'] == '') {
        header("Location: index.php?error=Please fill all required fields");
        exit;
    }

    try {

        $createdUser = $auth->createUser($userProperties);

        $auth->sendEmailVerificationLink($userProperties['email']);

        header("Location: index.php?success=Register Success, please verify your email");
        exit;

    } catch (Exception $e) {

        header("Location: index.php?error=Failed to register");
        exit;

    }

?>