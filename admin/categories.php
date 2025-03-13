<?php include('includes/header.php');?>

<div class="container-fluid px-4">
    
  <div class="card mt-4 shadow-sm">
      <div class="card-header">
        <h4 class="mb-0">Categories
          <a href="categories-create.php" class="btn btn-primary float-end">Add Category</a>
        </h4>
      </div>
      <div class="card-body">
        <?php alertMessage(); ?> 

        <?php
              $categories = getAll('categories');
              if(!$categories){
                echo '<h4>Something Went Wrong!</h4>';
                return false;
              }
              if(mysqli_num_rows($categories) > 0)
              {
              ?>
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Name</th>
                <th>Status</th> 
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              
                <?php foreach($categories as $item) : ?>
                <tr>
                  <td><?= $item['name']?></td>                 
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
                    <a href="categories-edit.php?id=<?=$item['id']?>" class="btn btn-success btn-sm">Edit</a>
                    <a href="categories-delete.php?id=<?=$item['id']?>" class="btn btn-danger btn-sm">Delete</a>
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

  <div class="card mt-4 shadow-sm">
  <div class="card-header">
    <h4 class="mb-0">Sub Categories</h4>
  </div>
  <div class="card-body">
    <?php alertMessage(); ?> 

    <?php

    function getSubCategoriesWithCategoryName() {
      global $conn;

      $query = "SELECT sc.id, sc.name AS subcategory_name, c.name AS category_name, sc.status 
                FROM sub_categories sc
                LEFT JOIN categories c ON sc.category_id = c.id";
      $result = mysqli_query($conn, $query);

      return $result;
    }

    $subCategories = getSubCategoriesWithCategoryName(); // Use the new function
    if (!$subCategories) {
      echo '<h4>Something Went Wrong!</h4>';
      return false;
    }
    if (mysqli_num_rows($subCategories) > 0) {
    ?>
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Category</th>
              <th>Name</th>
              <th>Status</th> 
            </tr>
          </thead>
          <tbody>
            <?php foreach ($subCategories as $item) : ?>
              <tr>
                <td><?= $item['category_name']; ?></td> <!-- Display category name -->
                <td><?= $item['subcategory_name']; ?></td> <!-- Display subcategory name -->
                <td>
                  <?php
                  if ($item['status'] == 1) {
                    echo '<span class="badge bg-danger">Hidden</span>';
                  } else {
                    echo '<span class="badge bg-primary">Visible</span>';
                  }
                  ?>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this subcategory?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form id="deleteForm" method="POST" action="code.php" style="display: inline;">
          <input type="hidden" name="subCategoryId" id="subCategoryId">
          <button type="submit" name="deleteSubCategory" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

  
</div>

<?php include('includes/footer.php');?>