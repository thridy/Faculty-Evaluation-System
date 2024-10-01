<?php
include('../../db_conn.php'); 

$acad_year_id = isset($_POST['acad_year_id']) ? intval($_POST['acad_year_id']) : 0;
$success = true;
$message = '';
$duplicate_classes = [];

if ($acad_year_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid academic year ID.']);
    exit;
}

// Check for duplicate classes
foreach ($_POST as $key => $value) {
    if (strpos($key, 'class_id_') === 0) {
        $class_id = intval($value);

        // Check if the class is already added to the evaluation_list for this academic year
        $check_query = "SELECT COUNT(*) as count FROM evaluation_list WHERE class_id = $class_id AND acad_year_id = $acad_year_id";
        $check_result = mysqli_query($conn, $check_query);
        
        if (!$check_result) {
            $success = false;
            $message = "Database error during checking: " . mysqli_error($conn);
            break;
        }

        $row = mysqli_fetch_assoc($check_result);

        if ($row['count'] > 0) {
            // Class is already added, mark as duplicate
            $duplicate_classes[] = $class_id;
        }
    }
}

if (count($duplicate_classes) > 0) {
    $success = false;
    $message = "Duplicate class IDs detected: " . implode(', ', $duplicate_classes) . ". Please select different classes.";
} else {
    // No duplicates found, proceed to insert all classes
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'class_id_') === 0) {
            $class_id = intval($value);

            // Insert new record into evaluation_list with updated columns
            $insert_query = "INSERT INTO evaluation_list (class_id, acad_year_id) 
                             VALUES ($class_id, $acad_year_id)";
            if (!mysqli_query($conn, $insert_query)) {
                $success = false;
                $message = "Database error during insertion: " . mysqli_error($conn);
                break;
            }
        }
    }
}

if ($success) {
    $message = "Classes successfully added!";
}

echo json_encode(['success' => $success, 'message' => $message, 'duplicate_classes' => $duplicate_classes]);

