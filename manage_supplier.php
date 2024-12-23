<?php
session_start();
include 'db.php';

$error = "";
$success = "";

// Thêm nhà cung cấp
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_supplier'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    if (empty($name) || empty($phone) || empty($address)) {
        $error = "Vui lòng điền đầy đủ thông tin nhà cung cấp.";
    } else {
        $sql = "INSERT INTO suppliers (name, phone, address) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $phone, $address);

        if ($stmt->execute()) {
            $success = "Thêm nhà cung cấp thành công!";
            header("Location: supplier.php");
        } else {
            $error = "Có lỗi xảy ra khi thêm nhà cung cấp.";
        }
    }
}

// Xóa nhà cung cấp
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM suppliers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success = "Xóa nhà cung cấp thành công!";
        header("Location: supplier.php");
    } else {
        $error = "Không thể xóa nhà cung cấp vì liên quan đến dữ liệu khác.";
    }
}

// Lấy danh sách nhà cung cấp
$suppliers = $conn->query("SELECT * FROM suppliers");
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý nhà cung cấp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'menu.php'; ?>
    <div class="container mt-5">
        <h2 class="text-center">Quản lý nhà cung cấp</h2>

        <!-- Hiển thị thông báo -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <!-- Form thêm nhà cung cấp -->
        <form method="POST" action="" class="mb-4">
            <div class="row">
                <div class="col">
                    <input type="text" name="name" class="form-control" placeholder="Tên nhà cung cấp" required>
                </div>
                <div class="col">
                    <input type="text" name="phone" class="form-control" placeholder="Số điện thoại" required>
                </div>
                <div class="col">
                    <input type="text" name="address" class="form-control" placeholder="Địa chỉ" required>
                </div>
                <div class="col">
                    <button type="submit" name="add_supplier" class="btn btn-primary">Thêm</button>
                </div>
            </div>
        </form>

        <!-- Bảng danh sách nhà cung cấp -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên</th>
                    <th>Số điện thoại</th>
                    <th>Địa chỉ</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($supplier = $suppliers->fetch_assoc()): ?>
                    <tr>
                        <td><?= $supplier['id'] ?></td>
                        <td><?= $supplier['name'] ?></td>
                        <td><?= $supplier['phone'] ?></td>
                        <td><?= $supplier['address'] ?></td>
                        <td>
                            <a href="edit_supplier.php?id=<?= $supplier['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                            <a href="supplier.php?delete=<?= $supplier['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa nhà cung cấp này?')">Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>