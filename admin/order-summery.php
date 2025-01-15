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
                            <table style="width: 100%; border-collapse: collapse;">
                                <tbody>
                                  <!-- Header Row -->
                                  <tr>
                                    <td style="text-align: center;" colspan="2">
                                      <span><img src="../assets/img/png.png" alt="dcs-logo" style="width: auto; height: 80px;"></span>
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
                                      <p style="font-size: 12px; line-height: 18px; margin: 0;">Customer Name: <?= $cRowData['name'] ?></p>
                                      <p style="font-size: 12px; line-height: 18px; margin: 0;">Customer Phone No.: <?= $cRowData['phone'] ?></p>
                                      <p style="font-size: 12px; line-height: 18px; margin: 0;">Customer Email ID: <?= $cRowData['email'] ?></p>
                                    </td>

                                    <!-- Invoice Details -->
                                    <td style="text-align: right;">
                                      <h5 style="font-size: 14px; line-height: 28px; margin: 0;">Invoice Details</h5>
                                      <p style="font-size: 12px; line-height: 18px; margin: 0;">Invoice No.: <?= $invoiceNo ?></p>
                                      <p style="font-size: 12px; line-height: 18px; margin: 0;">Invoice Date: <?= date('d M Y') ?></p>
                                      <br>
                                      <!-- <p style="font-size: 14px; line-height: 20px; margin: 0;">Address: 1st Main Road, Bangalore, India</p> -->
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
                  if(isset($_SESSION['productItems'])){

                    $sessionProducts = $_SESSION['productItems'];

                    ?>

                      <div class="table-responsive mb-3">

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
                            $totalAmount = 0;
                            foreach($sessionProducts as $key => $row) {
                              $totalAmount += $row['price'] * $row['quantity'];
                            ?>
                            <tr>
                              <td style="border-bottom: 1px solid #ccc; font-size: 14px;"><?= $i++; ?></td>
                              <td style="border-bottom: 1px solid #ccc; font-size: 14px;"><?= $row['name']; ?></td>
                              <td style="border-bottom: 1px solid #ccc; font-size: 14px;"><?= number_format($row['price'], 0); ?></td>
                              <td style="border-bottom: 1px solid #ccc; font-size: 14px;"><?= $row['quantity']; ?></td>
                              <td style="border-bottom: 1px solid #ccc; font-weight: bold;">
                                <?= number_format($row['price'] * $row['quantity'], 0); ?>
                              </td>
                            </tr>
                            <?php } ?>
                            <tr>
                              <td colspan="4" align="end" style="font-weight: bold; font-size: 14px;">Grand Total:</td>
                              <td colspan="1" style="font-weight: bold; font-size: 14px;"><?= number_format($totalAmount, 0); ?></td>
                            </tr>
                            <tr>
                              <td colspan="5" style="font-size: 14px;">Payment Mode: <?= $_SESSION['payment_mode']; ?></td>
                            </tr>
                            <tfoot>
                              <tr>
                                  <td colspan="5" style="padding-top: 20px;">
                                    <h5 style="font-size: 12px; margin-bottom: 5px;">Terms and Conditions</h5>
                                    <p tyle="font-size: 8px; line-height: 16px; margin: 0;">1. භාණ්ඩය විකුණුමෙන් පසු ආපසු ගත හෝ මාරු කළ නොහැක.</p>
                                    <p tyle="font-size: 8px; line-height: 16px; margin: 0;">2. වගකීම නිෂ්පාදන දෝෂයන්ට පමණක් අදාළ වේ. එය අධික වෝල්ටීයතාව, දියර දෝෂ, වැටීමෙන් ඇතිවූ හානි, හෝ නිල මුද්‍රාව දැක්වීමෙන් හෝ ඉවත් කිරීමෙන් ඇතිවූ හානි ආවරණය කරන්නේ නැත.</p>
                                    <p tyle="font-size: 8px; line-height: 16px; margin: 0;">3. වගකීමක් ඇති ජංගම දුරකථනයක ගැටළුවක් ඇති විට, නව ජංගම දුරකථනයක් ලබාදීමට වහාම සලස්වනු නොලැබේ.</p>
                                  </td>
                              </tr>
                            </tfoot>
                          </tbody>
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
                      </div>



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