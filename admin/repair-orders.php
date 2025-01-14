<?php
// Database connection
include '../config/dbCon.php';
include '../config/function.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $repairId = intval($_POST['repair_id']);
    $repairCost = floatval($_POST['repair_cost']);

    if ($repairId > 0 && $repairCost > 0) {
        // Generate unique invoice number
        $invoiceNumber = 'INV-' . time();

        // Insert cost into `repair_orders`
        $query = "INSERT INTO repair_orders (repair_id, invoice_number, repair_cost) 
                  VALUES ('$repairId', '$invoiceNumber', '$repairCost')";

        if (mysqli_query($conn, $query)) {
            header("Location: repairs-view.php?id=$repairId&success=cost_added");
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        header("Location: repairs-view.php?id=$repairId&error=invalid_data");
        exit;
    }
} else {
    header("Location: repairs.php");
    exit;
}
?>