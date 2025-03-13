<?php include('includes/header.php');?>

<div class="container-fluid px-4 mb-3">
    
  <div class="card mt-4 shadow-sm">
      <div class="card-header">
        <h4 class="mb-0">Add Category
          <a href="categories.php" class="btn btn-outline-primary float-end">Back</a>
        </h4>
      </div>
      <div class="card-body">

       <?php alertMessage(); ?> 

       <form action="code.php" method="POST">

          <div class="row">

            <div class="col-md-12 mb-3">
              <label for="">Name *</label>
              <input type="text" name="name" required class="form-control"/>
            </div>
            <div class="col-md-12 mb-3">
              <label for="">Description</label>
              <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-6">
              <label>Status (Uncheked=Visible, Checked=Hidden)</label>
              <br>
              <input type="checkbox" name="status" style="width: 20px; height: 20px";>
            </div>
  
            <div class="col-md-6 mb-3 text-end">
              <br>
              <button type="submit" name="saveCategory" class="btn btn-primary">Save</button>
            </div>

          </div>

       </form>
      </div>
  </div>

  <div class="card mt-4 shadow-sm">
      <div class="card-header">
        <h4 class="mb-0">Add Sub Category</h4>
      </div>
      <div class="card-body">

       <?php alertMessage(); ?> 

       <form action="code.php" method="POST">

          <div class="row">

            <div class="col-md-3 mb-3">
                <label>Main Category</label>
                <select name="categoryId" class="form-select">
                  <option value="">Select Category</option>
                  <?php
                  $categories = getAll('categories');
                  if ($categories) {
                    foreach ($categories as $cateItem) {
                      echo '<option value="' . $cateItem['id'] . '">' . $cateItem['name'] . '</option>';
                    }
                  }
                  ?>
                </select>
            </div>

            <div class="col-md-12 mb-3">
              <label for="">Name *</label>
              <input type="text" name="name" required class="form-control"/>
            </div>
            <div class="col-md-6">
              <label>Status (Uncheked=Visible, Checked=Hidden)</label>
              <br>
              <input type="checkbox" name="status" style="width: 20px; height: 20px";>
            </div>
  
            <div class="col-md-6 mb-3 text-end">
              <br>
              <button type="submit" name="saveSubCategory" class="btn btn-primary">Save</button>
            </div>

          </div>

       </form>
      </div>
  </div>
  
</div>

<?php include('includes/footer.php');?>