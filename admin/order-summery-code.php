<?php
// Start output buffering
ob_start();

session_start();
include('../config/dbCon.php'); // Include your database connection file

// Validate database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch total sales for the day (orders + repairs)
$daily_sales_query = "
    SELECT 
        SUM(o.total_amount) AS order_sales,
        SUM(CASE 
            WHEN r.status = 1 THEN ro.repair_cost
            WHEN r.status = 0 THEN ro.advanced_payment
            ELSE 0
        END) AS repair_sales
    FROM orders o
    LEFT JOIN repair_orders ro ON 1=1
    LEFT JOIN repairs r ON ro.repair_id = r.id
    WHERE DATE(o.order_date) = CURDATE() OR DATE(ro.created_at) = CURDATE()
";
$daily_sales_result = mysqli_query($conn, $daily_sales_query);
$daily_sales = mysqli_fetch_assoc($daily_sales_result);
$total_daily_sales = ($daily_sales['order_sales'] ?? 0) + ($daily_sales['repair_sales'] ?? 0);

// Fetch total expenses for the day (product_cost + utility_bills + bank_deposits)
$daily_expenses_query = "
    SELECT 
        SUM(pc.total_cost) AS product_cost,
        SUM(ub.amount) AS utility_bills,
        SUM(dd.deposit_amount) AS bank_deposits
    FROM products_cost pc
    LEFT JOIN utility_bills ub ON 1=1
    LEFT JOIN daily_deposits dd ON 1=1
    WHERE DATE(pc.date) = CURDATE() OR DATE(ub.bill_date) = CURDATE() OR DATE(dd.deposit_date) = CURDATE()
";
$daily_expenses_result = mysqli_query($conn, $daily_expenses_query);
$daily_expenses = mysqli_fetch_assoc($daily_expenses_result);
$total_daily_expenses = ($daily_expenses['product_cost'] ?? 0) + ($daily_expenses['utility_bills'] ?? 0) + ($daily_expenses['bank_deposits'] ?? 0);

// Calculate cashier balance (sales - expenses)
$cashier_balance = $total_daily_sales - $total_daily_expenses;

// Store results in session for use in the view
$_SESSION['daily_sales'] = $total_daily_sales;
$_SESSION['daily_expenses'] = $total_daily_expenses;
$_SESSION['cashier_balance'] = $cashier_balance;


// ---------------------------------------

// Fetch weekly sales (orders + repairs)
$weekly_sales_query = "
    SELECT 
        DAYNAME(o.order_date) AS day,
        SUM(o.total_amount) AS order_sales,
        SUM(CASE 
            WHEN r.status = 1 THEN ro.repair_cost
            WHEN r.status = 0 THEN ro.advanced_payment
            ELSE 0
        END) AS repair_sales
    FROM orders o
    LEFT JOIN repair_orders ro ON 1=1
    LEFT JOIN repairs r ON ro.repair_id = r.id
    WHERE YEARWEEK(o.order_date, 1) = YEARWEEK(CURDATE(), 1)
    GROUP BY DAYNAME(o.order_date)
";
$weekly_sales_result = mysqli_query($conn, $weekly_sales_query);
$weekly_sales = [];
while ($row = mysqli_fetch_assoc($weekly_sales_result)) {
    $weekly_sales[$row['day']] = $row;
}

// Fetch weekly expenses (product_cost + utility_bills + bank_deposits)
$weekly_expenses_query = "
    SELECT 
        DAYNAME(pc.date) AS day,
        SUM(pc.total_cost) AS product_cost,
        SUM(ub.amount) AS utility_bills,
        SUM(dd.deposit_amount) AS bank_deposits
    FROM products_cost pc
    LEFT JOIN utility_bills ub ON 1=1
    LEFT JOIN daily_deposits dd ON 1=1
    WHERE YEARWEEK(pc.date, 1) = YEARWEEK(CURDATE(), 1)
    GROUP BY DAYNAME(pc.date)
";
$weekly_expenses_result = mysqli_query($conn, $weekly_expenses_query);
$weekly_expenses = [];
while ($row = mysqli_fetch_assoc($weekly_expenses_result)) {
    $weekly_expenses[$row['day']] = $row;
}

// Calculate weekly profit for each day
$weekly_profit = [];
$days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
foreach ($days as $day) {
    $sales = ($weekly_sales[$day]['order_sales'] ?? 0) + ($weekly_sales[$day]['repair_sales'] ?? 0);
    $expenses = ($weekly_expenses[$day]['product_cost'] ?? 0) + ($weekly_expenses[$day]['utility_bills'] ?? 0) + ($weekly_expenses[$day]['bank_deposits'] ?? 0);
    $weekly_profit[$day] = $sales - $expenses;
}

// Store results in session for use in the view
$_SESSION['weekly_sales'] = $weekly_sales;
$_SESSION['weekly_expenses'] = $weekly_expenses;
$_SESSION['weekly_profit'] = $weekly_profit;

// Close the database connection
mysqli_close($conn);

// Clear the output buffer
ob_end_clean();

?>