<?php
session_start();
include('../config/dbCon.php'); // Include your database connection file

if (isset($_POST['add_bill_type'])) {
    // Add Bill Type
    $billType = mysqli_real_escape_string($conn, $_POST['bill_type']);

    // Check if bill type already exists
    $checkQuery = "SELECT * FROM utility_bills WHERE bill_type = '$billType'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $_SESSION['message'] = "Bill type already exists!";
    } else {
        // Insert new bill type with amount 0 (to distinguish bill types from actual bills)
        $insertQuery = "INSERT INTO utility_bills (bill_type, amount, bill_date) VALUES ('$billType', 0, NOW())";
        if (mysqli_query($conn, $insertQuery)) {
            $_SESSION['message'] = "Bill type added successfully!";
        } else {
            $_SESSION['message'] = "Error adding bill type: " . mysqli_error($conn);
        }
    }
    header("Location: utility-bills.php");
    exit();
}

if (isset($_POST['add_bill'])) {
    // Add Bill
    $billType = mysqli_real_escape_string($conn, $_POST['bill_type']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $billDate = mysqli_real_escape_string($conn, $_POST['bill_date']);

    // Insert bill
    $insertQuery = "INSERT INTO utility_bills (bill_type, amount, bill_date) VALUES ('$billType', '$amount', '$billDate')";
    if (mysqli_query($conn, $insertQuery)) {
        $_SESSION['message'] = "Bill added successfully!";
    } else {
        $_SESSION['message'] = "Error adding bill: " . mysqli_error($conn);
    }
    header("Location: utility-bills.php");
    exit();
}

if (isset($_POST['update_bill'])) {
    // Update Bill
    $billId = mysqli_real_escape_string($conn, $_POST['bill_id']);
    $billType = mysqli_real_escape_string($conn, $_POST['bill_type']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $billDate = mysqli_real_escape_string($conn, $_POST['bill_date']);

    // Update bill
    $updateQuery = "UPDATE utility_bills SET bill_type = '$billType', amount = '$amount', bill_date = '$billDate' WHERE id = '$billId'";
    if (mysqli_query($conn, $updateQuery)) {
        $_SESSION['message'] = "Bill updated successfully!";
    } else {
        $_SESSION['message'] = "Error updating bill: " . mysqli_error($conn);
    }
    header("Location: utility-bills.php");
    exit();
}
?>