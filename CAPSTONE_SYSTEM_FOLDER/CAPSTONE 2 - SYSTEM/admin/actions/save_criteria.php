<?php
include '../../db_conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['criteria'])) {
        $criteria = $_POST['criteria'];

        // Start a transaction
        $conn->begin_transaction();

        try {
            // 1. Increment the ordered_by of all existing criteria by 1
            $update_query = "UPDATE criteria_list SET ordered_by = ordered_by + 1";
            if (!$conn->query($update_query)) {
                throw new Exception("Error updating ordered_by values: " . $conn->error);
            }

            // 2. Insert the new criterion with ordered_by = 1
            $insert_query = "INSERT INTO criteria_list (criteria, ordered_by) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_query);
            $ordered_by = 1;  // The new criterion should have ordered_by = 1
            if ($stmt) {
                $stmt->bind_param("si", $criteria, $ordered_by);
                if (!$stmt->execute()) {
                    throw new Exception("Error inserting new criterion: " . $stmt->error);
                }
                $stmt->close();
            } else {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            // Commit the transaction
            $conn->commit();
            echo 'Success';
        } catch (Exception $e) {
            // Roll back the transaction on error
            $conn->rollback();
            echo 'Error: ' . $e->getMessage();
        }

        $conn->close();
    } else {
        echo 'No criteria provided.';
    }
} else {
    echo 'Invalid request method.';
}
