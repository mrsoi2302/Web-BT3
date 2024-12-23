<?php
session_start();
include 'db.php';

$error = "";
$success = "";

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Lấy danh sách nhà cung cấp
$suppliers = $conn->query("SELECT * FROM suppliers");

// Xử lý tạo đơn nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'];
    $user_id = $_SESSION['user']['id'];
    $items = $_POST['items'];

    if (empty($supplier_id)) {
        $error = "Vui lòng chọn nhà cung cấp.";
    } elseif (empty($items) || count(array_filter($items, fn($item) => !empty($item['quantity']))) === 0) {
        $error = "Vui lòng thêm ít nhất một sản phẩm vào đơn nhập.";
    } else {
        // Tạo đơn nhập
        $sql = "INSERT INTO orders (supplier_id, user_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $supplier_id, $user_id);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        // Thêm sản phẩm vào chi tiết đơn nhập
        $total = 0;
        foreach ($items as $product_id => $item) {
            if (!empty($item['quantity'])) {
                $quantity = $item['quantity'];
                $price = $item['price'];
                $total += $quantity * $price;

                $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
                $stmt->execute();
            }
        }

        // Cập nhật tổng tiền đơn nhập
        $conn->query("UPDATE orders SET total = $total WHERE id = $order_id");
        $success = "Tạo đơn nhập thành công!";
        header("Location order.php");
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo đơn nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php include 'menu.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center">Tạo đơn nhập</h2>

        <!-- Hiển thị thông báo -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <!-- Form chọn nhà cung cấp -->
        <form method="POST">
            <div class="mb-3">
                <label for="supplier" class="form-label">Nhà cung cấp</label>
                <select name="supplier_id" id="supplier" class="form-select" required>
                    <option value="">Chọn nhà cung cấp</option>
                    <?php while ($supplier = $suppliers->fetch_assoc()): ?>
                        <option value="<?= $supplier['id'] ?>"><?= $supplier['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Danh sách sản phẩm -->
            <div id="product-list" class="mt-4">
                <h5>Danh sách sản phẩm</h5>
                <p>Vui lòng chọn nhà cung cấp trước để hiển thị sản phẩm.</p>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Tạo đơn nhập</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Khi chọn nhà cung cấp
            $('#supplier').change(function() {
                const supplierId = $(this).val();

                if (supplierId) {
                    // Gửi AJAX để lấy danh sách sản phẩm
                    $.ajax({
                        url: 'get_products.php',
                        type: 'GET',
                        data: {
                            supplier_id: supplierId
                        },
                        success: function(response) {
                            $('#product-list').html(response);
                        },
                        error: function() {
                            $('#product-list').html('<p class="text-danger">Không thể tải danh sách sản phẩm. Vui lòng thử lại.</p>');
                        }
                    });
                } else {
                    $('#product-list').html('<p>Vui lòng chọn nhà cung cấp trước để hiển thị sản phẩm.</p>');
                }
            });
        });
    </script>
</body>

</html>