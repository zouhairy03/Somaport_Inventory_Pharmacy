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

        // Perform the deletion operation in the database
        $deleteSql = "DELETE FROM medicaments WHERE id = $medicamentId";
        if ($conn->query($deleteSql) === TRUE) {
            // Deletion successful
            header('Location: dashboard.php'); // Redirect to the dashboard or any other page
            exit;
        } else {
            // Error occurred during deletion
            echo "Error: " . $conn->error;
        }
    }
}
?>
