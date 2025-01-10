<?php 

include('../config/function.php');

if(isset($_POST['saveAdmin']))
{
  $name = validate($_POST['name']);
  $email = validate($_POST['email']);
  $password = validate($_POST['password']);
  $phone = validate($_POST['phone']);
  $is_ban = isset($_POST['is_ban']) == true ? 1:0;

  if($name != '' && $email != '' && $password != ''){

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
      'is_ban' => $is_ban
    ];
    $result = insert('admins', $data);
    if($result){
      redirect('admins.php', 'Admin Created Successfuly! ');
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
  $is_ban = isset($_POST['is_ban']) ? 1 : 0;

  // Hash the password only if a new password is provided
  $hashedPassword = $password !== '' ? password_hash($password, PASSWORD_BCRYPT) : $adminData['data']['password'];

  // Check for required fields
  if ($name !== '' && $email !== '') {
    // Prepare data for update
    $data = [
      'name' => $name,
      'email' => $email,
      'password' => $hashedPassword,
      'phone' => $phone,
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

?>