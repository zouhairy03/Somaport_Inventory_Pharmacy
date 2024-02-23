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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consult_medicament'])) {
    // Retrieve medicament details based on the provided medicament_id
    if (isset($_POST['medicament_id'])) {
        $medicament_id = $_POST['medicament_id'];

        // Query to retrieve detailed information about the selected medicament
        $query = "SELECT * FROM medicaments WHERE id = $medicament_id";
        $result = $conn->query($query);

        // Check if the query was successful
        if (!$result) {
            die("Query failed: " . $conn->error);
        }

        // Fetch medicament details
        $medicament = $result->fetch_assoc();
    } else {
        // If no medicament_id is provided, redirect to the main dashboard
        header('Location: dashboard.php');
        exit;
    }
} else {
    // If the form is not submitted, redirect to the main dashboard
    header('Location: dashboard.php');
    exit;
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Medicament Details - MedicamentStockDB</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <span class="navbar-brand mb-0 h1">MedicamentStockDB</span>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Se déconnecter</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Retour au tableau de bord</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Détails du médicament</h2>

    <?php if ($medicament): ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">ID du médicament: <?php echo $medicament['id']; ?></h5>
                <p class="card-text">Nom: <?php echo $medicament['name']; ?></p>
                <p class="card-text">LOT: <?php echo $medicament['LOT']; ?></p>
                <p class="card-text">Nºserie: <?php echo $medicament['N_serie']; ?></p>
                <p class="card-text">Prix ​par unité: <?php echo $medicament['ppv']; ?></p>
                <!-- Add more details as needed -->

                <!-- Add additional details or information as needed -->

            </div>
        </div>
    <?php else: ?>
        <p>Aucun détail sur le médicament disponible.</p>
    <?php endif; ?>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
