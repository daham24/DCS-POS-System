<?php
include('includes/header.php');
include('../config/dbCon.php'); // Include your database connection file

// Check if 'id' is passed as a parameter
if (isset($_GET['id'])) {
    $billId = $_GET['id'];

    // Fetch the bill record for editing
    $query = "SELECT * FROM utility_bills WHERE id = '$billId'";
    $result = mysqli_query($conn, $query);
    $bill = mysqli_fetch_assoc($result);

    // If bill not found
    if (!$bill) {
        echo "Bill not found!";
        exit;
    }
} else {
    echo "Invalid request!";
    exit;
}
?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4>Edit Bill</h4>
        </div>
        <div class="card-body">

            <?php alertMessage(); ?> <!-- Display the alert message if exists -->

            <form method="POST" action="utility-bill-process.php">
                <input type="hidden" name="bill_id" value="<?= $bill['id']; ?>">

                <!-- Bill Type -->
                <div class="mb-3">
                    <label for="bill_type">Bill Type</label>
                    <input type="text" name="bill_type" class="form-control" value="<?= $bill['bill_type']; ?>" required>
                </div>

                <!-- Amount -->
                <div class="mb-3">
                    <label for="amount">Amount</label>
                    <input type="number" name="amount" class="form-control" value="<?= $bill['amount']; ?>" required>
                </div>

                <!-- Bill Date -->
                <div class="mb-3">
                    <label for="bill_date">Bill Date</label>
                    <input type="date" name="bill_date" class="form-control" value="<?= $bill['bill_date']; ?>" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" name="update_bill" class="btn btn-primary mt-2">Update Bill</button>
            </form>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>