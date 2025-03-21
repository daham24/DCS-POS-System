<?php include('../config/function.php');?>

<?php

if(isset($_POST['saveAdmin']))
{
  $name = validate($_POST['name']);
  $email = validate($_POST['email']);
  $password = validate($_POST['password']);
  $phone = validate($_POST['phone']);
  $role = validate($_POST['role']); // New role field
  $is_ban = isset($_POST['is_ban']) == true ? 1 : 0;

  if($name != '' && $email != '' && $password != '' && $role != ''){

    $emailCheck = mysqli_query($conn, "SELECT * FROM admins WHERE email='$email' ");

    if($emailCheck){
      if(mysqli_num_rows($emailCheck) > 0){
        redirect('admins-create.php', 'Email Already used by another user. ');
      }
    }

    $bcrypt_password = password_hash($password, PASSWORD_BCRYPT);

    $data = [
      'name' => $name,
      'email' => $email,
      'password' => $bcrypt_password,
      'phone' => $phone,
      'role' => $role, // Add role to the data array
      'is_ban' => $is_ban
    ];
    $result = insert('admins', $data);
    if($result){
      redirect('admins.php', 'Admin Created Successfully! ');
    }else{
      redirect('admins-create.php', 'Something Went Wrong! ');
    }

  }else{
    redirect('admins-create.php', 'Please fill required fields. ');
  }

}


if (isset($_POST['updateAdmin'])) {

  // Validate and fetch the admin ID
  $adminID = validate($_POST['adminId']);

  // Retrieve admin data from the database
  $adminData = getById('admins', $adminID);
  if ($adminData['status'] != 200) {
    redirect('admins-edit.php?id=' . $adminID, 'Invalid Admin ID. Please try again.');
  }

  // Fetch and validate form data
  $name = validate($_POST['name']);
  $email = validate($_POST['email']);
  $password = validate($_POST['password']);
  $phone = validate($_POST['phone']);
  $role = validate($_POST['role']); // New role field
  $is_ban = isset($_POST['is_ban']) ? 1 : 0;

  $EmailCheckQuery = "SELECT * FROM admins WHERE email = '$email' AND id!= '$adminID'";
  $checkResult = mysqli_query($conn, $EmailCheckQuery);

  if($checkResult){

    if(mysqli_num_rows($checkResult) > 0){
      redirect('admins-edit.php?id=' . $adminID, 'Email already used by another user');
    }

  }

  // Hash the password only if a new password is provided
  $hashedPassword = $password !== '' ? password_hash($password, PASSWORD_BCRYPT) : $adminData['data']['password'];

  // Check for required fields
  if ($name !== '' && $email !== '' && $role !== '') {
    // Prepare data for update
    $data = [
      'name' => $name,
      'email' => $email,
      'password' => $hashedPassword,
      'phone' => $phone,
      'role' => $role, // Include role in the update
      'is_ban' => $is_ban
    ];

    // Attempt to update the admin record
    $result = update('admins', $adminID, $data);
    if ($result) {
      redirect('admins-edit.php?id=' . $adminID, 'Admin Updated Successfully!');
    } else {
      redirect('admins-edit.php?id=' . $adminID, 'Something went wrong during the update. Please try again.');
    }
  } else {
    // Redirect with an error if required fields are missing
    redirect('admins-edit.php?id=' . $adminID, 'Please fill in all required fields.');
  }
}


if (isset($_POST['saveCategory'])) {
  $name = validate($_POST['name']);
  $description = validate($_POST['description']);
  $status = isset($_POST['status']) ? 1 : 0;


  $data = [
      'name' => $name,
      'description' => $description,
      'status' => $status
  ];

  $result = insert('categories', $data);

  if ($result) {
      redirect('categories.php', 'Category Created Successfully!');
  } else {
      redirect('category-create.php', 'Something Went Wrong!');
  }
}

if (isset($_POST['updateCategory'])) {
  $categoryId = validate($_POST['categoryId']);

  $name = validate($_POST['name']);
  $description = validate($_POST['description']);
  $status = isset($_POST['status']) ? 1 : 0;

  $data = [
      'name' => $name,
      'description' => $description,
      'status' => $status
  ];

  $result = update('categories', $categoryId, $data);

  if ($result) {
      redirect('categories-edit.php?id=' . $categoryId, 'Category Updated Successfully!');
  } else {
      redirect('categories-edit.php?id=' . $categoryId, 'Something Went Wrong!');
  }
}

if (isset($_POST['saveSubCategory'])) {
  $categoryId = validate($_POST['categoryId']);
  $name = validate($_POST['name']);
  $status = isset($_POST['status']) ? 1 : 0;

  // Validate categoryId
  if (empty($categoryId) || !is_numeric($categoryId)) {
    redirect('categories-create.php', 'Invalid category ID!');
  }

  $data = [
      'name' => $name,
      'category_id' => (int)$categoryId, // Ensure category_id is an integer
      'status' => $status
  ];

  $result = insert('sub_categories', $data);

  if ($result) {
      redirect('categories.php', 'Sub Category Added Successfully!');
  } else {
      redirect('categories-create.php', 'Something Went Wrong!');
  }
}

if (isset($_POST['updateSubCategory'])) {
  $subCategoryId = validate($_POST['subCategoryId']);
  $name = validate($_POST['name']);
  $status = isset($_POST['status']) ? 1 : 0;

  $data = [
      'name' => $name,
      'status' => $status
  ];

  $result = update('sub_categories', $subCategoryId, $data);

  if ($result) {
      redirect('categories.php?id=' . $paramValue, 'Sub Category Updated Successfully!');
  } else {
      redirect('categories-edit.php?id=' . $paramValue, 'Something Went Wrong!');
  }
}

if (isset($_POST['deleteSubCategory'])) {
  $subCategoryId = validate($_POST['subCategoryId']);
  $categoryId = validate($_POST['categoryId']);

  if ($subCategoryId != '' && $categoryId != '') {
    // Use prepared statements to prevent SQL injection
    $query = "DELETE FROM sub_categories WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
      mysqli_stmt_bind_param($stmt, "i", $subCategoryId);
      mysqli_stmt_execute($stmt);

      if (mysqli_stmt_affected_rows($stmt) > 0) {
        redirect('categories-edit.php?id=' . $categoryId, 'Subcategory Deleted Successfully!');
      } else {
        redirect('categories-edit.php?id=' . $categoryId, 'Subcategory not found or already deleted.');
      }
      mysqli_stmt_close($stmt);
    } else {
      redirect('categories-edit.php?id=' . $categoryId, 'Something Went Wrong.');
    }
  } else {
    redirect('categories-edit.php?id=' . $categoryId, 'Invalid request.');
  }
}

if(isset($_POST['saveProduct']))
{
  $category_id = validate($_POST['category_id']);
  $sub_category_id = validate($_POST['sub_category_id']);
  $name = validate($_POST['name']);
  $description = validate($_POST['description']);
  $price = validate($_POST['price']);
  $sell_price = validate($_POST['sell_price']);
  $quantity = validate($_POST['quantity']);

  if (!isset($_POST['barcode']) || empty($_POST['barcode'])) {
    $barcode = null; // Set to null if no barcode is provided
  } else {
      $barcode = validate($_POST['barcode']);
  }

  if (!isset($_POST['imei_code']) || empty($_POST['imei_code'])) {
    $imei_code = null; 
  } else {
      $imei_code = validate($_POST['imei_code']);
  }
  
  $discount = isset($_POST['discount']) ? validate($_POST['discount']) : 0; 
  $warranty_period = isset($_POST['warranty_period']) ? validate($_POST['warranty_period']) : 0; 
  $status = isset($_POST['status']) == true ? 1 : 0;

  if($_FILES['image']['size'] > 0)
  {
    $path = "../assets/uploads/products";
    $image_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

    $filename = time().'.'.$image_ext;

    move_uploaded_file($_FILES['image']['tmp_name'], $path."/".$filename);
    $finalImage = "assets/uploads/products/".$filename;

  }else{
    $finalImage = '';
  }

  $data = [
    'category_id' => $category_id,
    'sub_category_id' => $sub_category_id, // Correct field name
    'name' => $name,
    'description' => $description,
    'price' => $price,
    'sell_price' => $sell_price,
    'quantity' => $quantity,
    'barcode' => $barcode,  // Adding barcode
    'imei_code' => $imei_code,
    'discount' => $discount, // Adding discount
    'warranty_period' => $warranty_period,
    'image' => $finalImage,
    'status' => $status
  ];

  $result = insert('products', $data);

  if($result){
    redirect('products.php', 'Product Created Successfully! ');
  }else{
    redirect('products-create.php', 'Something Went Wrong! ');
  }
}

if(isset($_POST['updateProduct']))
{
  $product_id = validate($_POST['product_id']);
  $productData = getById('products', $product_id);
  if(!$productData){
    redirect('products.php', 'No such product found');
  }

  $category_id = validate($_POST['category_id']);
  $name = validate($_POST['name']);
  $description = validate($_POST['description']);
  $price = validate($_POST['price']);
  $sell_price = validate($_POST['sell_price']);
  $quantity = validate($_POST['quantity']);
  $barcode = validate($_POST['barcode']); 
  $imei_code = validate($_POST['imei_code']);
  $discount = isset($_POST['discount']) ? validate($_POST['discount']) : 0;
  $warranty_period = isset($_POST['warranty_period']) ? validate($_POST['warranty_period']) : 0;  
  $status = isset($_POST['status']) == true ? 1 : 0;

  if($_FILES['image']['size'] > 0)
  {
    $path = "../assets/uploads/products";
    $image_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

    $filename = time().'.'.$image_ext;

    move_uploaded_file($_FILES['image']['tmp_name'], $path."/".$filename);
    $finalImage = "assets/uploads/products/".$filename;

    $deleteImage = "../".$productData['data']['image'];
    if(file_exists($deleteImage)){
      unlink($deleteImage);
    }

  }else{
    $finalImage = $productData['data']['image'];
  }

  $data = [
    'category_id' => $category_id,
    'name' => $name,
    'description' => $description,
    'price' => $price,
    'sell_price' => $sell_price,
    'quantity' => $quantity,
    'barcode' => $barcode,  // Adding barcode
    'imei_code' => $imei_code,
    'discount' => $discount, // Adding discount
    'warranty_period' => $warranty_period,
    'image' => $finalImage,
    'status' => $status
  ];
  $result = update('products', $product_id, $data);
  if($result){
    redirect('products.php?id='.$product_id, 'Product Updated Successfully! ');
  }else{
    redirect('products-edit.php?id='.$product_id, 'Something Went Wrong! ');
  }
}

if(isset($_POST['saveCustomer']))
{
  $name = validate($_POST['name']);
  $email = validate($_POST['email']);
  $phone = validate($_POST['phone']);
  $status = isset($_POST['status']) ? 1:0;

  if($name != '')
  {
    $emailCheck = mysqli_query($conn, "SELECT * FROM customers WHERE email='$email' ");
    if($emailCheck){
      if(mysqli_num_rows($emailCheck) > 0){
        redirect('customers.php', 'Email already used by another customer.');
      }
    }

    $data = [
      'name' => $name,
      'email' => $email,
      'phone' => $phone,
      'status' => $status
    ];

    $result = insert('customers', $data);
    
    if($result)
    {
      redirect('customers.php', 'Customer Created Successfully!');
    }else
    {
      redirect('customers.php', 'Something Went Wrong.');
    }


  }else
  {
    redirect('customers.php', 'Please fill required fields.');
  }
}


if(isset($_POST['updateCustomer']))
{
  $customerId = validate($_POST['customerId']);

  $name = validate($_POST['name']);
  $email = validate($_POST['email']);
  $phone = validate($_POST['phone']);
  $status = isset($_POST['status']) ? 1:0;

  if($name != '')
  {
    $emailCheck = mysqli_query($conn, "SELECT * FROM customers WHERE email='$email' AND id!='$customerId'");
    if($emailCheck){
      if(mysqli_num_rows($emailCheck) > 0){
        redirect('customers-edit.php?id='.$customerId, 'Email already used by another customer.');
      }
    }

    $data = [
      'name' => $name,
      'email' => $email,
      'phone' => $phone,
      'status' => $status
    ];

    $result = update('customers', $customerId, $data);
    
    if($result)
    {
      redirect('customers.php?id='.$customerId, 'Customer Updated Successfully!');
    }else
    {
      redirect('customers-edit.php?id='.$customerId, 'Something Went Wrong.');
    }

  
  }else
  {
    redirect('customers-edit.php?id='.$customerId, 'Please fill required fields.');
  }
}


if (isset($_POST['saveSupplier'])) {
  $name = validate($_POST['name']);
  $email = !empty($_POST['email']) ? validate($_POST['email']) : null; // Allow null
  $phone = validate($_POST['phone']);
  $status = isset($_POST['status']) ? 1 : 0;
  $products = isset($_POST['products']) ? $_POST['products'] : []; // Get selected products

  if ($name != '') {
    // Check if email is provided and already exists
    if ($email !== null) {
      $emailCheck = mysqli_query($conn, "SELECT * FROM suppliers WHERE email='$email'");
      if ($emailCheck && mysqli_num_rows($emailCheck) > 0) {
        redirect('suppliers.php', 'Email already used by another supplier.');
      }
    }

    // Insert supplier
    $data = [
      'name' => $name,
      'email' => $email,
      'phone' => $phone,
      'status' => $status
    ];
    $supplierId = insert('suppliers', $data);

    if ($supplierId) {
      // Insert supplier-product relationships
      foreach ($products as $productId) {
        $productId = validate($productId);
        mysqli_query($conn, "INSERT INTO supplier_products (supplier_id, product_id) VALUES ('$supplierId', '$productId')");
      }
      redirect('suppliers.php', 'Supplier Created Successfully!');
    } else {
      redirect('suppliers.php', 'Something Went Wrong.');
    }
  } else {
    redirect('suppliers.php', 'Please fill required fields.');
  }
}

if (isset($_POST['updateSupplier'])) {
  $supplierId = validate($_POST['supplierId']);
  $name = validate($_POST['name']);
  $email = !empty($_POST['email']) ? validate($_POST['email']) : null; // Allow null
  $phone = validate($_POST['phone']);
  $status = isset($_POST['status']) ? 1 : 0;

  if ($name != '') {
    // Check if email is provided and already exists (excluding current supplier)
    if ($email !== null) {
      $emailCheck = mysqli_query($conn, "SELECT * FROM suppliers WHERE email='$email' AND id!='$supplierId'");
      if ($emailCheck && mysqli_num_rows($emailCheck) > 0) {
        redirect('supplier-edit.php?id=' . $supplierId, 'Email already used by another supplier.');
      }
    }

    // Update supplier
    $data = [
      'name' => $name,
      'email' => $email,
      'phone' => $phone,
      'status' => $status
    ];
    $result = update('suppliers', $supplierId, $data);

    if ($result) {
      redirect('supplier-edit.php?id=' . $supplierId, 'Supplier Updated Successfully!');
    } else {
      redirect('supplier-edit.php?id=' . $supplierId, 'Something Went Wrong.');
    }
  } else {
    redirect('supplier-edit.php?id=' . $supplierId, 'Please fill required fields.');
  }
}

if (isset($_POST['saveSupplierProduct'])) {
  $supplierId = validate($_POST['supplierId']);
  $productId = validate($_POST['productId']);

  if ($supplierId != '' && $productId != '') {
    $query = "INSERT INTO supplier_products (supplier_id, product_id) VALUES ('$supplierId', '$productId')";
    $result = mysqli_query($conn, $query);

    if ($result) {
      redirect('supplier-edit.php?id=' . $supplierId, 'Product Added Successfully!');
    } else {
      redirect('supplier-edit.php?id=' . $supplierId, 'Something Went Wrong.');
    }
  } else {
    redirect('supplier-edit.php?id=' . $supplierId, 'Please fill required fields.');
  }
}

if (isset($_POST['updateSupplierProduct'])) {
  $supplierProductId = validate($_POST['supplierProductId']);
  $productId = validate($_POST['productId']);
  $supplierId = validate($_POST['supplierId']); // Add supplierId

  if ($supplierProductId != '' && $productId != '' && $supplierId != '') {
    // Use prepared statements to prevent SQL injection
    $query = "UPDATE supplier_products SET product_id=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
      mysqli_stmt_bind_param($stmt, "ii", $productId, $supplierProductId);
      mysqli_stmt_execute($stmt);

      if (mysqli_stmt_affected_rows($stmt) > 0) {
        redirect('supplier-edit.php?id=' . $supplierId, 'Product Updated Successfully!');
      } else {
        redirect('supplier-edit.php?id=' . $supplierId, 'No changes made or product not found.');
      }
      mysqli_stmt_close($stmt);
    } else {
      redirect('supplier-edit.php?id=' . $supplierId, 'Something Went Wrong.');
    }
  } else {
    redirect('supplier-edit.php?id=' . $supplierId, 'Please fill required fields.');
  }
}

if (isset($_POST['deleteSupplierProduct'])) {
  $supplierProductId = validate($_POST['supplierProductId']);
  $supplierId = validate($_POST['supplierId']);

  if ($supplierProductId != '' && $supplierId != '') {
    // Use prepared statements to prevent SQL injection
    $query = "DELETE FROM supplier_products WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
      mysqli_stmt_bind_param($stmt, "i", $supplierProductId);
      mysqli_stmt_execute($stmt);

      if (mysqli_stmt_affected_rows($stmt) > 0) {
        redirect('supplier-edit.php?id=' . $supplierId, 'Product Deleted Successfully!');
      } else {
        redirect('supplier-edit.php?id=' . $supplierId, 'Product not found or already deleted.');
      }
      mysqli_stmt_close($stmt);
    } else {
      redirect('supplier-edit.php?id=' . $supplierId, 'Something Went Wrong.');
    }
  } else {
    redirect('supplier-edit.php?id=' . $supplierId, 'Invalid request.');
  }
}

if (isset($_POST['updateRepair'])) {
  // Validate input data
  $repairId = validate($_POST['repairId']);
  $item_name = validate($_POST['item_name']);
  $customer_id = validate($_POST['customer_id']);
  $description = validate($_POST['description']);
  $status = isset($_POST['status']) ? 1 : 0;

  // Handle checkbox fields (physical_condition and received_items)
  $physical_condition = isset($_POST['physical_condition']) ? implode(', ', $_POST['physical_condition']) : '';
  $received_items = isset($_POST['received_items']) ? implode(', ', $_POST['received_items']) : '';

  // Ensure required fields are not empty
  if ($item_name != '' && $customer_id != '' && $description != '') {
      // Check if the selected customer exists
      $customerCheck = mysqli_query($conn, "SELECT * FROM customers WHERE id='$customer_id'");
      if ($customerCheck) {
          if (mysqli_num_rows($customerCheck) == 0) {
              redirect('repairs-edit.php?id=' . $repairId, 'Selected customer does not exist.');
              return;
          }
      } else {
          redirect('repairs-edit.php?id=' . $repairId, 'Error validating customer.');
          return;
      }

      // Prepare data to update
      $data = [
          'item_name' => $item_name,
          'customer_id' => $customer_id,
          'description' => $description,
          'physical_condition' => $physical_condition,
          'received_items' => $received_items,
          'status' => $status
      ];

      // Call the update function
      $result = update('repairs', $repairId, $data);

      // Redirect based on result
      if ($result) {
          redirect('repairs-edit.php?id=' . $repairId, 'Repair item updated successfully!');
      } else {
          redirect('repairs-edit.php?id=' . $repairId, 'Something went wrong.');
      }
  } else {
      redirect('repairs-edit.php?id=' . $repairId, 'Please fill in all required fields.');
  }
}


if (isset($_POST['returnItems'])) {
  // Get the selected items and reason for the return
  $items = $_POST['items']; // Items is an array of selected items
  $reason = validate($_POST['reason']); // Reason for the return

  // Loop through each selected item
  foreach ($items as $item) {
      list($order_id, $product_id) = explode('-', $item); // Get order ID and product ID

      // Check product stock, if needed
      // Here you can fetch product data if required

      // Insert the return information into the 'returns' table
      $query = "INSERT INTO returns (order_item_id, product_id, quantity, return_date, reason, status)
                VALUES ('$order_id', '$product_id', 1, CURDATE(), '$reason', 'pending')";
      $result = mysqli_query($conn, $query);

      if (!$result) {
          echo "Error processing return: " . mysqli_error($conn);
          exit; // Stop further processing if the query fails
      }
  }

  // Return processed successfully, redirect to return-item-view.php
  header("Location: return-items-view.php"); // Redirect to the return items view page
  exit; // Stop script execution after redirection
}


if (isset($_POST['updateReturn'])) {
  $returnId = $_POST['return_id']; // Get return ID
  $returnDate = $_POST['return_date']; // Get new return date
  $reason = $_POST['reason']; // Get updated reason
  $status = $_POST['status']; // Get new status

  // Update the return item in the database
  $query = "UPDATE returns 
            SET return_date = '$returnDate', reason = '$reason', status = '$status' 
            WHERE id = '$returnId'";

  if (mysqli_query($conn, $query)) {
      // Redirect back to the returns page or success page
      header("Location: return-items-view.php");
      exit;
  } else {
      echo "Error updating return: " . mysqli_error($conn);
  }
}


?>