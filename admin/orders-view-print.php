<?php include('includes/header.php');?>

<div class="container-fluid px-4 mb-4">
    
  <div class="card mt-4 shadow-sm">
      <div class="card-header">
        <h4 class="mb-0">Print Order
          <a href="orders.php" class="btn btn-danger btn-sm float-end">Back</a>
        </h4>
      </div>
      <div class="card-body">
      <div id="myBillingArea" style="width: 100%; font-family: Helvetica, sans-serif;">
        <?php
          if (isset($_GET['track'])) {
            $trackingNo = validate($_GET['track']);
            if ($trackingNo == '') {
              ?>
              <div class="text-center py-5">
                <h5>Please Provide Tracking Number</h5>
                <div>
                  <a href="orders.php" class="btn btn-primary mt-4 w-25">Go back to orders</a>
                </div>
              </div>
              <?php
            }

            $orderQuery = "SELECT o.*, c.* FROM orders o, customers c 
                          WHERE c.id=o.customer_id AND tracking_no='$trackingNo' LIMIT 1";
            $orderQueryRes = mysqli_query($conn, $orderQuery);
            if (!$orderQueryRes) {
              echo '<h5>Something Went Wrong!</h5>';
              return false;
            }

            if (mysqli_num_rows($orderQueryRes) > 0) {
              $orderDataRow = mysqli_fetch_assoc($orderQueryRes);
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

              <!-- Customer and Invoice Details -->
              <table style="width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px;">
                <tbody>
                  <tr>
                    <!-- Customer Details -->
                    <td style="width: 50%; padding: 10px; vertical-align: top; ">
                      <h5 style="font-size: 12px; margin: 0;">Customer Details</h5>
                      <p style="margin: 5px 0; font-size: 10px; line-height: 1.5;">
                        <strong>Name:</strong> <?= $orderDataRow['name']; ?><br>
                        <strong>Phone:</strong> <?= $orderDataRow['phone']; ?><br>
                        <strong>Email:</strong> <?= $orderDataRow['email']; ?>
                      </p>
                    </td>
                    <!-- Invoice Details -->
                    <td style="width: 50%; padding: 10px; vertical-align: top; text-align: right; ">
                      <h5 style="font-size: 12px; margin: 0;">Invoice Details</h5>
                      <p style="margin: 5px 0; font-size: 10px; line-height: 1.5;">
                        <strong>Invoice No:</strong> <?= $orderDataRow['invoice_no']; ?><br>
                        <strong>Date:</strong> <?= date('d M Y'); ?>
                      </p>
                    </td>
                  </tr>
                </tbody>
              </table>

              <?php
              $orderItemQuery = "SELECT oi.quantity as orderItemQuantity, oi.price as orderItemPrice, p.discount as orderItemDiscount, 
                                o.*, oi.*, p.*, p.warranty_period, p.imei_code
                                FROM orders o, order_items oi, products p
                                WHERE oi.order_id = o.id AND p.id = oi.product_id AND o.tracking_no = '$trackingNo'";

              $orderItemQueryRes = mysqli_query($conn, $orderItemQuery);  
              if ($orderItemQueryRes && mysqli_num_rows($orderItemQueryRes) > 0) {
                ?>
                <!-- Items Table -->
                <table style="width: 100%; margin-top: 10px; border-collapse: collapse; font-size: 12px;">
                  <thead>
                      <tr style="text-align: left;">
                          <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">ID</th>
                          <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">Product Name</th>
                          <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">Price</th>
                          <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">Discount</th>
                          <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">Warranty</th> 
                          <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">IMEI No.</th> 
                          <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">Quantity</th>
                          <th style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">Total</th>
                      </tr>
                  </thead>
                  <tbody>
                    <?php 
                    $i = 1;
                    $grandTotal = 0; // Initialize grand total
                    foreach ($orderItemQueryRes as $row) {
                      $totalPrice = ($row['orderItemPrice'] - $row['orderItemDiscount']) * $row['orderItemQuantity'];
                      $grandTotal += $totalPrice; // Add to grand total
                      ?>
                      <tr>
                        <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= $i++; ?></td>
                        <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= $row['name']; ?></td>
                        <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= number_format($row['orderItemPrice'], 2); ?></td>
                        <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= number_format($row['orderItemDiscount'], 2); ?></td>
                        <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= $row['warranty_period'] ?? 'N/A'; ?></td> 
                        <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= $row['imei_code'] ?? 'N/A'; ?></td> 
                        <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= $row['orderItemQuantity']; ?></td>
                        <td style="border-bottom: 1px solid #ccc; padding: 5px; font-weight: bold;"><?= number_format($totalPrice, 2); ?></td>
                      </tr>
                      <?php } ?>
                      <tr>
                        <td colspan="7" style="text-align: right; padding: 5px; font-weight: bold;">Grand Total:</td>
                        <td style="font-size:18px; color:#e55300; padding: 5px; font-weight: bold; "><?= number_format($grandTotal, 2); ?></td>
                      </tr>
                      <tr>
                          <td colspan="6" style="font-size: 12px;">Payment Mode: <?= $row['payment_mode']; ?></td>
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
                </table>


                 <!-- Signatures Section -->
                <div style="margin-top: 50px; display: flex; justify-content: space-between; align-items: center;">
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
                <div  style="background-color: #0077b6; color: #fff; padding:10px 15px; margin-top: 80px; font-size: 10px;">
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
              }
            } else {
              echo '<h5>No Data Found</h5>';
            }
          } else {
            ?>
            <div class="text-center py-5">
              <h5>No Tracking Number Parameter Found</h5>
              <div>
                <a href="orders.php" class="btn btn-primary mt-4 w-25">Go back to orders</a>
              </div>
            </div>
            <?php
          }
        ?>
      </div>

        <div class="mt-4 text-end">
          <button class="btn btn-info px-4 mx-1" onclick="printMyBillingArea()"><i class="fa-solid fa-print"></i> Print</button>
          <button class="btn btn-primary px-4 mx-1" onclick="downloadPDF('<?= $orderDataRow['invoice_no']; ?>')">Download PDF</button>
        </div>
      </div>
  </div>
  
</div>

<?php include('includes/footer.php');?>