<?php
// Include the header and database connection
include('includes/header.php');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection
include('../config/dbCon.php'); // Ensure this path is correct

// Validate database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch total sales for Today, Weekly, and Monthly (regular orders)
$queries = [
    'daily' => "SELECT SUM(total_amount) AS sales FROM orders WHERE order_date = CURDATE()",
    'weekly' => "SELECT SUM(total_amount) AS sales FROM orders WHERE YEARWEEK(order_date, 1) = YEARWEEK(CURDATE(), 1)",
    'monthly' => "SELECT SUM(total_amount) AS sales FROM orders WHERE MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())",
];

$sales_data = [];
foreach ($queries as $period => $query) {
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $sales_data[$period] = $row['sales'] ?? 0;
    } else {
        $sales_data[$period] = 0; // Default to 0 if query fails
    }
}

// Fetch total sales for Today, Weekly, and Monthly (repair orders)
$repair_queries = [
    'daily' => "SELECT SUM(
                    CASE 
                        WHEN r.status = 1 THEN ro.repair_cost
                        WHEN r.status = 0 THEN ro.advanced_payment
                        ELSE 0
                    END
                ) AS sales 
                FROM repair_orders ro
                JOIN repairs r ON ro.repair_id = r.id
                WHERE DATE(ro.created_at) = CURDATE()",
    'weekly' => "SELECT SUM(
                    CASE 
                        WHEN r.status = 1 THEN ro.repair_cost
                        WHEN r.status = 0 THEN ro.advanced_payment
                        ELSE 0
                    END
                ) AS sales 
                FROM repair_orders ro
                JOIN repairs r ON ro.repair_id = r.id
                WHERE YEARWEEK(ro.created_at, 1) = YEARWEEK(CURDATE(), 1)",
    'monthly' => "SELECT SUM(
                    CASE 
                        WHEN r.status = 1 THEN ro.repair_cost
                        WHEN r.status = 0 THEN ro.advanced_payment
                        ELSE 0
                    END
                ) AS sales 
                FROM repair_orders ro
                JOIN repairs r ON ro.repair_id = r.id
                WHERE MONTH(ro.created_at) = MONTH(CURDATE()) AND YEAR(ro.created_at) = YEAR(CURDATE())",
];

$repair_sales_data = [];
foreach ($repair_queries as $period => $query) {
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $repair_sales_data[$period] = $row['sales'] ?? 0;
    } else {
        $repair_sales_data[$period] = 0; // Default to 0 if query fails
    }
}

// Fetch expenses from products_cost table for Today, Weekly, and Monthly
$expenses_queries = [
    'daily' => "SELECT SUM(total_cost) AS total_expenses FROM products_cost WHERE DATE(date) = CURDATE()",
    'weekly' => "SELECT SUM(total_cost) AS total_expenses FROM products_cost WHERE YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1)",
    'monthly' => "SELECT SUM(total_cost) AS total_expenses FROM products_cost WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())",
];

$expenses_data = [];
foreach ($expenses_queries as $period => $query) {
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $expenses_data[$period] = $row['total_expenses'] ?? 0;
    } else {
        $expenses_data[$period] = 0; // Default to 0 if query fails
    }
}

// Fetch data for charts (regular orders)
$daily_chart_data = fetchChartData($conn, "DATE(order_date)", "order_date = CURDATE()");
$weekly_chart_data = fetchChartData($conn, "DATE(order_date)", "YEARWEEK(order_date, 1) = YEARWEEK(CURDATE(), 1)");
$monthly_chart_data = fetchChartData($conn, "DATE(order_date)", "MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())");

// Fetch data for charts (repair orders)
$daily_repair_chart_data = fetchChartData($conn, "DATE(ro.created_at)", "DATE(ro.created_at) = CURDATE()", 
    "repair_orders ro JOIN repairs r ON ro.repair_id = r.id", 
    "CASE 
        WHEN r.status = 1 THEN ro.repair_cost
        WHEN r.status = 0 THEN ro.advanced_payment
        ELSE 0
    END");
$weekly_repair_chart_data = fetchChartData($conn, "DATE(ro.created_at)", "YEARWEEK(ro.created_at, 1) = YEARWEEK(CURDATE(), 1)", 
    "repair_orders ro JOIN repairs r ON ro.repair_id = r.id", 
    "CASE 
        WHEN r.status = 1 THEN ro.repair_cost
        WHEN r.status = 0 THEN ro.advanced_payment
        ELSE 0
    END");
$monthly_repair_chart_data = fetchChartData($conn, "DATE(ro.created_at)", "MONTH(ro.created_at) = MONTH(CURDATE()) AND YEAR(ro.created_at) = YEAR(CURDATE())", 
    "repair_orders ro JOIN repairs r ON ro.repair_id = r.id", 
    "CASE 
        WHEN r.status = 1 THEN ro.repair_cost
        WHEN r.status = 0 THEN ro.advanced_payment
        ELSE 0
    END");

// Fetch orders and repair orders for the selected period
$period = isset($_GET['period']) && in_array($_GET['period'], ['daily', 'weekly', 'monthly']) ? $_GET['period'] : 'daily';

$orders_query = "";
$repair_orders_query = "";

if ($period == 'daily') {
    $orders_query = "SELECT * FROM orders WHERE order_date = CURDATE()";
    $repair_orders_query = "SELECT ro.*, r.status 
                            FROM repair_orders ro
                            JOIN repairs r ON ro.repair_id = r.id
                            WHERE DATE(ro.created_at) = CURDATE()";
} elseif ($period == 'weekly') {
    $orders_query = "SELECT * FROM orders WHERE YEARWEEK(order_date, 1) = YEARWEEK(CURDATE(), 1)";
    $repair_orders_query = "SELECT ro.*, r.status 
                            FROM repair_orders ro
                            JOIN repairs r ON ro.repair_id = r.id
                            WHERE YEARWEEK(ro.created_at, 1) = YEARWEEK(CURDATE(), 1)";
} else {
    $orders_query = "SELECT * FROM orders WHERE MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())";
    $repair_orders_query = "SELECT ro.*, r.status 
                            FROM repair_orders ro
                            JOIN repairs r ON ro.repair_id = r.id
                            WHERE MONTH(ro.created_at) = MONTH(CURDATE()) AND YEAR(ro.created_at) = YEAR(CURDATE())";
}

$orders_result = mysqli_query($conn, $orders_query);
$orders = [];
if ($orders_result) {
    while ($row = mysqli_fetch_assoc($orders_result)) {
        $orders[] = $row;
    }
}

$repair_orders_result = mysqli_query($conn, $repair_orders_query);
$repair_orders = [];
if ($repair_orders_result) {
    while ($row = mysqli_fetch_assoc($repair_orders_result)) {
        $repair_orders[] = $row;
    }
}

// Fetch product costs for the selected period
$product_costs_query = "";
if ($period == 'daily') {
    $product_costs_query = "SELECT * FROM products_cost WHERE DATE(date) = CURDATE()";
} elseif ($period == 'weekly') {
    $product_costs_query = "SELECT * FROM products_cost WHERE YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1)";
} else {
    $product_costs_query = "SELECT * FROM products_cost WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())";
}

$product_costs_result = mysqli_query($conn, $product_costs_query);
$product_costs = [];
if ($product_costs_result) {
    while ($row = mysqli_fetch_assoc($product_costs_result)) {
        $product_costs[] = $row;
    }
}

// Function to fetch chart data
function fetchChartData($conn, $groupBy, $condition, $table = "orders", $amountField = "total_amount") {
    $query = "SELECT $groupBy AS label, SUM($amountField) AS sales 
              FROM $table 
              WHERE $condition 
              GROUP BY $groupBy 
              ORDER BY label";
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data['labels'][] = $row['label'];
        $data['sales'][] = $row['sales'];
    }
    return $data;
}

// Close the database connection
mysqli_close($conn);
?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid px-4 mt-4 mb-3">
    <h2 class="mb-4 fw-bold">Sales Summary</h2>
    <hr>
    <div class="row">
        <!-- Today's Sales Card -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Today's Sales</h5>
                </div>
                <div class="card-body">
                    <h3 class="card-text">Rs.<?= number_format($sales_data['daily'] + $repair_sales_data['daily'] - $expenses_data['daily'], 2) ?></h3>
                    <canvas id="dailyChart" width="100%" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Weekly Sales Card -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Weekly Sales</h5>
                </div>
                <div class="card-body">
                    <h3 class="card-text">Rs.<?= number_format($sales_data['weekly'] + $repair_sales_data['weekly'] - $expenses_data['weekly'], 2) ?></h3>
                    <canvas id="weeklyChart" width="100%" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Sales Card -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">Monthly Sales</h5>
                </div>
                <div class="card-body">
                    <h3 class="card-text">Rs.<?= number_format($sales_data['monthly'] + $repair_sales_data['monthly'] - $expenses_data['monthly'], 2) ?></h3>
                    <canvas id="monthlyChart" width="100%" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
      <!-- Daily Expenses Card -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">Daily Expenses</h5>
                </div>
                <div class="card-body">
                    <h3 class="card-text">Rs.<?= number_format($expenses_data['daily'], 2) ?></h3>
                </div>
            </div>
        </div>

        <!-- Weekly Expenses Card -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">Weekly Expenses</h5>
                </div>
                <div class="card-body">
                    <h3 class="card-text">Rs.<?= number_format($expenses_data['weekly'], 2) ?></h3>
                </div>
            </div>
        </div>

        <!-- Monthly Expenses Card -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">Monthly Expenses</h5>
                </div>
                <div class="card-body">
                    <h3 class="card-text">Rs.<?= number_format($expenses_data['monthly'], 2) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <!-- Orders Table -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Orders for <?= ucfirst($period) ?></h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Order Date</th>
                            <th>Total Amount (Rs.)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= $order['id'] ?></td>
                                    <td><?= $order['order_date'] ?></td>
                                    <td><?= number_format($order['total_amount'], 2) ?></td>
                                    <td><?= $order['order_status'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No orders found for the selected period.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <hr>

    <!-- Repair Orders Table -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Repair Orders for <?= ucfirst($period) ?></h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Repair ID</th>
                            <th>Invoice Number</th>
                            <th>Repair Cost (Rs.)</th>
                            <th>Advanced Payment (Rs.)</th>
                            <th>Shortage (Rs.)</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($repair_orders)): ?>
                            <?php foreach ($repair_orders as $repair_order): ?>
                                <?php
                                $shortage = $repair_order['repair_cost'] - $repair_order['advanced_payment'];
                                ?>
                                <tr>
                                    <td><?= $repair_order['repair_id'] ?></td>
                                    <td><?= $repair_order['invoice_number'] ?></td>
                                    <td><?= number_format($repair_order['repair_cost'], 2) ?></td>
                                    <td><?= number_format($repair_order['advanced_payment'], 2) ?></td>
                                    <td><?= number_format($shortage, 2) ?></td>
                                    <td><?= $repair_order['status'] == 1 ? 'Completed' : 'Pending' ?></td>
                                    <td><?= $repair_order['created_at'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No repair orders found for the selected period.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <hr>

    <!-- Product Costs Table -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Product Costs for <?= ucfirst($period) ?></h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Quantity</th>
                            <th>Unit Price (Rs.)</th>
                            <th>Total Cost (Rs.)</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($product_costs)): ?>
                            <?php foreach ($product_costs as $product_cost): ?>
                                <tr>
                                    <td><?= $product_cost['product_id'] ?></td>
                                    <td><?= $product_cost['quantity'] ?></td>
                                    <td><?= number_format($product_cost['unit_price'], 2) ?></td>
                                    <td><?= number_format($product_cost['total_cost'], 2) ?></td>
                                    <td><?= $product_cost['date'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No product costs found for the selected period.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script>
    // Pass PHP data to JavaScript
    const dailyData = {
        labels: <?= json_encode($daily_chart_data['labels'] ?? []) ?>,
        datasets: [{
            label: 'Today\'s Sales',
            data: <?= json_encode($daily_chart_data['sales'] ?? []) ?>,
            borderColor: 'rgba(0, 123, 255, 1)',
            backgroundColor: 'rgba(0, 123, 255, 0.2)',
            tension: 0.4
        }]
    };

    const weeklyData = {
        labels: <?= json_encode($weekly_chart_data['labels'] ?? []) ?>,
        datasets: [{
            label: 'Weekly Sales',
            data: <?= json_encode($weekly_chart_data['sales'] ?? []) ?>,
            borderColor: 'rgba(40, 167, 69, 1)',
            backgroundColor: 'rgba(40, 167, 69, 0.2)',
            tension: 0.4
        }]
    };

    const monthlyData = {
        labels: <?= json_encode($monthly_chart_data['labels'] ?? []) ?>,
        datasets: [{
            label: 'Monthly Sales',
            data: <?= json_encode($monthly_chart_data['sales'] ?? []) ?>,
            borderColor: 'rgba(255, 193, 7, 1)',
            backgroundColor: 'rgba(255, 193, 7, 0.2)',
            tension: 0.4
        }]
    };

    // Render Today's Sales Chart
    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: dailyData,
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Render Weekly Sales Chart
    new Chart(document.getElementById('weeklyChart'), {
        type: 'line',
        data: weeklyData,
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Render Monthly Sales Chart
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: monthlyData,
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<?php include('includes/footer.php'); ?>