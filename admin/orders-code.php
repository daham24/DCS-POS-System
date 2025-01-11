<?php 

include('../config/function.php');


if (isset($_POST['addItem'])) {
    $productId = validate($_POST['product_id']);
    $quantity = validate($_POST['quantity']);

    $checkProduct = mysqli_query($conn, "SELECT * FROM products WHERE id='$productId' LIMIT 1");

    if ($checkProduct) {
        if (mysqli_num_rows($checkProduct) > 0) {
            $row = mysqli_fetch_assoc($checkProduct);

            if ($row['quantity'] < $quantity) {
                redirect('order-create.php', 'Only ' . $row['quantity'] . ' quantity available!');
            }

            $productData = [
                'product_id' => $row['id'],
                'name' => $row['name'],
                'image' => $row['image'],
                'price' => $row['price'],
                'quantity' => $quantity
            ];

            // Initialize session arrays if they don't exist
            if (!isset($_SESSION['productItemIds'])) {
                $_SESSION['productItemIds'] = [];
            }
            if (!isset($_SESSION['productItems'])) {
                $_SESSION['productItems'] = [];
            }

            // Check if product already exists in session
            if (!in_array($row['id'], $_SESSION['productItemIds'])) {
                // Add new product
                $_SESSION['productItemIds'][] = $row['id'];
                $_SESSION['productItems'][] = $productData;
            } else {
                // Update quantity for existing product
                foreach ($_SESSION['productItems'] as $key => $productSessionItem) {
                    if ($productSessionItem['product_id'] == $row['id']) {
                        $newQuantity = $productSessionItem['quantity'] + $quantity;

                        if ($newQuantity > $row['quantity']) {
                            redirect('order-create.php', 'Only ' . $row['quantity'] . ' quantity available!');
                        }

                        $_SESSION['productItems'][$key]['quantity'] = $newQuantity;
                        break;
                    }
                }
            }

            redirect('order-create.php', 'Item added: ' . $row['name']);
        } else {
            redirect('order-create.php', 'No such product found!');
        }
    } else {
        redirect('order-create.php', 'Something Went Wrong.');
    }
}

if(isset($_POST['productIncDec']))
{
    $productId = validate($_POST['product_id']);
    $quantity = validate($_POST['quantity']);


    $flag = false;
    foreach($_SESSION['productItems'] as $key => $item){

      if($item['product_id'] == $productId){
        
        $flag = true;
        $_SESSION['productItems'][$key]['quantity'] = $quantity;

      }

    }

    if($flag){

      jsonResponse(200, 'success', 'Quantity Updated');

    }else{

      jsonResponse(500, 'error', 'Somethin Went Wrong. Please refresh.');

    }

}

?>