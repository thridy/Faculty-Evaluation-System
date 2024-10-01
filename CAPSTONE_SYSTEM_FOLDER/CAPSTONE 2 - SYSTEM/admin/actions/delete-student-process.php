<?php
include('../../db_conn.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $student_id = isset($data['student_id']) ? intval($data['student_id']) : 0;

    if ($student_id > 0) {
        // Delete the student from the students_eval_restriction table
        $delete_query = "DELETE FROM students_eval_restriction WHERE student_id = $student_id";
        if (mysqli_query($conn, $delete_query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete the student.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid student ID.']);
    }
    exit;
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}

