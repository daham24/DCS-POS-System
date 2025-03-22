<?php
include('includes/header.php');
include('../config/dbCon.php');

// Check if 'id' is passed as a parameter
if (isset($_GET['id'])) {
    $depositId = $_GET['id'];

    // Fetch the deposit record for editing
    $query = "SELECT dd.*, ba.account_number, b.name AS bank_name 
              FROM daily_deposits dd
              JOIN bank_accounts ba ON dd.bank_account_id = ba.id
              JOIN banks b ON ba.bank_id = b.id
              WHERE dd.id = '$depositId'";

    $result = mysqli_query($conn, $query);
    $deposit = mysqli_fetch_assoc($result);

    // If deposit not found
    if (!$deposit) {
        echo "Deposit not found!";
        exit;
    }
} else {
    echo "Invalid request!";
    exit;
}

// Fetch all banks and accounts for dropdowns
$banks = mysqli_query($conn, "SELECT * FROM banks");
$accounts = mysqli_query($conn, "SELECT * FROM bank_accounts");

?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4>Edit Deposit</h4>
        </div>
        <div class="card-body">

            <?php alertMessage(); ?> <!-- Display the alert message if exists -->

            <form method="POST" action="bank-deposit-process.php">
                <input type="hidden" name="deposit_id" value="<?= $deposit['id']; ?>">

                <!-- Select Bank -->
                <div class="mb-3">
                    <label for="bank_id">Select Bank</label>
                    <select name="bank_id" class="form-select" required>
                        <option value="">Select a Bank</option>
                        <?php while ($bank = mysqli_fetch_assoc($banks)) : ?>
                            <option value="<?= $bank['id']; ?>" 
                                <?= $bank['id'] == $deposit['bank_account_id'] ? 'selected' : ''; ?>>
                                <?= $bank['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Select Account -->
                <div class="mb-3">
                    <label for="account_id">Select Account</label>
                    <select name="account_id" class="form-select" required>
                        <option value="<?= $deposit['bank_account_id']; ?>" selected><?= $deposit['account_number']; ?></option>
                        <?php while ($account = mysqli_fetch_assoc($accounts)) : ?>
                            <option value="<?= $account['id']; ?>"><?= $account['account_number']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Deposit Amount -->
                <div class="mb-3">
                    <label for="deposit_amount">Deposit Amount</label>
                    <input type="number" name="deposit_amount" class="form-control" value="<?= $deposit['deposit_amount']; ?>" required>
                </div>

                <!-- Deposit Date -->
                <div class="mb-3">
                    <label for="deposit_date">Deposit Date</label>
                    <input type="date" name="deposit_date" class="form-control" value="<?= $deposit['deposit_date']; ?>" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="update_deposit" class="btn btn-primary mt-2">Update Deposit</button>
            </form>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
