<?php
$conn = new mysqli("localhost", "root", "", "quan_ly_kho");
if (isset($_POST['btn_submit'])) {
    $id = $_POST['id_sp'];
    $sl_moi = $_POST['sl_thay_doi'];
    $loai = $_POST['loai_hinh']; // 'nhap' hoặc 'xuat'

    if ($loai == 'nhap') {
        $conn->query("UPDATE san_pham SET so_luong_kho = so_luong_kho + $sl_moi WHERE id_sp = $id");
    } else {
        $conn->query("UPDATE san_pham SET so_luong_kho = so_luong_kho - $sl_moi WHERE id_sp = $id");
    }
    header("Location: index.php");
}
?>
<form method="POST" style="width:300px; margin:50px auto; font-family:Arial; border:1px solid #ccc; padding:20px;">
    <h3>NHẬP / XUẤT KHO</h3>
    Sản phẩm:
    <select name="id_sp" style="width:100%; margin-bottom:10px;">
        <?php
        $sps = $conn->query("SELECT * FROM san_pham");
        while($r = $sps->fetch_assoc()) echo "<option value='".$r['id_sp']."'>".$r['ten_sp']."</option>";
        ?>
    </select>
    Loại hình:
    <select name="loai_hinh" style="width:100%; margin-bottom:10px;">
        <option value="nhap">Nhập kho (+)</option>
        <option value="xuat">Xuất kho (-)</option>
    </select>
    Số lượng thay đổi:
    <input type="number" name="sl_thay_doi" required style="width:100%; margin-bottom:15px;">
    <button name="btn_submit" type="submit" style="width:100%; background:blue; color:white; padding:10px; border:none;">Xác nhận</button>
</form>