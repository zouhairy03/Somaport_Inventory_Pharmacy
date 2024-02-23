<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

// Include database configuration
require_once 'config.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $ppv = $_POST['ppv'];
    $name = $_POST['name'];
    $lot = $_POST['lot'];
    $n_serie = $_POST['n_serie'];
    $arrival_date = $_POST['arrival_date'];
    $quantity = $_POST['quantity'];
    $expiry_date = $_POST['expiry_date'];

    // Insert new medicament into the database
    $sql = "INSERT INTO medicaments (ppv, name, LOT, N_serie) VALUES ('$ppv', '$name', '$lot', '$n_serie')";
    if ($conn->query($sql) === TRUE) {
        $medicament_id = $conn->insert_id;

        // Insert stock control details
        $stock_control_sql = "INSERT INTO stock_controls (medicament_id, arrival_date, quantity, expiry_date)
                             VALUES ('$medicament_id', '$arrival_date', '$quantity', '$expiry_date')";
        if ($conn->query($stock_control_sql) === TRUE) {
            $success_message = 'Medicament and stock control added successfully.';
        } else {
            $error_message = 'Error adding stock control: ' . $conn->error;
        }
    } else {
        $error_message = 'Error adding medicament: ' . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Add Medicament - MedicamentStockDB</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <span class="navbar-brand mb-0 h1">MedicamentStockDB</span>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Ajouter un médicament</h2>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <form action="" method="post">
        <div class="form-group">
            <label for="ppv">Prix unitaire (DH)</label>
            <input type="text" class="form-control" id="ppv" name="ppv" required>
        </div>
        <div class="form-group">
            <label for="name">Nom:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="lot">LOT:</label>
            <input type="text" class="form-control" id="lot" name="lot" required>
        </div>
        <div class="form-group">
            <label for="n_serie">Nºserie:</label>
            <input type="text" class="form-control" id="n_serie" name="n_serie" required>
        </div>
        <!-- New fields for stock control -->
        <div class="form-group">
            <label for="arrival_date">Date d'arrivée:</label>
            <input type="date" class="form-control" id="arrival_date" name="arrival_date" required>
        </div>
        <div class="form-group">
            <label for="quantity">Quantité:</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>
        <div class="form-group">
            <label for="expiry_date">Date d'expiration:</label>
            <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter un médicament</button>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
