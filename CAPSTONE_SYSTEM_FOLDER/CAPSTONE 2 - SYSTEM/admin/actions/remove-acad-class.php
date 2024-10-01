<?php
include('../../db_conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = intval($_POST['class_id']);
    $acad_year_id = intval($_POST['acad_year_id']);

    // Perform the deletion query from evaluation_list table, including acad_year_id in the condition
    $query = "DELETE FROM evaluation_list WHERE class_id = $class_id AND acad_year_id = $acad_year_id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Class removed successfully from evaluation list.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove class from evaluation list: ' . mysqli_error($conn)]);
    }
}
