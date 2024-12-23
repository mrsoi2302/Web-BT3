<?php
session_start();
include 'db.php';

// Kiểm tra quyền đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Lấy thống kê tổng quát
$total_suppliers = $conn->query("SELECT COUNT(*) AS total FROM suppliers")->fetch_assoc()['total'];
$total_products = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];
$total_orders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];
$total_revenue = $conn->query("SELECT SUM(total) AS revenue FROM orders")->fetch_assoc()['revenue'];

// Lấy danh sách sản phẩm có số lượng nhập nhiều nhất
$top_products = $conn->query("
    SELECT p.name, SUM(oi.quantity) AS total_quantity
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    GROUP BY oi.product_id
    ORDER BY total_quantity DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'menu.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center">Thống kê tổng quát</h2>
        <div class="row mt-4">
            <!-- Thống kê tổng quát -->
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Tổng nhà cung cấp</h5>
                        <p class="card-text display-4"><?= $total_suppliers ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Tổng sản phẩm</h5>
                        <p class="card-text display-4"><?= $total_products ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Tổng đơn nhập</h5>
                        <p class="card-text display-4"><?= $total_orders ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Tổng chi tổng</h5>
                        <p class="card-text display-4"><?= number_format($total_revenue, 0, ',', '.') ?> VND</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sản phẩm nhập nhiều nhất -->
        <div class="mt-5">
            <h4 class="text-center">Top 5 sản phẩm nhập nhiều nhất</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng nhập</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php while ($product = $top_products->fetch_assoc()): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $product['name'] ?></td>
                            <td><?= $product['total_quantity'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>