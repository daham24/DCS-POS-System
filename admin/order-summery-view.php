<?php
// Include the header and start session
include('includes/header.php');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include the backend logic file
include('order-summery-code.php');
?>

<div class="container-fluid px-4 mt-4 mb-3">
    <h2 class="mb-4 fw-bold">Sales Summary</h2>
    <hr>

    <!-- First Section: Daily Summary -->
    <div class="row mb-4">
        <!-- Today's Sales Card -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Today's Sales</h5>
                </div>
                <div class="card-body">
                    <h3 class="card-text">Rs.<?= number_format($_SESSION['daily_sales'] ?? 0, 2) ?></h3>
                </div>
            </div>
        </div>

        <!-- Today's Expenses Card -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">Today's Expenses</h5>
                </div>
                <div class="card-body">
                    <h3 class="card-text">Rs.<?= number_format($_SESSION['daily_expenses'] ?? 0, 2) ?></h3>
                </div>
            </div>
        </div>

        <!-- Cashier Balance Card -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Cashier Balance</h5>
                </div>
                <div class="card-body">
                    <h3 class="card-text">Rs.<?= number_format($_SESSION['cashier_balance'] ?? 0, 2) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <!-- Second Section: Weekly Summary -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4>Weekly Summary</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Total Sales</th>
                        <th>Total Expenses</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    foreach ($days as $day) {
                        $sales = ($_SESSION['weekly_sales'][$day]['order_sales'] ?? 0) + ($_SESSION['weekly_sales'][$day]['repair_sales'] ?? 0);
                        $expenses = ($_SESSION['weekly_expenses'][$day]['product_cost'] ?? 0) + ($_SESSION['weekly_expenses'][$day]['utility_bills'] ?? 0) + ($_SESSION['weekly_expenses'][$day]['bank_deposits'] ?? 0);
                        $profit = $_SESSION['weekly_profit'][$day] ?? 0;
                        echo "<tr>
                                <td>$day</td>
                                <td>Rs." . number_format($sales, 2) . "</td>
                                <td>Rs." . number_format($expenses, 2) . "</td>
                                <td>Rs." . number_format($profit, 2) . "</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bar Chart for Weekly Summary -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4>Weekly Summary Chart</h4>
            <canvas id="weeklyChart" width="400" height="200"></canvas>
        </div>
    </div>

    <hr>

    <!-- Third Section: Monthly Summary -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4>Monthly Summary</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Week</th>
                        <th>Total Sales</th>
                        <th>Total Expenses</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($week = 1; $week <= 5; $week++) {
                        $sales = ($_SESSION['monthly_sales'][$week]['order_sales'] ?? 0) + ($_SESSION['monthly_sales'][$week]['repair_sales'] ?? 0);
                        $expenses = ($_SESSION['monthly_expenses'][$week]['product_cost'] ?? 0) + ($_SESSION['monthly_expenses'][$week]['utility_bills'] ?? 0) + ($_SESSION['monthly_expenses'][$week]['bank_deposits'] ?? 0);
                        $profit = $_SESSION['monthly_profit'][$week] ?? 0;
                        echo "<tr>
                                <td>Week $week</td>
                                <td>Rs." . number_format($sales, 2) . "</td>
                                <td>Rs." . number_format($expenses, 2) . "</td>
                                <td>Rs." . number_format($profit, 2) . "</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bar Chart for Monthly Summary -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h4>Monthly Summary Chart</h4>
            <canvas id="monthlyChart" width="400" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Weekly Summary Bar Chart
const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
const weeklyChart = new Chart(weeklyCtx, {
    type: 'bar',
    data: {
        labels: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        datasets: [{
            label: 'Sales',
            data: [
                <?= ($_SESSION['weekly_sales']['Sunday']['order_sales'] ?? 0) + ($_SESSION['weekly_sales']['Sunday']['repair_sales'] ?? 0) ?>,
                <?= ($_SESSION['weekly_sales']['Monday']['order_sales'] ?? 0) + ($_SESSION['weekly_sales']['Monday']['repair_sales'] ?? 0) ?>,
                <?= ($_SESSION['weekly_sales']['Tuesday']['order_sales'] ?? 0) + ($_SESSION['weekly_sales']['Tuesday']['repair_sales'] ?? 0) ?>,
                <?= ($_SESSION['weekly_sales']['Wednesday']['order_sales'] ?? 0) + ($_SESSION['weekly_sales']['Wednesday']['repair_sales'] ?? 0) ?>,
                <?= ($_SESSION['weekly_sales']['Thursday']['order_sales'] ?? 0) + ($_SESSION['weekly_sales']['Thursday']['repair_sales'] ?? 0) ?>,
                <?= ($_SESSION['weekly_sales']['Friday']['order_sales'] ?? 0) + ($_SESSION['weekly_sales']['Friday']['repair_sales'] ?? 0) ?>,
                <?= ($_SESSION['weekly_sales']['Saturday']['order_sales'] ?? 0) + ($_SESSION['weekly_sales']['Saturday']['repair_sales'] ?? 0) ?>
            ],
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }, {
            label: 'Expenses',
            data: [
                <?= ($_SESSION['weekly_expenses']['Sunday']['product_cost'] ?? 0) + ($_SESSION['weekly_expenses']['Sunday']['utility_bills'] ?? 0) + ($_SESSION['weekly_expenses']['Sunday']['bank_deposits'] ?? 0) ?>,
                <?= ($_SESSION['weekly_expenses']['Monday']['product_cost'] ?? 0) + ($_SESSION['weekly_expenses']['Monday']['utility_bills'] ?? 0) + ($_SESSION['weekly_expenses']['Monday']['bank_deposits'] ?? 0) ?>,
                <?= ($_SESSION['weekly_expenses']['Tuesday']['product_cost'] ?? 0) + ($_SESSION['weekly_expenses']['Tuesday']['utility_bills'] ?? 0) + ($_SESSION['weekly_expenses']['Tuesday']['bank_deposits'] ?? 0) ?>,
                <?= ($_SESSION['weekly_expenses']['Wednesday']['product_cost'] ?? 0) + ($_SESSION['weekly_expenses']['Wednesday']['utility_bills'] ?? 0) + ($_SESSION['weekly_expenses']['Wednesday']['bank_deposits'] ?? 0) ?>,
                <?= ($_SESSION['weekly_expenses']['Thursday']['product_cost'] ?? 0) + ($_SESSION['weekly_expenses']['Thursday']['utility_bills'] ?? 0) + ($_SESSION['weekly_expenses']['Thursday']['bank_deposits'] ?? 0) ?>,
                <?= ($_SESSION['weekly_expenses']['Friday']['product_cost'] ?? 0) + ($_SESSION['weekly_expenses']['Friday']['utility_bills'] ?? 0) + ($_SESSION['weekly_expenses']['Friday']['bank_deposits'] ?? 0) ?>,
                <?= ($_SESSION['weekly_expenses']['Saturday']['product_cost'] ?? 0) + ($_SESSION['weekly_expenses']['Saturday']['utility_bills'] ?? 0) + ($_SESSION['weekly_expenses']['Saturday']['bank_deposits'] ?? 0) ?>
            ],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Monthly Summary Bar Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'],
        datasets: [{
            label: 'Sales',
            data: [
                <?= ($_SESSION['monthly_sales'][1]['order_sales'] ?? 0) + ($_SESSION['monthly_sales'][1]['repair_sales'] ?? 0) ?>,
                <?= ($_SESSION['monthly_sales'][2]['order_sales'] ?? 0) + ($_SESSION['monthly_sales'][2]['repair_sales'] ?? 0) ?>,
                <?= ($_SESSION['monthly_sales'][3]['order_sales'] ?? 0) + ($_SESSION['monthly_sales'][3]['repair_sales'] ?? 0) ?>,
                <?= ($_SESSION['monthly_sales'][4]['order_sales'] ?? 0) + ($_SESSION['monthly_sales'][4]['repair_sales'] ?? 0) ?>,
                <?= ($_SESSION['monthly_sales'][5]['order_sales'] ?? 0) + ($_SESSION['monthly_sales'][5]['repair_sales'] ?? 0) ?>
            ],
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }, {
            label: 'Expenses',
            data: [
                <?= ($_SESSION['monthly_expenses'][1]['product_cost'] ?? 0) + ($_SESSION['monthly_expenses'][1]['utility_bills'] ?? 0) + ($_SESSION['monthly_expenses'][1]['bank_deposits'] ?? 0) ?>,
                <?= ($_SESSION['monthly_expenses'][2]['product_cost'] ?? 0) + ($_SESSION['monthly_expenses'][2]['utility_bills'] ?? 0) + ($_SESSION['monthly_expenses'][2]['bank_deposits'] ?? 0) ?>,
                <?= ($_SESSION['monthly_expenses'][3]['product_cost'] ?? 0) + ($_SESSION['monthly_expenses'][3]['utility_bills'] ?? 0) + ($_SESSION['monthly_expenses'][3]['bank_deposits'] ?? 0) ?>,
                <?= ($_SESSION['monthly_expenses'][4]['product_cost'] ?? 0) + ($_SESSION['monthly_expenses'][4]['utility_bills'] ?? 0) + ($_SESSION['monthly_expenses'][4]['bank_deposits'] ?? 0) ?>,
                <?= ($_SESSION['monthly_expenses'][5]['product_cost'] ?? 0) + ($_SESSION['monthly_expenses'][5]['utility_bills'] ?? 0) + ($_SESSION['monthly_expenses'][5]['bank_deposits'] ?? 0) ?>
            ],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include('includes/footer.php'); ?>