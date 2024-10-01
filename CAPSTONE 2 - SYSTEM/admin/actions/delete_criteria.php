<?php
include '../../db_conn.php'; // Ensure the database connection is correct

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['criteria_id'])) {
        $criteria_id = $_POST['criteria_id'];

        // Prepare and execute the SQL query to delete the criteria
        $stmt = $conn->prepare("DELETE FROM criteria_list WHERE criteria_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $criteria_id);  // Bind the criteria_id as an integer

            if ($stmt->execute()) {
                // Reorder the remaining criteria after deletion
                reorderCriteria($conn);

                echo 'Success';
            } else {
                echo 'Error: ' . $stmt->error;
            }

            $stmt->close();
        } else {
            echo 'Prepare failed: ' . $conn->error;
        }

        $conn->close();
    } else {
        echo 'No criteria_id provided.';
    }
} else {
    echo 'Invalid request method.';
}

// Function to reorder the 'ordered_by' column after deletion
function reorderCriteria($conn) {
    // Get all the criteria ordered by the current 'ordered_by' value
    $result = $conn->query("SELECT criteria_id FROM criteria_list ORDER BY ordered_by ASC");

    if ($result->num_rows > 0) {
        $newOrder = 1;

        // Loop through the rows and update 'ordered_by' to be consecutive
        while ($row = $result->fetch_assoc()) {
            $criteria_id = $row['criteria_id'];

            // Update the 'ordered_by' field
            $stmt = $conn->prepare("UPDATE criteria_list SET ordered_by = ? WHERE criteria_id = ?");
            $stmt->bind_param("ii", $newOrder, $criteria_id);

            if (!$stmt->execute()) {
                echo 'Error: ' . $stmt->error;
            }

            $newOrder++;
        }

        $stmt->close();
    }
}
