<?php
require 'firebase_config.php';

$id = $_POST['id'] ?? '';

if ($id == '') {
    header("Location: index.php?error=Invalid task ID");
    exit;
}

try {

    $database->getReference('tasks/'.$id)->remove();

    header("Location: index.php?success=Task deleted successfully");
    exit;

} catch (Exception $e) {

    header("Location: index.php?error=Failed to delete task");
    exit;

}
?>