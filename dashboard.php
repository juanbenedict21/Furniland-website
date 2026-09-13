<?php 
require_once '../config/db.php';
require '../config/validator.php';

isLogin();
isAdmin();

include '../utils/header.php';
?>

<div class="container admin-dashboard">
    
    <h1 class="page-title">Admin Dashboard</h1>

    <div class="dashboard-grid">
        
        <a href="manage_product.php">
            <div class="dashboard-card">
                <h3>Manage Products</h3>
                <p>View, edit, and add furniture products.</p>
            </div>
        </a>

        <a href="manage_vendor.php">
            <div class="dashboard-card">
                <h3>Manage Vendors</h3>
                <p>View and manage furniture vendors.</p>
            </div>
        </a>

        <a href="manage_users.php">
            <div class="dashboard-card">
                <h3>Manage Users</h3>
                <p>View and control user accounts.</p>
            </div>
        </a>

        <a href="view_transactions.php">
            <div class="dashboard-card">
                <h3>View Transactions</h3>
                <p>Monitor purchase history and details.</p>
            </div>
        </a>

    </div>

    <div class="welcome-banner">
        <h2 class="welcome-title">Welcome, Admin <?= htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p class="welcome-sub">Use the cards above to manage the platform. Keep track of users, inventory, vendors, and transactions efficiently.</p>
    </div>

</div>

<?php include '../utils/footer.php'; ?>