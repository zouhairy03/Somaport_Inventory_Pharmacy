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
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2a9d8f;
            --secondary-color: #264653;
            --accent-color: #e9c46a;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .update-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--primary-color);
        }

        .form-header h2 {
            color: var(--secondary-color);
            font-weight: 600;
            margin: 0;
        }

        .form-group label {
            color: var(--secondary-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .input-group-icon {
            border-right: none;
            background-color: white;
            color: var(--primary-color);
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(42,157,143,0.1);
        }

        .btn-update {
            background: var(--primary-color);
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            border: none;
        }

        .btn-update:hover {
            background: #228176;
            transform: translateY(-2px);
        }

        .back-link {
            color: var(--secondary-color);
            text-decoration: none;
            transition: color 0.3s ease;
            display: inline-block;
            margin-top: 1rem;
        }

        .back-link:hover {
            color: var(--primary-color);
        }

        .input-group-prepend {
            margin-right: -1px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="update-card">
        <div class="form-header">
            <h2><i class="fas fa-pills mr-2"></i>Modifier Médicament</h2>
        </div>
        
        <form action="update_continue.php" method="post">
            <input type="hidden" name="medicament_id" value="<?php echo $medicament['id']; ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="update_name"><i class="fas fa-tag mr-2"></i>Nom</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-icon">
                                    <i class="fas fa-pills"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" id="update_name" name="update_name" 
                                   value="<?php echo $medicament['name']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="update_ppv"><i class="fas fa-coins mr-2"></i>Prix par unité (DH)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" id="update_ppv" name="update_ppv" 
                                   value="<?php echo $medicament['ppv']; ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="update_lot"><i class="fas fa-barcode mr-2"></i>LOT</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-icon">
                                    <i class="fas fa-boxes"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" id="update_lot" name="update_lot" 
                                   value="<?php echo $medicament['LOT']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="update_n_serie"><i class="fas fa-hashtag mr-2"></i>Nº de série</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-icon">
                                    <i class="fas fa-list-ol"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" id="update_n_serie" name="update_n_serie" 
                                   value="<?php echo $medicament['N_serie']; ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="update_arrival_date"><i class="fas fa-calendar-day mr-2"></i>Date d'arrivée</label>
                        <input type="date" class="form-control" id="update_arrival_date" name="update_arrival_date" 
                               value="<?php echo $medicament['arrival_date']; ?>" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="update_expiry_date"><i class="fas fa-calendar-times mr-2"></i>Date d'expiration</label>
                        <input type="date" class="form-control" id="update_expiry_date" name="update_expiry_date" 
                               value="<?php echo $medicament['expiry_date']; ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="update_quantity"><i class="fas fa-cubes mr-2"></i>Quantité</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text input-group-icon">
                            <i class="fas fa-sort-numeric-up"></i>
                        </span>
                    </div>
                    <input type="number" class="form-control" id="update_quantity" name="update_quantity" 
                           value="<?php echo $medicament['quantity']; ?>" required>
                </div>
            </div>

            <button type="submit" class="btn btn-update" name="update_medicament">
                <i class="fas fa-save mr-2"></i>Mettre à jour
            </button>
            
            <a href="dashboard.php" class="back-link">
                <i class="fas fa-arrow-left mr-2"></i>Retour au tableau de bord
            </a>
        </form>
    </div>
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
