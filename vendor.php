<?php
require_once '../../config/db.php';
require_once '../../config/validator.php';

isLogin();
isAdmin();

if (!isset($_GET['id'])) {
    header("Location: ../manage_vendor.php");
    exit;
}

$id = $_GET['id'];
$error = "";

$stmt = $conn->prepare("SELECT * FROM vendors WHERE vendorID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();

if (!$vendor) {
    header("Location: ../manage_vendor.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vendorName = trim($_POST['vendorName']);
    $location = trim($_POST['location']);

    if (empty($vendorName) || empty($location)) {
        $error = "All fields must be filled.";
    } elseif (strlen($vendorName) > 20) {
        $error = "Vendor Name maximum 20 characters.";
    } elseif (strlen($location) > 100) {
        $error = "Location maximum 100 characters.";
    } else {
        $updateStmt = $conn->prepare("UPDATE vendors SET vendorName = ?, location = ? WHERE vendorID = ?");
        $updateStmt->bind_param("ssi", $vendorName, $location, $id);

        if ($updateStmt->execute()) {
            header("Location: ../manage_vendor.php");
            exit;
        } else {
            $error = "Database Error: " . $conn->error;
        }
    }
}

include '../../utils/header.php';
?>


<div class="auth-page">
    <div class="auth-card wide">
        <h2 class="auth-titles">Edit Vendor</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Vendor Name</label>
                <input type="text" name="vendorName" maxlength="20" required 
                       value="<?= htmlspecialchars($vendor['vendorName']) ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Location</label>
                <input type="text" name="location" maxlength="100" required 
                       value="<?= htmlspecialchars($vendor['location']) ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Update Vendor</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../utils/footer.php'; ?>