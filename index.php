<?php
    require_once 'config/db.php';
    require 'config/validator.php';

    $query = "SELECT p.*, v.vendorName 
          FROM products p 
          JOIN vendors v ON p.vendorID = v.vendorID 
          ORDER BY RAND() 
          LIMIT 6";

    $result = $conn->query($query);

    include 'utils/header.php';
?>

<div class="home-hero">
    <div class="home-left">
        <h1 class="home-title">Furniland</h1>
        <p class="home-subtitle">Furnitures you might love</p>
    </div>
    <div class="hero-action">
        <a href="product/catalog.php" class="view-all-link">View All &rarr;</a>
    </div>
</div>

<div class="container">
    

    <div class="product-grid">
        
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <div class="product-img-wrapper">
                        <img src="assets/uploads/<?= htmlspecialchars($row['image']) ?>" 
                             alt="<?= htmlspecialchars($row['productName']) ?>" 
                             class="product-img-display"
                             onerror="this.src='assets/images/placeholder.jpg';">
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
                        <a href="product/detail.php?id=<?= $row['productID'] ?>" class="btn btn-full btn-center">
                            View Details
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="empty-message">No products available yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'utils/footer.php'; ?>