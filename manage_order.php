<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Xóa đơn nhập
if (isset($_GET['delete'])) {
    $order_id = $_GET['delete'];

    // Xóa các sản phẩm liên quan trong order_items
    $conn->query("DELETE FROM order_items WHERE order_id = $order_id");

    // Xóa đơn nhập
    if ($conn->query("DELETE FROM orders WHERE id = $order_id")) {
        $success = "Xóa đơn nhập thành công!";
    } else {
        $error = "Có lỗi xảy ra khi xóa đơn nhập.";
    }
}

// Lấy danh sách đơn nhập
$orders = $conn->query("
    SELECT o.id, o.order_date, o.total, s.name AS supplier_name, u.name AS created_by
    FROM orders o
    JOIN suppliers s ON o.supplier_id = s.id
    JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
");
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'menu.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center">Quản lý đơn nhập</h2>

        <!-- Hiển thị thông báo -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <div>
            <a href="order.php" class="btn btn-primary">Tạo đơn nhập</a>
        </div>
        <!-- Bảng danh sách đơn nhập -->
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ngày tạo</th>
                    <th>Nhà cung cấp</th>
                    <th>Người tạo</th>
                    <th>Tổng tiền (VND)</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= $order['order_date'] ?></td>
                        <td><?= $order['supplier_name'] ?></td>
                        <td><?= $order['created_by'] ?></td>
                        <td><?= number_format($order['total'], 0, ',', '.') ?></td>
                        <td>
                            <a href="view_order.php?id=<?= $order['id'] ?>" class="btn btn-info btn-sm">Xem chi tiết</a>
                            <a href="manage_orders.php?delete=<?= $order['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn nhập này?')">Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>