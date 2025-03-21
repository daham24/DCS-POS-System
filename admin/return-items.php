<?php
include('includes/header.php'); // Include your header for consistency

?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Return Items</h4>
        </div>
        <div class="card-body">

            <!-- Form to enter customer phone number -->
            <form method="POST" action="return-items.php">
                <div class="row">
                    <div class="col-md-6">
                        <label for="phone">Enter Customer Phone Number:</label>
                        <input type="text" class="form-control" name="phone" id="phone" required>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" name="fetchOrders" class="btn btn-primary mt-4">Fetch Orders</button>
                    </div>
                </div>
            </form>

            <?php
            // Check if the phone number is entered
            if (isset($_POST['fetchOrders'])) {
                $phone = trim($_POST['phone']); // Trim any extra spaces

                // Check if the phone number is empty
                if (empty($phone)) {
                    echo '<p class="text-danger mt-4">Please enter a phone number to fetch orders.</p>';
                } else {
                    // Fetch orders for the customer
                    $query = "SELECT o.id as order_id, o.tracking_no, oi.product_id, oi.quantity, p.name, p.price 
                              FROM orders o 
                              JOIN order_items oi ON oi.order_id = o.id 
                              JOIN products p ON oi.product_id = p.id 
                              JOIN customers c ON c.id = o.customer_id 
                              WHERE c.phone = '$phone'";

                    $result = mysqli_query($conn, $query);
                    if ($result && mysqli_num_rows($result) > 0) {
                        // Store the fetched orders in the session for future page loads
                        $_SESSION['orders'] = mysqli_fetch_all($result, MYSQLI_ASSOC);

                        echo '<form method="POST" action="code.php">';
                        echo '<table class="table table-bordered table-striped mt-4">';
                        echo '<thead><tr><th>Select</th><th>Product Name</th><th>Price</th><th>Quantity</th></tr></thead>';
                        echo '<tbody>';

                        foreach ($_SESSION['orders'] as $row) {
                            echo '<tr>';
                            echo '<td><input type="checkbox" name="items[]" value="' . $row['order_id'] . '-' . $row['product_id'] . '" class="return-item-checkbox"></td>';
                            echo '<td>' . $row['name'] . '</td>';
                            echo '<td>' . number_format($row['price'], 2) . '</td>';
                            echo '<td>' . $row['quantity'] . '</td>';
                            echo '</tr>';
                        }

                        echo '</tbody>';
                        echo '</table>';

                        // Reason for return input
                        echo '<div class="form-group mt-3">';
                        echo '<label for="reason">Reason for Return:</label>';
                        echo '<textarea name="reason" id="reason" class="form-control" rows="4" required></textarea>';
                        echo '</div>';

                        // Submit button to process the return
                        echo '<button type="submit" name="returnItems" class="btn btn-danger mt-3">Return Selected Items</button>';
                        echo '</form>';
                    } else {
                        echo '<p class="mt-4">No orders found for this customer.</p>';
                    }
                }
            } else {
                // Display message when the form is not submitted or phone number is empty
                echo '<p class="mt-4">Please enter a customer phone number to fetch orders.</p>';
            }
            ?>

        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
