<?php
include('../../db_conn.php');
session_start();

header('Content-Type: application/json');

// Enable detailed error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_eval_id = isset($_POST['subject_eval_id']) ? intval($_POST['subject_eval_id']) : 0;

    if ($subject_eval_id > 0) {
        // Start a transaction
        mysqli_begin_transaction($conn);
        try {
            // Delete the subject from the database
            $delete_query = "DELETE FROM subject_to_eval WHERE subject_eval_id = ?";
            $stmt = mysqli_prepare($conn, $delete_query);
            mysqli_stmt_bind_param($stmt, 'i', $subject_eval_id);
            mysqli_stmt_execute($stmt);

            // Commit the transaction
            mysqli_commit($conn);

            echo json_encode(['success' => true]);
        } catch (mysqli_sql_exception $e) {
            // Rollback the transaction
            mysqli_rollback($conn);

            // Return detailed error message
            echo json_encode(['success' => false, 'message' => 'Error deleting subject: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid subject ID.']);
    }
}

