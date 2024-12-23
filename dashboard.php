<?php
session_start();
include 'db.php';

// Kiểm tra quyền đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Lấy dữ liệu tổng chi theo tháng
$report = $conn->query("
    SELECT 
        DATE_FORMAT(order_date, '%Y-%m') AS month, 
        SUM(total) AS revenue
    FROM orders
    GROUP BY DATE_FORMAT(order_date, '%Y-%m')
    ORDER BY month DESC
");
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo tổng chi theo tháng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include 'menu.php'; ?>

    <div class="container mt-5">

        <h2 class="text-center">Báo cáo tổng chi theo tháng</h2>

        <!-- Bảng tổng chi -->
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Tháng</th>
                    <th>Tổng chi (VND)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $months = [];
                $revenues = [];
                while ($row = $report->fetch_assoc()):
                    $months[] = $row['month'];
                    $revenues[] = $row['revenue'];
                ?>
                    <tr>
                        <td><?= $row['month'] ?></td>
                        <td><?= number_format($row['revenue'], 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Biểu đồ tổng chi -->
        <canvas id="revenueChart" height="100"></canvas>
    </div>

    <script>
        // Lấy dữ liệu từ PHP
        const months = <?= json_encode($months) ?>;
        const revenues = <?= json_encode($revenues) ?>;

        // Vẽ biểu đồ
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Tổng chi (VND)',
                    data: revenues,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Tổng chi theo tháng'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>