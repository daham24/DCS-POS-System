<?php include('includes/header.php'); ?>

<style>
    @media print {
        /* Set A5 paper size */
        @page {
            size: A5;
            margin: 10mm;
        }

        /* Reduce font sizes */
        body {
            font-size: 10px;
            line-height: 1.2;
        }

        h4, h5 {
            font-size: 12px;
            margin-bottom: 5px;
        }

        p {
            margin: 2px 0;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 4px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Remove unnecessary margins for print */
        .container-fluid {
            margin: 0;
            padding: 0;
        }

        /* Hide buttons and non-printable elements */
        .btn, .no-print {
            display: none;
        }
    }
</style>

<div class="container-fluid px-4 mb-4">
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Print Repair
                <a href="repairs.php" class="btn btn-danger btn-sm float-end">Back</a>
            </h4>
        </div>
        <div class="card-body">
            <div id="myBillingArea">
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
                            SELECT invoice_number, repair_cost
                            FROM repair_orders
                            WHERE repair_id = $repairId
                        ";
                        $orderResult = mysqli_query($conn, $orderQuery);

                        ?>
                        <table style="width: 100%; margin-bottom: 20px; border-collapse: collapse;">
                            <!-- Header Row -->
                            <tr>
                            <td style="text-align: center;" colspan="2">
                                <span><img src="../assets/img/png.png" alt="dcs-logo" style="width: auto; height: 80px;"></span>
                                <h4 style="font-size: 23px; line-height: 30px; margin: 2px;">Dimuthu Cellular Service</h4>
                                <p style="font-size: 16px; line-height: 24px; margin: 2px;">319/1A, Urubokka Road, Heegoda.</p>
                                <p style="font-size: 16px; line-height: 24px; margin: 2px;">070 691 7666 | 077 791 7666 | 070 391 7666</p>
                                <p style="font-size: 16px; line-height: 24px; margin: 2px;">www.dcs.lk | info@dcs.lk</p>
                            </td>
                            </tr>

                            <!-- Customer and Repair Details -->
                            <tr>
                                <td>
                                    <h5 style="font-size: 20px; line-height: 30px; margin: 0;">Customer Details</h5>
                                    <p style="font-size: 14px; line-height: 20px; margin: 0;">Name: <?= htmlspecialchars($repairData['customer_name']); ?></p>
                                    <p style="font-size: 14px; line-height: 20px; margin: 0;">Phone: <?= htmlspecialchars($repairData['customer_phone']); ?></p>
                                    <p style="font-size: 14px; line-height: 20px; margin: 0;">Email: <?= htmlspecialchars($repairData['customer_email']); ?></p>
                                </td>
                                <td style="text-align: right;">
                                    <h5 style="font-size: 20px; line-height: 30px; margin: 0;">Repair Details</h5>
                                    <p style="font-size: 14px; line-height: 20px; margin: 0;">Repair ID: <?= htmlspecialchars($repairData['id']); ?></p>
                                    <p style="font-size: 14px; line-height: 20px; margin: 0;">Repair Date: <?= date('d M Y', strtotime($repairData['created_at'])); ?></p>
                                    <p style="font-size: 14px; line-height: 20px; margin: 0;">Description: <?= htmlspecialchars($repairData['description']); ?></p>
                                </td>
                            </tr>
                        </table>

                        <!-- Combined Repair and Invoice Details -->
                        <h5 style="font-size: 20px; line-height: 30px; margin-bottom: 20px; margin-top: 20px;">Repair and Invoice Details</h5>
                        <table style="width: 100%; border-collapse: collapse;"  cellpadding="5">
                            <thead>
                                <tr>
                                    <th align="start" style="border-bottom: 1px solid #ccc;" width="15%">Invoice Number</th>
                                    <th align="start" style="border-bottom: 1px solid #ccc;" width="30%">Item Name</th>
                                    <th align="start" style="border-bottom: 1px solid #ccc;">Physical Condition</th>
                                    <th align="start" style="border-bottom: 1px solid #ccc;">Received Items</th>
                                    <th align="start" style="border-bottom: 1px solid #ccc;">Repair Cost (Rs.)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $grandTotal = 0;
                                if ($orderResult && mysqli_num_rows($orderResult) > 0) {
                                    while ($order = mysqli_fetch_assoc($orderResult)) {
                                        $grandTotal += $order['repair_cost'];
                                        ?>
                                        <tr>
                                            <td style="border-bottom: 1px solid #ccc;"><?= htmlspecialchars($order['invoice_number']); ?></td>
                                            <td style="border-bottom: 1px solid #ccc;"><?= htmlspecialchars($repairData['item_name']); ?></td>
                                            <td style="border-bottom: 1px solid #ccc;"><?= htmlspecialchars($repairData['physical_condition']); ?></td>
                                            <td style="border-bottom: 1px solid #ccc;"><?= htmlspecialchars($repairData['received_items']); ?></td>
                                            <td style="border-bottom: 1px solid #ccc;"><?= number_format($order['repair_cost'], 2); ?></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center;">No invoice details found.</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <td colspan="4" align="end" style="text-align: right; font-weight: bold;">Grand Total</td>
                                    <td colspan="1" style="font-weight: bold;"><?= number_format($grandTotal, 2); ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Terms and Conditions -->
                        <tr>
                            <td colspan="5" style="padding-top: 20px;">
                              <h5 style="font-size: 20px; margin-bottom: 10px;">Terms and Conditions</h5>
                              <p style="font-size: 14px; line-height: 20px; margin: 0;">1. භාණ්ඩය විකුණුමෙන් පසු ආපසු ගත හෝ මාරු කළ නොහැක.</p>
                              <p style="font-size: 14px; line-height: 20px; margin: 0;">2. වගකීම නිෂ්පාදන දෝෂයන්ට පමණක් අදාළ වේ. එය අධික වෝල්ටීයතාව, දියර දෝෂ, වැටීමෙන් ඇතිවූ හානි, හෝ නිල මුද්‍රාව දැක්වීමෙන් හෝ ඉවත් කිරීමෙන් ඇතිවූ හානි ආවරණය කරන්නේ නැත.</p>
                              <p style="font-size: 14px; line-height: 20px; margin: 0;">3. වගකීමක් ඇති ජංගම දුරකථනයක ගැටළුවක් ඇති විට, නව ජංගම දුරකථනයක් ලබාදීමට වහාම සලස්වනු නොලැබේ.</p>
                            </td>
                        </tr>

                        <!-- Signatures -->
                        <div style="margin-top: 100px; display: flex; justify-content: space-between;">
                            <div style="text-align: center;">
                                <p>_________________________</p>
                                <p>Customer Signature</p>
                            </div>
                            <div style="text-align: center;">
                                <p>_________________________</p>
                                <p>Authorized Signature</p>
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