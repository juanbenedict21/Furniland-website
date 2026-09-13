<?php
require_once '../../config/db.php';
require_once '../../config/validator.php';

isLogin();
isAdmin();

if (!isset($_GET['id'])) {
    header("Location: ../manage_product.php");
    exit;
}

$id = $_GET['id'];
$error = "";

$stmt = $conn->prepare("SELECT * FROM products WHERE productID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: ../manage_product.php");
    exit;
}

$vendors = $conn->query("SELECT * FROM vendors");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = trim($_POST['productName']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $vendorID = $_POST['vendorID'];
    
    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $imageError = $_FILES['image']['error'];

    if (strlen($productName) < 3 || strlen($productName) > 30) {
        $error = "Product Name must be between 3 and 30 characters.";
    } elseif (empty($description)) {
        $error = "Description must be filled.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "Price must be a number greater than 0.";
    } elseif (empty($vendorID)) {
        $error = "Please select a vendor.";
    } else {
        $finalImageName = $product['image']; 

        if ($imageError !== 4) {
            $allowedExt = ['jpg', 'jpeg', 'png'];
            $fileExt = explode('.', $imageName);
            $fileActualExt = strtolower(end($fileExt));

            if (!in_array($fileActualExt, $allowedExt)) {
                $error = "Invalid file type! Only JPG and PNG allowed.";
            } else {
                if (file_exists("../../assets/uploads/" . $product['image'])) {
                    unlink("../../assets/uploads/" . $product['image']);
                }

                $newImageName = uniqid('', true) . "." . $fileActualExt;
                if (move_uploaded_file($imageTmp, '../../assets/uploads/' . $newImageName)) {
                    $finalImageName = $newImageName;
                } else {
                    $error = "Failed to upload new image.";
                }
            }
        }

        if (empty($error)) {
            $updateStmt = $conn->prepare("UPDATE products SET productName=?, description=?, price=?, image=?, vendorID=? WHERE productID=?");
            $updateStmt->bind_param("ssisii", $productName, $description, $price, $finalImageName, $vendorID, $id);

            if ($updateStmt->execute()) {
                header("Location: ../manage_product.php");
                exit;
            } else {
                $error = "Database Error: " . $conn->error;
            }
        }
    }
}

include '../../utils/header.php';
?>

<div class="auth-page">
    <div class="auth-card wide product-box">
        <h2 style="text-align: left;" class="auth-title">Edit Product</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label class="form-label">Product Name</label>
                <input type="text" name="productName" required 
                       value="<?= htmlspecialchars($product['productName']) ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <label class="form-label">Price (IDR)</label>
                    <input type="number" name="price" min="1" required 
                           value="<?= htmlspecialchars($product['price']) ?>">
                </div>

                <div class="form-col">
                    <label class="form-label">Vendor</label>
                    <select name="vendorID" required>
                        <option value="" disabled>Select Vendor</option>
                        <?php while($v = $vendors->fetch_assoc()): ?>
                            <option value="<?= $v['vendorID'] ?>" 
                                <?= ($product['vendorID'] == $v['vendorID']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($v['vendorName']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Product Image (Leave blank to keep current)</label>
                
                <input type="file" name="image" id="imageInput" accept=".jpg, .jpeg, .png" class="file-input">
                
                <div class="image-preview-box">
                    <span class="preview-note">Current / New Preview:</span>
                    <img id="imagePreview" src="../../assets/uploads/<?= htmlspecialchars($product['image']) ?>" alt="Preview" class="preview-img">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-full">Update Product</button>
            </div>

        </form>
    </div>
</div>

<?php include '../../utils/footer.php'; ?>