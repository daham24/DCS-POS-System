<?php
// Database connection
include '../config/dbCon.php';
include '../config/function.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $repairId = intval($_POST['repair_id']);
    $repairCost = floatval($_POST['repair_cost']);

    if ($repairId > 0 && $repairCost > 0) {
        // Generate structured invoice number
        $yearMonth = date('Y-m'); // Current year and month
        $prefix = 'INV';

        // Fetch the last invoice number for the current month
        $query = "SELECT invoice_number FROM repair_orders WHERE invoice_number LIKE '$prefix-$yearMonth-%' ORDER BY id DESC LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            // Extract the last sequence number and increment it
            $lastNumber = intval(substr($row['invoice_number'], strrpos($row['invoice_number'], '-') + 1));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT); // Pad with leading zeros
        } else {
            $newNumber = '001'; // Start with 001 if no invoices exist
        }

        $invoiceNumber = "$prefix-$yearMonth-$newNumber";

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