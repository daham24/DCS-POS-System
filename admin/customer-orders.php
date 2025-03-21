<?php
include('includes/header.php'); // Include your header for consistency

// Get customer ID from URL
$customerId = isset($_GET['id']) ? $_GET['id'] : 0;
if ($customerId == 0) {
    echo '<p>Invalid Customer ID</p>';
    exit;
}

// Fetch customer orders
$query = "SELECT o.id AS order_id, o.tracking_no, o.total_amount, o.order_date, o.order_status 
          FROM orders o 
          WHERE o.customer_id = '$customerId'";

$orders = mysqli_query($conn, $query);

?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Customer Orders</h4>
        </div>
        <div class="card-body">
            <?php if (mysqli_num_rows($orders) > 0) { ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Tracking No.</th>
                        <th>Total Amount</th>
                        <th>Order Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = mysqli_fetch_assoc($orders)) { ?>
                    <tr>
                        <td><?= $order['tracking_no'] ?></td>
                        <td><?= number_format($order['total_amount'], 2) ?></td>
                        <td><?= date('d M, Y', strtotime($order['order_date'])) ?></td>
                        <td><?= ucfirst($order['order_status']) ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } else { ?>
                <p>No orders found for this customer.</p>
            <?php } ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

