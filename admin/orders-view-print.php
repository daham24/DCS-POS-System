<?php include('includes/header.php');?>

<div class="container-fluid px-4 mb-4">
    
  <div class="card mt-4 shadow-sm">
      <div class="card-header">
        <h4 class="mb-0">Print Order
          <a href="orders.php" class="btn btn-danger btn-sm float-end">Back</a>
        </h4>
      </div>
      <div class="card-body">
        <div id="myBillingArea">
          <?php
            if(isset($_GET['track']))
            {

              $trackingNo = validate($_GET['track']);
              if($trackingNo == ''){
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
              if(!$orderQueryRes)
              {
                echo '<h5>Something Went Wrong!</h5>';
                return false;
              }

              if(mysqli_num_rows($orderQueryRes) > 0)
              {
                $orderDataRow = mysqli_fetch_assoc($orderQueryRes);
                ?>
                  <table style="width: 100%;   border-collapse: collapse;">
                      <tbody>
                        <!-- Header Row -->
                        <tr>
                          <td style="text-align: center;" colspan="2">
                            <span><img src="../assets/img/png.png" alt="dcs-logo" style="width: auto; height: 50px;"></span>
                            <h4 style="font-size: 18px; line-height: 28px; margin: 2px;">Dimuthu Cellular Service</h4>
                            <p style="font-size: 14px; line-height: 22px; margin: 2px;">319/1A, Urubokka Road, Heegoda.</p>
                            <p style="font-size: 14px; line-height: 22px; margin: 2px;">070 691 7666 | 077 791 7666 | 070 391 7666</p>
                            <p style="font-size: 14px; line-height: 22px; margin: 2px;">www.dcs.lk | info@dcs.lk</p>
                          </td>
                        </tr>

                        <!-- Customer and Invoice Details Row -->
                        <tr>
                          <!-- Customer Details -->
                          <td>
                            <h5 style="font-size: 14px; line-height: 28px; margin: 0;">Customer Details</h5>
                            <p style="font-size: 12px; line-height: 18px; margin: 0;">Customer Name: <?= $orderDataRow['name'] ?></p>
                            <p style="font-size: 12px; line-height: 18px; margin: 0;">Customer Phone No.: <?= $orderDataRow['phone'] ?></p>
                            <p style="font-size: 12px; line-height: 18px; margin: 0;">Customer Email ID: <?= $orderDataRow['email'] ?></p>
                          </td>

                          <!-- Invoice Details -->
                          <td style="text-align: right;">
                            <h5 style="font-size: 14px; line-height: 28px; margin: 0;">Invoice Details</h5>
                            <p style="font-size: 12px; line-height: 18px; margin: 0;">Invoice No.: <?= $orderDataRow['invoice_no']; ?></p>
                            <p style="font-size: 12px; line-height: 18px; margin: 0;">Invoice Date: <?= date('d M Y') ?></p>
                            <br>
                            <!-- <p style="font-size: 14px; line-height: 20px; margin: 0;">Address: 1st Main Road, Bangalore, India</p> -->
                          </td>
                        </tr>
                      </tbody>
                  </table>
                <?php

              }else
              {
                echo '<h5>No Data Found</h5>';
                return false;
              }

              $orderItemQuery = "SELECT oi.quantity as orderItemQuantity, oi.price as orderItemPrice, o.*, oi.*, p.* FROM orders o, order_items oi, products p
                                WHERE oi.order_id = o.id AND p.id = oi.product_id AND o.tracking_no = '$trackingNo' ";

              $orderItemQueryRes = mysqli_query($conn, $orderItemQuery);  
              if($orderItemQueryRes)
              {
                if(mysqli_num_rows($orderItemQueryRes) > 0)
                {
                  ?>
                    <table style="width:100%;" cellpadding="5">
                      <thead>
                        <tr>
                          <th align="start" style="border-bottom: 1px solid #ccc; font-size: 14px;" width="5%">ID</th>
                          <th align="start" style="border-bottom: 1px solid #ccc; font-size: 14px;">Product Name</th>
                          <th align="start" style="border-bottom: 1px solid #ccc; font-size: 14px;" width="10%">Price</th>
                          <th align="start" style="border-bottom: 1px solid #ccc; font-size: 14px;" width="10%">Quantity</th>
                          <th align="start" style="border-bottom: 1px solid #ccc; font-size: 14px;" width="15%">Total Price</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $i = 1;
                        foreach($orderItemQueryRes as $key => $row) {
                        
                        ?>
                        <tr>
                          <td style="border-bottom: 1px solid #ccc; font-size: 14px;"><?= $i++; ?></td>
                          <td style="border-bottom: 1px solid #ccc; font-size: 14px;"><?= $row['name']; ?></td>
                          <td style="border-bottom: 1px solid #ccc; font-size: 14px;"><?= number_format($row['orderItemPrice'], 0); ?></td>
                          <td style="border-bottom: 1px solid #ccc; font-size: 14px;"><?= $row['orderItemQuantity']; ?></td>
                          <td style="border-bottom: 1px solid #ccc; font-weight: bold; font-size: 14px;">
                            <?= number_format($row['orderItemPrice'] * $row['orderItemQuantity'], 0); ?>
                          </td>
                        </tr>
                        <?php } ?>
                        <tr>
                          <td colspan="4" align="end" style="font-weight: bold; font-size: 14px;">Grand Total:</td>
                          <td colspan="1" style="font-weight: bold; font-size: 14px;"><?= number_format($row['total_amount'], 0); ?></td>
                        </tr>
                        <tr>
                          <td colspan="5" style="font-size: 14px;">Payment Mode: <?= $row['payment_mode']; ?></td>
                        </tr>
                      </tbody>
                      <tfoot>
                         <!-- Terms and Conditions -->
                        <tr>
                            <td colspan="5" style="padding-top: 20px;">
                              <h5 style="font-size: 12px; margin-bottom: 5px;">Terms and Conditions</h5>
                              <p style="font-size: 8px; line-height: 16px; margin: 0;">1. භාණ්ඩය විකුණුමෙන් පසු ආපසු ගත හෝ මාරු කළ නොහැක.</p>
                              <p style="font-size: 8px; line-height: 16px; margin: 0;">2. වගකීම නිෂ්පාදන දෝෂයන්ට පමණක් අදාළ වේ. එය අධික වෝල්ටීයතාව, දියර දෝෂ, වැටීමෙන් ඇතිවූ හානි, හෝ නිල මුද්‍රාව දැක්වීමෙන් හෝ ඉවත් කිරීමෙන් ඇතිවූ හානි ආවරණය කරන්නේ නැත.</p>
                              <p style="font-size: 8px; line-height: 16px; margin: 0;">3. වගකීමක් ඇති ජංගම දුරකථනයක ගැටළුවක් ඇති විට, නව ජංගම දුරකථනයක් ලබාදීමට වහාම සලස්වනු නොලැබේ.</p>
                            </td>
                        </tr>
                      </tfoot>
                    </table>

                   

                    <!-- Signatures -->
                    <div style="margin-top: 50px; display: flex; justify-content: space-between;">
                        <div style="text-align: center; font-size: 12px; line-height: 12px; margin: 0;">
                            <p>_________________________</p>
                            <p>Customer Signature</p>
                        </div>
                        <div style="text-align: center; font-size: 12px; line-height: 12px; margin: 0;">
                            <p>_________________________</p>
                            <p>Authorized Signature</p>
                        </div>
                    </div>
                  <?php
                }
              }else
              {
                echo '<h5>Something Went Wrong!</h5>';
                return false;
              }           


            }else{
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
          <button class="btn btn-info px-4 mx-1" onclick="printMyBillingArea()">Print</button>
          <button class="btn btn-primary px-4 mx-1" onclick="downloadPDF('<?= $orderDataRow['invoice_no']; ?>')">Download PDF</button>
        </div>
      </div>
  </div>
  
</div>

<?php include('includes/footer.php');?>