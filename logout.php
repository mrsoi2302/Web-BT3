<?php
session_start();

// Hủy session
session_unset();
session_destroy();

// Chuyển hướng về trang đăng nhập
header("Location: index.php");
exit();
?>