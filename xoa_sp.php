<?php
session_start();
// Chỉ Admin mới được xóa
if (!isset($_SESSION['user_admin']) || $_SESSION['vai_tro'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "quan_ly_kho");
if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM san_pham WHERE id_sp = $id");
}

// ĐỒNG BỘ: Xóa xong quay về trang chủ bán hàng
header("Location: index.php");
exit();
?>