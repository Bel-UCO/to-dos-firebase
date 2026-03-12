<?php
require 'firebase_config.php';

$id = $_POST['task_id'] ?? '';

$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$deadline = $_POST['deadline'] ?? '';
$priority = $_POST['priority'] ?? '';
$status = $_POST['status'] ?? '';

if ($id == '') {
    header("Location: index.php?error=Invalid task ID");
    exit;
}

if ($title == '' || $description == '' || $deadline == '') {
    header("Location: index.php?error=Please fill all required fields");
    exit;
}

try {

    $database->getReference('tasks/'.$id)->update([
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