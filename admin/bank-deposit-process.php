<?php
include('../config/dbCon.php'); 

// Handle adding a new bank
if (isset($_POST['add_bank'])) {
    $bankName = $_POST['bank_name'];
    $query = "INSERT INTO banks (name) VALUES ('$bankName')";
    mysqli_query($conn, $query);
    header('Location: bank-deposits.php'); // Redirect back to the main page
}

// Handle adding a new account
if (isset($_POST['add_account'])) {
    $bankId = $_POST['bank_id'];
    $accountNumber = $_POST['account_number'];
    $query = "INSERT INTO bank_accounts (bank_id, account_number) VALUES ('$bankId', '$accountNumber')";
    mysqli_query($conn, $query);
    header('Location: bank-deposits.php'); // Redirect back to the main page
}

// Handle adding a new deposit
if (isset($_POST['add_deposit'])) {
    $accountId = $_POST['account_id'];
    $depositAmount = $_POST['deposit_amount'];
    $depositDate = $_POST['deposit_date'];

    $query = "INSERT INTO daily_deposits (bank_account_id, deposit_amount, deposit_date) 
              VALUES ('$accountId', '$depositAmount', '$depositDate')";
    mysqli_query($conn, $query);
    header('Location: bank-deposits.php'); // Redirect back to the main page
}

// Handle updating an existing bank
if (isset($_POST['update_bank'])) {
    $bankId = $_POST['bank_id'];
    $bankName = $_POST['bank_name'];
    $query = "UPDATE banks SET name = '$bankName' WHERE id = '$bankId'";
    mysqli_query($conn, $query);
    header('Location: bank-deposits.php'); // Redirect back to the main page
}

// Handle updating an existing account
if (isset($_POST['update_account'])) {
    $accountId = $_POST['account_id'];
    $bankId = $_POST['bank_id'];
    $accountNumber = $_POST['account_number'];
    $query = "UPDATE bank_accounts SET bank_id = '$bankId', account_number = '$accountNumber' WHERE id = '$accountId'";
    mysqli_query($conn, $query);
    header('Location: bank-deposits.php'); // Redirect back to the main page
}

// Handle updating an existing deposit
if (isset($_POST['update_deposit'])) {
  $depositId = $_POST['deposit_id'];
  $accountId = $_POST['account_id'];
  $depositAmount = $_POST['deposit_amount'];
  $depositDate = $_POST['deposit_date'];

  // Update the deposit record
  $query = "UPDATE daily_deposits 
            SET bank_account_id = '$accountId', deposit_amount = '$depositAmount', deposit_date = '$depositDate' 
            WHERE id = '$depositId'";

  $result = mysqli_query($conn, $query);

  if ($result) {
      // Set success message
      $_SESSION['message'] = 'Deposit updated successfully!';
  } else {
      // Set error message
      $_SESSION['message'] = 'Failed to update deposit. Please try again.';
  }

  // Redirect back to the deposit edit page with the success/error message
  header('Location: bank-deposits.php?id=' . $depositId);
  exit();
}

// Get bank id from the query string (for account selection)
$bankId = isset($_GET['bank_id']) ? $_GET['bank_id'] : 0;

if ($bankId) {
    // Fetch accounts for the selected bank
    $query = "SELECT * FROM bank_accounts WHERE bank_id = '$bankId'";
    $result = mysqli_query($conn, $query);

    // Return the result as a JSON response
    $accounts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $accounts[] = $row;
    }

    echo json_encode($accounts);
} else {
    echo json_encode([]);
}
?>
