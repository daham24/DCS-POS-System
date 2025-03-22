<?php

require '../config/function.php';

// Get the deposit ID from the URL
$depositId = checkParamId('id');

if (is_numeric($depositId)) {

    $depositId = validate($depositId);

    // Fetch the deposit by ID
    $deposit = getById('daily_deposits', $depositId);

    if ($deposit['status'] == 200) {

        // Optionally, fetch and delete any related data (if necessary)
        // For example, if you have related records in another table, delete them first
        // Here, we assume there are no related records other than the deposit itself

        // Delete the main deposit record
        $depositDeleteRes = delete('daily_deposits', $depositId);

        if ($depositDeleteRes) {
            // Set success message in session
            $_SESSION['message'] = 'Deposit deleted successfully!';
            header('Location: bank-deposits.php'); // Redirect back to the main page
            exit();
        } else {
            $_SESSION['message'] = 'Something went wrong. Please try again.';
            header('Location: bank-deposits.php');
            exit();
        }

    } else {
        $_SESSION['message'] = $deposit['message'];
        header('Location: bank-deposits.php');
        exit();
    }

} else {
    $_SESSION['message'] = 'Invalid deposit ID.';
    header('Location: bank-deposits.php');
    exit();
}
