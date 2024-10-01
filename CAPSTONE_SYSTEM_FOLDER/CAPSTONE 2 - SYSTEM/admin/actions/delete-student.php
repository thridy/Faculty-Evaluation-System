<?php
session_start();
include('../../db_conn.php');

if (isset($_POST['student_id'])) {
    $studentId = $_POST['student_id'];

    // Prepare the DELETE query
    $query = "DELETE FROM student_account WHERE student_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $studentId);

        if (mysqli_stmt_execute($stmt)) {
            // Set success message
            $_SESSION['message'] = 'Student account deleted successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            // Set error message
            $_SESSION['message'] = 'Error deleting student account.';
            $_SESSION['message_type'] = 'danger';
        }

        mysqli_stmt_close($stmt);
    } else {
        // Set error message
        $_SESSION['message'] = 'Failed to prepare the deletion query.';
        $_SESSION['message_type'] = 'danger';
    }

    // Redirect back to student list
    header("Location: ../student-list.php");
    exit();
} else {
    // Set error message
    $_SESSION['message'] = 'Invalid request.';
    $_SESSION['message_type'] = 'danger';

    // Redirect back to student list
    header("Location: ../student-list.php");
    exit();
}

