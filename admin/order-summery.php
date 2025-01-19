<?php 

include('includes/header.php');



// Redirect if 'productItems' session is not set
if (!isset($_SESSION['productItems'])) {
    echo '<script>window.location.href = "order-create.php"</script>';
    exit;
}

?>


<div class="modal fade" id="orderSuccessModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">

        <div class="mb-3 p-4">
          <h5 id="orderPlaceSuccessMessage"></h5>
        </div>

        <a href="orders.php" class="btn btn-secondary">Close</a>
        <button type="button" class="btn btn-danger" onclick="printMyBillingArea()">Print</button>
        <button type="button" class="btn btn-warning" onclick="downloadPDF('<?= $_SESSION['invoice_no']; ?>')">Download PDF</button>
      </div>
    </div>
  </div>
</div>



<div class="container-fluid px-4 mb-4" >
  <div class="row">
    <div class="col-md-12">
      <div class="card mt-4">
        <div class="card-header">
          <h4 class="mb-0">Order Summary
            <a href="order-create.php" class="btn btn-sm btn-primary float-end">Back to create order</a>
          </h4>
        </div>
        <div class="card-body">

            <?php alertMessage(); ?>

            <div id="myBillingArea">

                <?php 
                // Check if customer phone is set
                if (isset($_SESSION['cphone']) && isset($_SESSION['invoice_no'])) {
                  $phone = validate($_SESSION['cphone']);
                  $invoiceNo = validate($_SESSION['invoice_no']);
              
                  // Fetch customer details
                  $customerQuery = mysqli_query($conn, "SELECT * FROM customers WHERE phone='$phone' LIMIT 1");
                  if ($customerQuery) {
                      if (mysqli_num_rows($customerQuery) > 0) {
                          $cRowData = mysqli_fetch_assoc($customerQuery);
                          ?>

                          <!-- Header Section -->
                          <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ccc; padding: 10px;">
                            <div>
                              <h2 style="margin: 0; font-size: 24px;">INVOICE</h2>
                            </div>
                            <div>
                              <img src="../assets/img/png.png" alt="Logo" style="width: auto; height: 60px;">
                            </div>
                          </div>



                          <table style="width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px;">
                              <tbody>
              
                                <!-- Customer and Invoice Details Row -->
                                <tr>
                                  <!-- Customer Details -->
                                  <td style="width: 50%; padding: 10px; vertical-align: top; ">
                                    <h5 style="font-size: 12px; margin: 0;">Customer Details</h5>
                                    <p style="margin: 5px 0; font-size: 10px; line-height: 1.5;"><strong>Name:</strong> <?= $cRowData['name'] ?></p>
                                    <p style="margin: 5px 0; font-size: 10px; line-height: 1.5;"><strong>Phone:</strong> <?= $cRowData['phone'] ?></p>
                                    <p style="margin: 5px 0; font-size: 10px; line-height: 1.5;"><strong>Email:</strong> <?= $cRowData['email'] ?></p>
                                  </td>
              
                                  <!-- Invoice Details -->
                                  <td style="width: 50%; padding: 10px; vertical-align: top; text-align: right; ">
                                    <h5 style="font-size: 12px; margin: 0;">Invoice Details</h5>
                                    <p style="margin: 5px 0; font-size: 10px; line-height: 1.5;"><strong>Invoice No:</strong> <?= $invoiceNo ?></p>
                                    <p style="margin: 5px 0; font-size: 10px; line-height: 1.5;"><strong>Date:</strong> <?= date('d M Y') ?></p>
                                    <p style="margin: 5px 0; font-size: 10px; line-height: 1.5;"><strong>IMEI:</strong> <?= isset($_SESSION['imei_code']) ? $_SESSION['imei_code'] : 'Not Provided'; ?></p>
                                    <p style="margin: 5px 0; font-size: 10px; line-height: 1.5;"><strong>Warranty:</strong> <?= isset($_SESSION['warrenty_period']) ? $_SESSION['warrenty_period'] : 'Not Provided'; ?></p>
                                  </td>
                                </tr>
                              </tbody>
                          </table>
                          <?php
                      } else {
                          echo '<h5>No Customer Found</h5>';
                          return;
                      }
                  } else {
                      echo '<h5>Query Failed: ' . mysqli_error($conn) . '</h5>';
                      return;
                  }
              } else {
                  echo '<h5>Session values missing. Please create an order first.</h5>';
                  return;
              }
              ?>
              
              <?php 
              if (isset($_SESSION['productItems'])) {
                  $sessionProducts = $_SESSION['productItems'];
                  ?>
              
                  <div class="table-responsive mb-3">
                      <table style="width: 100%; margin-top: 10px; border-collapse: collapse; font-size: 12px;">
                          <thead>
                              <tr style="text-align: left;">
                                  <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;" width="5%">ID</th>
                                  <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">Product Name</th>
                                  <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;" >Price</th>
                                  <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;" >Discount</th>
                                  <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;" >Quantity</th>
                                  <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;" >Total</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php 
                              $i = 1;
                              $totalAmount = 0;
                              $grandTotal = 0;
                              foreach ($sessionProducts as $item) {
                                $productId = $item['product_id'];

                                // Fetch product discount from the database
                                $productQuery = "SELECT discount FROM products WHERE id='$productId' LIMIT 1";
                                $productResult = mysqli_query($conn, $productQuery);

                                if ($productResult && mysqli_num_rows($productResult) > 0) {
                                    $productData = mysqli_fetch_assoc($productResult);
                                    $discount = $productData['discount'];
                                } else {
                                    $discount = 0; // Default discount if not found
                                }
                                // Calculate discounted price and total
                                $discountedPrice = 
                                $totalPrice = ($item['price'] - $discount) * $item['quantity'];
                                $grandTotal += $totalPrice;
                              ?>
                              <tr>
                                  <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= $i++; ?></td>
                                  <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= $item['name']; ?></td>
                                  <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= number_format($item['price'], 2); ?></td>
                                  <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= number_format($discount, 2); ?></td>
                                  <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= $item['quantity']; ?></td>
                                  <td style="border-bottom: 1px solid #ccc; padding: 5px;">
                                      <?= number_format($totalPrice, 2); ?>
                                  </td>
                              </tr>
                              <?php } ?>
                              <tr>
                                  <td colspan="5" align="end" style="text-align: right; padding: 5px; font-weight: bold;">Grand Total:</td>
                                  <td colspan="1" style="font-size:18px; color:#e55300; padding: 5px; font-weight: bold; "> <?= number_format($grandTotal, 2); ?></td>
                              </tr>
                              <tr>
                                  <td colspan="6" style="font-size: 12px;">Payment Mode: <?= $_SESSION['payment_mode']; ?></td>
                              </tr>
                          </tbody>
                          <tfoot>
                        </table>
                    <?php

                  }else{
                    echo '<h5 class="text-center">No Item Added</h5>';
                  }
                ?>
              
            </div>

            <?php if(isset($_SESSION['productItems'])) :  ?>
            <div class="mt-4 text-end">
              <button type="button" class="btn btn-primary px-4 mx-1" id="saveOrder">Save</button>
              <button class="btn btn-info px-4 mx-1" onclick="printMyBillingArea()">Print</button>
              <button class="btn btn-warning px-4 mx-1" onclick="downloadPDF('<?= $_SESSION['invoice_no']; ?>')">Download PDF</button>
            </div>
            <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>