<?php
session_start();
include('../config/dbCon.php'); // Include your database connection file

// Check if 'id' is passed as a parameter
if (isset($_GET['id'])) {
    $billId = $_GET['id'];

    // Fetch the bill record
    $query = "SELECT * FROM utility_bills WHERE id = '$billId'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Delete the bill
        $deleteQuery = "DELETE FROM utility_bills WHERE id = '$billId'";
        if (mysqli_query($conn, $deleteQuery)) {
            $_SESSION['message'] = "Bill deleted successfully!";
        } else {
            $_SESSION['message'] = "Error deleting bill: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['message'] = "Bill not found!";
    }
} else {
    $_SESSION['message'] = "Invalid request!";
}

header("Location: utility-bills.php");
exit();
?>