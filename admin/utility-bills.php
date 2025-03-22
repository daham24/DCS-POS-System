<?php
include('includes/header.php');
include('../config/dbCon.php'); // Include your database connection file
?>

<div class="container-fluid px-4 mt-4 mb-3">
    <?php
    // Display message if exists
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
    ?>

    <!-- Add Bill Type Modal -->
    <div class="modal fade" id="addBillTypeModal" tabindex="-1" aria-labelledby="addBillTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBillTypeModalLabel">Add Bill Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="utility-bill-process.php">
                        <div class="mb-3">
                            <label for="bill_type">Bill Type</label>
                            <input type="text" name="bill_type" class="form-control" required>
                        </div>
                        <button type="submit" name="add_bill_type" class="btn btn-primary">Add Bill Type</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Card for Adding Bills -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Add Utility Bill</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBillTypeModal">Add Bill Type</button>
        </div>
        <div class="card-body">
            <form method="POST" action="utility-bill-process.php">
                <!-- Bill Type Selection -->
                <div class="mb-3">
                    <label for="bill_type">Select Bill Type</label>
                    <select name="bill_type" class="form-select" required>
                        <option value="">Select a Bill Type</option>
                        <?php
                        // Fetch bill types
                        $billTypes = mysqli_query($conn, "SELECT DISTINCT bill_type FROM utility_bills");
                        while ($billType = mysqli_fetch_assoc($billTypes)) {
                            echo "<option value='{$billType['bill_type']}'>{$billType['bill_type']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Amount and Date -->
                <div class="mb-3">
                    <label for="amount">Amount</label>
                    <input type="number" name="amount" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="bill_date">Bill Date</label>
                    <input type="date" name="bill_date" class="form-control" required>
                </div>
                <button type="submit" name="add_bill" class="btn btn-primary">Add Bill</button>
            </form>
        </div>
    </div>

    <!-- Table to Display Bills -->
    <div class="card mt-4 shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">All Utility Bills</h4>
      </div>
      <div class="card-body">
          <?php
          // Query to fetch bills
          $query = "SELECT * FROM utility_bills WHERE amount > 0 ORDER BY bill_date DESC";
          $bills = mysqli_query($conn, $query);
          ?>
          <table class="table table-striped table-bordered mt-4">
              <thead>
                  <tr>
                      <th>Bill Type</th>
                      <th>Amount</th>
                      <th>Bill Date</th>
                      <th>Created At</th>
                      <th>Action</th>
                  </tr>
              </thead>
              <tbody>
                  <?php while ($bill = mysqli_fetch_assoc($bills)) : ?>
                      <tr>
                          <td><?= $bill['bill_type']; ?></td>
                          <td><?= $bill['amount']; ?></td>
                          <td><?= $bill['bill_date']; ?></td>
                          <td><?= $bill['created_at']; ?></td>
                          <td>
                              <a href="utility-bill-edit.php?id=<?= $bill['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                              <a href="utility-bill-delete.php?id=<?= $bill['id']; ?>" class="btn btn-danger btn-sm delete-btn">Delete</a>
                          </td>
                      </tr>
                  <?php endwhile; ?>
              </tbody>
          </table>
      </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>