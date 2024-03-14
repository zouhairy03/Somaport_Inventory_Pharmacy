<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

// Include database configuration
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateId'])) {
    $medicament_id = $_POST['updateId'];

    // Retrieve the medicament details from the database based on the ID
    $select_sql = "SELECT * FROM Somap_med WHERE id = '$medicament_id'";
    $result = $conn->query($select_sql);

    if ($result->num_rows > 0) {
        $medicament = $result->fetch_assoc();
        // Display a form with the current details of the medicament for updating
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <title>Update Medicament</title>
            <!-- Bootstrap CSS -->
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        </head>
        <body>
        <div class="container mt-5">
            <h2>Modifier Médicament</h2>
            <form action="update_continue.php" method="post">
                <input type="hidden" name="medicament_id" value="<?php echo $medicament['id']; ?>">
                <div class="form-group">
                    <label for="update_ppv">Prix par unité</label>
                    <input type="text" class="form-control" id="update_ppv" name="update_ppv" value="<?php echo $medicament['ppv']; ?>">
                </div>
                <div class="form-group">
                    <label for="update_name">Nom :</label>
                    <input type="text" class="form-control" id="update_name" name="update_name" value="<?php echo $medicament['name']; ?>">
                </div>
                <div class="form-group">
                    <label for="update_lot">LOT :</label>
                    <input type="text" class="form-control" id="update_lot" name="update_lot" value="<?php echo $medicament['LOT']; ?>">
                </div>
                <div class="form-group">
                    <label for="update_n_serie">Nº de série :</label>
                    <input type="text" class="form-control" id="update_n_serie" name="update_n_serie" value="<?php echo $medicament['N_serie']; ?>">
                </div>
                <div class="form-group">
                    <label for="update_arrival_date">Date d'arrivée :</label>
                    <input type="date" class="form-control" id="update_arrival_date" name="update_arrival_date" value="<?php echo $medicament['arrival_date']; ?>">
                </div>
                <div class="form-group">
                    <label for="update_quantity">Quantité :</label>
                    <input type="number" class="form-control" id="update_quantity" name="update_quantity" value="<?php echo $medicament['quantity']; ?>">
                </div>
                <div class="form-group">
                    <label for="update_expiry_date">Date d'expiration :</label>
                    <input type="date" class="form-control" id="update_expiry_date" name="update_expiry_date" value="<?php echo $medicament['expiry_date']; ?>">
                </div>
                <!-- Add other fields as needed -->

                <button type="submit" class="btn btn-primary" name="update_medicament">Mettre à jour</button>
            </form>
        </div>

        <!-- Bootstrap JS and dependencies -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        </body>
        </html>
        <?php
    } else {
        echo "Medicament not found.";
    }
} else {
    header('Location: dashboard.php');
    exit;
}
?>
