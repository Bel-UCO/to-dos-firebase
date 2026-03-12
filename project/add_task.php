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

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$deadline = trim($_POST['deadline'] ?? '');
$priority = trim($_POST['priority'] ?? 'low');
$status = trim($_POST['status'] ?? 'pending');

if ($title == '' || $description == '' || $deadline == '') {
    header("Location: index.php?error=Please fill all required fields");
    exit;
}

try {
    $database->getReference('tasks')->push([
        'title' => $title,
        'description' => $description,
        'deadline' => $deadline,
        'priority' => $priority,
        'status' => $status,
        'created_by' => $uid
    ]);

    header("Location: index.php?success=Task added successfully");
    exit;
} catch (Exception $e) {
    header("Location: index.php?error=Failed to add task");
    exit;
}
?>