<?php 
    require_once '../config/db.php';
    require '../config/validator.php';

    isLogin();
    isAdmin();

    $message = "";
    $messageType = "";

    if (isset($_POST['delete_product'])) {
        $productID = $_POST['product_id'];
        $imageName = $_POST['image_name'];

        try {
            $stmt = $conn->prepare("DELETE FROM products WHERE productID = ?");
            $stmt->bind_param("i", $productID);

            if ($stmt->execute()) {
                if ($imageName && file_exists("assets/uploads/" . $imageName)) {
                    unlink("assets/uploads/" . $imageName);
                }
                $message = "Product deleted successfully.";
                $messageType = "alert-success";
            } else {
                throw new Exception($conn->error);
            }
        } catch (Exception $e) {
            if ($conn->errno == 1451) {
                $message = "Cannot delete product! It exists in transactions or carts.";
            } else {
                $message = "Error deleting product: " . $e->getMessage();
            }
            $messageType = "alert-error";
        }
    }

    $sql = "SELECT p.*, v.vendorName 
            FROM products p 
            JOIN vendors v ON p.vendorID = v.vendorID 
            WHERE 1=1";

    $search = $_GET['search'] ?? '';
    if (!empty($search)) {

        $safe_search = $conn->real_escape_string($search);
   
        $sql .= " AND p.productName LIKE '%$safe_search%'";
    }

    $filterVendor = $_GET['vendor'] ?? '';
    if (!empty($filterVendor)) {
        $safe_vendor = $conn->real_escape_string($filterVendor);
        $sql .= " AND p.vendorID = '$safe_vendor'";
    }


    $sql .= " ORDER BY p.productID DESC";


    $products = $conn->query($sql); 


    $vendors = $conn->query("SELECT * FROM vendors");

    include '../utils/header.php';
?>

<div class="container" style="margin-top: 40px;">
    
    <h1 class="page-title">Manage Products</h1>

    <?php if ($message): ?>
        <div class="alert <?= $messageType ?>"><?= $message ?></div>
    <?php endif; ?>

    <div class="header-actions">
        <form method="GET" class="toolbar-form">
            <div class="toolbar-left">
                <div class="search-group">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search product name...">
                </div>

                <div class="filter-group">
                    <select name="vendor" onchange="this.form.submit()">
                        <option value="">All Vendors</option>
                        <?php while($v = $vendors->fetch_assoc()): ?>
                            <option value="<?= $v['vendorID'] ?>" <?= ($filterVendor == $v['vendorID']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($v['vendorName']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
        </form>

        <div class="header-actions-rights">
            <a href="add/product.php" class="btn btn-adds">+ Add Product</a >
        </div>
    </div>

    <table class="data-table compact">
        <thead>
            <tr>
                <th style="width: 64px;">Image</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Vendor</th>
                <th style="text-align: left;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($products->num_rows > 0): ?>
                <?php while ($row = $products->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <img src="../assets/uploads/<?= htmlspecialchars($row['image']) ?>" alt="Img" class="product-thumb">
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($row['productName']) ?></strong>
                            <!-- <span class="product-desc"><?= substr(htmlspecialchars($row['description']), 0, 50) ?>...</span> -->
                        </td>
                        <td class="price-tag">
                            Rp <?= number_format($row['price'], 0, ',', '.') ?>
                        </td>
                        <td><?= htmlspecialchars($row['vendorName']) ?></td>
                        <td style="text-align: left;" class="action-linksz">
                            
                            <a href="edit/product.php?id=<?= $row['productID'] ?>" class="text-edit">Edit</a>
                            
                            <div class="action-linkszz">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?= $row['productID'] ?>">
                                    <input type="hidden" name="image_name" value="<?= $row['image'] ?>">
                                    <button type="submit" name="delete_product" class="text-delete btn-delete-confirm">Delete</button>
                                </form>
                            </div>

                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="empty-state">No products found matching your criteria.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<?php include '../utils/footer.php'; ?>