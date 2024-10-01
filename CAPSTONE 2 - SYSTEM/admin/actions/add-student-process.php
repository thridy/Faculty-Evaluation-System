<?php
// Include database connection
include('../../db_conn.php');

// Ensure proper JSON header
header('Content-Type: application/json');

// Enable error logging and display errors
ini_set('display_errors', 0); // Do not display errors to the client
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log'); // Update with your error log path
error_reporting(E_ALL);

// Clear any previous output
ob_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['studentId'])) {
    $class_id = intval($_POST['class_id']);
    $student_id = intval($_POST['studentId']);
    $acad_year_id = isset($_POST['acad_year_id']) ? intval($_POST['acad_year_id']) : 0;

    // Validate inputs
    if ($class_id == 0 || $student_id == 0 || $acad_year_id == 0) {
        ob_end_clean();
        echo json_encode(['error' => 'Invalid input! Please register the student first.']);
        exit;
    }

    // Check if the student exists in student_account
    $student_exists_query = "SELECT COUNT(*) AS student_exists FROM student_account WHERE student_id = $student_id";
    $student_exists_result = mysqli_query($conn, $student_exists_query);
    $student_exists_row = mysqli_fetch_assoc($student_exists_result);

    if ($student_exists_row['student_exists'] == 0) {
        // Student does not exist in the student_account table
        ob_end_clean();
        echo json_encode(['error' => 'This student does not exist in the student account database.']);
        exit;
    }

    // Fetch evaluation_id based on class_id and acad_year_id
    $query = "
        SELECT evaluation_id
        FROM evaluation_list
        WHERE class_id = $class_id
        AND acad_year_id = $acad_year_id";

    $result = mysqli_query($conn, $query);

    if (!$result || mysqli_num_rows($result) == 0) {
        ob_end_clean();
        echo json_encode(['error' => 'Evaluation not found for this class and academic year ID.']);
        exit;
    }

    $eval_row = mysqli_fetch_assoc($result);
    $evaluation_id = $eval_row['evaluation_id'];

    // Check if the student is already added to any evaluation (including other classes)
    $check_query = "
        SELECT COUNT(*) AS student_count 
        FROM students_eval_restriction 
        WHERE student_id = $student_id
    ";

    $check_result = mysqli_query($conn, $check_query);
    $check_row = mysqli_fetch_assoc($check_result);

    if ($check_row['student_count'] > 0) {
        // Student already exists in another class
        ob_end_clean();
        echo json_encode(['error' => 'This student has already been added to this class or other other classes.']);
        exit;
    }

    // Insert the student into students_eval_restriction table
    $insert_query = "
        INSERT INTO students_eval_restriction (evaluation_id, student_id) 
        VALUES ($evaluation_id, $student_id)";

    if (mysqli_query($conn, $insert_query)) {
        ob_end_clean();
        echo json_encode([
            'success' => 'Student added successfully!',
            'student_id' => $student_id  // Include student_id in the response
        ]);
    } else {
        ob_end_clean();
        echo json_encode(['error' => 'Failed to add student: ' . mysqli_error($conn)]);
    }
        
    exit;
} else {
    ob_end_clean();
    echo json_encode(['error' => 'Invalid request.']);
    exit;
}
