<?php 
    $page = substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], "/") + 1);
?>

<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Core</div>

                <a class="nav-link <?= $page == 'index.php' ? 'active' : ''; ?>" href="index.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>

                <a class="nav-link <?= $page == 'order-create.php' ? 'active' : ''; ?>" href="order-create.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-bell"></i></div>
                    Create Order
                </a>

                <a class="nav-link <?= $page == 'orders.php' ? 'active' : ''; ?>" href="orders.php"  >
                    <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                    Orders
                </a>

                <div class="sb-sidenav-menu-heading">Interface</div>

                <a class="nav-link <?= ($page == 'categories-create.php') || ($page == 'categories.php') ? 'collapse active' : 'collapsed'; ?>" 
                    href="#" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#collapseCategory" aria-expanded="false" aria-controls="collapseCategory"
                    <?= $_SESSION['role'] == 'staff' ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?> >
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-layer-group"></i></div>
                    Categories
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?= ($page == 'categories-create.php') || ($page == 'categories.php') ? 'show' : ''; ?>" 
                    id="collapseCategory" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?= $page == 'categories-create.php' ? 'active' : ''; ?>" href="categories-create.php" <?= $_SESSION['role'] == 'staff' ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?> >Create Category</a>
                        <a class="nav-link <?= $page == 'categories.php' ? 'active' : ''; ?>" href="categories.php" <?= $_SESSION['role'] == 'staff' ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?> >View Categories</a>
                    </nav>
                </div>

                <a class="nav-link <?= ($page == 'products-create.php') || ($page == 'products.php') ? 'collapse active' : 'collapsed'; ?>" href="#" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#collapseProduct" aria-expanded="false" aria-controls="collapseProduct" >
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-list-check"></i></div>
                    Products
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?= ($page == 'products-create.php') || ($page == 'products.php') ? 'show' : ''; ?>" id="collapseProduct" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?= $page == 'products-create.php' ? 'active' : ''; ?>" href="products-create.php" <?= $_SESSION['role'] == 'staff' ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?> >Create Product</a>
                        <a class="nav-link <?= $page == 'products.php' ? 'active' : ''; ?>" href="products.php" >View Products</a>
                    </nav>
                </div>

                <a class="nav-link <?= ($page == 'repairs-create.php') || ($page == 'repairs.php') ? 'collapse active' : 'collapsed'; ?>" href="#" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#collapseRepair" aria-expanded="false" aria-controls="collapseRepair">
                    <div class="sb-nav-link-icon"><i class="fas fa-tools"></i></div>
                    Repairs
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?= ($page == 'repairs-create.php') || ($page == 'repairs.php') ? 'show' : ''; ?>" id="collapseRepair" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?= $page == 'repairs-create.php' ? 'active' : ''; ?>" href="repairs-create.php">Create Repair Item</a>
                        <a class="nav-link <?= $page == 'repairs.php' ? 'active' : ''; ?>" href="repairs.php">View Repair Items</a>
                    </nav>
                </div>

                <a class="nav-link <?= ($page == 'return-items.php') || ($page == 'return-items-view.php') ? 'collapse active' : 'collapsed'; ?>" href="#" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#collapseReturn" aria-expanded="false" aria-controls="collapseReturn">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-rotate-left"></i></div>
                    Returns
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?= ($page == 'return-items.php') || ($page == 'return-items-view.php') ? 'show' : ''; ?>" id="collapseReturn" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?= $page == 'return-items.php' ? 'active' : ''; ?>" href="return-items.php">Create Return Item</a>
                        <a class="nav-link <?= $page == 'return-items-view.php' ? 'active' : ''; ?>" href="return-items-view.php">View Return Items</a>
                    </nav>
                </div>

                <div class="sb-sidenav-menu-heading">Analytics</div>

                <a class="nav-link <?= $page == 'order-summery-view.php' ? 'active' : ''; ?>" href="order-summery-view.php" <?= $_SESSION['role'] == 'staff' ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?> >
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-clipboard-check"></i></div>
                    Summery
                </a>

                <div class="sb-sidenav-menu-heading">Expences</div>

                <a class="nav-link <?= $page == 'product-cost.php' ? 'active' : ''; ?>" href="product-cost.php"  <?= $_SESSION['role'] == 'staff' ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?> >
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-sack-dollar"></i></div>
                    Product Cost
                </a>

                <a class="nav-link <?= $page == 'bank-deposits.php' ? 'active' : ''; ?>" href="bank-deposits.php"  >
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-building-columns"></i></div>
                    Bank Deposits
                </a>

                <a class="nav-link <?= $page == 'utility-bills.php' ? 'active' : ''; ?>" href="utility-bills.php"  >
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-wallet"></i></div>
                    Utility Bills
                </a>

                <div class="sb-sidenav-menu-heading">Manage Users</div>

                <a class="nav-link <?= ($page == 'admins-create.php') || ($page == 'admins.php') ? 'collapse active' : 'collapsed'; ?>" href="#" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#collapseAdmins" 
                    aria-expanded="false" aria-controls="collapseAdmins"
                    <?= $_SESSION['role'] == 'staff' ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?> >
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-user-tie"></i></i></div>
                    Admins/Staff
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?= ($page == 'admins-create.php') || ($page == 'admins.php') ? 'show' : ''; ?>" id="collapseAdmins" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?= $page == 'admins-create.php' ? 'active' : ''; ?>" href="admins-create.php" <?= $_SESSION['role'] == 'staff' ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?> >Add Admins</a>
                        <a class="nav-link <?= $page == 'admins.php' ? 'active' : ''; ?>" href="admins.php" <?= $_SESSION['role'] == 'staff' ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?> >View Admins</a>
                    </nav>
                </div>


                <a class="nav-link <?= ($page == 'customers-create.php') || ($page == 'customers.php') ? 'collapse active' : 'collapsed'; ?>" href="#" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#collapseCustomers" 
                    aria-expanded="false" aria-controls="collapseCustomers"
                    <?= $_SESSION['role'] == 'staff' ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?> >
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-users"></i></i></div>
                    Customers
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?= ($page == 'customers-create.php') || ($page == 'customers.php') ? 'show' : ''; ?>" id="collapseCustomers" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?= $page == 'customers-create.php' ? 'active' : ''; ?>" href="customers-create.php" <?= $_SESSION['role'] == 'staff' ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?> >Add Customer</a>
                        <a class="nav-link <?= $page == 'customers.php' ? 'active' : ''; ?>" href="customers.php" <?= $_SESSION['role'] == 'staff' ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?> >View Customers</a>
                    </nav>
                </div>


                <a class="nav-link <?= ($page == 'supplier-create.php') || ($page == 'suppliers.php') ? 'collapse active' : 'collapsed'; ?>" href="#" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#collapseSuppliers" 
                    aria-expanded="false" aria-controls="collapseSuppliers"
                    <?= $_SESSION['role'] == 'staff' ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?> >
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-truck-fast"></i></div>
                    Suppliers
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?= ($page == 'supplier-create.php') || ($page == 'suppliers.php') ? 'show' : ''; ?>" id="collapseSuppliers" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?= $page == 'supplier-create.php' ? 'active' : ''; ?>" href="supplier-create.php" <?= $_SESSION['role'] == 'staff' ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?> >Add Supplier</a>
                        <a class="nav-link <?= $page == 'suppliers.php' ? 'active' : ''; ?>" href="suppliers.php" <?= $_SESSION['role'] == 'staff' ? 'style="pointer-events: none; opacity: 0.6;"' : ''; ?> >View Suppliers</a>
                    </nav>
                </div>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            DCS.lk
        </div>
    </nav>
</div>