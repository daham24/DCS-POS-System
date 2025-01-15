<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Repair Items
                <a href="repairs-create.php" class="btn btn-primary float-end">Add Repair</a>
            </h4>
        </div>
        <div class="card-body">
            <!-- Alert Messages -->
            <?php alertMessage(); ?>

            <!-- Search Form -->
            <form class="d-flex mb-4" method="GET" action="">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control me-2" 
                    placeholder="Search repairs by Item Name..." 
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                >
                <select name="status" class="form-select me-2">
                    <option value="">All Status</option>
                    <option value="0" <?= isset($_GET['status']) && $_GET['status'] === '0' ? 'selected' : '' ?>>Pending</option>
                    <option value="1" <?= isset($_GET['status']) && $_GET['status'] === '1' ? 'selected' : '' ?>>Completed</option>
                </select>
                <button type="submit" class="btn btn-dark">Search</button>
            </form>

            <?php
            // Search and Filter Logic
            $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
            $statusFilter = isset($_GET['status']) ? trim($_GET['status']) : '';

            $query = "
                SELECT repairs.*, customers.name AS customer_name, customers.email, customers.phone 
                FROM repairs 
                INNER JOIN customers ON repairs.customer_id = customers.id
                WHERE 1
            ";

            // Add search condition
            if ($searchQuery !== '') {
                $query .= " AND repairs.item_name LIKE '%" . mysqli_real_escape_string($conn, $searchQuery) . "%'";
            }

            // Add status filter
            if ($statusFilter !== '') {
                $query .= " AND repairs.status = " . intval($statusFilter);
            }

            $query .= " ORDER BY repairs.created_at DESC"; // Sort by latest

            $repairs = mysqli_query($conn, $query);

            // Display results
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
                                        <?= !empty($repair['physical_condition']) 
                                            ? implode(', ', explode(', ', $repair['physical_condition'])) 
                                            : 'N/A'; ?>
                                    </td>
                                    <td>
                                        <?= !empty($repair['received_items']) 
                                            ? implode(', ', explode(', ', $repair['received_items'])) 
                                            : 'N/A'; ?>
                                    </td>
                                    <td><?= $repair['description']; ?></td>
                                    <td>
                                        <?= $repair['status'] == 1 
                                            ? '<span class="badge bg-success">Completed</span>' 
                                            : '<span class="badge bg-danger">Pending</span>'; ?>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="actionDropdown<?= $repair['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="actionDropdown<?= $repair['id']; ?>">
                                                <li>
                                                    <a class="dropdown-item" href="repairs-edit.php?id=<?= $repair['id']; ?>">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="repairs-delete.php?id=<?= $repair['id']; ?>" 
                                                    onclick="return confirm('Are you sure you want to delete this repair?');">
                                                        <i class="fas fa-trash-alt"></i> Delete
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="repairs-view.php?id=<?= $repair['id']; ?>">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <h4>No Repair Items Found
            <?php } ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
