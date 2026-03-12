<?php
require 'firebase_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?error=Invalid request");
    exit;
}

if (empty($_COOKIE['firebase_token'])) {
    header("Location: index.php?error=Please login first");
    exit;
}

try {
    $verifiedIdToken = $auth->verifyIdToken($_COOKIE['firebase_token']);
    $uid = $verifiedIdToken->claims()->get('sub');
} catch (Exception $e) {
    setcookie("firebase_token", "", time() - 3600, "/");
    header("Location: index.php?error=Session expired. Please login again");
    exit;
}

$id = trim($_POST['id'] ?? '');

if ($id == '') {
    header("Location: index.php?error=Invalid task ID");
    exit;
}

try {
    $taskReference = $database->getReference('tasks/' . $id);
    $task = $taskReference->getValue();

    if (!$task) {
        header("Location: index.php?error=Task not found");
        exit;
    }

    if (($task['created_by'] ?? '') !== $uid) {
        header("Location: index.php?error=You are not allowed to delete this task");
        exit;
    }

    $taskReference->remove();

    header("Location: index.php?success=Task deleted successfully");
    exit;
} catch (Exception $e) {
    header("Location: index.php?error=Failed to delete task");
    exit;
}
?>