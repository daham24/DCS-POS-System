<?php 

include('../config/function.php');

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


if(isset($_POST['saveCategory']))
{
  $name = validate($_POST['name']);
  $description = validate($_POST['description']);
  $status = isset($_POST['status']) == true ? 1:0;


  $data = [
    'name' => $name,
    'description' => $description,
    'status' => $status
  ];
  $result = insert('categories', $data);
  if($result){
    redirect('categories.php', 'Category Created Successfuly! ');
  }else{
    redirect('categories-create.php', 'Something Went Wrong! ');
  }

}


if(isset($_POST['updateCategory']))
{
  $categoryId = validate($_POST['categoryId']);

  $name = validate($_POST['name']);
  $description = validate($_POST['description']);
  $status = isset($_POST['status']) == true ? 1:0;


  $data = [
    'name' => $name,
    'description' => $description,
    'status' => $status
  ];
  $result = update('categories', $categoryId, $data);
  if($result){
    redirect('categories-edit.php?id='.$categoryId, 'Category Updated Successfuly! ');
  }else{
    redirect('categories-edit.php?id='.$categoryId, 'Something Went Wrong! ');
  }
}

if(isset($_POST['saveProduct']))
{
  $category_id = validate($_POST['category_id']);
  $name = validate($_POST['name']);
  $description = validate($_POST['description']);
  $price = validate($_POST['price']);
  $quantity = validate($_POST['quantity']);

  if (!isset($_POST['barcode']) || empty($_POST['barcode'])) {
    $barcode = null; // Set to null if no barcode is provided
  } else {
      $barcode = validate($_POST['barcode']);
  }
  
  $discount = isset($_POST['discount']) ? validate($_POST['discount']) : 0; // New field with default
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
    'name' => $name,
    'description' => $description,
    'price' => $price,
    'quantity' => $quantity,
    'barcode' => $barcode,  // Adding barcode
    'discount' => $discount, // Adding discount
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
  $quantity = validate($_POST['quantity']);
  $barcode = validate($_POST['barcode']); // New field
  $discount = isset($_POST['discount']) ? validate($_POST['discount']) : 0; // New field with default
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
    'quantity' => $quantity,
    'barcode' => $barcode,  // Adding barcode
    'discount' => $discount, // Adding discount
    'image' => $finalImage,
    'status' => $status
  ];
  $result = update('products', $product_id, $data);
  if($result){
    redirect('products-edit.php?id='.$product_id, 'Product Updated Successfully! ');
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
      redirect('customers-edit.php?id='.$customerId, 'Customer Updated Successfully!');
    }else
    {
      redirect('customers-edit.php?id='.$customerId, 'Something Went Wrong.');
    }

  
  }else
  {
    redirect('customers-edit.php?id='.$customerId, 'Please fill required fields.');
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



?>