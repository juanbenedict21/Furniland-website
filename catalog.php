<?php
    require_once '../config/db.php';
    require '../config/validator.php';

    $sql = "SELECT products.*, vendors.vendorName 
        FROM products 
        JOIN vendors ON products.vendorID = vendors.vendorID 
        WHERE 1=1";

    $selected_vendor = isset($_GET['vendor']) ? $_GET['vendor'] : '';
    if ($selected_vendor != '') {
        $safe_vendor = $conn->real_escape_string($selected_vendor);
        $sql .= " AND products.vendorID = '$safe_vendor'";
    }

    $search_keyword = isset($_GET['search']) ? $_GET['search'] : '';
    if ($search_keyword != '') {
        $safe_keyword = $conn->real_escape_string($search_keyword);
        $sql .= " AND products.productName LIKE '%$safe_keyword%'";
    }

    $sort_option = isset($_GET['sort']) ? $_GET['sort'] : '';
    if ($sort_option == 'price_asc') {
        $sql .= " ORDER BY products.price ASC";
    } elseif ($sort_option == 'price_desc') {
        $sql .= " ORDER BY products.price DESC";
    } else {
        $sql .= " ORDER BY products.productName ASC";
    }
    $result = $conn->query($sql);


    $vendors = $conn->query("SELECT * FROM vendors");

    include '../utils/header.php';
?>

<div class="container page-container">
    
    <div class="catalog-header">
        <h1 class="section-title">Product Catalog</h1>
        <span class="products-count">Showing <?= $result->num_rows ?> products</span>
    </div>

    <div class="toolbar">
        <form method="GET" class="toolbar-form">

            <div class="toolbar-left">
                <div class="filter-group">
                    <select name="vendor">
                        <option value="">All Vendors</option>
                        <?php while($v = $vendors->fetch_assoc()): ?>
                            <option value="<?= $v['vendorID'] ?>" <?= ($selected_vendor == $v['vendorID']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($v['vendorName']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <select name="sort">
                        <option value="newest" <?= ($sort_option == 'newest') ? 'selected' : '' ?>>Newest Arrival</option>
                        <option value="price_asc" <?= ($sort_option == 'price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_desc" <?= ($sort_option == 'price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="name_asc" <?= ($sort_option == 'name_asc') ? 'selected' : '' ?>>Name: A - Z</option>
                    </select>
                </div>

                <?php if(!empty($search_keyword) || !empty($selected_vendor) || $sort_option !== 'newest'): ?>
                    <a href="catalog.php" class="btn btn-reset">Clear Filter</a>
                <?php endif; ?>
            </div>

            <div class="toolbar-right">
                <div class="search-group">
                    <input type="text" name="search" value="<?= htmlspecialchars($search_keyword) ?>" placeholder="Search products">
                </div>

                <button type="submit" class="btn btn-apply">Apply</button>
            </div>

        </form>
    </div>

    <div class="product-grid">
        
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <div class="product-img-wrapper">
                        <img src="../assets/uploads/<?= htmlspecialchars($row['image']) ?>" 
                             alt="<?= htmlspecialchars($row['productName']) ?>" 
                             class="product-img-display">
                    </div>

                    <div class="product-info">
                        <div class="product-title"><?= htmlspecialchars($row['productName']) ?></div>
                        
                        <div class="product-short-desc">
                            <?= htmlspecialchars($row['description']) ?>
                        </div>

                        <div class="product-vendor">by <?= htmlspecialchars($row['vendorName']) ?></div>
                        
                        <div class="product-price">
                            Rp <?= number_format($row['price'], 0, ',', '.') ?>
                        </div>

                        <a href="detail.php?id=<?= $row['productID'] ?>" class="btn btn-full btn-center">
                            View Details
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        
        <?php else: ?>
            <div class="empty-catalog-box">
                <h3 class="empty-title">No products found matching your search.</h3>
                <a href="catalog.php" class="link-accent">View all products</a>
            </div>
        <?php endif; ?>

    </div>

</div>

<?php include '../utils/footer.php'; ?>