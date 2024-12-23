<?php
session_start();
include 'db.php';

$error = ""; // Biến chứa thông báo lỗi
$success = ""; // Biến chứa thông báo thành công

// Xử lý thêm sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $supplier_id = $_POST['supplier_id'];

    if (empty($name) || empty($price) || empty($stock) || empty($supplier_id)) {
        $error = "Vui lòng điền đầy đủ thông tin sản phẩm.";
    } elseif ($price <= 0 || $stock <= 0) {
        $error = "Giá và số lượng phải lớn hơn 0.";
    } else {
        // Kiểm tra sản phẩm đã tồn tại
        $sql_check = "SELECT id, stock FROM products WHERE name = ? AND supplier_id = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("si", $name, $supplier_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            // Sản phẩm đã tồn tại -> Cập nhật stock
            $product = $result_check->fetch_assoc();
            $new_stock = $product['stock'] + $stock;

            $sql_update = "UPDATE products SET stock = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ii", $new_stock, $product['id']);

            if ($stmt_update->execute()) {
                $success = "Sản phẩm đã tồn tại, số lượng đã được cập nhật!";
            } else {
                $error = "Có lỗi xảy ra khi cập nhật sản phẩm.";
            }
        } else {
            // Sản phẩm chưa tồn tại -> Thêm mới
            $sql_insert = "INSERT INTO products (name, price, stock, supplier_id) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("sdii", $name, $price, $stock, $supplier_id);

            if ($stmt_insert->execute()) {
                $success = "Thêm sản phẩm mới thành công!";
                header("Location: product.php");
            } else {
                $error = "Có lỗi xảy ra khi thêm sản phẩm mới.";
            }
        }
    }
}

// Lấy danh sách sản phẩm và nhà cung cấp
$products = $conn->query("SELECT products.*, suppliers.name AS supplier_name FROM products JOIN suppliers ON products.supplier_id = suppliers.id");
$suppliers = $conn->query("SELECT * FROM suppliers");
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'menu.php'; ?>
    <div class="container mt-5">
        <h2 class="text-center">Quản lý sản phẩm</h2>

        <!-- Hiển thị thông báo -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="mb-3">
            <div class="row">
                <div class="col">
                    <input type="text" name="name" class="form-control" placeholder="Tên sản phẩm" required>
                </div>
                <div class="col">
                    <input type="number" name="price" class="form-control" placeholder="Giá sản phẩm" step="0.01" required>
                </div>
                <div class="col">
                    <input type="number" name="stock" class="form-control" placeholder="Số lượng" required>
                </div>
                <div class="col">
                    <select name="supplier_id" class="form-select" required>
                        <option value="">Chọn nhà cung cấp</option>
                        <?php while ($supplier = $suppliers->fetch_assoc()): ?>
                            <option value="<?= $supplier['id'] ?>"><?= $supplier['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col">
                    <button type="submit" name="add" class="btn btn-primary">Thêm sản phẩm</button>
                </div>
            </div>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Nhà cung cấp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= $product['name'] ?></td>
                        <td><?= $product['price'] ?></td>
                        <td><?= $product['stock'] ?></td>
                        <td><?= $product['supplier_name'] ?></td>
                        <td>
                            <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>