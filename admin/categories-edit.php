<?php include('includes/header.php');?>

<div class="container-fluid px-4 mb-3">
    
  <div class="card mt-4 shadow-sm">
      <div class="card-header">
        <h4 class="mb-0">Edit Category
          <a href="categories.php" class="btn btn-outline-secondary float-end">Back</a>
        </h4>
      </div>
      <div class="card-body">

       <?php alertMessage(); ?> 

       <form action="code.php" method="POST">

          <?php 
          $paramValue = checkParamId('id');
          
          if(!is_numeric($paramValue)){
            echo '<h5>'.$paramValue.'</h5>';
            return false;
          }

          $category = getById('categories', $paramValue); 
          if($category['status'] == 200)
          {            
          ?>

          <input type="hidden" name="categoryId" value="<?= $category['data']['id'];?>" >

          <div class="row">
            <div class="col-md-12 mb-3">
              <label for="">Name *</label>
              <input type="text" name="name" value="<?= $category['data']['name']; ?>" required class="form-control"/>
            </div>
            <div class="col-md-12 mb-3">
              <label for="">Description</label>
              <textarea name="description"  class="form-control" rows="3"><?= $category['data']['description']; ?></textarea>
            </div>
            <div class="col-md-6">
              <label>Status (Uncheked=Visible, Checked=Hidden)</label>
              <br>
              <input type="checkbox" name="status" <?= $category['data']['status'] == true ? 'checked':''; ?> style="width: 20px; height:20px";>
            </div>
  
            <div class="col-md-6 mb-3 text-end">
              <br>
              <button type="submit" name="updateCategory" class="btn btn-dark">Save Changes</button>
            </div>

          </div>
          <?php
          }
          else{
            echo '<h5>'.$category['message'].'</h5>';
          }
          ?>
       </form>
      </div>
  </div>

  <div class="card mt-4 shadow-sm">
    <div class="card-header">
      <h4 class="mb-0">Edit Sub Categories</h4>
    </div>
    <div class="card-body">
      <?php alertMessage(); ?> 

      <?php

      function getSubCategoriesByCategoryId($categoryId) {
        global $conn;
      
        $query = "SELECT * FROM sub_categories WHERE category_id = $categoryId";
        $result = mysqli_query($conn, $query);
      
        return $result;
      }

      // Fetch all subcategories for the selected category
      $paramValue = checkParamId('id');
      if (is_numeric($paramValue)) {
        $subCategories = getSubCategoriesByCategoryId($paramValue); // Custom function to fetch subcategories
        if ($subCategories && mysqli_num_rows($subCategories) > 0) {
          while ($subCategory = mysqli_fetch_assoc($subCategories)) {
      ?>
            <form action="code.php" method="POST" class="mb-4">
              <input type="hidden" name="subCategoryId" value="<?= $subCategory['id']; ?>" >

              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="">Name *</label>
                  <input type="text" name="name" value="<?= $subCategory['name']; ?>" required class="form-control"/>
                </div>
                <div class="col-md-4">
                  <label>Status (Unchecked=Visible, Checked=Hidden)</label>
                  <br>
                  <input type="checkbox" name="status" <?= $subCategory['status'] == 1 ? 'checked' : ''; ?> style="width: 20px; height:20px";>
                </div>
                <div class="col-md-4 mb-3 text-end">
                  <br>
                  <button type="submit" name="updateSubCategory" class="btn btn-outline-dark">Update</button>
                  <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" onclick="setDeleteId(<?= $subCategory['id']; ?>)">Delete</button>
                </div>
              </div>
            </form>
      <?php
          }
        } else {
          echo '<p>No subcategories found for this category.</p>';
        }
      } else {
        echo '<p>Invalid category ID.</p>';
      }
      ?>
    </div>
  </div>

  <div class="card mt-4 shadow-sm">
    <div class="card-header">
      <h4 class="mb-0">Add New Sub Category</h4>
    </div>
    <div class="card-body">
      <form action="code.php" method="POST">
        <input type="hidden" name="categoryId" value="<?= $paramValue; ?>" >

        <div class="row">
          <div class="col-md-12 mb-3">
            <label for="">Name *</label>
            <input type="text" name="name" required class="form-control"/>
          </div>
          <div class="col-md-6">
            <label class="fs-6">Status (Unchecked=Visible, Checked=Hidden)</label>
            <br>
            <input type="checkbox" name="status" style="width: 20px; height:20px";>
          </div>
          <div class="col-md-6 mb-3 text-end">
            <br>
            <button type="submit" name="saveSubCategory" class="btn btn-dark"><i class="fa-solid fa-square-plus"></i> &nbsp;Add</button>
          </div>
        </div>
      </form>
    </div>
  </div>  
</div>



<?php include('includes/footer.php');?>


<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this subcategory?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmDeleteButton" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>


<script>
// Set the subcategory ID to delete
let deleteSubCategoryId = null;

function setDeleteId(id) {
  deleteSubCategoryId = id;
}

// Handle the delete confirmation
document.getElementById('confirmDeleteButton').addEventListener('click', function () {
  if (deleteSubCategoryId) {
    // Submit the form with the delete action
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'code.php';

    const subCategoryIdInput = document.createElement('input');
    subCategoryIdInput.type = 'hidden';
    subCategoryIdInput.name = 'subCategoryId';
    subCategoryIdInput.value = deleteSubCategoryId;

    const categoryIdInput = document.createElement('input');
    categoryIdInput.type = 'hidden';
    categoryIdInput.name = 'categoryId';
    categoryIdInput.value = <?= $paramValue; ?>;

    const deleteActionInput = document.createElement('input');
    deleteActionInput.type = 'hidden';
    deleteActionInput.name = 'deleteSubCategory';
    deleteActionInput.value = '1';

    form.appendChild(subCategoryIdInput);
    form.appendChild(categoryIdInput);
    form.appendChild(deleteActionInput);

    document.body.appendChild(form);
    form.submit();
  }
});
</script>

