<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
  <!-- Suppliers Section -->
  <div class="card mt-4 shadow-sm">
    <div class="card-header">
      <h4 class="mb-0">Suppliers
        <a href="supplier-create.php" class="btn btn-primary float-end">Add Supplier</a>
      </h4>
    </div>
    <div class="card-body">
      <?php alertMessage(); ?> 

      <?php
      // Fetch all suppliers
      $suppliers = getAll('suppliers');
      if (!$suppliers) {
        echo '<h4>Something Went Wrong!</h4>';
        return false;
      }

      if (mysqli_num_rows($suppliers) > 0) {
      ?>
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($suppliers as $item) : ?>
                <tr>
                  <td><?= $item['name']; ?></td>
                  <td><?= $item['email']; ?></td>
                  <td><?= $item['phone']; ?></td>
                  <td>
                    <?php
                    if ($item['status'] == 1) {
                      echo '<span class="badge bg-danger">Hidden</span>';
                    } else {
                      echo '<span class="badge bg-primary">Visible</span>';
                    }
                    ?>
                  </td>
                  <td>
                    <a href="supplier-edit.php?id=<?= $item['id']; ?>" class="btn btn-success btn-sm">Edit</a>
                    <a 
                        href="supplier-delete.php?id=<?= $item['id']; ?>" 
                        class="btn btn-danger btn-sm delete-btn" 
                        data-delete-url="supplier-delete.php?id=<?= $item['id']; ?>"
                    >
                        Delete
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php
      } else {
        echo '<h4 class="mb-0">No Record Found</h4>';
      }
      ?>
    </div>
  </div>

  <!-- Products Supplied by Suppliers Section -->
  <div class="card mt-4 shadow-sm mb-3">
    <div class="card-header">
      <h4 class="mb-0">Products Supplied by Suppliers</h4>
    </div>
    <div class="card-body">
      <?php alertMessage(); ?>

      <?php
      // Function to fetch suppliers with their supplied products
      function getSuppliersWithProducts() {
        global $conn;

        $query = "SELECT s.id AS supplier_id, s.name AS supplier_name, p.id AS product_id, p.name AS product_name 
                  FROM suppliers s
                  LEFT JOIN supplier_products sp ON s.id = sp.supplier_id
                  LEFT JOIN products p ON sp.product_id = p.id
                  ORDER BY s.name, p.name"; // Order by supplier and product name
        $result = mysqli_query($conn, $query);

        return $result;
      }

      $suppliersWithProducts = getSuppliersWithProducts(); // Fetch data
      if (!$suppliersWithProducts) {
        echo '<h4>Something Went Wrong!</h4>';
        return false;
      }

      if (mysqli_num_rows($suppliersWithProducts) > 0) {
        // Group products by supplier
        $groupedSuppliers = [];
        while ($row = mysqli_fetch_assoc($suppliersWithProducts)) {
          $supplierName = $row['supplier_name'];
          $groupedSuppliers[$supplierName][] = $row;
        }
      ?>
        <div class="row">
          <?php foreach ($groupedSuppliers as $supplierName => $products) : ?>
            <div class="col-md-3 mb-4"> <!-- 4 cards per row (12 columns / 3 = 4 cards) -->
              <div class="card h-100 shadow-sm">
                <div class="card-header">
                  <h5 class="card-title mb-0"><?= $supplierName; ?></h5> <!-- Supplier Name -->
                </div>
                <div class="card-body">
                  <ul class="list-group list-group-flush">
                    <?php foreach ($products as $product) : ?>
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><?= $product['product_name']; ?></span> <!-- Product Name -->
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php
      } else {
        echo '<h4 class="mb-0">No Record Found</h4>';
      }
      ?>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // SweetAlert for delete confirmation
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
});
</script>