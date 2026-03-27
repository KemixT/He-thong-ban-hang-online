<?php
$conn = new mysqli("localhost", "root", "", "quan_ly_kho");
$result = $conn->query("SELECT * FROM san_pham");
?>
<div style="font-family: Arial; padding: 20px;">
    <h2>📝 KIỂM KÊ KHO THỰC TẾ</h2>
    <table border="1" cellspacing="0" cellpadding="10" style="width: 100%; border-collapse: collapse;">
        <tr style="background: #eee;">
            <th>Tên sản phẩm</th>
            <th>Số lượng trên máy</th>
            <th>Số lượng thực tế (Bạn đếm)</th>
            <th>Chênh lệch</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['ten_sp']; ?></td>
            <td id="may_<?php echo $row['id_sp']; ?>"><?php echo $row['so_luong_kho']; ?></td>
            <td>
                <input type="number" oninput="tinhLech(<?php echo $row['id_sp']; ?>, this.value)" placeholder="Nhập số đếm được">
            </td>
            <td id="lech_<?php echo $row['id_sp']; ?>" style="font-weight: bold;">0</td>
        </tr>
        <?php endwhile; ?>
    </table>
    <br><a href="index.php">Quay lại trang chủ</a>
</div>

<script>
function tinhLech(id, val) {
    let trenMay = parseInt(document.getElementById('may_' + id).innerText);
    let thucTe = parseInt(val) || 0;
    let ketQua = thucTe - trenMay;
    let oLech = document.getElementById('lech_' + id);
    
    oLech.innerText = (ketQua > 0 ? "+" : "") + ketQua;
    oLech.style.color = (ketQua === 0) ? "green" : "red";
}
</script>