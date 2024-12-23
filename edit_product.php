<?php
session_start();
include 'db.php';

$error = "";
$success = "";

// Lấy thông tin sản phẩm qua ID
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $sql_product = "SELECT * FROM products WHERE id = ?";
    $stmt_product = $conn->prepare($sql_product);
    $stmt_product->bind_param("i", $product_id);
    $stmt_product->execute();
    $product = $stmt_product->get_result()->fetch_assoc();

    if (!$product) {
        $error = "Sản phẩm không tồn tại.";
    }
} else {
    exit();
}

// Xử lý cập nhật sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $supplier_id = $_POST['supplier_id'];

    if (empty($name) || empty($price) || empty($stock) || empty($supplier_id)) {
        $error = "Vui lòng điền đầy đủ thông tin.";
    } else {
        $sql_update = "UPDATE products SET name = ?, price = ?, stock = ?, supplier_id = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sdiii", $name, $price, $stock, $supplier_id, $product_id);

        if ($stmt_update->execute()) {
            $success = "Cập nhật sản phẩm thành công!";
        } else {
            $error = "Có lỗi xảy ra khi cập nhật sản phẩm.";
        }
    }
}

// Lấy danh sách nhà cung cấp (để chọn nhà cung cấp)
$suppliers = $conn->query("SELECT * FROM suppliers");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Cập nhật sản phẩm</h2>

    <!-- Hiển thị thông báo -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <button onclick="window.history.back()" class="btn btn-secondary">Quay lại</button>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($product): ?>
        <!-- Form cập nhật sản phẩm -->
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Tên sản phẩm</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= $product['name'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Giá (VND)</label>
                <input type="number" id="price" name="price" class="form-control" value="<?= $product['price'] ?>" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Số lượng</label>
                <input type="number" id="stock" name="stock" class="form-control" value="<?= $product['stock'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="supplier_id" class="form-label">Nhà cung cấp</label>
                <select id="supplier_id" name="supplier_id" class="form-select" required>
                    <?php while ($supplier = $suppliers->fetch_assoc()): ?>
                        <option value="<?= $supplier['id'] ?>" <?= $supplier['id'] == $product['supplier_id'] ? 'selected' : '' ?>>
                            <?= $supplier['name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="manage_product.php" class="btn btn-secondary">Quay lại</a>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
