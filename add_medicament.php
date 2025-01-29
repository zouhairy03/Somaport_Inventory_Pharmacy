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
    // Retrieve form data and sanitize inputs
    $ppv = $_POST['ppv'];
    $name = $_POST['name'];
    $lot = $_POST['lot'];
    $n_serie = $_POST['n_serie'];
    $arrival_date = $_POST['arrival_date'];
    $quantity = $_POST['quantity'];
    $expiry_date = $_POST['expiry_date'];

    // Use prepared statements for security
    $medicamentSql = "INSERT INTO Somap_med (ppv, name, LOT, N_serie, arrival_date, quantity, expiry_date)
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($medicamentSql);
    $stmt->bind_param("dssssii", $ppv, $name, $lot, $n_serie, $arrival_date, $quantity, $expiry_date);

    if ($stmt->execute()) {
        $success_message = '✅ Médicament ajouté avec succès.';
    } else {
        $error_message = '❌ Erreur lors de l\'ajout du médicament : ' . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ajouter un Médicament</title>
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

        .add-card {
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

        .btn-add {
            background: var(--primary-color);
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            border: none;
        }

        .btn-add:hover {
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
    <div class="add-card">
        <div class="form-header">
            <h2><i class="fas fa-plus-circle mr-2"></i>Ajouter un Médicament</h2>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success" role="alert"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-tag mr-2"></i>Nom</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="ppv"><i class="fas fa-coins mr-2"></i>Prix unitaire (DH)</label>
                        <input type="text" class="form-control" id="ppv" name="ppv" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="lot"><i class="fas fa-barcode mr-2"></i>LOT</label>
                <input type="text" class="form-control" id="lot" name="lot" required>
            </div>

            <div class="form-group">
                <label for="n_serie"><i class="fas fa-hashtag mr-2"></i>Nº de série</label>
                <input type="text" class="form-control" id="n_serie" name="n_serie" required>
            </div>

            <div class="form-group">
                <label for="arrival_date"><i class="fas fa-calendar-day mr-2"></i>Date d'arrivée</label>
                <input type="date" class="form-control" id="arrival_date" name="arrival_date" required>
            </div>

            <div class="form-group">
                <label for="expiry_date"><i class="fas fa-calendar-times mr-2"></i>Date d'expiration</label>
                <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
            </div>

            <div class="form-group">
                <label for="quantity"><i class="fas fa-cubes mr-2"></i>Quantité</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
            </div>

            <button type="submit" class="btn btn-add"><i class="fas fa-save mr-2"></i>Ajouter</button>
        </form>
    </div>
</div>
</body>
</html>
