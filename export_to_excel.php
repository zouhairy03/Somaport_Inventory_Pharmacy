<?php
require 'vendor/autoload.php'; // Include the PhpSpreadsheet autoloader

// Include database configuration
require_once 'config.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create a new PhpSpreadsheet object
$spreadsheet = new Spreadsheet();

// Add a worksheet
$sheet = $spreadsheet->getActiveSheet();

// Set column headers
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Nom');
$sheet->setCellValue('C1', 'LOT');
$sheet->setCellValue('D1', 'Nº de série');
$sheet->setCellValue('E1', 'Prix En(DH)');
$sheet->setCellValue('F1', 'Date d\'arrivée');
$sheet->setCellValue('G1', 'Quantité');
$sheet->setCellValue('H1', 'Date d\'expiration');
$sheet->setCellValue('I1', 'Statut');
$sheet->setCellValue('J1', 'Jours restants');

// Retrieve data from the database (sample query)
$sql = "SELECT Somap_med.*, Somap_med.arrival_date, Somap_med.quantity, Somap_med.expiry_date
        FROM Somap_med
        ORDER BY Somap_med.arrival_date DESC
        LIMIT 10"; // Adjust the query based on your requirements

$result = $conn->query($sql);

// Check for query execution errors
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Populate the Excel sheet with data
$rowNumber = 2; // Start from the second row
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $row['id']);
    $sheet->setCellValue('B' . $rowNumber, $row['name']);
    $sheet->setCellValue('C' . $rowNumber, $row['LOT']);
    $sheet->setCellValue('D' . $rowNumber, $row['N_serie']);
    $sheet->setCellValue('E' . $rowNumber, $row['ppv']);
    $sheet->setCellValue('F' . $rowNumber, $row['arrival_date']);
    $sheet->setCellValue('G' . $rowNumber, $row['quantity']);
    $sheet->setCellValue('H' . $rowNumber, $row['expiry_date']);
    $sheet->setCellValue('I' . $rowNumber, ($row['quantity'] < 10) ? 'rupture de stock' : 'en stock');
    
    $expiryDate = strtotime($row['expiry_date']);
    $currentDate = strtotime(date('Y-m-d'));
    $daysRemaining = floor(($expiryDate - $currentDate) / (60 * 60 * 24));
    $sheet->setCellValue('J' . $rowNumber, $daysRemaining);

    $rowNumber++;
}

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="medicament_data.xlsx"');
header('Cache-Control: max-age=0');

// Save the Excel file to the output buffer
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Close the database connection
$conn->close();
?>