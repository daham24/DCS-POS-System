<?php 

if (isset($_SESSION['loggedIn'])) {

    $email = validate($_SESSION['loggedInUser']['email']);

    $query = "SELECT * FROM admins WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 0) {
        logoutSession();
        redirect('../login.php', 'Access Denied!');
    } else {
        $row = mysqli_fetch_assoc($result);

        // Check if the account is banned
        if ($row['is_ban'] == 1) {
            logoutSession();
            redirect('../login.php', 'Your account has been banned! Please contact the Admin.');
        }

        // Store user role in the session
        $_SESSION['role'] = $row['role'];

        // Get current file and folder
        $currentPage = basename($_SERVER['PHP_SELF']);
        $currentFolder = basename(dirname($_SERVER['PHP_SELF']));

        // Define staff-allowed files
        $staffAllowedFiles = [
            'dashboard.php',
            'order-create.php',
            'repairs-create.php',
            'index.php',
            'order-summery.php',
            'orders.php',
            'orders-view.php',
            'orders-view-print.php',
            'repairs.php',
            'repairs-view.php',
            'repairs-view-print.php',
            'products.php'
        ];

        // Access control logic
        if ($_SESSION['role'] == 'staff') {
            // Staff has restricted access
            if (!in_array($currentPage, $staffAllowedFiles)) {
                die('Access Denied: You do not have permission to access this page.');
            }
        } elseif ($_SESSION['role'] == 'admin') {
            // Admin has unrestricted access
            // No additional checks needed for admins
        } else {
            // Default denial for unknown roles
            die('Access Denied: You do not have permission to access this page.');
        }
    }

} else {
    redirect('../login.php', 'Login to continue...');
}

?>