<?php
// Bật báo lỗi để biết chính xác tại sao trắng trang
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("localhost", "root", "", "quan_ly_kho");

if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}

$sql = "SELECT * FROM san_pham ORDER BY id_sp DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>ElectroHub - Cửa hàng</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 20px; }
        .san-pham-container { display: flex; flex-wrap: wrap; gap: 20px; }
        .the-san-pham { background: white; border-radius: 10px; padding: 15px; width: 250px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .the-san-pham img { width: 100%; height: 200px; object-fit: cover; border-radius: 8px; }
        .gia { color: #6366f1; font-weight: bold; font-size: 20px; }
    </style>
</head>
<body>
    <h1>SẢN PHẨM MỚI NHẤT</h1>
    <div class="san-pham-container">
        <?php 
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $image = !empty($row['hinh_anh']) ? "uploads/".$row['hinh_anh'] : "https://via.placeholder.com/250";
                echo "<div class='the-san-pham'>";
                echo "<img src='$image'>";
                echo "<h3>" . $row['ten_sp'] . "</h3>";
                echo "<p class='gia'>" . number_format($row['gia_tien']) . "đ</p>";
                echo "<p>Số lượng: " . ($row['so_luong'] ?? 0) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<h2>Admin chưa có hàng hoặc Database đang trống!</h2>";
        }
        ?>
    </div>
</body>
</html>