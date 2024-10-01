<?php
// Include the database connection file
include '../../db_conn.php'; 

// Start the session to use session variables
session_start();

// Check if the database connection is available
if (!isset($conn)) {
    $_SESSION['message'] = 'Database connection failed.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ../admin-acad-year.php');
    exit();
}

// Check if the ID is set in the query string
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare the SQL query to delete the academic year by ID
    $deleteQuery = "DELETE FROM academic_year WHERE acad_year_id = ?";
    $stmt = $conn->prepare($deleteQuery);

    // Check if the statement was prepared successfully
    if ($stmt === false) {
        $_SESSION['message'] = 'Error preparing the query: ' . $conn->error;
        $_SESSION['message_type'] = 'danger';
        header('Location: ../admin-acad-year.php');
        exit();
    }

    // Bind the parameter and execute the statement
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Academic year deleted successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error executing the query: ' . $stmt->error;
        $_SESSION['message_type'] = 'danger';
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Redirect back to the academic year page
    header('Location: ../admin-acad-year.php');
    exit();
} else {
    // If no ID is set, show an error message
    $_SESSION['message'] = 'Invalid request.';
    $_SESSION['message_type'] = 'danger';
    header('Location: ../admin-acad-year.php');
    exit();
}
