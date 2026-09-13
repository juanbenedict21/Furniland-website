<?php 
    require_once '../config/db.php';
    require '../config/validator.php';

    if (!isset($_GET['id'])) {
        header("Location: index.php");
        exit;
    }

    $productID = $_GET['id'];

    $stmt = $conn->prepare("SELECT p.*, v.vendorName, v.location 
                        FROM products p 
                        JOIN vendors v ON p.vendorID = v.vendorID 
                        WHERE p.productID = ?");
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
       
        if (!isset($_SESSION['userID'])) {
            header("Location: ../login.php");
            exit;
        }

        $userID = $_SESSION['userID'];
        $qty = (int) $_POST['quantity'];
        

        $checkCart = $conn->prepare("SELECT cartID, quantity FROM cart WHERE userID = ? AND productID = ?");
        $checkCart->bind_param("ii", $userID, $productID);
        $checkCart->execute();
        $existingItem = $checkCart->get_result()->fetch_assoc();

        if ($existingItem) {
            $newQty = $existingItem['quantity'] + $qty;
            $updateStmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cartID = ?");
            $updateStmt->bind_param("ii", $newQty, $existingItem['cartID']);
            $updateStmt->execute();
        } else {
            $insertStmt = $conn->prepare("INSERT INTO cart (userID, productID, quantity) VALUES (?, ?, ?)");
            $insertStmt->bind_param("iii", $userID, $productID, $qty);
            $insertStmt->execute();
        }

        header("Location: ../cart.php");
        exit;
    }

    include '../utils/header.php';
?>

<?php if ($product): ?>
    <div class="container detail-page-container">
        
        <div class="detail-wrapper">
            
            <div class="detail-image-col">
                <img src="../assets/uploads/<?= htmlspecialchars($product['image']) ?>" 
                     alt="<?= htmlspecialchars($product['productName']) ?>" 
                     class="detail-img-large">
            </div>
    
            <div class="detail-info-col">

                <h1 class="detail-title"><?= htmlspecialchars($product['productName']) ?></h1>

                <?php
                    $short_desc = htmlspecialchars($product['description']);
                    if (strlen($short_desc) > 120) {
                        $short_sub = substr($short_desc, 0, 120) . '...';
                    } else {
                        $short_sub = $short_desc;
                    }
                ?>
                <div class="detail-subtitle"><?= nl2br($short_sub) ?></div>

                <div class="detail-price">Rp <?= number_format($product['price'], 0, ',', '.') ?></div>

                <div class="detail-meta">
                    <div class="detail-vendor"><strong>Vendor:</strong> <?= htmlspecialchars($product['vendorName']) ?></div>
                    <div class="detail-location"><strong>Location:</strong> <?= htmlspecialchars($product['location']) ?></div>
                </div>

                <form method="POST" class="add-cart-form">
                    <div class="cart-actions">
                        <label class="qty-inline">Quantity:</label>
                        <input type="number" name="quantity" value="1" min="1" class="quantity-input" required>
                    </div>

                    <div class="cart-button-row">
                        <button type="submit" name="add_to_cart" class="btn btn-add-cart btn-add-full">Add to Cart</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

<?php else: ?>
    <div class='container detail-page-container'>
        <h2 style='color:white; text-align:center;'>Product not found.</h2>
        <a href="catalog.php">Go back to Catalog</a>
    </div>
<?php endif; ?>

<?php include '../utils/footer.php'; ?>
