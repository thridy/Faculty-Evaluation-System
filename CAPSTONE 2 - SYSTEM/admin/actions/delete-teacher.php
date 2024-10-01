<?php
session_start();
include('../../db_conn.php');

if (isset($_POST['teacher_id'])) {
    $teacherId = $_POST['teacher_id'];

    // Prepare the DELETE query
    $query = "DELETE FROM teacher_account WHERE teacher_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $teacherId);

        if (mysqli_stmt_execute($stmt)) {
            // Set success message
            $_SESSION['message'] = 'Teacher account deleted successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            // Set error message
            $_SESSION['message'] = 'Error deleting teacher account.';
            $_SESSION['message_type'] = 'danger';
        }

        mysqli_stmt_close($stmt);
    } else {
        // Set error message
        $_SESSION['message'] = 'Failed to prepare the deletion query.';
        $_SESSION['message_type'] = 'danger';
    }

    // Redirect back to teacher list
    header("Location: ../teacher-list.php");
    exit();
} else {
    // Set error message
    $_SESSION['message'] = 'Invalid request.';
    $_SESSION['message_type'] = 'danger';

    // Redirect back to teacher list
    header("Location: ../teacher-list.php");
    exit();
}

