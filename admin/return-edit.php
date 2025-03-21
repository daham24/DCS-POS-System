<?php
include('includes/header.php'); // Include your header for consistency

// Include database connection
include('../config/dbCon.php');

// Fetch the return item id from the URL
$returnId = isset($_GET['id']) ? $_GET['id'] : 0;
if ($returnId == 0) {
    echo '<p>Invalid return ID.</p>';
    exit;
}

// Fetch return details from the database based on the ID
$query = "SELECT r.*, oi.product_id, oi.quantity AS order_quantity, p.name AS product_name, p.price AS product_price, 
          r.return_date, r.reason, r.status, o.tracking_no 
          FROM returns r 
          JOIN order_items oi ON r.order_item_id = oi.id 
          JOIN products p ON r.product_id = p.id
          JOIN orders o ON o.id = oi.order_id
          WHERE r.id = '$returnId'";

$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $returnData = mysqli_fetch_assoc($result);
} else {
    echo '<p>Return item not found.</p>';
    exit;
}
?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Edit Return Item</h4>
        </div>
        <div class="card-body">

            <!-- Form to edit return item details -->
            <form method="POST" action="code.php">

                <!-- Hidden field to hold the return ID -->
                <input type="hidden" name="return_id" value="<?= $returnData['id'] ?>">

                <div class="mb-3">
                    <label for="tracking_no" class="form-label">Tracking No.</label>
                    <input type="text" class="form-control" id="tracking_no" value="<?= $returnData['tracking_no'] ?>" disabled>
                </div>

                <div class="mb-3">
                    <label for="product_name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="product_name" value="<?= $returnData['product_name'] ?>" disabled>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" value="<?= $returnData['order_quantity'] ?>" disabled>
                </div>

                <div class="mb-3">
                    <label for="return_date" class="form-label">Return Date</label>
                    <input type="date" class="form-control" id="return_date" name="return_date" value="<?= date('Y-m-d', strtotime($returnData['return_date'])) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label">Reason for Return</label>
                    <textarea class="form-control" id="reason" name="reason" rows="4" required><?= $returnData['reason'] ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="pending" <?= $returnData['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="completed" <?= $returnData['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                </div>

                <div class="mb-3">
                    <button type="submit" name="updateReturn" class="btn btn-primary">Update Return</button>
                </div>
            </form>

        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
