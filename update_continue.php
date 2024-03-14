<?php
session_start();

// Include database configuration
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_medicament'])) {
    $medicament_id = $_POST['medicament_id'];

    // Retrieve form data
    $update_ppv = $_POST['update_ppv'];
    $update_name = $_POST['update_name'];
    $update_lot = $_POST['update_lot'];
    $update_n_serie = $_POST['update_n_serie'];
    $update_arrival_date = $_POST['update_arrival_date'];
    $update_quantity = $_POST['update_quantity'];
    $update_expiry_date = $_POST['update_expiry_date'];

    // Update the medicament in the database
    $update_sql = "UPDATE Somap_med
                   SET ppv = '$update_ppv',
                       name = '$update_name',
                       LOT = '$update_lot',
                       N_serie = '$update_n_serie',
                       arrival_date = '$update_arrival_date',
                       quantity = '$update_quantity',
                       expiry_date = '$update_expiry_date'
                   WHERE id = '$medicament_id'";

    if ($conn->query($update_sql) === TRUE) {
        $success_message = 'Médicament mis à jour avec succès.';
    } else {
        $error_message = 'Erreur lors de la mise à jour du médicament : ' . $conn->error;
    }
}

// Redirect back to the dashboard.php with a success or error message
header('Location: dashboard.php' . (isset($success_message) ? '?success=' . urlencode($success_message) : (isset($error_message) ? '?error=' . urlencode($error_message) : '')));
exit;
?>
