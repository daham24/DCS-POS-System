<?php
include('../config/dbCon.php'); // Include database connection

if (isset($_POST['barcode'])) {
  $barcode = mysqli_real_escape_string($conn, $_POST['barcode']);

  $query = "SELECT id FROM products WHERE barcode = '$barcode' LIMIT 1";
  $result = mysqli_query($conn, $query);

  if ($result && mysqli_num_rows($result) > 0) {
      $product = mysqli_fetch_assoc($result);
      echo json_encode(['status' => 200, 'product_id' => $product['id']]);
  } else {
      echo json_encode(['status' => 404, 'message' => 'Product not found']);
  }
} else {
  echo json_encode(['status' => 400, 'message' => 'Invalid request']);
}

?>