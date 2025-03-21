<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">
  <div class="card mt-4 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4 class="mb-0">Products</h4>
      <form class="d-flex" method="GET" action="">
        <input 
          type="text" 
          name="search" 
          class="form-control me-2" 
          placeholder="Search products..."
          value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
        >
        <select name="category" class="form-select me-2">
          <option value="">All Categories</option>
          <?php
            $categories = getAll('categories');
            foreach ($categories as $category) {
              $selected = isset($_GET['category']) && $_GET['category'] == $category['id'] ? 'selected' : '';
              echo "<option value='{$category['id']}' $selected>{$category['name']}</option>";
            }
          ?>
        </select>
        <button type="submit" class="btn btn-dark">Search</button>
      </form>
      <a href="products-create.php" class="btn btn-primary">Add Product</a>
    </div>
    <div class="card-body">
      <?php alertMessage(); ?> 

      <?php
        // Fetch products based on search and category filters
        $searchQuery = isset($_GET['search']) ? validate($_GET['search']) : '';
        $categoryFilter = isset($_GET['category']) ? validate($_GET['category']) : '';

        $query = "SELECT p.*, c.name AS category_name, sc.name AS subcategory_name 
                  FROM products p
                  LEFT JOIN categories c ON p.category_id = c.id
                  LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id
                  WHERE 1=1";

        if ($searchQuery) {
          // Search by both name and description
          $query .= " AND (p.name LIKE '%$searchQuery%' OR p.description LIKE '%$searchQuery%')";
        }

        if ($categoryFilter) {
          $query .= " AND p.category_id = '$categoryFilter'";
        }

        $products = mysqli_query($conn, $query);

        if (!$products) {
          echo '<h4>Something Went Wrong!</h4>';
          return false;
        }
        if (mysqli_num_rows($products) > 0) {
      ?>
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Image</th>
              <th>Name</th>
              <th>Category</th> <!-- New Column -->
              <th>Subcategory</th> <!-- New Column -->
              <th>Actual Price</th>
              <th>Selling Price</th>
              <th>Discount</th>
              <th>Quantity</th>
              <th>Warranty</th>
              <th>Barcode</th>
              <th>IMEI No.</th>
              <th>Description</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $item) : ?>
              <tr>
                <td>
                  <img src="../<?= $item['image']; ?>" style="width:50px; height: 50px;" alt="Img">
                </td>
                <td><?= $item['name']; ?></td>
                <td><?= $item['category_name']; ?></td> <!-- Display Category Name -->
                <td><?= $item['subcategory_name']; ?></td> <!-- Display Subcategory Name -->
                <td><?= $item['price']; ?></td>
                <td><?= $item['sell_price']; ?></td>
                <td><?= $item['discount']; ?></td>
                <td><span class="badge bg-dark"><?= $item['quantity']; ?></span></td>
                <td><?= !empty($item['warranty_period']) ? $item['warranty_period'] : 'N/A'; ?></td>
                <td><?= !empty($item['barcode']) ? $item['barcode'] : 'N/A'; ?></td>
                <td><?= !empty($item['imei_code']) ? $item['imei_code'] : 'N/A'; ?></td>
                <td><?= !empty($item['description']) ? $item['description'] : 'N/A'; ?></td>
                <td>
                  <!-- Dropdown for Edit and Delete actions -->
                  <div class="dropdown">
                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                      Actions
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                      <!-- Edit action -->
                      <li>
                        <a href="products-edit.php?id=<?= $item['id']; ?>" class="dropdown-item <?= ($_SESSION['role'] == 'staff') ? 'disabled' : ''; ?>">Edit</a>
                      </li>
                      <!-- Delete action -->
                      <li>
                        <a href="products-delete.php?id=<?= $item['id']; ?>" class="dropdown-item delete-btn <?= ($_SESSION['role'] == 'staff') ? 'disabled' : ''; ?>" data-delete-url="products-delete.php?id=<?= $item['id']; ?>">Delete</a>
                      </li>
                    </ul>
                  </div>
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
</div>

<?php include('includes/footer.php'); ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // SweetAlert for delete confirmation
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default link behavior

            // Check if the button is disabled (for staff role)
            if (this.classList.contains('disabled')) {
                return;
            }

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
