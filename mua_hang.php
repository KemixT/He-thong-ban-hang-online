<?php
session_start();
$conn = new mysqli("localhost", "root", "", "quan_ly_kho");
mysqli_set_charset($conn, "utf8");

// --- BƯỚC 1: LOGIC TÍNH TỔNG SỐ LƯỢNG TRONG GIỎ HÀNG ---
$total_items = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $total_items += $qty;
    }
}

// 1. LẤY CÁC THAM SỐ TỪ URL
$search = isset($_GET['query']) ? $_GET['query'] : "";
$sort = isset($_GET['sort']) ? $_GET['sort'] : "newest";
$category = isset($_GET['category']) ? $_GET['category'] : "";

$order_sql = "san_pham.id_sp DESC"; 
if($sort == "price_asc") $order_sql = "san_pham.gia_tien ASC";
if($sort == "price_desc") $order_sql = "san_pham.gia_tien DESC";

$where_clauses = [];
if ($search != "") {
    $where_clauses[] = "(san_pham.ten_sp LIKE '%$search%' OR san_pham.ma_san_pham LIKE '%$search%')";
}
if ($category != "") {
    $where_clauses[] = "danh_muc.ten_danh_muc = '$category'";
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = " WHERE " . implode(" AND ", $where_clauses);
}

$sql = "SELECT san_pham.*, danh_muc.ten_danh_muc 
        FROM san_pham 
        LEFT JOIN danh_muc ON san_pham.ma_danh_muc = danh_muc.id_danh_muc 
        $where_sql 
        ORDER BY $order_sql";
$result = $conn->query($sql);

// Lấy tên hiển thị
$name_to_show = "";
if (isset($_SESSION['ho_ten'])) {
    $name_to_show = $_SESSION['ho_ten'];
} elseif (isset($_SESSION['user_admin'])) {
    $name_to_show = $_SESSION['user_admin'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>ElectroHub - Mua Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .navbar { background: white; border-bottom: 1px solid #ddd; padding: 15px 0; }
        .search-box { position: relative; width: 40%; }
        .search-btn { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none; color: #007bff; cursor: pointer; }
        .category-title { font-weight: bold; font-size: 16px; margin-bottom: 15px; color: #333; }
        .category-list { list-style: none; padding: 0; }
        .category-item { padding: 8px 0; border-bottom: 1px solid #eee; }
        .category-link { text-decoration: none; color: #333; font-size: 14px; }
        .category-link:hover, .category-link.active { color: #ee4d2d; font-weight: bold; }
        .sort-bar { background: #f0f0f0; padding: 10px 20px; border-radius: 4px; display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
        .sort-btn { background: white; border: 1px solid #ddd; padding: 6px 12px; font-size: 14px; border-radius: 2px; text-decoration: none; color: #333; }
        .sort-btn.active { background: #ee4d2d; color: white; border-color: #ee4d2d; }
        
        .cart-wrapper { position: relative; display: inline-block; color: #666; text-decoration: none; }
        .cart-badge { 
            position: absolute; top: -8px; right: -12px; 
            background: #ff4757; color: white; 
            border-radius: 50%; padding: 2px 6px; 
            font-size: 11px; font-weight: bold; border: 2px solid white;
        }
        .user-info { font-size: 13px; line-height: 1.2; text-align: right; }

        .qty-input-group { display: flex; align-items: center; justify-content: center; gap: 5px; margin-bottom: 10px; }
        .btn-qty { width: 30px; height: 30px; padding: 0; line-height: 1; border: 1px solid #ddd; background: #fff; border-radius: 4px; }
        .input-qty { width: 45px; height: 30px; text-align: center; border: 1px solid #ddd; border-radius: 4px; font-weight: bold; }
        
        .btn-login-main { background: #007bff; color: white; border-radius: 20px; font-weight: bold; font-size: 13px; padding: 5px 15px; text-decoration: none; transition: 0.3s; }
        .btn-login-main:hover { background: #0056b3; color: white; }
    </style>
</head>
<body>

<nav class="navbar sticky-top mb-4 shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand fw-bold text-primary fs-3" href="mua_hang.php">ElectroHub</a>
        
        <form class="search-box" action="mua_hang.php" method="GET">
            <input type="text" name="query" class="form-control rounded-pill pe-5" 
                   placeholder="Bạn cần tìm linh kiện gì?" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="search-btn"><i class="fa fa-search fs-5"></i></button>
        </form>

        <div class="d-flex align-items-center">
            <div class="me-4">
                <?php if ($name_to_show != ""): ?>
                    <div class="dropdown">
                        <a class="text-dark dropdown-toggle fw-bold text-decoration-none" href="#" data-bs-toggle="dropdown" style="font-size: 14px;">
                            Chào, <?php echo $name_to_show; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item text-danger" href="dang_xuat.php"><i class="fa fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="dang_nhap.php" class="btn-login-main"><i class="fa fa-user me-1"></i> ĐĂNG NHẬP</a>
                <?php endif; ?>
            </div>

            <a href="<?php echo isset($_SESSION['user_admin']) ? 'giohang.php' : 'dang_nhap.php'; ?>" class="cart-wrapper" onclick="<?php if(!isset($_SESSION['user_admin'])) echo "alert('Vui lòng đăng nhập để xem giỏ hàng!');"; ?>">
                <i class="fa fa-shopping-cart fs-3 text-secondary"></i>
                <span id="cart-count" class="cart-badge" style="<?php echo ($total_items > 0) ? '' : 'display:none;'; ?>">
                    <?php echo $total_items; ?>
                </span>
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-3 col-lg-2">
            <div class="category-title"><i class="fa fa-list me-2"></i> DANH MỤC</div>
            <ul class="category-list">
                <li class="category-item">
                    <a href="mua_hang.php" class="category-link <?php echo ($category == '') ? 'active' : ''; ?>">Tất cả</a>
                </li>
                <?php
                $res_dm = $conn->query("SELECT * FROM danh_muc");
                while($dm = $res_dm->fetch_assoc()):
                    $ten_dm = $dm['ten_danh_muc'];
                ?>
                <li class="category-item">
                    <a href="?category=<?php echo urlencode($ten_dm); ?>&query=<?php echo urlencode($search); ?>" 
                       class="category-link <?php echo ($category == $ten_dm) ? 'active' : ''; ?>">
                        <?php echo $ten_dm; ?>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <div class="col-md-9 col-lg-10">
            <div class="sort-bar">
                <span style="font-size:13px; color: #666;">Sắp xếp:</span>
                <a href="?sort=newest&query=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>" class="sort-btn <?php echo ($sort == 'newest') ? 'active' : ''; ?>">Mới nhất</a>
                <a href="?sort=price_asc&query=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>" class="sort-btn <?php echo ($sort == 'price_asc') ? 'active' : ''; ?>">Giá thấp</a>
                <a href="?sort=price_desc&query=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>" class="sort-btn <?php echo ($sort == 'price_desc') ? 'active' : ''; ?>">Giá cao</a>
            </div>

            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm" style="border-radius:15px; overflow: hidden;">
                                <a href="chi_tiet_sp.php?id=<?php echo $row['id_sp']; ?>">
                                    <img src="uploads/<?php echo $row['hinh_anh']; ?>" class="card-img-top" 
                                         style="height:170px; object-fit:cover;" 
                                         onerror="this.src='https://via.placeholder.com/200'">
                                </a>
                                
                                <div class="card-body text-center">
                                    <h6 class="fw-bold mb-1" style="font-size: 15px;">
                                        <a href="chi_tiet_sp.php?id=<?php echo $row['id_sp']; ?>" style="text-decoration: none; color: inherit;">
                                            <?php echo $row['ten_sp']; ?>
                                        </a>
                                    </h6>
                                    
                                    <p class="text-danger fw-bold mb-2"><?php echo number_format($row['gia_tien']); ?>đ</p>
                                    
                                    <div class="qty-input-group">
                                        <button type="button" class="btn-qty" onclick="changeQty(<?php echo $row['id_sp']; ?>, -1)">-</button>
                                        <input type="text" id="qty_<?php echo $row['id_sp']; ?>" value="1" readonly class="input-qty">
                                        <button type="button" class="btn-qty" onclick="changeQty(<?php echo $row['id_sp']; ?>, 1)">+</button>
                                    </div>

                                    <button onclick="addToCart(<?php echo $row['id_sp']; ?>)" class="btn btn-sm btn-primary rounded-pill px-3 w-100 fw-bold">
                                        <i class="fa fa-cart-plus me-1"></i> Thêm vào giỏ
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Không tìm thấy sản phẩm nào.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function changeQty(id, delta) {
    let input = document.getElementById('qty_' + id);
    let val = parseInt(input.value) + delta;
    if (val >= 1) input.value = val;
}

function addToCart(id) {
    <?php if (!isset($_SESSION['user_admin'])): ?>
        alert("Ông giáo ơi! Phải đăng nhập thì mới mua hàng được nhé.");
        window.location.href = 'dang_nhap.php';
        return;
    <?php endif; ?>

    let inputQty = document.getElementById('qty_' + id);
    let quantity = parseInt(inputQty.value);
    
    fetch('xuly_giohang.php?id=' + id + '&quantity=' + quantity)
    .then(response => response.json())
    .then(data => {
        if(data.status === 'error') {
            alert("❌ THẤT BẠI: " + data.message);
        } else {
            let badge = document.getElementById('cart-count');
            if(badge) {
                badge.innerText = data.totalItems;
                badge.style.display = 'inline-block';
            }
            alert("✅ Đã thêm vào giỏ hàng thành công!");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Có lỗi kết nối hệ thống.");
    });
}
</script>
</body>
</html>