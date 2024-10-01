<?php
session_start();
include('../../db_conn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        die("Session expired. Please log in again.");
    }

    // Get user ID from session
    $id = intval($_SESSION['user_id']);

    // Retrieve form data
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Check if cropped image data is available and process it
    if (!empty($_POST['croppedImageData'])) {
        // Handle base64 encoded image
        $avatar = $_POST['croppedImageData'];
        $avatar = str_replace('data:image/jpeg;base64,', '', $avatar); // Remove base64 header
        $avatar = str_replace(' ', '+', $avatar); // Ensure correct base64 format
        $avatar = base64_decode($avatar); // Decode base64 to binary
    } else {
        $avatar = null;  // No image uploaded or cropped
    }

    // Build the SQL UPDATE query
    $updateQuery = "UPDATE admin_account SET 
        firstName='$firstName', 
        middleName='$middleName', 
        lastName='$lastName', 
        email='$email'";

    // Update password only if provided
    if ($password) {
        $updateQuery .= ", password='$password'";
    }

    // Update avatar only if a new image is uploaded and cropped
    if ($avatar) {
        $avatar = addslashes($avatar); // Escape the binary data for safe SQL insertion
        $updateQuery .= ", avatar='$avatar'";
    }

    // Complete the SQL query
    $updateQuery .= " WHERE admin_id='$id'";

    // Execute the query
    if (mysqli_query($conn, $updateQuery)) {
        // Redirect to dashboard on success
        header('Location: ../admin-dashboard.php');
        exit();
    } else {
        // Output error for debugging
        echo "Error updating record: " . mysqli_error($conn);
    }
} else {
    // Handle invalid request method
    echo "Invalid request method";
}

