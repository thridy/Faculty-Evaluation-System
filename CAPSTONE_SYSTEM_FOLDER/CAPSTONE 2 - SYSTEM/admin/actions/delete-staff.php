<?php
session_start();
include('../../db_conn.php');

if (isset($_POST['staff_id'])) {
    $staffId = $_POST['staff_id'];

    // Prepare the DELETE query
    $query = "DELETE FROM staff_account WHERE staff_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $staffId);

        if (mysqli_stmt_execute($stmt)) {
            // Set success message
            $_SESSION['message'] = 'Staff account deleted successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            // Set error message
            $_SESSION['message'] = 'Error deleting staff account.';
            $_SESSION['message_type'] = 'danger';
        }

        mysqli_stmt_close($stmt);
    } else {
        // Set error message
        $_SESSION['message'] = 'Failed to prepare the deletion query.';
        $_SESSION['message_type'] = 'danger';
    }

    // Redirect back to staff list
    header("Location: ../staff-list.php");
    exit();
} else {
    // Set error message
    $_SESSION['message'] = 'Invalid request.';
    $_SESSION['message_type'] = 'danger';

    // Redirect back to staff list
    header("Location: ../staff-list.php");
    exit();
}

