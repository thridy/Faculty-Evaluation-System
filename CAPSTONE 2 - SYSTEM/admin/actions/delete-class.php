<?php
session_start();
include '../../db_conn.php';

if (isset($_GET['id'])) {
    $classId = $_GET['id'];

    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM class_list WHERE class_id = ?");
    $stmt->bind_param("i", $classId);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Class deleted successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to delete class.";
        $_SESSION['message_type'] = "danger";
    }

    $stmt->close();
    header("Location: ../admin-classes.php");
    exit();
} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../admin-classes.php");
    exit();
}
