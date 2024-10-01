<?php
session_start();
include '../../db_conn.php';

if (isset($_GET['id'])) {
    $subjectId = $_GET['id'];

    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM subject_list WHERE subject_id = ?");
    $stmt->bind_param("i", $subjectId);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Subject deleted successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to delete subject.";
        $_SESSION['message_type'] = "danger";
    }

    $stmt->close();
    $conn->close();
    header("Location: ../admin-subjects.php");
    exit();
} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../admin-subjects.php");
    exit();
}
