<?php include('includes/header.php');?>

<div class="container-fluid px-4">
    
  <div class="card mt-4 shadow-sm">
      <div class="card-header">
        <h4 class="mb-0">Add Product
          <a href="products.php" class="btn btn-primary float-end">Back</a>
        </h4>
      </div>
      <div class="card-body">

       <?php alertMessage(); ?> 

       <form action="code.php" method="POST" enctype="multipart/form-data">

          <div class="row">
            <div class="col-md-12 mb-3">
              <label>Category Id</label>
              <select name="category_id" class="form-select">
                <option value="">Select Category</option>
                <?php 
                  $categories = getAll('categories');
                  if($categories){
                    if(mysqli_num_rows($categories) > 0){
                      foreach($categories as $cateItem){
                        echo '<option value="'.$cateItem['id'].'">'.$cateItem['name'].'</option>';
                      }
                    }else{
                      echo '<option value="">No Category Found!</option>';
                    }
                  }else{
                    echo '<option value="">Something Went Wrong!</option>';
                  }
                ?>
              </select>
            </div>

            <div class="col-md-12 mb-3">
              <label for="">Product Name *</label>
              <input type="text" name="name" required class="form-control"/>
            </div>
            <div class="col-md-12 mb-3">
              <label for="">Description</label>
              <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-4 mb-3">
              <label for="">Actual Price *</label>
              <input type="number" name="price" required id="actual_price" class="form-control" oninput="calculateDiscount()"/>
            </div>
            <div class="col-md-4 mb-3">
              <label for="">Selling Price *</label>
              <input type="number" name="sell_price" required id="selling_price" class="form-control" oninput="calculateDiscount()"/>
            </div>
            <div class="col-md-4 mb-3">
                <label for="discount">Discount</label>
                <input type="number" step="0.01" name="discount" id="discount" class="form-control" readonly>
            </div>
            <div class="col-md-4 mb-3">
                <label for="barcode">Barcode</label>
                <input type="text" name="barcode" id="barcode" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
              <label for="">Quantity *</label>
              <input type="text" name="quantity" required class="form-control"/>
            </div>
            <div class="col-md-4 mb-3">
              <label for="">Image </label>
              <input type="file" name="image" class="form-control"/>
            </div>
            <div class="col-md-6">
              <label>Status (Unchecked = Visible, Checked = Hidden)</label>
              <br>
              <input type="checkbox" name="status" style="width: 30px; height:30px";>
            </div>
  
            <div class="col-md-6 mb-3 text-end">
              <br>
              <button type="submit" name="saveProduct" class="btn btn-primary">Save</button>
            </div>

          </div>

       </form>
      </div>
  </div>
  
</div>

<?php include('includes/footer.php');?>


<script>
  // Function to calculate the discount
  function calculateDiscount() {
    var actualPrice = parseFloat(document.getElementById('actual_price').value);
    var sellingPrice = parseFloat(document.getElementById('selling_price').value);

    // Ensure the values are numbers and calculate discount
    if (!isNaN(actualPrice) && !isNaN(sellingPrice)) {
      var discount = actualPrice - sellingPrice;
      document.getElementById('discount').value = discount.toFixed(2); // Display discount with 2 decimal points
    }
  }
</script>
