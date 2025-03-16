<?php
include('includes/header.php'); 
include('../config/dbCon.php'); // Assuming this file connects to the database

$editData = null; 

// Fetch all products for the dropdown
$products = mysqli_query($conn, "SELECT id, name FROM products");

// Fetch all product cost records with search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "
    SELECT pc.*, p.name AS product_name 
    FROM products_cost pc
    LEFT JOIN products p ON pc.product_id = p.id
    WHERE p.name LIKE '%$search%'
    ORDER BY pc.date DESC
";
$productCosts = mysqli_query($conn, $query);
?>

<div class="container-fluid px-4">
  <div class="card mt-4 shadow-sm">
    <div class="card-header">
      <h4 class="mb-0">Product Cost Management
        <a href="products.php" class="btn btn-primary float-end">Back to Products</a>
      </h4>
    </div>
    <div class="card-body">
      <?php alertMessage(); ?> 

      <!-- Product Cost Form -->
      <form action="product-cost-code.php" method="POST" id="product-cost-form">
          <input type="hidden" name="edit_id" value="<?= $editData ? $editData['id'] : ''; ?>">
          <div class="row">
              <div class="col-md-3 mb-3">
                  <label for="product_id">Select Product</label>
                  <select name="product_id" class="form-select" required>
                      <option value="">-- Select Product --</option>
                      <?php
                          while ($product = mysqli_fetch_assoc($products)) {
                              $selected = ($editData && $editData['product_id'] == $product['id']) ? 'selected' : '';
                              echo "<option value='{$product['id']}' $selected>{$product['name']}</option>";
                          }
                      ?>
                  </select>
              </div>
              <div class="col-md-2 mb-3">
                  <label for="quantity">Quantity</label>
                  <input type="number" name="quantity" class="form-control" placeholder="Enter quantity" value="<?= $editData ? $editData['quantity'] : ''; ?>" required>
              </div>
              <div class="col-md-2 mb-3">
                  <label for="unit_price">Unit Price (Rs.)</label>
                  <input type="number" step="0.01" name="unit_price" class="form-control" placeholder="Enter unit price" value="<?= $editData ? $editData['unit_price'] : ''; ?>" required>
              </div>
              <div class="col-md-2 mb-3">
                  <label for="total_cost">Total Cost (Rs.)</label>
                  <input type="number" step="0.01" name="total_cost" class="form-control" placeholder="Total cost" readonly value="<?= $editData ? $editData['total_cost'] : ''; ?>">
              </div>
              <div class="col-md-2 mb-3">
                  <label for="date">Date</label>
                  <input type="date" name="date" class="form-control" value="<?= $editData ? $editData['date'] : date('Y-m-d'); ?>" readonly>
              </div>
              <div class="col-md-1 mb-3 text-end">
                  <br />
                  <button type="submit" name="<?= $editData ? 'updateCost' : 'saveCost'; ?>" class="btn btn-primary">
                      <?= $editData ? 'Update' : 'Save'; ?>
                  </button>
              </div>
          </div>
      </form>

      <hr/>

      <!-- Search Form -->
       <div class="row">
        <div class="col-4">
          <form class="d-flex" method="GET" action="">
            <input 
                type="text" 
                name="search" 
                class="form-control me-2" 
                placeholder="Search products..." 
                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
            >
            <button type="submit" class="btn btn-dark">Search</button>
          </form>
        </div>
       </div>
      

      <!-- Table to Display Product Cost Data -->
      <div class="table-responsive mt-4">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Product Name</th>
              <th>Quantity</th>
              <th>Unit Price (Rs.)</th>
              <th>Total Cost (Rs.)</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
              if (mysqli_num_rows($productCosts) > 0) {
                  while ($cost = mysqli_fetch_assoc($productCosts)) :
            ?>
                    <tr>
                      <td><?= $cost['product_name']; ?></td>
                      <td><?= $cost['quantity']; ?></td>
                      <td><?= number_format($cost['unit_price'], 2); ?></td>
                      <td><?= number_format($cost['total_cost'], 2); ?></td>
                      <td><?= date('d M Y', strtotime($cost['date'])); ?></td>
                      <td>
                        <a href="product-cost.php?edit_id=<?= $cost['id']; ?>" class="btn btn-success btn-sm">Edit</a>
                        <a href="product-cost-code.php?delete_id=<?= $cost['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                      </td>
                    </tr>
            <?php
                  endwhile;
              } else {
                  echo '<tr><td colspan="7">No records found</td></tr>';
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>