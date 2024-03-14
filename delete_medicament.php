<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

// Include database configuration
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['deleteId'])) {
        $medicamentId = $_POST['deleteId'];

        // Retrieve additional details before deletion (if needed)
        $selectSql = "SELECT * FROM Somap_med WHERE id = $medicamentId";
        $result = $conn->query($selectSql);

        if (!$result) {
            die("Query failed: " . $conn->error);
        }

        // Fetch additional details
        $medicament = $result->fetch_assoc();

        // Perform the deletion operation in the database for Somap_med
        $deleteSomapMedSql = "DELETE FROM Somap_med WHERE id = $medicamentId";
        if ($conn->query($deleteSomapMedSql) === TRUE) {
            // Deletion successful for Somap_med

            // Perform the deletion operation in the database for other related tables (if needed)
            // ...

            // Redirect to the dashboard or any other page
            header('Location: dashboard.php');
            exit;
        } else {
            // Error occurred during deletion
            echo "Error: " . $conn->error;
        }
    }
}
?>
