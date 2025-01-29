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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
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
        }

        .navbar {
            background: var(--secondary-color) !important;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 0.5rem 1rem;
        }

        .navbar-brand img {
            width: 180px;
            margin-left: 20px;
            transition: transform 0.3s ease;
        }

        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            padding: 0.8rem 1.2rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: white !important;
            background: rgba(255,255,255,0.1);
        }

        .welcome-heading {
            color: var(--secondary-color);
            font-weight: 600;
            margin: 2rem 0;
            text-align: center;
            position: relative;
            padding-bottom: 1rem;
        }

        .welcome-heading::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--primary-color);
        }

        .dashboard-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background: var(--secondary-color);
            color: white;
            border: none;
            padding: 1rem;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(42,157,143,0.05);
        }

        .btn-success {
            background: var(--primary-color);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background: #228176;
            transform: translateY(-2px);
        }

        .search-form .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 0.8rem 1.2rem;
        }

        .search-form .btn {
            border-radius: 0 8px 8px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .badge-danger {
            background: #e76f51;
            color: white;
        }

        .badge-success {
            background: #2a9d8f;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-buttons .btn {
            width: 36px;
            height: 36px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .pagination .page-link {
            color: var(--secondary-color);
            border: none;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .alert-warning {
            background: #ffe8cc;
            color: #d9480f;
            border: none;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .navbar-brand img {
                width: 140px;
                margin-left: 10px;
            }
            
            .table-responsive {
                border: 1px solid #dee2e6;
            }
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="#">
        <img src="somaport-removebg-preview.png" alt="Logo Somaport">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="add_medicament.php">
                    <i class="fas fa-plus-circle mr-2"></i>Ajouter
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>Déconnexion
                </a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2 class="welcome-heading">Gestion du Stock Médicaments</h2>

    <?php if (!empty($expiryMessage)) : ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <?php echo $expiryMessage; ?>
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="dashboard-card">
        <div class="card-body">
            <form method="post" class="search-form mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Rechercher par nom..." name="searchName" value="<?php echo $searchName; ?>">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-success" name="search">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Liste des Médicaments</h5>
                <form method="post" action="export_to_excel.php">
                    <button type="submit" class="btn btn-success" name="exportToExcel">
                        <i class="fas fa-file-excel mr-2"></i>Exporter Excel
                    </button>
                </form>
            </div>

            <?php if ($result->num_rows > 0) : ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>LOT</th>
                                <th>Nº série</th>
                                <th>Prix (DH)</th>
                                <th>Date arrivée</th>
                                <th>Quantité</th>
                                <th>Expiration</th>
                                <th>Statut</th>
                                <th>Jours restants</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['LOT']; ?></td>
                                <td><?php echo $row['N_serie']; ?></td>
                                <td><?php echo $row['ppv']; ?></td>
                                <td><?php echo $row['arrival_date']; ?></td>
                                <td><?php echo $row['quantity']; ?></td>
                                <td><?php echo $row['expiry_date']; ?></td>
                                <td>
                                    <span class="status-badge <?php echo ($row['quantity'] < 10) ? 'badge-danger' : 'badge-success'; ?>">
                                        <?php echo ($row['quantity'] < 10) ? 'Rupture' : 'En stock'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $expiryDate = strtotime($row['expiry_date']);
                                    $currentDate = strtotime(date('Y-m-d'));
                                    $daysRemaining = floor(($expiryDate - $currentDate) / (60 * 60 * 24));
                                    
                                    $daysClass = ($daysRemaining <= 5) ? 'text-danger font-weight-bold' : '';
                                    echo "<span class='$daysClass'>$daysRemaining Jours</span>";
                                    ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <form action="update_medicament.php" method="post">
                                            <input type="hidden" name="updateId" value="<?php echo $row['id'] ?>">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        </form>
                                        <form onsubmit="return confirm('Confirmer la suppression?');" action="delete_medicament.php" method="post">
                                            <input type="hidden" name="deleteId" value="<?php echo $row['id'] ?>">
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        <form action="consult_medicament.php" method="post">
                                            <input type="hidden" name="medicament_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-info">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($page = 1; $page <= $totalPages; $page++) : ?>
                            <li class="page-item <?php echo ($page == $currentPage) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page; ?>">
                                    <?php echo $page; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php else : ?>
                <div class="text-center py-4">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucun médicament trouvé</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Scripts remain the same -->
</body>
</html>