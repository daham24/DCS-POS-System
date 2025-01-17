<?php
include('../config/dbCon.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $repairId = intval($_POST['repair_id']);
    $repairCost = floatval($_POST['repair_cost']);
    $advancedPayment = floatval($_POST['advanced_payment'] ?? 0); // Fetch advanced payment, default to 0 if not provided

    if ($repairId > 0 && $repairCost >= 0 && $advancedPayment >= 0) {
        $invoiceNumber = 'INV-' . uniqid(); // Generate a unique invoice number

        // Insert into the database
        $query = "
            INSERT INTO repair_orders (repair_id, invoice_number, repair_cost, advanced_payment, created_at, updated_at)
            VALUES ($repairId, '$invoiceNumber', $repairCost, $advancedPayment, NOW(), NOW())
        ";

        if (mysqli_query($conn, $query)) {
            // Redirect back to repairs-view.php with a success flag
            header("Location: repairs-view.php?id=$repairId&success=1");
            exit;
        } else {
            // Handle database insert failure
            echo "<div class='alert alert-danger'>Failed to add repair cost. Please try again.</div>";
        }
    } else {
        // Handle invalid input
        echo "<div class='alert alert-danger'>Invalid inputs provided.</div>";
    }
} else {
    // Redirect back to repairs.php if accessed directly
    header('Location: repairs.php');
    exit;
}
?>