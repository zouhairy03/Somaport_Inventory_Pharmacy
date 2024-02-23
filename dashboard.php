<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

// Include database configuration
require_once 'config.php';

// Check for impending expiry on login
$impendingExpirySql = "SELECT medicaments.name, stock_controls.expiry_date
                      FROM medicaments
                      INNER JOIN stock_controls ON medicaments.id = stock_controls.medicament_id
                      WHERE stock_controls.expiry_date >= CURDATE() AND stock_controls.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 5 DAY)";
$impendingExpiryResult = $conn->query($impendingExpirySql);

$impendingExpiryMedicaments = [];
if ($impendingExpiryResult) {
    while ($row = $impendingExpiryResult->fetch_assoc()) {
        $impendingExpiryMedicaments[] = $row['name'];
    }
} else {
    die("Query failed: " . $conn->error);
}

// Display a message if there are medicaments nearing expiry
if (!empty($impendingExpiryMedicaments)) {
    $medicamentList = implode(', ', $impendingExpiryMedicaments);
    echo '<div class="alert alert-warning" role="alert">';
    echo 'les médicaments suivants arrivent à expiration dans un 1 jour : ' . $medicamentList;
    echo '</div>';
}

// Retrieve data from the database (sample query)
$sql = "SELECT medicaments.*, stock_controls.arrival_date, stock_controls.quantity, stock_controls.expiry_date
        FROM medicaments
        INNER JOIN stock_controls ON medicaments.id = stock_controls.medicament_id
        ORDER BY stock_controls.arrival_date DESC
        LIMIT 10"; // Adjust the query based on your requirements

$result = $conn->query($sql);

// Check for query execution errors
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!-- The rest of your HTML and PHP code remains unchanged -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard - MedicamentStockDB</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
                <a class="nav-link" href="add_medicament.php">Ajouter un médicament</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
<h2 class="display-8" style="text-align: center; margin-bottom: 50px;">Bienvenue sur le tableau de bord MedicamentStockDB</h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-hover">
            <!-- Table headers -->
            <thead>
            <tr>
    <th>ID</th>
    <th>Nom</th>
    <th>LOT</th>
    <th>Nº de série</th>
    <th>Prix par unité (DH)</th>
    <th>Date d'arrivée</th>
    <th>Quantité</th>
    <th>Date d'expiration</th>
    <th>Statut</th>
    <th>Jours restants</th>
    <th>Action</th>
</tr>

            </thead>
            <!-- Table body -->
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['LOT']; ?></td>
                    <td><?php echo $row['N_serie']; ?></td>
                    <td><?php echo $row['ppv']; ?></td>
                    <td><?php echo $row['arrival_date']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo $row['expiry_date']; ?></td>
                    <td style="color: <?php echo ($row['quantity'] < 10) ? 'red' : 'green'; ?>">
                        <?php echo ($row['quantity'] < 10) ? 'repture de stock' : 'en stock'; ?>
                    </td>
                    <td>
                        <?php
                        $expiryDate = strtotime($row['expiry_date']);
                        $currentDate = strtotime(date('Y-m-d'));
                        $daysRemaining = floor(($expiryDate - $currentDate) / (60 * 60 * 24));

                        if ($daysRemaining <= 5) {
                            echo '<span style="color: red;">' . $daysRemaining . ' Jours restants</span>';
                        } else {
                            echo $daysRemaining . ' Jours restants';
                        }
                        ?>
                    </td>
                    <!-- Inside the <td> for Update, Delete, and Consult buttons -->
                    <td style="display: flex; gap:10px">
                        <!-- Update Button - Show Modal -->
                        <form action="update_medicament.php" method="post">
                            <input type="hidden" name="updateId" value="<?php echo $row['id'] ?>" />
                            <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        </form>

                        <!-- Delete Button - Show Modal -->
                     <!-- Inside the <td> for Update, Delete, and Consult buttons -->
<!-- Delete Button - Show Modal -->

    <form onsubmit="return confirm('Are you sure you want to delete this medicament?');" action="delete_medicament.php" method="post">
        <input type="hidden" name="deleteId" value="<?php echo $row['id'] ?>" />
        <button type="submit" class="btn btn-danger">
            <i class="fa-solid fa-trash"></i>
        </button>
    </form>



                        <!-- Consult Button - Form Submission -->
                        <form action="consult_medicament.php" method="post" style="display: inline;">
                            <input type="hidden" name="medicament_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn btn-info" name="consult_medicament"><i class="fa-solid fa-eye"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No data available.</p>
    <?php endif; ?>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
