<?php
session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_name'])) {
    header("Location: dang_nhap.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin đặt hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow p-4 border-0" style="border-radius: 20px;">
                <h3 class="text-center fw-bold mb-4">THÔNG TIN GIAO HÀNG</h3>
                <form action="hoan_tat.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Họ và tên khách hàng:</label>
                        <input type="text" name="ho_ten" class="form-control" required placeholder="Ví dụ: Nguyễn Văn A">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại:</label>
                        <input type="number" name="sdt" class="form-control" required placeholder="Để chúng tôi liên hệ giao hàng">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ nhận hàng:</label>
                        <textarea name="dia_chi" class="form-control" rows="3" required placeholder="Số nhà, tên đường, phường/xã..."></textarea>
                    </div>
                    <hr>
                    <div class="alert alert-info small">
                        Lưu ý: Sau khi xác nhận, hệ thống sẽ hiện mã QR để bạn thanh toán đơn hàng này.
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg shadow">XÁC NHẬN & HIỆN MÃ QR</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>