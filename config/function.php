<?php
session_start();

require 'dbCon.php';

// Input field validation
function validate($inputData) { 
  global $conn;

  if (!is_string($inputData)) {
      return ''; // Return an empty string or handle it appropriately
  }

  $validatedData = mysqli_real_escape_string($conn, $inputData);
  return trim($validatedData);
}

//Redirect from one page to another page with the message (status)
function redirect($url, $status){

  $_SESSION['status'] = $status;
  header('Location: '.$url);
  exit(0);
}

//Display messages or status after any process.
function alertMessage(){

  if (isset($_SESSION['status'])) {
    
    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
      <h6>'.$_SESSION['status'].'</h6>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    unset ($_SESSION['status']);
  }
}

//Insert record using this function
function insert($tableName, $data) {
  global $conn;

  // Validate table name
  $table = validate($tableName);

  // Prepare columns and placeholders
  $columns = array_keys($data);
  $placeholders = array_fill(0, count($data), '?'); // Generates placeholders like `?, ?, ?`
  
  // Prepare SQL query
  $query = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
  $stmt = mysqli_prepare($conn, $query);

  if (!$stmt) {
      die("Failed to prepare statement: " . mysqli_error($conn));
  }

  // Bind parameters dynamically
  $types = ''; // Data type string for `bind_param`
  $values = [];
  foreach ($data as $value) {
      $types .= is_null($value) ? 's' : (is_int($value) ? 'i' : 's'); // `i` for int, `s` for string
      $values[] = $value;
  }

  // Bind parameters
  mysqli_stmt_bind_param($stmt, $types, ...$values);

  // Execute query
  $result = mysqli_stmt_execute($stmt);

  if (!$result) {
      die("Failed to execute query: " . mysqli_stmt_error($stmt));
  }

  mysqli_stmt_close($stmt);

  return $result;
}

//Update data using this function
function update($tableName, $id, $data) {
  global $conn;

  // Validate table name and ID
  $table = validate($tableName);
  $id = validate($id);

  // Initialize the update query string
  $updateDataString = "";

  foreach ($data as $column => $value) {
      // Validate each value and ensure null or integer consistency
      if ($value === null) {
          $updateDataString .= "$column=NULL, ";
      } elseif (is_numeric($value)) {
          $updateDataString .= "$column=" . intval($value) . ", ";
      } else {
          $updateDataString .= "$column='" . validate($value) . "', ";
      }
  }

  // Remove the trailing comma and space
  $updateDataString = rtrim($updateDataString, ', ');

  // Construct and execute the update query
  $query = "UPDATE $table SET $updateDataString WHERE id='$id'";
  $result = mysqli_query($conn, $query);

  // Handle errors
  if (!$result) {
      throw new Exception("Update failed: " . mysqli_error($conn));
  }

  return $result;
}

function getAll($tableName, $status = NULL){

  global $conn;

  $table = validate($tableName);
  $status = validate($status);

  if($status == 'status')
  {
    $query = "SELECT * FROM $table WHERE status='0' ";
  }
  else{
    $query = "SELECT * FROM $table";
  }
  return mysqli_query($conn, $query);

}

function getById($tableName, $id) {
  global $conn;

  $table = validate($tableName);
  $id = validate($id);

  $query = "SELECT * FROM $table WHERE id='$id' LIMIT 1";
  $result = mysqli_query($conn, $query);

  if ($result) {
      if (mysqli_num_rows($result) == 1) {
          $row = mysqli_fetch_assoc($result);
          return [
              'status' => 200,
              'data' => $row,
              'message' => 'Record Found'
          ];
      } else {
          return [
              'status' => 404,
              'message' => 'No Data Found'
          ];
      }
  } else {
      return [
          'status' => 500,
          'message' => 'Something Went Wrong: ' . mysqli_error($conn)
      ];
  }
}

//Delete data from database
function delete($tableName, $id){

  global $conn;
  
  $table = validate($tableName);
  $status = validate($id);

  $query = "DELETE FROM $table WHERE id='$id' LIMIT 1";
  $result = mysqli_query($conn, $query);
  return $result;

}

function checkParamId($type){

  if(isset($_GET[$type])){

    if($_GET[$type] != ''){

      return $_GET[$type];
      
    }else{
      return '<h5>No Id Found</h5>';
    }

  }else{
    return '<h5>No Id Given</h5>';
  }
}


function logoutSession(){
  unset($_SESSION['loggedIn']);
  unset($_SESSION['loggedInUser']);
}

function jsonResponse($status, $status_type, $message){

    $response = [
      'status' => $status,
      'status_type' => $status_type,
      'message' => $message
    ];
    echo json_encode($response);
    return;
}

function getCount($tableName){
  global $conn;

  $table = validate($tableName);

  $query = "SELECT * FROM $table";
  $query_run = mysqli_query($conn, $query);
  
  if($query_run){

    $totalCount = mysqli_num_rows($query_run);
    return $totalCount;

  }else{
    return 'Something Went Wrong!';
  }
}

function getByColumn($table, $column, $value) {
  global $conn;

  // Escape inputs to prevent SQL injection
  $table = mysqli_real_escape_string($conn, $table);
  $column = mysqli_real_escape_string($conn, $column);
  $value = mysqli_real_escape_string($conn, $value);

  // Build the query
  $query = "SELECT * FROM $table WHERE $column='$value'";
  $result = mysqli_query($conn, $query);

  if ($result) {
      // Fetch all rows as an associative array
      $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
      return [
          'status' => 200,
          'data' => $data
      ];
  } else {
      return [
          'status' => 500,
          'message' => 'Something Went Wrong.'
      ];
  }
}


?>