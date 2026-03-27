<?php
session_start();
if (!isset($_SESSION['user_admin'])) { header("Location: dang_nhap.php"); exit(); }

$conn = new mysqli("localhost", "root", "", "quan_ly_kho");
mysqli_set_charset($conn, "utf8");

if (isset($_POST['btn_ban_hang'])) {
    $ma_hd = "HD-" . time(); // Tạo mã HD duy nhất dựa trên thời gian
    $id_khach = 0; // Tạm thời để mặc định
    $ghi_chu = $conn->real_escape_string($_POST['ghi_chu']);
    
    // Giả sử ông giáo chọn 1 sản phẩm (sau này có thể làm giỏ hàng chọn nhiều)
    $id_sp = $_POST['id_sp'];
    $sl_ban = (int)$_POST['so_luong'];
    
    // Lấy giá bán hiện tại của sản phẩm
    $sp_info = $conn->query("SELECT gia_tien, so_luong_kho FROM san_pham WHERE id_sp = $id_sp")->fetch_assoc();
    $gia_ban = $sp_info['gia_tien'];
    $ton_kho = $sp_info['so_luong_kho'];
    $tong_tien = $sl_ban * $gia_ban;

    if ($sl_ban > $ton_kho) {
        echo "<script>alert('Lỗi: Kho không đủ hàng!'); window.history.back();</script>";
        exit();
    }

    // BẮT ĐẦU TRANSACTION (Đảm bảo tất cả lệnh đều chạy hoặc không lệnh nào chạy)
    $conn->begin_transaction();

    try {
        // Bước 1: Lưu vào hoa_don_ban (Master)
        $sql_hd = "INSERT INTO hoa_don_ban (ma_hd, id_khach, tong_tien, ghi_chu) 
                   VALUES ('$ma_hd', '$id_khach', '$tong_tien', '$ghi_chu')";
        $conn->query($sql_hd);
        $id_hdb_vua_tao = $conn->insert_id;

        // Bước 2: Lưu vào chi_tiet_hdb (Detail)
        $sql_ct = "INSERT INTO chi_tiet_hdb (id_hdb, id_sp, so_luong_ban, gia_ban_luc_do) 
                   VALUES ('$id_hdb_vua_tao', '$id_sp', '$sl_ban', '$gia_ban')";
        $conn->query($sql_ct);

        // Bước 3: Trừ kho tự động
        $sql_update_kho = "UPDATE san_pham SET so_luong_kho = so_luong_kho - $sl_ban WHERE id_sp = $id_sp";
        $conn->query($sql_update_kho);

        $conn->commit(); // Hoàn tất các lệnh
        echo "<script>alert('Bán hàng thành công! Hóa đơn: $ma_hd'); window.location.href='index.php';</script>";

    } catch (Exception $e) {
        $conn->rollback(); // Nếu lỗi thì hủy hết các lệnh trên
        echo "Lỗi hệ thống: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bán hàng Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
<div class="container bg-white p-4 shadow rounded" style="max-width: 500px;">
    <h3 class="text-center text-success mb-4">MẪU BÁN HÀNG (MASTER-DETAIL)</h3>
    <form method="POST">
        <div class="mb-3">
            <label class="fw-bold">Chọn sản phẩm:</label>
            <select name="id_sp" class="form-select" required>
                <?php
                $sps = $conn->query("SELECT id_sp, ten_sp, gia_tien FROM san_pham WHERE so_luong_kho > 0");
                while($row = $sps->fetch_assoc()) {
                    echo "<option value='".$row['id_sp']."'>".$row['ten_sp']." (".number_format($row['gia_tien'])."đ)</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="fw-bold">Số lượng bán:</label>
            <input type="number" name="so_luong" class="form-control" value="1" min="1" required>
        </div>
        <div class="mb-3">
            <label class="fw-bold">Ghi chú đơn hàng:</label>
            <textarea name="ghi_chu" class="form-control" rows="2"></textarea>
        </div>
        <div class="d-grid gap-2">
            <button name="btn_ban_hang" class="btn btn-primary fw-bold">XÁC NHẬN BÁN & TRỪ KHO</button>
            <a href="index.php" class="btn btn-secondary">Quay lại trang chủ</a>
        </div>
    </form>
</div>
</body>
</html>