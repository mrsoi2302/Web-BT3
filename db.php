<?php
// db.php
$host = 'localhost';
$user = 'root';
$password = '';
$db_name = 'world';

$conn = new mysqli($host, $user, $password, $db_name);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
