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
        <button type="button" class="btn btn-danger" onclick="printMyBillingArea()"><i class="fa-solid fa-print"></i> Print</button>
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
                                  <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">Price</th>
                                  <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">Discount</th>
                                  <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">Warranty</th> <!-- New Column -->
                                  <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">IMEI Code</th> <!-- New Column -->
                                  <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">Quantity</th>
                                  <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">Total</th>
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
                                  <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= $item['warranty_period'] ?? 'N/A'; ?></td> <!-- Warranty Period -->
                                  <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= $item['imei_code'] ?? 'N/A'; ?></td> <!-- IMEI Code -->
                                  <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= $item['quantity']; ?></td>
                                  <td style="border-bottom: 1px solid #ccc; padding: 5px;">
                                      <?= number_format($totalPrice, 2); ?>
                                  </td>
                              </tr>
                              <?php } ?>
                              <tr>
                                  <td colspan="7" align="end" style="text-align: right; padding: 5px; font-weight: bold;">Grand Total:</td>
                                  <td colspan="1" style="font-size:18px; color:#e55300; padding: 5px; font-weight: bold; "> <?= number_format($grandTotal, 2); ?></td>
                              </tr>
                              <tr>
                                  <td colspan="6" style="font-size: 12px;">Payment Mode: <?= $_SESSION['payment_mode']; ?></td>
                              </tr>
                              <!-- Terms and Conditions -->
                              <tr>
                                  <td colspan="5" style="padding-top: 20px;">
                                    <h5 style="font-size: 12px; margin-bottom: 5px;">Terms and Conditions</h5>
                                    <p style="font-size: 10px; line-height: 16px; margin: 0;">1. භාණ්ඩය විකුණුමෙන් පසු ආපසු ගත හෝ මාරු කළ නොහැක.</p>
                                    <p style="font-size: 10px; line-height: 16px; margin: 0;">2. වගකීම නිෂ්පාදන දෝෂයන්ට පමණක් අදාළ වේ. එය අධික වෝල්ටීයතාව, දියර දෝෂ, වැටීමෙන් ඇතිවූ හානි, හෝ නිල මුද්‍රාව දැක්වීමෙන් හෝ ඉවත් කිරීමෙන් ඇතිවූ හානි ආවරණය කරන්නේ නැත.</p>
                                    <p style="font-size: 10px; line-height: 16px; margin: 0;">3. වගකීමක් ඇති ජංගම දුරකථනයක ගැටළුවක් ඇති විට, නව ජංගම දුරකථනයක් ලබාදීමට වහාම සලස්වනු නොලැබේ.</p>
                                  </td>
                              </tr>
                          </tbody>
                          <tfoot>
                        </table>
                        <!-- Signatures Section -->
                        <div style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center;">
                          <!-- Customer Signature -->
                          <div style="text-align: center; font-size: 12px; line-height: 1.5; margin: 0;">
                            <p style="margin: 0; font-weight: bold;">_________________________</p>
                            <p style="margin: 5px 0 0; font-size: 10px;">Customer Signature</p>
                          </div>
                          <!-- Authorized Signature -->
                          <div style="text-align: center; font-size: 12px; line-height: 1.5; margin: 0;">
                            <p style="margin: 0; font-weight: bold;">_________________________</p>
                            <p style="margin: 5px 0 0; font-size: 10px;">Authorized Signature</p>
                          </div>
                        </div>

                        <!-- Footer Section -->
                        <div style="background-color: #0077b6; color: #fff; padding:10px 15px; margin-top: 50px; font-size: 10px;">
                          <div style="display: flex; justify-content: space-between; align-items: center;">
                            <!-- Web and Email -->
                            <div style="flex: 1;">
                              <p style="margin: 0; font-size: 12px; font-weight: bold;">Web & Email</p>
                              <p style="margin: 0; font-size: 10px;">www.dcs.lk</p>
                              <p style="margin: 0; font-size: 10px;">info@dcs.lk</p>
                            </div>
                            <!-- Address -->
                            <div style="flex: 1;">
                              <p style="margin: 0; font-size: 12px; font-weight: bold;">Address</p>
                              <p style="margin: 0; font-size: 10px;">319/A, Urubokka Road</p>
                              <p style="margin: 0; font-size: 10px;">Heegoda.</p>
                            </div>
                            <!-- Contact -->
                            <div style="flex: 1;">
                              <p style="margin: 0; font-size: 12px; font-weight: bold;">Contact</p>
                              <p style="margin: 0; font-size: 10px;">070 691 7666</p>
                              <p style="margin: 0; font-size: 10px;">077 791 7666</p>
                              <p style="margin: 0; font-size: 10px;">070 391 7666</p>
                            </div>
                            <!-- QR Code -->
                            <div style="flex: 0.5; text-align: right;">
                              <img src="../assets/img/qr-code.jpeg" alt="QR Code" style="width: 50px; height: auto;  padding: 5px; border-radius: 5px;">
                            </div>
                          </div>
                        </div>
                    <?php

                  }else{
                    echo '<h5 class="text-center">No Item Added</h5>';
                  }
                ?>
              
            </div>
        </div>
      </div>
      <?php if(isset($_SESSION['productItems'])) :  ?>
      <div class="mt-4 text-end mb-4 ">
        <button type="button" class="btn btn-primary px-4 mx-1" id="saveOrder">Save</button>
        <button class="btn btn-info px-4 mx-1" onclick="printMyBillingArea()"><i class="fa-solid fa-print"></i> Print</button>
        <button class="btn btn-warning px-4 mx-1" onclick="downloadPDF('<?= $_SESSION['invoice_no']; ?>')">Download PDF</button>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>