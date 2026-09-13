<?php
require_once '../config/db.php';
require '../config/validator.php';

isLogin();
isAdmin();

$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $userID = $_POST['user_id'];
    
    if ($userID == $_SESSION['userID']) {
        $message = "You cannot delete your own account!";
        $messageType = "alert-error";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE userID = ?");
        $stmt->bind_param("i", $userID);
        
        if ($stmt->execute()) {
            $message = "User deleted successfully.";
            $messageType = "alert-success";
        } else {
            $message = "Error deleting user: " . $conn->error;
            $messageType = "alert-error";
        }
    }
}

$result = $conn->query("SELECT * FROM users ORDER BY role ASC, username ASC");

include '../utils/header.php';
?>

<div class="container manage-container">
    
    <h1 class="page-title">Manage Users</h1>

    <?php if ($message): ?>
        <div class="alert <?= $messageType ?>"><?= $message ?></div>
    <?php endif; ?>

    <table class="data-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Gender</th>
                <th>Date of Birth</th>
                <th>Role</th>
                <th style="text-align: left;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['username']) ?></strong></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['gender']) ?></td>
                        <td><?= htmlspecialchars($row['dob']) ?></td>
                        
                        <td>
                            <span class="user-badge <?= ($row['role'] == 'Admin') ? 'badge-admin' : 'badge-member' ?>">
                                <?= htmlspecialchars($row['role']) ?>
                            </span>
                        </td>

                        <td style="text-align: left;" class="action-links">
                            
                            <?php if ($row['userID'] == $_SESSION['userID']): ?>
                                <span class="current-user-text">Current Admin</span>
                            
                            <?php else: ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $row['userID'] ?>">
                                    <button type="submit" name="delete_user" class="text-delete btn-delete-confirm">Delete</button>
                                </form>
                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="empty-state">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<?php include '../utils/footer.php'; ?>