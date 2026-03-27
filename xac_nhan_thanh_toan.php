<?php
session_start();
$conn = new mysqli("localhost", "root", "", "quan_ly_kho");
mysqli_set_charset($conn, "utf8");

if (empty($_SESSION['cart'])) { header("Location: mua_hang.php"); exit(); }

$tong_cong = 0;
foreach ($_SESSION['cart'] as $id => $sl) {
    $res = $conn->query("SELECT gia_tien FROM san_pham WHERE id_sp = $id");
    if ($res && $sp = $res->fetch_assoc()) $tong_cong += ($sp['gia_tien'] * $sl);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background: #121212; color: white; padding: 40px; }</style>
</head>
<body>
    <div class="container border p-4 rounded" style="max-width: 500px; background: #1e1e1e;">
        <h3 class="text-center text-danger mb-4">XÁC NHẬN THANH TOÁN</h3>
        
        <form action="xuly_giohang.php" method="POST">
    <input type="hidden" name="action" value="xac_nhan_tru_kho">
    
    <div class="mb-3">
        <label>Họ tên:</label>
        <input type="text" name="ho_ten" class="form-control bg-dark text-white" required>
    </div>
    <div class="mb-3">
        <label>Số điện thoại:</label>
        <input type="text" name="sdt" class="form-control bg-dark text-white" required>
    </div>

    <button type="submit" class="btn btn-danger w-100 py-3 fw-bold">XÁC NHẬN & TRỪ KHO</button>
</form>
    </div>
</body>
</html>