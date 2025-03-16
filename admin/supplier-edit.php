<?php include('includes/header.php'); ?>

<div class="container-fluid px-4 mb-3">
  <!-- Edit Supplier Section -->
  <div class="card mt-4 shadow-sm">
    <div class="card-header">
      <h4 class="mb-0">Edit Supplier
        <a href="suppliers.php" class="btn btn-outline-secondary float-end">Back</a>
      </h4>
    </div>
    <div class="card-body">
      <?php alertMessage(); ?> 

      <?php
      $supplierId = validate($_GET['id']);
      $supplier = getById('suppliers', $supplierId);
      if ($supplier['status'] != 200) {
        redirect('suppliers.php', $supplier['message']);
      }
      ?>

      <form action="code.php" method="POST">
        <input type="hidden" name="supplierId" value="<?= $supplier['data']['id']; ?>">
        <div class="row">
          <div class="col-md-12 mb-3">
            <label for="name">Name *</label>
            <input type="text" name="name" value="<?= $supplier['data']['name']; ?>" required class="form-control"/>
          </div>
          <div class="col-md-12 mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" value="<?= $supplier['data']['email']; ?>" class="form-control"/>
          </div>
          <div class="col-md-12 mb-3">
            <label for="phone">Phone</label>
            <input type="text" name="phone" value="<?= $supplier['data']['phone']; ?>" class="form-control"/>
          </div>
          <div class="col-md-6">
            <label>Status (Unchecked=Visible, Checked=Hidden)</label>
            <br>
            <input type="checkbox" name="status" <?= $supplier['data']['status'] == 1 ? 'checked' : ''; ?> style="width: 20px; height:20px;">
          </div>
          <div class="col-md-6 mb-3 text-end">
            <br>
            <button type="submit" name="updateSupplier" class="btn btn-dark">Save Changes</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Products Supplied by Supplier Section -->
  <div class="card mt-4 shadow-sm">
    <div class="card-header">
      <h4 class="mb-0">Edit Products Supplied by Supplier</h4>
    </div>
    <div class="card-body">
      <?php alertMessage(); ?> 

      <?php
      // Fetch products supplied by this supplier
      $supplierProducts = mysqli_query($conn, "
        SELECT sp.id AS supplier_product_id, p.id AS product_id, p.name AS product_name 
        FROM supplier_products sp
        LEFT JOIN products p ON sp.product_id = p.id
        WHERE sp.supplier_id = '$supplierId'
      ");

      if ($supplierProducts && mysqli_num_rows($supplierProducts) > 0) {
        while ($supplierProduct = mysqli_fetch_assoc($supplierProducts)) {
      ?>
          <form action="code.php" method="POST" class="mb-4">
            <input type="hidden" name="supplierProductId" value="<?= $supplierProduct['supplier_product_id']; ?>">
            <input type="hidden" name="supplierId" value="<?= $supplierId; ?>"> <!-- Add supplierId -->

            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="product">Product *</label>
                <select name="productId" class="form-select">
                  <?php
                  // Fetch all products
                  $products = mysqli_query($conn, "SELECT id, name FROM products");
                  if ($products && mysqli_num_rows($products) > 0) {
                    while ($product = mysqli_fetch_assoc($products)) {
                      $selected = ($product['id'] == $supplierProduct['product_id']) ? 'selected' : '';
                      echo "<option value='{$product['id']}' $selected>{$product['name']}</option>";
                    }
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-4 mb-3 text-end">
                <br>
                <button type="submit" name="updateSupplierProduct" class="btn btn-outline-dark">Update</button>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" onclick="setDeleteId(<?= $supplierProduct['supplier_product_id']; ?>)">Delete</button>
              </div>
            </div>
          </form>
      <?php
        }
      } else {
        echo '<p>No products found for this supplier.</p>';
      }
      ?>
    </div>
  </div>

  <!-- Add New Products Supplied by Supplier Section -->
  <div class="card mt-4 shadow-sm">
    <div class="card-header">
      <h4 class="mb-0">Add New Products Supplied by Supplier</h4>
    </div>
    <div class="card-body">
      <form action="code.php" method="POST">
        <input type="hidden" name="supplierId" value="<?= $supplierId; ?>">

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="product">Product *</label>
            <select name="productId" class="form-select">
              <option value="">Select Product</option>
              <?php
              // Fetch all products
              $products = mysqli_query($conn, "SELECT id, name FROM products");
              if ($products && mysqli_num_rows($products) > 0) {
                while ($product = mysqli_fetch_assoc($products)) {
                  echo "<option value='{$product['id']}'>{$product['name']}</option>";
                }
              }
              ?>
            </select>
          </div>
          <div class="col-md-6 mb-3 text-end">
            <br>
            <button type="submit" name="saveSupplierProduct" class="btn btn-dark"><i class="fa-solid fa-square-plus"></i> &nbsp;Add</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>


<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this product?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmDeleteButton" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>


<script>
// Set the supplier product ID to delete
let deleteSupplierProductId = null;

function setDeleteId(id) {
  deleteSupplierProductId = id;
}

// Handle the delete confirmation
document.getElementById('confirmDeleteButton').addEventListener('click', function () {
  if (deleteSupplierProductId) {
    // Submit the form with the delete action
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'code.php';

    const supplierProductIdInput = document.createElement('input');
    supplierProductIdInput.type = 'hidden';
    supplierProductIdInput.name = 'supplierProductId';
    supplierProductIdInput.value = deleteSupplierProductId;

    const supplierIdInput = document.createElement('input');
    supplierIdInput.type = 'hidden';
    supplierIdInput.name = 'supplierId';
    supplierIdInput.value = <?= $supplierId; ?>;

    const deleteActionInput = document.createElement('input');
    deleteActionInput.type = 'hidden';
    deleteActionInput.name = 'deleteSupplierProduct';
    deleteActionInput.value = '1';

    form.appendChild(supplierProductIdInput);
    form.appendChild(supplierIdInput);
    form.appendChild(deleteActionInput);

    document.body.appendChild(form);
    form.submit();
  }
});
</script>