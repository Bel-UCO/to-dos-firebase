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

$id = trim($_POST['task_id'] ?? '');
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$deadline = trim($_POST['deadline'] ?? '');
$priority = trim($_POST['priority'] ?? '');
$status = trim($_POST['status'] ?? '');

if ($id == '') {
    header("Location: index.php?error=Invalid task ID");
    exit;
}

if ($title == '' || $description == '' || $deadline == '') {
    header("Location: index.php?error=Please fill all required fields");
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
        header("Location: index.php?error=You are not allowed to update this task");
        exit;
    }

    $taskReference->update([
        'title' => $title,
        'description' => $description,
        'deadline' => $deadline,
        'priority' => $priority,
        'status' => $status
    ]);

    header("Location: index.php?success=Task updated successfully");
    exit;
} catch (Exception $e) {
    header("Location: index.php?error=Failed to update task");
    exit;
}
?>