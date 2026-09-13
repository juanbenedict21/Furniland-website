<?php 
require_once 'config/db.php';
require 'config/validator.php';

isLogin();

$query = "SELECT * FROM transactions WHERE userID = ? ORDER BY transactionDate DESC, transactionID DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['userID']);
$stmt->execute();
$transactions = $stmt->get_result();

include 'utils/header.php';
?>

<div class="container page-container">
    
    <div class="page-header">
        <h1 class="page-title">Transaction History</h1>
        
        <?php if ($transactions->num_rows == 0): ?>
            <div class="empty-cart-box">
                <p class="empty-cart-sub">You haven't made any transactions yet.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php while ($trx = $transactions->fetch_assoc()): ?>
        
        <div class="history-card">
            <div class="history-header">
                <div>
                    <div class="history-id">Transaction ID: #<?= $trx['transactionID'] ?></div>
                    <div class="history-date">Date: <?= date("d F Y", strtotime($trx['transactionDate'])) ?></div>
                </div>
                <div class="history-total">
                    Total: Rp <?= number_format($trx['totalPrice'], 0, ',', '.') ?>
                </div>
            </div>

            <table class="history-items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                            <th class="th-qty">Qty</th>
                            <th class="text-right th-subtotal">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $trxID = $trx['transactionID'];
                    $queryDetail = "SELECT d.*, p.productName 
                                    FROM transaction_details d 
                                    JOIN products p ON d.productID = p.productID 
                                    WHERE d.transactionID = ?";
                    $stmtDetail = $conn->prepare($queryDetail);
                    $stmtDetail->bind_param("i", $trxID);
                    $stmtDetail->execute();
                    $details = $stmtDetail->get_result();

                    while ($item = $details->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['productName']) ?></td>
                            <td style="text-align: center;"><?= $item['quantity'] ?></td>
                            <td class="text-right">
                                Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endwhile; ?>
</div>

<?php include 'utils/footer.php'; ?>