<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

// Include database configuration
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['medicament_id'])) {
    $medicament_id = $_GET['medicament_id'];

    // Perform deletion from the database
    $delete_stock_sql = "DELETE FROM stock_controls WHERE medicament_id='$medicament_id'";
    $conn->query($delete_stock_sql);

    $delete_medicament_sql = "DELETE FROM medicaments WHERE id='$medicament_id'";
    if ($conn->query($delete_medicament_sql) === TRUE) {
        // Successfully deleted, you can redirect or show a success message
        echo "Médicament supprimé avec succès.";
    } else {
        // Handle the error
        echo "Erreur lors de la suppression de l'enregistrement : " . $conn->error;
    }
} else {
    // Redirect to the dashboard if the request method is not GET or medicament_id is not set
    header('Location: dashboard.php');
    exit;
}
?>
