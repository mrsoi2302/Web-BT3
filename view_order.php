<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Lấy thông tin đơn nhập
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // Lấy thông tin chi tiết đơn nhập
    $order = $conn->query("
        SELECT o.id, o.order_date, o.total, s.name AS supplier_name, u.name AS created_by
        FROM orders o
        JOIN suppliers s ON o.supplier_id = s.id
        JOIN users u ON o.user_id = u.id
        WHERE o.id = $order_id
    ")->fetch_assoc();

    // Lấy danh sách sản phẩm trong đơn nhập
    $order_items = $conn->query("
        SELECT p.name AS product_name, oi.quantity, oi.price
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = $order_id
    ");
} else {
    header("Location: manage_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'menu.php'; ?>
    <div class="container mt-5">

        <h2 class="text-center">Chi tiết đơn nhập #<?= $order['id'] ?></h2>
        <p>Ngày tạo: <?= $order['order_date'] ?></p>
        <p>Nhà cung cấp: <?= $order['supplier_name'] ?></p>
        <p>Người tạo: <?= $order['created_by'] ?></p>
        <p>Tổng tiền: <?= number_format($order['total'], 0, ',', '.') ?> VND</p>

        <h4>Danh sách sản phẩm</h4>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Đơn giá (VND)</th>
                    <th>Thành tiền (VND)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                while ($item = $order_items->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= $item['product_name'] ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td><?= number_format($item['quantity'] * $item['price'], 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="manage_order.php" class="btn btn-secondary mt-3">Quay lại</a>
    </div>
</body>

</html>