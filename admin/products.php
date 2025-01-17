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
              foreach($categories as $category) {
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

          $query = "SELECT * FROM products WHERE 1=1";

          if($searchQuery) {
            $query .= " AND name LIKE '%$searchQuery%'";
          }

          if($categoryFilter) {
            $query .= " AND category_id = '$categoryFilter'";
          }

          $products = mysqli_query($conn, $query);

          if(!$products){
            echo '<h4>Something Went Wrong!</h4>';
            return false;
          }
          if(mysqli_num_rows($products) > 0)
          {
        ?>
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Barcode</th>
                <th>Discount (%)</th>
                <th>Status</th> 
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              
                <?php foreach($products as $item) : ?>
                <tr>
                  <td><?= $item['id']?></td>
                  <td>
                    <img src="../<?= $item['image']?>" style="width:50px; height: 50px;" alt="Img">
                  </td>
                  <td><?= $item['name']?></td> 
                  <td><?= $item['price']?></td>  
                  <td><?= $item['quantity']?></td>
                  <td><?= $item['barcode']?></td> <!-- Display barcode -->
                  <td><?= $item['discount']?></td> <!-- Display discount -->
                  <td>
                    <?php
                      if($item['status'] == 1){
                        echo '<span class="badge bg-danger">Hidden</span>';
                      }else{
                        echo '<span class="badge bg-primary">Visible</span>';
                      }                    
                    ?>
                  </td>
                  <td>
                    <a href="products-edit.php?id=<?=$item['id']?>" class="btn btn-success btn-sm">Edit</a>
                    <a 
                        href="products-delete.php?id=<?=$item['id']?>" 
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure you want to delete this image?')"
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
              }
              else
              {
                ?>
                  <h4 class="mb-0">No Record Found</h4>
                <?php
              }
        ?>
      </div>
  </div>
  
</div>

<?php include('includes/footer.php');?>