<?php include('includes/header.php');?>

<div class="container-fluid px-4">
  <div class="card mt-4 shadow-sm">
      <div class="card-header"> 
        <div class="row">
          <div class="col-md-4">
            <h4 class="mb-0">Orders</h4>
          </div>
          <div class="col-md-8">
            <form action="" method="GET">
              <div class="row g-1">
                <div class="col-md-3">
                  <input type="text" 
                    name="search_tracking" 
                    class="form-control" 
                    placeholder="Search by Tracking No."
                    value="<?= isset($_GET['search_tracking']) ? htmlspecialchars($_GET['search_tracking']) : ''; ?>"
                  />
                </div>
                <div class="col-md-3">
                  <input type="date" 
                    name="date" 
                    class="form-control"
                    value="<?= isset($_GET['date']) ? $_GET['date'] : ''; ?>"
                  />
                </div>
                <div class="col-md-3">
                  <select name="payment_status" class="form-select">
                    <option value="">Select Payment Status</option>
                    <option value="Cash Payment" <?= isset($_GET['payment_status']) && $_GET['payment_status'] == 'Cash Payment' ? 'selected' : ''; ?>>Cash Payment</option>
                    <option value="Online Payment" <?= isset($_GET['payment_status']) && $_GET['payment_status'] == 'Online Payment' ? 'selected' : ''; ?>>Online Payment</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <button type="submit" class="btn btn-primary">Filter</button>
                  <a href="orders.php" class="btn btn-danger">Reset</a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="card-body">

        <?php 
          // Get filter values
          $searchTracking = isset($_GET['search_tracking']) ? validate($_GET['search_tracking']) : '';
          $orderDate = isset($_GET['date']) ? validate($_GET['date']) : '';
          $paymentStatus = isset($_GET['payment_status']) ? validate($_GET['payment_status']) : '';

          // Build the query with conditions
          $query = "SELECT o.*, c.* FROM orders o, customers c WHERE c.id = o.customer_id";

          if ($searchTracking != '') {
            $query .= " AND o.tracking_no LIKE '%$searchTracking%'";
          }
          if ($orderDate != '') {
            $query .= " AND o.order_date = '$orderDate'";
          }
          if ($paymentStatus != '') {
            $query .= " AND o.payment_mode = '$paymentStatus'";
          }

          $query .= " ORDER BY o.id DESC";  // Sort the orders by ID descending

          // Execute the query
          $orders = mysqli_query($conn, $query);
          if ($orders) {
            if (mysqli_num_rows($orders) > 0) {
        ?>
        <table class="table table-striped table-bordered align-items-center justify-content-center">
          <thead>
            <tr>
              <th>Tracking No.</th>
              <th>C Name</th>
              <th>C Phone</th>
              <th>Order Date</th>
              <th>Order Status</th>
              <th>Payment Mode</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($orders as $orderItem) : ?>
              <tr>
                <td class="fw-bold"><?= $orderItem['tracking_no']; ?></td>
                <td><?= $orderItem['name']; ?></td>
                <td><?= $orderItem['phone']; ?></td>
                <td><?= date('d M, Y', strtotime($orderItem['order_date'])); ?></td>
                <td><?= $orderItem['order_status']; ?></td>
                <td><?= $orderItem['payment_mode']; ?></td>
                <td>
                  <a href="orders-view.php?track=<?= $orderItem['tracking_no']; ?>" class="btn btn-info mb-0 px-2 btn-sm">View</a>
                  <a href="orders-view-print.php?track=<?= $orderItem['tracking_no']; ?>" class="btn btn-primary mb-0 px-2 btn-sm"><i class="fa-solid fa-print"></i> Print</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php
            } else {
              echo '<h5>No Record Available</h5>';
            }
          } else {
            echo '<h5>Something Went Wrong</h5>';
          }
        ?>

      </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>
