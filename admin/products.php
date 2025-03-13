<?php include('includes/header.php');?>

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
          $query .= " AND p.name LIKE '%$searchQuery%'";
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
              <th>Barcode</th>
              <th>Status</th> 
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
                <td><?= $item['barcode']; ?></td>
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
                  <a href="products-edit.php?id=<?= $item['id']; ?>" class="btn btn-success btn-sm">Edit</a>
                  <a 
                    href="products-delete.php?id=<?= $item['id']; ?>" 
                    class="btn btn-danger btn-sm"
                    onclick="return confirm('Are you sure you want to delete this product?')"
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
</div>

<?php include('includes/footer.php');?>