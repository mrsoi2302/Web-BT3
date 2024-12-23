<?php
session_start();
include 'db.php';

$error = "";
$success = "";

// Lấy thông tin nhà cung cấp
if (isset($_GET['id'])) {
    $supplier_id = $_GET['id'];
    $sql_supplier = "SELECT * FROM suppliers WHERE id = ?";
    $stmt_supplier = $conn->prepare($sql_supplier);
    $stmt_supplier->bind_param("i", $supplier_id);
    $stmt_supplier->execute();
    $supplier = $stmt_supplier->get_result()->fetch_assoc();

    if (!$supplier) {
        $error = "Nhà cung cấp không tồn tại.";
    }
} else {
    exit();
}

// Xử lý cập nhật nhà cung cấp
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    if (empty($name) || empty($phone) || empty($address)) {
        $error = "Vui lòng điền đầy đủ thông tin.";
    } else {
        $sql_update = "UPDATE suppliers SET name = ?, phone = ?, address = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $name, $phone, $address, $supplier_id);

        if ($stmt_update->execute()) {
            $success = "Cập nhật nhà cung cấp thành công!";
        } else {
            $error = "Có lỗi xảy ra khi cập nhật nhà cung cấp.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Cập nhật nhà cung cấp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Cập nhật nhà cung cấp</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <button onclick="window.history.back()" class="btn btn-secondary">Quay lại</button>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if ($supplier): ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Tên nhà cung cấp</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= $supplier['name'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?= $supplier['phone'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <input type="text" id="address" name="address" class="form-control" value="<?= $supplier['address'] ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="manage_supplier.php" class="btn btn-secondary">Quay lại</a>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>