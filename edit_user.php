<?php
session_start();
include 'db.php';

$error = "";
$success = "";

// Lấy thông tin nhân viên
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $sql_user = "SELECT * FROM users WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $user = $stmt_user->get_result()->fetch_assoc();

    if (!$user) {
        $error = "Nhân viên không tồn tại.";
    }
} else {
    exit();
}

// Xử lý cập nhật nhân viên
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $role = $_POST['role'];

    if (empty($name) || empty($role)) {
        $error = "Vui lòng điền đầy đủ thông tin.";
    } else {
        $sql_update = "UPDATE users SET name = ?, role = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $name, $role, $user_id);

        if ($stmt_update->execute()) {
            $success = "Cập nhật nhân viên thành công!";
            header("Location: manage_user.php");
        } else {
            $error = "Có lỗi xảy ra khi cập nhật nhân viên.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Cập nhật nhân viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Cập nhật nhân viên</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <button onclick="window.history.back()" class="btn btn-secondary">Quay lại</button>

        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if ($user): ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Tên đầy đủ</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= $user['name'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Vai trò</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="employee" <?= $user['role'] === 'employee' ? 'selected' : '' ?>>Nhân viên</option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="manage_user.php" class="btn btn-secondary">Quay lại</a>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>