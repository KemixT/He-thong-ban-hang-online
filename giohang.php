<?php
session_start();
// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "quan_ly_kho");
mysqli_set_charset($conn, "utf8");

// 1. XỬ LÝ LOGIC (Tăng/Giảm/Xóa)
if (isset($_GET['action'])) {
    $id = $_GET['id'];
    if ($_GET['action'] == 'delete') {
        unset($_SESSION['cart'][$id]);
    } elseif ($_GET['action'] == 'update') {
        $type = $_GET['type'];
        if ($type == 'plus') {
            $_SESSION['cart'][$id]++;
        } elseif ($type == 'minus' && $_SESSION['cart'][$id] > 1) {
            $_SESSION['cart'][$id]--;
        }
    }
    header("Location: giohang.php"); // Làm mới trang để cập nhật số liệu
    exit();
}

$tong_cong = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng - ElectroHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; color: #333; font-family: 'Segoe UI', Arial, sans-serif; }
        .container { margin-top: 40px; max-width: 1000px; }
        .cart-card { background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 30px; }
        .table thead { background-color: #f1f1f1; }
        .table td { vertical-align: middle; padding: 20px 15px; border-color: #eee; }
        .qty-box { display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-qty { 
            width: 32px; height: 32px; border: 1px solid #ddd; background: white; 
            display: flex; align-items: center; justify-content: center;
            color: #333; border-radius: 4px; text-decoration: none;
        }
        .btn-qty:hover { background: #f8f9fa; }
        .btn-checkout { 
            background-color: #ff4d2d; color: white; border: none; 
            padding: 12px 45px; font-weight: bold; border-radius: 4px;
            transition: 0.3s; text-transform: uppercase; text-decoration: none; display: inline-block;
        }
        .btn-checkout:hover { background-color: #e63e1c; color: white; transform: translateY(-2px); }
        .text-orange { color: #ff4d2d; }
        .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; margin-right: 15px; border: 1px solid #eee; }
    </style>
</head>
<body>

<div class="container">
    <div class="cart-card">
        <h2 class="mb-4 fw-bold">GIỎ HÀNG <i class="fa fa-shopping-bag text-orange"></i></h2>
        <a href="mua_hang.php" class="mb-4 d-inline-block text-muted text-decoration-none"><i class="fa fa-arrow-left"></i> Tiếp tục mua sắm</a>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr class="text-center text-uppercase">
                        <th class="text-start">Sản phẩm</th>
                        <th>Giá bán</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <?php foreach ($_SESSION['cart'] as $id => $sl): 
                            $res = $conn->query("SELECT * FROM san_pham WHERE id_sp = $id");
                            if($res && $sp = $res->fetch_assoc()):
                                $thanh_tien = $sp['gia_tien'] * $sl;
                                $tong_cong += $thanh_tien;
                        ?>
                        <tr class="text-center">
                            <td class="text-start">
                                <div class="d-flex align-items-center">
                                    <img src="uploads/<?php echo $sp['hinh_anh']; ?>" class="product-img" onerror="this.src='https://via.placeholder.com/60'">
                                    <span class="fw-semibold"><?php echo $sp['ten_sp']; ?></span>
                                </div>
                            </td>
                            <td class="text-muted"><?php echo number_format($sp['gia_tien'], 0, ',', '.'); ?> đ</td>
                            <td>
                                <div class="qty-box">
                                    <a href="?action=update&type=minus&id=<?php echo $id; ?>" class="btn-qty">-</a>
                                    <span class="fw-bold"><?php echo $sl; ?></span>
                                    <a href="?action=update&type=plus&id=<?php echo $id; ?>" class="btn-qty">+</a>
                                </div>
                            </td>
                            <td class="fw-bold text-dark"><?php echo number_format($thanh_tien, 0, ',', '.'); ?> đ</td>
                            <td>
                                <a href="?action=delete&id=<?php echo $id; ?>" class="btn btn-sm text-danger text-decoration-none" onclick="return confirm('Xóa sản phẩm này?')">
                                    <i class="fa fa-trash-can"></i> Xóa
                                </a>
                            </td>
                        </tr>
                        <?php endif; endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <p class="text-muted">Giỏ hàng trống trơn!</p>
                                <a href="mua_hang.php" class="btn btn-outline-dark btn-sm">Mua hàng ngay</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($tong_cong > 0): ?>
        <div class="row mt-5">
            <div class="col-md-12 text-end">
                <p class="mb-1 text-muted">Tổng cộng thanh toán:</p>
                <h3 class="fw-bold text-orange mb-4"><?php echo number_format($tong_cong, 0, ',', '.'); ?> VNĐ</h3>
                
                <a href="khach_hang_thanh_toan.php" class="btn btn-checkout shadow-sm">
                    THANH TOÁN NGAY <i class="fa fa-chevron-right ms-2"></i>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>