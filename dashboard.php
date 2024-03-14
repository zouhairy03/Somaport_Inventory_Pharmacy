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
$impendingExpirySql = "SELECT Somap_med.name, Somap_med.expiry_date
                      FROM Somap_med
                      WHERE Somap_med.expiry_date >= CURDATE() AND Somap_med.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 5 DAY)";
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
    $expiryMessage = 'Les médicaments suivants arrivent à expiration dans 1 jour  : ' . $medicamentList;
} else {
    $expiryMessage = '';
}

// Number of items per page
$itemsPerPage = 10;

// Initialize variables for search
$searchName = '';
$whereClause = '';

// Handle search form submission
if (isset($_POST['search'])) {
    $searchName = mysqli_real_escape_string($conn, $_POST['searchName']);
    $whereClause = " WHERE Somap_med.name LIKE '%$searchName%'";
}

// Retrieve total number of rows without limiting for pagination and search
$totalRowsSql = "SELECT COUNT(*) as total FROM Somap_med" . $whereClause;
$totalRowsResult = $conn->query($totalRowsSql);
$totalRows = ($totalRowsResult) ? $totalRowsResult->fetch_assoc()['total'] : 0;

// Calculate the total number of pages
$totalPages = ceil($totalRows / $itemsPerPage);

// Get the current page number (default to 1 if not set)
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Calculate the offset for the SQL query
$offset = ($currentPage - 1) * $itemsPerPage;

// Retrieve data from the database with pagination and search
$sql = "SELECT Somap_med.*, Somap_med.arrival_date, Somap_med.quantity, Somap_med.expiry_date
        FROM Somap_med" . $whereClause . "
        ORDER BY id DESC
        LIMIT $offset, $itemsPerPage"; // Adjust the query based on your requirements

$result = $conn->query($sql);

// Check for query execution errors
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard - MedicamentStockDB</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Add your custom styles here */
        body {
            padding-top: 20px;
        }

        .navbar {
            margin-bottom: 20px;
        }

        .welcome-heading {
            text-align: center;
            margin-bottom: 50px;
        }

        .search-form,
        .export-form {
            margin-bottom: 20px;
        }

        .pagination {
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <span class="navbar-brand mb-0 h1"><img src="somaport-removebg-preview.png" style="width: 50%; margin-left: 40px;"  alt="Logo Somaport" ></span>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
         
                <li class="nav-item">
                    <a class="nav-link" href="add_medicament.php">Ajouter un médicament</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Se déconnecter <i class="fa-solid fa-arrow-right-from-bracket"></i></a>
                </li>

            </ul>
        </div>
    </nav>

    <div class="container">
        <h2 class="display-8 welcome-heading">Bienvenue sur le tableau de bord MedicamentStockDB</h2>

        <!-- Expiry Alert -->
        <?php if (!empty($expiryMessage)) : ?>
            <div class="alert alert-warning" role="alert">
                <?php echo $expiryMessage; ?>
            </div>
        <?php endif; ?>

        <!-- Search by Name Form -->
        <form method="post" class="search-form">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Rechercher par nom" name="searchName" value="<?php echo $searchName; ?>">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-outline-secondary" name="search">Rechercher</button>
                </div>
            </div>
        </form>

        <!-- Export to Excel Button -->
        <form method="post" action="export_to_excel.php" class="export-form">
            <button type="submit" class="btn btn-success" name="exportToExcel">Exporter vers Excel <i class="fa-sharp fa-solid fa-file-excel"></i> </button>
        </form>

        <?php if ($result->num_rows > 0) : ?>
            <table class="table table-hover">
                <!-- Table headers -->
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>LOT</th>
                        <th>Nº de série</th>
                        <th>Prix En(DH)</th>
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
                    <?php while ($row = $result->fetch_assoc()) : ?>
                    <!-- ... (your existing table row content) ... -->
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
        <?php echo ($row['quantity'] < 10) ? 'rupture de stock' : 'en stock'; ?>
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
    <td style="display: flex; gap:10px">
        <!-- Update Button - Show Modal -->
        <form action="update_medicament.php" method="post">
            <input type="hidden" name="updateId" value="<?php echo $row['id'] ?>" />
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-pen"></i>
            </button>
        </form>

        <!-- Delete Button - Show Modal -->
        <form onsubmit="return confirm('Are you sure you want to delete this medicament?');" action="delete_medicament.php" method="post">
            <input type="hidden" name="deleteId" value="<?php echo $row['id'] ?>" />
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i>
            </button>
        </form>

        <!-- Consult Button - Form Submission -->
        <form action="consult_medicament.php" method="post" style="display: inline;">
            <input type="hidden" name="medicament_id" value="<?php echo $row['id']; ?>">
            <button type="submit" class="btn btn-info" name="consult_medicament">
                <i class="fas fa-eye"></i>
            </button>
        </form>
    </td>
</tr>
<!-- ... (end of your existing table row content) ... -->
<?php endwhile; ?>


                </tbody>
            </table>

            <!-- Display pagination links -->
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <?php for ($page = 1; $page <= $totalPages; $page++) : ?>
                        <li class="page-item <?php echo ($page == $currentPage) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page; ?>"><?php echo $page; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php else : ?>
            <p class="text-muted">Aucune donnée disponible.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>

</html>
