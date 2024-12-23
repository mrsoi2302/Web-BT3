<?php
session_start();
include 'db.php';

$error = "";
$success = "";

// Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Xử lý thêm nhân viên
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $role = $_POST['role'];

    if (empty($username) || empty($password) || empty($name)) {
        $error = "Vui lòng điền đầy đủ thông tin nhân viên.";
    } else {
        // Kiểm tra xem tên đăng nhập đã tồn tại chưa
        $sql_check = "SELECT * FROM users WHERE username = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $error = "Tên đăng nhập đã tồn tại.";
        } else {
            // Thêm nhân viên
            $sql = "INSERT INTO users (username, password, name, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $username, $password, $name, $role);

            if ($stmt->execute()) {
                $success = "Thêm nhân viên thành công!";
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại.";
            }
        }
    }
}

// Lấy danh sách nhân viên
$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý nhân viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'menu.php'; ?>
    <div class="container mt-5">
        <h2 class="text-center">Quản lý nhân viên</h2>

        <!-- Hiển thị thông báo -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" class="mb-3">
            <div class="row">
                <div class="col">
                    <input type="text" name="username" class="form-control" placeholder="Tên đăng nhập" required>
                </div>
                <div class="col">
                    <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
                </div>
                <div class="col">
                    <input type="text" name="name" class="form-control" placeholder="Tên đầy đủ" required>
                </div>
                <div class="col">
                    <select name="role" class="form-select" required>
                        <option value="employee">Nhân viên</option>
                        <option value="admin">Quản trị viên</option>
                    </select>
                </div>
                <div class="col">
                    <button type="submit" name="add_user" class="btn btn-primary">Thêm nhân viên</button>
                </div>
            </div>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên đăng nhập</th>
                    <th>Tên đầy đủ</th>
                    <th>Vai trò</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= $user['username'] ?></td>
                        <td><?= $user['name'] ?></td>
                        <td><?= $user['role'] ?></td>
                        <td><a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>