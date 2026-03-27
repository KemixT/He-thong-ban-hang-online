<?php
include 'db_connect.php'; // Giả sử ông đã tách file kết nối
$res = $conn->query("SELECT * FROM nha_cung_cap");
?>
<div class="container mt-4">
    <h3>DANH SÁCH NHÀ CUNG CẤP</h3>
    <table class="table table-striped border">
        <thead class="table-dark">
            <tr>
                <th>Mã NCC</th>
                <th>Tên Nhà Cung Cấp</th>
                <th>Địa chỉ</th>
                <th>Số điện thoại</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $res->fetch_assoc()): ?>
            <tr>
                <td>NCC00<?php echo $row['id_ncc']; ?></td>
                <td><?php echo $row['ten_ncc']; ?></td>
                <td><?php echo $row['dia_chi']; ?></td>
                <td><?php echo $row['so_dien_thoai']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>