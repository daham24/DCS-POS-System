<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Repair Items
                <a href="repairs-create.php" class="btn btn-primary float-end">Add Repair</a>
            </h4>
        </div>
        <div class="card-body">
            <?php alertMessage(); ?>
            <?php
            $repairs = mysqli_query($conn, "
                SELECT repairs.*, customers.name AS customer_name, customers.email, customers.phone 
                FROM repairs 
                INNER JOIN customers ON repairs.customer_id = customers.id
            ");

            if ($repairs && mysqli_num_rows($repairs) > 0) {
            ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Item Name</th>
                                <th>Customer</th>
                                <th>Physical Condition</th>
                                <th>Received Items</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($repair = mysqli_fetch_assoc($repairs)) { ?>
                                <tr>
                                    <td><?= $repair['id']; ?></td>
                                    <td><?= $repair['item_name']; ?></td>
                                    <td>
                                        <?= $repair['customer_name']; ?> <br>
                                        <small><?= $repair['email']; ?>, <?= $repair['phone']; ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        // Split the stored string into an array and display as a comma-separated list
                                        if (!empty($repair['physical_condition'])) {
                                            $conditions = explode(', ', $repair['physical_condition']);
                                            echo implode(', ', $conditions); // Display as a readable list
                                        } else {
                                            echo 'N/A'; // Default value if empty
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        // Split the stored string into an array and display as a comma-separated list
                                        if (!empty($repair['received_items'])) {
                                            $items = explode(', ', $repair['received_items']);
                                            echo implode(', ', $items); // Display as a readable list
                                        } else {
                                            echo 'N/A'; // Default value if empty
                                        }
                                        ?>
                                    </td>

                                    <td><?= $repair['description']; ?></td>
                                    <td>
                                        <?php
                                        if($repair['status'] == 1){
                                            echo '<span class="badge bg-success">Completed</span>';
                                        }else{
                                            echo '<span class="badge bg-danger">Pending</span>';
                                        }                    
                                        ?>
                                    </td>
                                    <td>
                                        <a href="repairs-edit.php?id=<?= $repair['id']; ?>" class="btn btn-success btn-sm">Edit</a>
                                        <a href="repairs-delete.php?id=<?= $repair['id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to delete this repair?');">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <h4>No Repair Items Found</h4>
            <?php } ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
