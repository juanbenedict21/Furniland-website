<?php
require_once '../config/db.php';
require_once '../config/validator.php';

isLogin();
isAdmin();

$query = "SELECT t.transactionID, t.totalPrice, t.transactionDate, u.username 
          FROM transactions t 
          JOIN users u ON t.userID = u.userID 
          ORDER BY t.transactionDate DESC";

$result = $conn->query($query);

include '../utils/header.php';
?>

<div class="container manage-container">
    
    <h1 class="page-title">All Transactions</h1>

    <table class="data-table">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>User</th>
                <th>Total Price</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <strong>#<?= htmlspecialchars($row['transactionID']) ?></strong>
                        </td>
                        
                        <td>
                            <?= htmlspecialchars($row['username']) ?>
                        </td>

                        <td style="color: #00bcd4; font-weight: bold;">
                            Rp <?= number_format($row['totalPrice'], 0, ',', '.') ?>
                        </td>

                        <td>
                            <?= date("d M Y", strtotime($row['transactionDate'])) ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="empty-state">No transactions found yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<?php include '../utils/footer.php'; ?>