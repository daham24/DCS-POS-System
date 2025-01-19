<?php include('includes/header.php'); ?>


<div class="container-fluid px-4 mb-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Print Repair
                <a href="repairs.php" class="btn btn-danger btn-sm float-end">Back</a>
            </h4>
        </div>
        <div class="card-body">
            <div id="myBillingArea" style="font-family: Helvetica, sans-serif;">
                <?php
                if (isset($_GET['id'])) {
                    $repairId = intval($_GET['id']); // Sanitize input

                    if ($repairId == 0) {
                        echo "<div class='alert alert-danger'>Invalid Repair ID.</div>";
                        exit;
                    }

                    // Fetch repair details
                    $repairQuery = "
                        SELECT r.*, c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone
                        FROM repairs r
                        LEFT JOIN customers c ON r.customer_id = c.id
                        WHERE r.id = $repairId
                    ";
                    $repairResult = mysqli_query($conn, $repairQuery);

                    if ($repairResult && mysqli_num_rows($repairResult) > 0) {
                        $repairData = mysqli_fetch_assoc($repairResult);

                        // Fetch invoice details
                        $orderQuery = "
                            SELECT invoice_number, repair_cost, advanced_payment
                            FROM repair_orders
                            WHERE repair_id = $repairId
                        ";
                        $orderResult = mysqli_query($conn, $orderQuery);

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

                        <table style="width: 100%; margin-bottom: 20px; margin-top: 10px; border-collapse: collapse;">
                            
                            <!-- Customer and Repair Details -->
                            <tr>
                                <td style="width: 50%; padding: 10px; vertical-align: top; ">
                                    <h5 style="font-size: 12px; margin-bottom: 5px;">Customer Details</h5>
                                    <p style="font-size: 10px; line-height: 1.5; margin:0;"><strong>Name: </strong> <?= htmlspecialchars($repairData['customer_name']); ?></p>
                                    <p style="font-size: 10px; line-height: 1.5; margin:0;"><strong>Phone: </strong> <?= htmlspecialchars($repairData['customer_phone']); ?></p>
                                    <p style="font-size: 10px; line-height: 1.5; margin:0;"><strong>Email: </strong> <?= htmlspecialchars($repairData['customer_email']); ?></p>
                                </td>
                                <td style="width: 50%; padding: 10px; vertical-align: top; text-align: right; ">
                                    <h5 style="font-size: 12px; margin-bottom: 5px;">Repair Details</h5>
                                    <p style="font-size: 10px; line-height: 1.5; margin:0;"><strong>Repair ID: </strong> <?= htmlspecialchars($repairData['id']); ?></p>
                                    <p style="font-size: 10px; line-height: 1.5; margin:0;"><strong>Repair Date: </strong> <?= date('d M Y', strtotime($repairData['created_at'])); ?></p>
                                    <p style="font-size: 10px; line-height: 1.5; margin:0;"><strong>Description: </strong> <?= htmlspecialchars($repairData['description']); ?></p>
                                </td>
                            </tr>
                        </table>

                        <!-- Combined Repair and Invoice Details -->
                        <h5 style="font-size: 14px; line-height: 30px; margin-bottom: 5px; margin-top: 20px;">Repair and Invoice Details</h5>
                        <table style="width: 100%; margin-top: 10px; border-collapse: collapse; font-size: 12px;">
                            <thead>
                                <tr>
                                    <th align="start" style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;" width="15%">Invoice Number</th>
                                    <th align="start" style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;" width="30%">Item Name</th>
                                    <th align="start" style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">Physical Condition</th>
                                    <th align="start" style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">Received Items</th>
                                    <th align="start" style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">Advanced Payment </th>
                                    <th align="start" style="background-color:#e9ecef; border-bottom: 1px solid #e9ecef; padding: 5px;">Repair Cost </th>
                                    
                                    
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $grandTotal = 0;
                                $totalAdvanced = 0; // Initialize totalAdvanced to 0
                                if ($orderResult && mysqli_num_rows($orderResult) > 0) {
                                    while ($order = mysqli_fetch_assoc($orderResult)) {
                                        $repairCost = floatval($order['repair_cost'] ?? 0); // Ensure numeric value
                                        $advancedPayment = floatval($order['advanced_payment'] ?? 0); // Ensure numeric value
                                        $grandTotal += $repairCost; // Increment grand total
                                        $totalAdvanced += $advancedPayment; // Increment total advanced payment
                                        ?>
                                        <tr>
                                            <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= htmlspecialchars($order['invoice_number']); ?></td>
                                            <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= htmlspecialchars($repairData['item_name']); ?></td>
                                            <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= htmlspecialchars($repairData['physical_condition']); ?></td>
                                            <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= htmlspecialchars($repairData['received_items']); ?></td>
                                            <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= number_format($advancedPayment, 2); ?></td>
                                            <td style="border-bottom: 1px solid #ccc; padding: 5px;"><?= number_format($repairCost, 2); ?></td>
                                           
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center;">No invoice details found.</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <td colspan="5" align="end" style="text-align: right; padding:5px;  font-weight: normal;">Grand Total: </td>
                                    <td colspan="2" style="font-weight: normal;"><?= number_format($grandTotal, 2); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="5" align="end" style="text-align: right; padding:5px;  font-weight: normal;">Total Advanced Payment: </td>
                                    <td colspan="2" style="font-weight: normal;"><?= number_format($totalAdvanced, 2); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="5" align="end" style="text-align: right; padding:5px; font-weight: bold;">Outstanding Amount: </td>
                                    <td colspan="2" style="font-size:18px; color:#e55300;  font-weight: bold; "><?= number_format($grandTotal - $totalAdvanced, 2); ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Terms and Conditions -->
                        <tr>
                            <td colspan="5" style="padding-top: 20px;">
                              <h5 style="font-size: 12px; margin-bottom: 5px; font-weight: bold;">Terms and Conditions</h5>
                              <p style="font-size: 10px; line-height: 16px; margin: 0;">1. භාණ්ඩය විකුණුමෙන් පසු ආපසු ගත හෝ මාරු කළ නොහැක.</p>
                              <p style="font-size: 10px; line-height: 16px; margin: 0;">2. වගකීම නිෂ්පාදන දෝෂයන්ට පමණක් අදාළ වේ. එය අධික වෝල්ටීයතාව, දියර දෝෂ, වැටීමෙන් ඇතිවූ හානි, හෝ නිල මුද්‍රාව දැක්වීමෙන් හෝ ඉවත් කිරීමෙන් ඇතිවූ හානි ආවරණය කරන්නේ නැත.</p>
                              <p style="font-size: 10px; line-height: 16px; margin: 0;">3. වගකීමක් ඇති ජංගම දුරකථනයක ගැටළුවක් ඇති විට, නව ජංගම දුරකථනයක් ලබාදීමට වහාම සලස්වනු නොලැබේ.</p>
                            </td>
                        </tr>

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
                        <div style="background-color: #333; color: #fff; padding:10px 15px; margin-top: 50px; font-size: 10px;">
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
                    } else {
                        echo "<div class='alert alert-danger'>No repair details found.</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>No Repair ID provided.</div>";
                }
                ?>
            </div>

            <div class="mt-4 text-end">
                <button class="btn btn-info px-4 mx-1" onclick="printMyBillingArea()">Print</button>
                <button class="btn btn-primary px-4 mx-1" onclick="downloadPDF('<?= $repairData['id']; ?>')">Download PDF</button>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>