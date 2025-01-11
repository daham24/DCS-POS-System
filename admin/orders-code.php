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


if(isset($_POST['proceedToPlaceBtn'])){
    $phone = validate($_POST['cphone']);
    $payment_mode = validate($_POST['payment_mode']);

    //checking for Customer
    $checkingCustomer = mysqli_query($conn, "SELECT * FROM customers WHERE phone='$phone' LIMIT 1");

    if($checkingCustomer){
        if(mysqli_num_rows($checkingCustomer) > 0){

            $_SESSION['invoice_no'] = "INV-".rand(111111,999999);
            $_SESSION['cphone'] = $phone;
            $_SESSION['payment_mode'] = $payment_mode;
            jsonResponse(200, 'success', 'Customer Found');

        }
        else
        {
            $_SESSION['cphone'] = $phone;
            jsonResponse(404, 'warining', 'Customer Not Found!');
        }
    }else{
        jsonResponse(500, 'error', 'Something Went Wrong');
    }
    
}

if(isset($_POST['saveCustomerBtn']))
{
    $name = validate($_POST['name']);
    $phone = validate($_POST['phone']);
    $email = validate($_POST['email']);

    if($name != '' && $phone != ''){

        $data = [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
        ];
        $result = insert('customers', $data);

        if ($result) {
            jsonResponse(200, 'success', 'Customer Created Successfully');
        }else{
            jsonResponse(500, 'error', 'Something Went Wrong');
        }


    }else{
        jsonResponse(422, 'warning', 'Please fill required fields');
    }
}

?>