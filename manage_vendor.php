<?php 
require_once '../config/db.php';
require '../config/validator.php';

isLogin();
isAdmin();

$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_vendor'])) {
    $vendorID = $_POST['vendor_id'];

    try {
        $stmt = $conn->prepare("DELETE FROM vendors WHERE vendorID = ?");
        $stmt->bind_param("i", $vendorID);
        
        if ($stmt->execute()) {
            $message = "Vendor deleted successfully.";
            $messageType = "alert-success";
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        if ($conn->errno == 1451) {
            $message = "Cannot delete vendor! This vendor is currently assigned to one or more products.";
        } else {
            $message = "Error deleting vendor: " . $e->getMessage();
        }
        $messageType = "alert-error";
    }
}

$result = $conn->query("SELECT * FROM vendors");

include '../utils/header.php';
?>

<div class="container admin-dashboard">
    
    <div class="header-actions">
        <h1 class="page-title">Manage Vendors</h1>
        <a href="add/vendor.php" class="btn-add">+ Add Vendor</a>
    </div>

    <?php if ($message): ?>
        <div class="alert <?= $messageType ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <table class="data-table">
        <thead>
            <tr>
                <th>Vendor Name</th>
                <th>Location</th>
                <th class="th-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['vendorName']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td class="action-linksz">
                            
                            <a href="edit/vendor.php?id=<?= $row['vendorID'] ?>" class="text-edit">Edit</a>
                            
                            <div class="action-linkszz">
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="vendor_id" value="<?= $row['vendorID'] ?>">
                                    <button type="submit" name="delete_vendor" class="text-delete btn-delete-confirm">Delete</button>
                                </form>
                            </div>

                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align:center;">No vendors found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<?php include '../utils/footer.php'; ?>