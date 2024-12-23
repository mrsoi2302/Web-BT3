<?php
include 'db.php';

// Kiểm tra tham số supplier_id
if (isset($_GET['supplier_id'])) {
    $supplier_id = $_GET['supplier_id'];

    // Lấy danh sách sản phẩm của nhà cung cấp
    $products = $conn->query("SELECT * FROM products WHERE supplier_id = $supplier_id");

    if ($products->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá (VND)</th>
                    <th>Số lượng</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= $product['name'] ?></td>
                        <td><?= number_format($product['price'], 0, ',', '.') ?></td>
                        <td>
                            <input type="number" name="items[<?= $product['id'] ?>][quantity]" class="form-control" placeholder="Số lượng">
                            <input type="hidden" name="items[<?= $product['id'] ?>][price]" value="<?= $product['price'] ?>">
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Không có sản phẩm nào thuộc nhà cung cấp này.</p>
    <?php endif;
}
?>
