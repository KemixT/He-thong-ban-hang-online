<?php
session_start();
if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
    header("Location: mua_hang.php"); exit();
}
$conn = new mysqli("localhost", "root", "", "quan_ly_kho");
mysqli_set_charset($conn, "utf8");

$tu_ngay = isset($_GET['tu_ngay']) ? $_GET['tu_ngay'] : date('Y-m-01'); 
$den_ngay = isset($_GET['den_ngay']) ? $_GET['den_ngay'] : date('Y-m-d');

$sql = "SELECT hoa_don.*, nguoi_dung.ho_ten 
        FROM hoa_don 
        LEFT JOIN nguoi_dung ON hoa_don.id_nguoi_dung = nguoi_dung.id_nd 
        WHERE DATE(ngay_thanh_toan) BETWEEN '$tu_ngay' AND '$den_ngay' 
        ORDER BY ngay_thanh_toan DESC"; 
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo doanh thu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container bg-white p-4 shadow rounded">
        <h2 class="text-center text-primary mb-4">LỊCH SỬ BÁN HÀNG</h2>
        <form class="row g-3 mb-4" method="GET">
            <div class="col-md-4"><label>Từ ngày:</label><input type="date" name="tu_ngay" class="form-control" value="<?php echo $tu_ngay; ?>"></div>
            <div class="col-md-4"><label>Đến ngày:</label><input type="date" name="den_ngay" class="form-control" value="<?php echo $den_ngay; ?>"></div>
            <div class="col-md-4 mt-auto"><button type="submit" class="btn btn-primary w-100">LỌC DỮ LIỆU</button></div>
        </form>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Ngày bán</th>
                    <th>Người mua</th>
                    <th>Sản phẩm</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $tong = 0;
                while($row = $result->fetch_assoc()): 
                    $tt = $row['gia_ban'] * $row['so_luong_mua'];
                    $tong += $tt;
                ?>
                <tr>
                    <td><?php echo date('d/m/H:i', strtotime($row['ngay_thanh_toan'])); ?></td>
                    <td><?php echo $row['ho_ten'] ? $row['ho_ten'] : 'Khách vãng lai'; ?></td>
                    <td><?php echo $row['ten_sp']; ?> (x<?php echo $row['so_luong_mua']; ?>)</td>
                    <td class="fw-bold text-danger"><?php echo number_format($tt); ?>đ</td>
                </tr>
                <?php endwhile; ?>
                <tr class="table-warning fw-bold">
                    <td colspan="3" class="text-end">TỔNG DOANH THU:</td>
                    <td class="text-danger"><?php echo number_format($tong); ?>đ</td>
                </tr>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-secondary">Quay lại</a>
    </div>
</body>
</html>