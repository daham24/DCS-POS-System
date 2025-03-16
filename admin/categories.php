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

  <div class="card mt-4 shadow-sm mb-3">
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
                      LEFT JOIN categories c ON sc.category_id = c.id
                      ORDER BY c.name, sc.name"; // Order by category and subcategory name
            $result = mysqli_query($conn, $query);

            return $result;
        }

        $subCategories = getSubCategoriesWithCategoryName(); // Use the new function
        if (!$subCategories) {
            echo '<h4>Something Went Wrong!</h4>';
            return false;
        }

        if (mysqli_num_rows($subCategories) > 0) {
            // Group subcategories by category
            $groupedSubCategories = [];
            while ($row = mysqli_fetch_assoc($subCategories)) {
                $categoryName = $row['category_name'];
                $groupedSubCategories[$categoryName][] = $row;
            }
        ?>
            <div class="row">
                <?php foreach ($groupedSubCategories as $categoryName => $subCategories) : ?>
                    <div class="col-md-3 mb-4"> <!-- 4 cards per row (12 columns / 3 = 4 cards) -->
                        <div class="card h-100 shadow-sm">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><?= $categoryName; ?></h5> <!-- Category Name -->
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($subCategories as $subCategory) : ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><?= $subCategory['subcategory_name']; ?></span> <!-- Subcategory Name -->
                                            <span>
                                                <?php
                                                if ($subCategory['status'] == 1) {
                                                    echo '<span class="badge bg-danger">Hidden</span>';
                                                } else {
                                                    echo '<span class="badge bg-primary">Visible</span>';
                                                }
                                                ?>
                                            </span>
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