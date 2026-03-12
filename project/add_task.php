<?php
require 'firebase_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $deadline = $_POST['deadline'] ?? '';
    $priority = $_POST['priority'] ?? '';
    $status = $_POST['status'] ?? '';

    // validasi
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
            'status' => $status
        ]);

        header("Location: index.php?success=Task added successfully");
        exit;

    } catch (Exception $e) {

        header("Location: index.php?error=Failed to add task");
        exit;

    }
}
?>