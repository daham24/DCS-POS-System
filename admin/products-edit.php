<?php include('includes/header.php'); ?>

<div class="container-fluid px-4">

  <div class="card mt-4 shadow-sm">
    <div class="card-header">
      <h4 class="mb-0">Edit Product
        <a href="products.php" class="btn btn-primary float-end">Back</a>
      </h4>
    </div>
    <div class="card-body">

      <?php alertMessage(); ?>

      <form action="code.php" method="POST" enctype="multipart/form-data">

        <?php 
          $paramValue = checkParamId('id');
          if (!is_numeric($paramValue)) {
              echo '<h5>ID is not valid</h5>';
              return false;
          }

          $product = getById('products', $paramValue);
          if ($product) {
            if ($product['status'] == 200) {
        ?>

        <input type="hidden" name="product_id" value="<?= $product['data']['id']; ?>"> 
        <div class="row">
          <div class="col-md-12 mb-3">
            <label>Category</label>
            <select name="category_id" class="form-select">
              <option value="">Select Category</option>
              <?php 
                $categories = getAll('categories');
                if ($categories) {
                  if (mysqli_num_rows($categories) > 0) {
                    foreach ($categories as $cateItem) {
              ?>
                    <option 
                        value="<?= $cateItem['id']; ?>"
                        <?= $product['data']['category_id'] == $cateItem['id'] ? 'selected' : ''; ?>
                    >
                      <?= $cateItem['name']; ?>
                    </option>
              <?php                        
                    }
                  } else {
                    echo '<option value="">No Category Found!</option>';
                  }
                } else {
                  echo '<option value="">Something Went Wrong!</option>';
                }
              ?>
            </select>
          </div>

          <div class="col-md-12 mb-3">
            <label for="">Product Name *</label>
            <input type="text" name="name" required value="<?= htmlspecialchars($product['data']['name']); ?>" class="form-control" />
          </div>
          <div class="col-md-12 mb-3">
            <label for="">Description</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['data']['description']); ?></textarea>
          </div>
          <div class="col-md-4 mb-3">
            <label for="">Actual Price *</label>
            <input type="number" name="price" required id="actual_price" value="<?= htmlspecialchars($product['data']['price']); ?>" class="form-control" oninput="calculateDiscount()"/>
          </div>
          <div class="col-md-4 mb-3">
            <label for="">Selling Price *</label>
            <input type="number" name="sell_price" required id="selling_price" value="<?= htmlspecialchars($product['data']['sell_price']); ?>" class="form-control" oninput="calculateDiscount()"/>
          </div>
          <div class="col-md-4 mb-3">
            <label for="discount">Discount (%)</label>
            <input type="number" step="0.01" name="discount" id="discount" value="<?= htmlspecialchars($product['data']['discount']); ?>" class="form-control" readonly />
          </div>
          <div class="col-md-4 mb-3">
            <label for="">Quantity *</label>
            <input type="text" name="quantity" required value="<?= htmlspecialchars($product['data']['quantity']); ?>" class="form-control" />
          </div>
          <div class="col-md-4 mb-3">
            <label for="">Barcode *</label>
            <input type="text" name="barcode" required value="<?= htmlspecialchars($product['data']['barcode']); ?>" class="form-control" />
          </div>
          <div class="col-md-4 mb-3">
            <label for="">Image</label>
            <input type="file" name="image" class="form-control" />
            <img src="../<?= htmlspecialchars($product['data']['image']); ?>" style="width: 40px; height:40px;" alt="Img" />
          </div>
          <div class="col-md-6">
            <label>Status (Unchecked = Visible, Checked = Hidden)</label>
            <br>
            <input type="checkbox" name="status" <?= $product['data']['status'] ? 'checked' : ''; ?> style="width: 30px; height: 30px;">
          </div>

          <div class="col-md-6 mb-3 text-end">
            <br>
            <button type="submit" name="updateProduct" class="btn btn-primary">Update</button>
          </div>
        </div>
        <?php
            } else {
              echo '<h5>' . htmlspecialchars($product['message']) . '</h5>';
            }
          } else {
            echo '<h5>Product not found!</h5>';
          }
        ?>

      </form>
    </div>
  </div>

</div>

<?php include('includes/footer.php'); ?>


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
