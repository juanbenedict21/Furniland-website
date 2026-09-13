<?php 
    require_once 'config/db.php';
    require 'config/validator.php';
    isLogin();

    $userID = $_SESSION['userID'];

    if (isset($_POST['remove_item'])) {
        $cartID = $_POST['cart_id'];
        $stmt = $conn->prepare("DELETE FROM cart WHERE cartID = ? AND userID = ?");
        $stmt->bind_param("ii", $cartID, $userID);
        $stmt->execute();
    }

    if (isset($_POST['checkout'])) {
        $queryTotal = "SELECT SUM(p.price * c.quantity) as grandTotal 
                    FROM cart c 
                    JOIN products p ON c.productID = p.productID 
                    WHERE c.userID = ?";
        $stmtTotal = $conn->prepare($queryTotal);
        $stmtTotal->bind_param("i", $userID);
        $stmtTotal->execute();
        $resultTotal = $stmtTotal->get_result()->fetch_assoc();
        $grandTotal = $resultTotal['grandTotal'];

        if ($grandTotal > 0) {
            $date = date('Y-m-d');
            $stmtTrx = $conn->prepare("INSERT INTO transactions (userID, totalPrice, transactionDate) VALUES (?, ?, ?)");
            $stmtTrx->bind_param("ids", $userID, $grandTotal, $date);
            
            if ($stmtTrx->execute()) {
                $transactionID = $conn->insert_id; 

                $queryCart = "SELECT c.productID, c.quantity, p.price 
                            FROM cart c 
                            JOIN products p ON c.productID = p.productID 
                            WHERE c.userID = ?";
                $stmtCart = $conn->prepare($queryCart);
                $stmtCart->bind_param("i", $userID);
                $stmtCart->execute();
                $cartItems = $stmtCart->get_result();

                $stmtDetail = $conn->prepare("INSERT INTO transaction_details (transactionID, productID, quantity, subtotal) VALUES (?, ?, ?, ?)");
                while ($item = $cartItems->fetch_assoc()) {
                    $subtotal = $item['price'] * $item['quantity'];
                    $stmtDetail->bind_param("iiii", $transactionID, $item['productID'], $item['quantity'], $subtotal);
                    $stmtDetail->execute();
                }

                $stmtClear = $conn->prepare("DELETE FROM cart WHERE userID = ?");
                $stmtClear->bind_param("i", $userID);
                $stmtClear->execute();

                header("Location: history.php");
                exit;
            }
        }
    }

    $queryDisplay = "SELECT c.cartID, c.quantity, p.productName, p.price, p.image 
                    FROM cart c 
                    JOIN products p ON c.productID = p.productID 
                    WHERE c.userID = ?";
    $stmtDisplay = $conn->prepare($queryDisplay);
    $stmtDisplay->bind_param("i", $userID);
    $stmtDisplay->execute();
    $cartResult = $stmtDisplay->get_result();

    include 'utils/header.php';
?>

<div class="container page-container">
    
    <div class="page-header">
        <h1 class="page-title">Your Cart</h1>
        <?php if ($cartResult->num_rows == 0): ?>
            <div class="empty-cart-box">
                <p class="empty-cart-sub">Your cart is empty.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($cartResult->num_rows > 0): ?>
        
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="th-qty">Qty</th>
                    <th class="th-subtotal">Subtotal</th>
                    <th class="th-action">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grandTotalDisplay = 0;
                while ($row = $cartResult->fetch_assoc()): 
                    $subtotal = $row['price'] * $row['quantity'];
                    $grandTotalDisplay += $subtotal;
                ?>
                    <tr>
                        <td>
                            <div class="cart-item">
                                <span class="product-name">
                                    <?= htmlspecialchars($row['productName']) ?>
                                </span>
                            </div>
                        </td>
                        <td class="cart-qty">
                            <?= $row['quantity'] ?>
                        </td>
                        <td class="cart-subtotal">
                            Rp <?= number_format($subtotal, 0, ',', '.') ?>
                        </td>
                        <td style="text-align: center;">
                            <form method="POST">
                                <input type="hidden" name="cart_id" value="<?= $row['cartID'] ?>">
                                <button type="submit" name="remove_item" class="btn-remove btn-delete-confirm">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <form method="POST" class="inline-form">
                <button type="submit" name="checkout" class="btn-checkout">Checkout</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php include 'utils/footer.php'; ?>