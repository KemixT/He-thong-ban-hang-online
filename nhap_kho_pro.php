<?php
session_start();
if (!isset($_SESSION['user_admin'])) { header("Location: dang_nhap.php"); exit(); }

$conn = new mysqli("localhost", "root", "", "quan_ly_kho");
mysqli_set_charset($conn, "utf8");

if (isset($_POST['btn_nhap_hang'])) {
    $id_sp = $_POST['id_sp'];
    $sl_nhap = (int)$_POST['so_luong'];
    $gia_nhap = $_POST['gia_nhap'];
    $ncc = $conn->real_escape_string($_POST['ncc']);
    $nguoi_nhap = $_SESSION['user_admin'];
    $tong_tien = $sl_nhap * $gia_nhap;

    // BẮT ĐẦU XỬ LÝ 3 BƯỚC:
    
    // Bước A: Lưu vào bảng phieu_nhap
    $sql_pn = "INSERT INTO phieu_nhap (nguoi_nhap, nha_cung_cap, tong_tien_nhap) 
               VALUES ('$nguoi_nhap', '$ncc', '$tong_tien')";
    $conn->query($sql_pn);
    $id_pn_vua_tao = $conn->insert_id; // Lấy ID của phiếu vừa chèn

    // Bước B: Lưu vào chi tiết phiếu nhập
    $sql_ct = "INSERT INTO chi_tiet_pn (id_pn, id_sp, so_luong, gia_nhap) 
               VALUES ('$id_pn_vua_tao', '$id_sp', '$sl_nhap', '$gia_nhap')";
    $conn->query($sql_ct);

    // Bước C: Cập nhật số lượng vào bảng san_pham (Kho tự nhảy số)
    $sql_update_kho = "UPDATE san_pham SET so_luong_kho = so_luong_kho + $sl_nhap WHERE id_sp = $id_sp";
    
    if ($conn->query($sql_update_kho)) {
        echo "<script>alert('Nhập kho thành công! Kho đã tự động cập nhật.'); window.location.href='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhập kho nâng cao</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
<div class="container bg-white p-4 shadow rounded" style="max-width: 600px;">
    <h3 class="text-center text-primary mb-4">MẪU PHIẾU NHẬP KHO</h3>
    <form method="POST">
        <div class="mb-3">
            <label class="fw-bold">Chọn sản phẩm nhập:</label>
            <select name="id_sp" class="form-select" required>
                <?php
                $sps = $conn->query("SELECT id_sp, ten_sp FROM san_pham");
                while($row = $sps->fetch_assoc()) {
                    echo "<option value='".$row['id_sp']."'>".$row['ten_sp']."</option>";
                }
                ?>
            </select>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="fw-bold">Số lượng nhập:</label>
                <input type="number" name="so_luong" class="form-control" required min="1">
            </div>
            <div class="col-md-6 mb-3">
                <label class="fw-bold">Giá nhập (VNĐ):</label>
                <input type="number" name="gia_nhap" class="form-control" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="fw-bold">Nhà cung cấp:</label>
            <input type="text" name="ncc" class="form-control" placeholder="Tên công ty hoặc người giao">
        </div>
        <div class="d-grid gap-2">
            <button name="btn_nhap_hang" class="btn btn-success fw-bold">XÁC NHẬN NHẬP KHO</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>
</body>
</html>