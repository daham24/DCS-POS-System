<?php include('includes/header.php');?>

<div class="container-fluid px-4">
    
    <div class="row">
        <div class="col-md-12">
            <h1 class="mt-4 fw-bold">Dashboard</h1>
            <?php alertMessage(); ?>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card card-body bg-dark bg-gradient text-white p-3">
                <p class="text-sm mb-0 text-capitalize">Total Category</p>
                <h5 class="fw-bold mb-0">
                    <?= getCount('categories'); ?>
                </h5>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card card-body bg-dark bg-gradient text-white p-3">
                <p class="text-sm mb-0 text-capitalize">Total Products</p>
                <h5 class="fw-bold mb-0">
                    <?= getCount('products'); ?>
                </h5>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card card-body bg-dark bg-gradient text-white p-3">
                <p class="text-sm mb-0 text-capitalize">Total Admins</p>
                <h5 class="fw-bold mb-0">
                    <?= getCount('admins'); ?>
                </h5>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card card-body bg-dark bg-gradient text-white p-3">
                <p class="text-sm mb-0 text-capitalize">Total Customers</p>
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
                <p class="text-sm mb-0 text-capitalize">Today Orders</p>
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
                <p class="text-sm mb-0 text-capitalize">Total Orders</p>
                <h5 class="fw-bold mb-0">
                    <?= getCount('orders'); ?>
                </h5>
            </div>
        </div>

        <!-- New Card for Pending Repair Items -->
        <div class="col-md-3 mb-3">
            <div class="card card-body bg-secondary bg-gradient text-white p-3">
                <p class="text-sm mb-0 text-capitalize">Pending Repair Items</p>
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
                <p class="text-sm mb-0 text-capitalize">Total Repairs</p>
                <h5 class="fw-bold mb-0">
                    <?= getCount('repairs'); ?>
                </h5>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <hr>
            <h1 class="fw-bold text-primary">Welcome <?= $_SESSION['loggedInUser']['name']; ?> !</h1>
            <h3 class="fw-lighter">Have A Nice Day.</h3>
            <div class="image-container">
                <img
                    loading="lazy"
                    src="https://cdn.builder.io/api/v1/image/assets/TEMP/654a26b17a81e6ce82d734845f2c366cfee884002396d68461a460731a7ce5a7?apiKey=2758efc56d724d1aacd00d329c35c80b&"
                    alt="Dimuthu Cellular Service welcome image"
                    class="img-fluid"
                />
            </div>
        </div>
    </div>
                       
</div>

<?php include('includes/footer.php');?>