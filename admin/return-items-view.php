<?php
include('includes/header.php'); // Include header for consistency

// Include database connection
include('../config/dbCon.php'); 

?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Returned Items</h4>
        </div>
        <div class="card-body">

            <!-- Display any alerts or messages -->
            <?php alertMessage(); ?>

            <!-- Fetch and display the returned items -->
            <?php
            // Query to fetch returned items from the 'returns' table, along with tracking number from orders
            $query = "SELECT r.*, oi.product_id, oi.quantity AS order_quantity, p.name AS product_name, p.price AS product_price, 
                      r.return_date, r.reason, r.status, o.tracking_no 
                      FROM returns r 
                      JOIN order_items oi ON r.order_item_id = oi.id 
                      JOIN products p ON r.product_id = p.id
                      JOIN orders o ON o.id = oi.order_id
                      ORDER BY r.return_date DESC";  // Sorting by the return date

            $result = mysqli_query($conn, $query);
            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    // Start table structure
                    echo '<table class="table table-bordered table-striped mt-4">';
                    echo '<thead>
                            <tr>
                                <th>Tracking No.</th>
                                <th>Product Name</th>
                                <th>Price (Rs.)</th>
                                <th>Quantity</th>
                                <th>Return Date</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>';

                    // Loop through and display each return item
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Fetch return details and product information
                        $orderId = $row['order_item_id']; // ID of the order item returned
                        $trackingNo = $row['tracking_no']; // Get tracking number from orders
                        $productName = $row['product_name'];
                        $price = number_format($row['product_price'], 2); // Format price
                        $quantity = $row['quantity'];
                        $returnDate = date('d M, Y', strtotime($row['return_date'])); // Format return date
                        $reason = $row['reason'];
                        $status = ucfirst($row['status']); // Return status (pending/completed)

                        // Display the row in the table
                        echo '<tr>';
                        echo '<td>' . $trackingNo . '</td>'; 
                        echo '<td>' . $productName . '</td>';
                        echo '<td>' . $price . '</td>';
                        echo '<td>' . $quantity . '</td>';
                        echo '<td>' . $returnDate . '</td>';
                        echo '<td>' . $reason . '</td>';
                        echo '<td>' . $status . '</td>';
                        echo '<td>
                                <a href="return-edit.php?id=' . $row['id'] . '" class="btn btn-success btn-sm">Edit</a>
                                <a href="return-delete.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm delete-btn" data-delete-url="return-delete.php?id=' . $row['id'] . '">Delete</a>
                              </td>';
                        echo '</tr>';
                    }

                    // End the table structure
                    echo '</tbody>';
                    echo '</table>';
                } else {
                    echo '<p>No returned items found.</p>';
                }
            } else {
                echo 'Error: ' . mysqli_error($conn);
            }

            ?>

        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script>
  // Adding SweetAlert for delete button confirmation
  const deleteButtons = document.querySelectorAll('.delete-btn');
  deleteButtons.forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent the default link behavior

        const deleteUrl = this.getAttribute('data-delete-url');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = deleteUrl; // Redirect to delete URL
            }
        });
    });
  });
</script>
