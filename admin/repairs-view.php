<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Repair Details
                <a href="repairs.php" class="btn btn-danger btn-sm float-end">Back</a>
            </h4>
        </div>
        <div class="card-body">
            <?php
            if (isset($_GET['id'])) {
                $repairId = intval($_GET['id']); // Sanitize input

                if ($repairId == 0) {
                    echo "<div class='alert alert-danger'>Invalid Repair ID.</div>";
                    exit;
                }

                // Fetch repair details
                $repairQuery = "
                    SELECT r.*, c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone
                    FROM repairs r
                    LEFT JOIN customers c ON r.customer_id = c.id
                    WHERE r.id = $repairId
                ";
                $repairResult = mysqli_query($conn, $repairQuery);

                if ($repairResult && mysqli_num_rows($repairResult) > 0) {
                    $repairData = mysqli_fetch_assoc($repairResult);

                    // Check if repair cost is added
                    $costQuery = "
                        SELECT invoice_number, repair_cost 
                        FROM repair_orders 
                        WHERE repair_id = $repairId
                    ";
                    $costResult = mysqli_query($conn, $costQuery);

                    // Display success message if redirected after saving cost
                    if (isset($_GET['success']) && $_GET['success'] == 1) {
                        echo "<div class='alert alert-success'>Repair cost added successfully!</div>";
                    }

                    // Display repair details
                    ?>
                    <h5>Repair Details</h5>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Repair ID</th>
                                <td><?= htmlspecialchars($repairData['id']); ?></td>
                            </tr>
                            <tr>
                                <th>Customer Name</th>
                                <td><?= htmlspecialchars($repairData['customer_name']); ?></td>
                            </tr>
                            <tr>
                                <th>Customer Email</th>
                                <td><?= htmlspecialchars($repairData['customer_email']); ?></td>
                            </tr>
                            <tr>
                                <th>Customer Phone</th>
                                <td><?= htmlspecialchars($repairData['customer_phone']); ?></td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td><?= htmlspecialchars($repairData['description']); ?></td>
                            </tr>
                            <tr>
                                <th>Repair Date</th>
                                <td><?= date('d M Y', strtotime($repairData['created_at'])); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <?php if ($costResult && mysqli_num_rows($costResult) > 0) { 
                        $costData = mysqli_fetch_assoc($costResult);
                        ?>
                        <h5>Invoice Details</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Invoice Number</th>
                                <td><?= htmlspecialchars($costData['invoice_number']); ?></td>
                            </tr>
                            <tr>
                                <th>Repair Cost (Rs.)</th>
                                <td><?= number_format($costData['repair_cost'], 2); ?></td>
                            </tr>
                        </table>
                        <?php
                    } else {
                        // If cost is not added, show the form
                        ?>
                        <div class="alert alert-warning">
                            No repair cost added for this repair. Please add the cost below.
                        </div>
                        <form action="add-repair-cost.php" method="POST">
                            <input type="hidden" name="repair_id" value="<?= $repairId ?>">
                            <div class="form-group mb-3">
                                <label for="repair_cost">Enter Repair Cost (Rs.):</label>
                                <input type="number" step="0.01" name="repair_cost" id="repair_cost" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success">Save Cost</button>
                        </form>
                        <?php
                    }
                    ?>

                    <div class="mt-4 text-end">
                        <a href="repairs-view-print.php?id=<?= $repairData['id']; ?>" class="btn btn-info px-4 mx-1">Print</a>
                        <button class="btn btn-primary px-4 mx-1" onclick="downloadPDF('<?= $repairData['id']; ?>')">Download PDF</button>
                    </div>
                    <?php
                } else {
                    echo "<div class='alert alert-danger'>No repair details found.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>No Repair ID provided.</div>";
            }
            ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>