<?php 
    require_once __DIR__ . '/../config/validator.php';

    $role = getUserRole();
    $name = getUsername();
    $current_page = basename($_SERVER['PHP_SELF']);
    $base_url = "/";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurniLand</title>
    <link rel="stylesheet" href="<?= $base_url ?>assets/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <a href="<?= $base_url ?>" class="logo">Furniland</a>

            <div class="nav-links">
                <?php if ($role === 'Guest'): ?>
                    <a href="<?= $base_url ?>" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Home</a>
                
                <?php elseif ($role === 'Member'): ?>
                    <a href="<?= $base_url ?>" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Home</a>
                    <a href="<?= $base_url ?>product/catalog.php" class="<?= $current_page == 'catalog.php' ? 'active' : '' ?>">Catalog</a>
                
                <?php elseif ($role === 'Admin'): ?>
                    <a href="<?= $base_url ?>admin/dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="nav-right nav-links">
            <?php if ($role === 'Guest'): ?>
                <a href="<?= $base_url ?>login.php">Login</a>

            <?php elseif ($role === 'Member'): ?>
                <a href="<?= $base_url ?>profile.php" class="user-badge">Hello, <?= htmlspecialchars($name) ?></a>
                
                <a href="<?= $base_url ?>cart.php" class="<?= $current_page == 'cart.php' ? 'active' : '' ?>">Cart</a>
                
                <a href="<?= $base_url ?>history.php" class="<?= $current_page == 'history.php' ? 'active' : '' ?>">History</a>
                
                <a href="<?= $base_url ?>logout.php" class="btn-logout">Logout</a>

            <?php elseif ($role === 'Admin'): ?>
                <a href="<?= $base_url ?>profile.php" class="user-badge">Hello, <?= htmlspecialchars($name) ?></a>
                <a href="<?= $base_url ?>logout.php" class="btn-logout">Logout</a>
            <?php endif; ?>
        </div>
    </nav>
