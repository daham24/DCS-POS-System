<?php include('includes/header.php');?>

<?php
// Set the trial period expiry date
$expiryDate = '2026-03-03'; // Update this date as needed
$currentDate = date('Y-m-d');

if ($currentDate > $expiryDate) {
    // Redirect to an expired page or stop the application
    echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Trial Expired',
                text: 'Your trial period has ended. Please contact support to activate the application.',
                footer: '<a href=\"mailto:support@yourdomain.com\">Contact Support</a>',
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'Close'
            }).then(() => {
                window.location.href = '../index.php'; // Optional: Redirect to your website or info page
            });
        </script>
    ";
    exit; // Stop further execution
}
?>

<div class="container-fluid px-4">

    <div class="row">
        <!-- Dashboard Title -->
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h1 class="mt-4 fw-bold">Dashboard</h1>
            <!-- Live Clock -->
            <div id="live-clock" style="font-size: 18px; font-weight: bold; color: #333; margin-top: 10px;">
                <!-- JavaScript will update this -->
            </div>
        </div>
        
        <?php alertMessage(); ?>
    </div>

    <!-- Dashboard Cards -->
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card card-body bg-dark bg-gradient text-white p-3">
                <p class="text-sm mb-0 text-capitalize"><i class="fa-solid fa-layer-group"></i> Total Category</p>
                <h5 class="fw-bold mb-0">
                    <?= getCount('categories'); ?>
                </h5>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card card-body bg-dark bg-gradient text-white p-3">
                <p class="text-sm mb-0 text-capitalize"><i class="fa-solid fa-list-check"></i> Total Products</p>
                <h5 class="fw-bold mb-0">
                    <?= getCount('products'); ?>
                </h5>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card card-body bg-dark bg-gradient text-white p-3">
                <p class="text-sm mb-0 text-capitalize"><i class="fa-solid fa-user-tie"></i> Total Admins</p>
                <h5 class="fw-bold mb-0">
                    <?= getCount('admins'); ?>
                </h5>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card card-body bg-dark bg-gradient text-white p-3">
                <p class="text-sm mb-0 text-capitalize"><i class="fa-solid fa-users"></i> Total Customers</p>
                <h5 class="fw-bold mb-0">
                    <?= getCount('customers'); ?>
                </h5>
            </div>
        </div>

        <div class="col-md-12 mb-3">
            <hr>
            <h5>Orders</h5>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card card-body bg-secondary bg-gradient text-white p-3">
                <p class="text-sm mb-0 text-capitalize"><i class="fa-solid fa-calendar-week"></i></i> Today Orders</p>
                <h5 class="fw-bold mb-0">
                   <?php 
                        $todayDate = date('Y-m-d');
                        $todayOrders = mysqli_query($conn, "SELECT * FROM orders WHERE order_date='$todayDate'");

                        if($todayOrders){
                            if(mysqli_num_rows($todayOrders) > 0){
                                $totalCountOrders = mysqli_num_rows($todayOrders);
                                echo $totalCountOrders;
                            }else{
                                echo "0";
                            }
                        }else{
                            echo 'Something Went Wrong!';
                        }
                   ?>
                </h5>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card card-body bg-secondary bg-gradient text-white p-3">
                <p class="text-sm mb-0 text-capitalize"><i class="fas fa-list"></i> Total Orders</p>
                <h5 class="fw-bold mb-0">
                    <?= getCount('orders'); ?>
                </h5>
            </div>
        </div>

        <!-- New Card for Pending Repair Items -->
        <div class="col-md-3 mb-3">
            <div class="card card-body bg-secondary bg-gradient text-white p-3">
                <p class="text-sm mb-0 text-capitalize"><i class="fa-solid fa-clock"></i> Pending Repair Items</p>
                <h5 class="fw-bold mb-0">
                    <?php
                        $pendingRepairs = mysqli_query($conn, "SELECT * FROM repairs WHERE status = 0"); // Assuming 0 = Pending

                        if ($pendingRepairs) {
                            echo mysqli_num_rows($pendingRepairs);
                        } else {
                            echo "0";
                        }
                    ?>
                </h5>
            </div>
        </div>

        <!-- New Card for Total Repairs -->
        <div class="col-md-3 mb-3">
            <div class="card card-body bg-secondary bg-gradient text-white p-3">
                <p class="text-sm mb-0 text-capitalize"><i class="fas fa-tools"></i> Total Repairs</p>
                <h5 class="fw-bold mb-0">
                    <?= getCount('repairs'); ?>
                </h5>
            </div>
        </div>
    </div>

    <?php
        $queryUnavailableItems = "
            SELECT p.id, p.name AS product_name, c.name AS category_name, p.price 
            FROM products p 
            INNER JOIN categories c ON p.category_id = c.id 
            WHERE p.quantity = 0";
        $resultUnavailableItems = mysqli_query($conn, $queryUnavailableItems);
    ?>
    <?php if ($resultUnavailableItems && mysqli_num_rows($resultUnavailableItems) > 0): ?>
        <div class="card mt-4">
        <div class="card-header">
            <h4>Unavailable Items</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($resultUnavailableItems)): ?>
                    <tr>
                    <td><?= $row['product_name']; ?></td>
                    <td><?= $row['category_name']; ?></td>
                    <td><?= number_format($row['price'], 2); ?></td>
                    <td><span class="badge bg-danger">Not Available</span></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        </div>
        </div>
    <?php endif; ?>

    <!-- Welcome Section -->
    <div class="row">
        <div class="col-md-12">
            <hr>
            <h1 class="fw-bold welome-tag">Welcome <?= $_SESSION['loggedInUser']['name']; ?> !</h1>
            <h3 class="fw-lighter">Have A Nice Day.</h3>
            <div class="image-container">
                <img
                    loading="lazy"
                    src="../assets/img/mainBanner.jpg"
                    alt="Dimuthu Cellular Service welcome image"
                    class="img-fluid"
                />
            </div>
        </div>
    </div>
</div>



<!-- JavaScript for Live Clock -->
<script>
  function updateClock() {
    const clockElement = document.getElementById('live-clock');
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    clockElement.innerHTML = `${hours}:${minutes}:${seconds}`;
  }

  // Update clock every second
  setInterval(updateClock, 1000);

  // Initialize clock
  updateClock();
</script>

<?php include('includes/footer.php');?>