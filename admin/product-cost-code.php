<?php
include('../config/dbCon.php'); // Assuming this file connects to the database

// Save or Update Product Cost
if (isset($_POST['saveCost']) || isset($_POST['updateCost'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];
    $total_cost = $quantity * $unit_price; // Calculate the total cost
    $date = $_POST['date'];

    // If the date is empty, set it to the current date
    if (empty($date)) {
        $date = date('Y-m-d');
    }

    if (isset($_POST['saveCost'])) {
        // Insert product cost into the database
        $query = "INSERT INTO products_cost (product_id, quantity, unit_price, total_cost, date) 
                  VALUES ('$product_id', '$quantity', '$unit_price', '$total_cost', '$date')";
        $message = "Product cost added successfully!";
    } elseif (isset($_POST['updateCost'])) {
        // Update product cost in the database
        $edit_id = $_POST['edit_id'];
        $query = "UPDATE products_cost 
                  SET product_id = '$product_id', quantity = '$quantity', unit_price = '$unit_price', total_cost = '$total_cost', date = '$date'
                  WHERE id = '$edit_id'";
        $message = "Product cost updated successfully!";
    }

    if (mysqli_query($conn, $query)) {
        header("Location: product-cost.php?message=$message");
    } else {
        header("Location: product-cost.php?message=Error: " . mysqli_error($conn));
    }
}

// Delete Product Cost by ID
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $query = "DELETE FROM products_cost WHERE id = '$delete_id'";
    
    if (mysqli_query($conn, $query)) {
        header("Location: product-cost.php?message=Product cost deleted successfully!");
    } else {
        header("Location: product-cost.php?message=Error: " . mysqli_error($conn));
    }
}

?>