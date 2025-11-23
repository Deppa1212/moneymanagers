<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];

if (isset($_POST['tambah'])) {
    $jumlah = $_POST['jumlah'];
    $ket    = $_POST['keterangan'];

    $cek_saldo = mysqli_query($conn, "SELECT * FROM saldo WHERE user_id = '$user_id'");
    if (mysqli_num_rows($cek_saldo) == 0) {
        mysqli_query($conn, "INSERT INTO saldo (user_id, total_saldo) VALUES ('$user_id', 0)");
    }
    

    $insert_pemasukan = mysqli_query($conn, "
        INSERT INTO pemasukan (user_id, jumlah, keterangan, tanggal)
        VALUES ('$user_id', '$jumlah', '$ket', NOW())
    ");
    
    if ($insert_pemasukan) {

        $update_saldo = mysqli_query($conn, "
            UPDATE saldo SET total_saldo = total_saldo + $jumlah 
            WHERE user_id = '$user_id'
        ");
        
        echo "<script>
            alert('Pemasukan berhasil ditambahkan!');
            window.location.href = 'index.php';
        </script>";
    } else {
        echo "<script>alert('Gagal menambah pemasukan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
   <title>Tambah Pemasukan</title>
<link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div class="form-container">
        <div class="pemasukan-items">
            <div class="pemasukan-items2">
                <h2>Tambah Pemasukan</h2>
                <form method="post"> 
                    <div class="form-group2">
                        <small>Jumlah:</small>
                        <input type="number" name="jumlah" placeholder="Masukkan jumlah" required>
                    </div>
                    <div class="form-group2">
                        <small>Keterangan:</small>
                        <input type="text" name="keterangan" placeholder="Masukkan keterangan" required> <!-- PERBAIKAN DI SINI -->
                    </div>
                    <button type="submit" name="tambah" class="btn">Tambah Pemasukan</button>
                </form>
                <a href="index.php" class="back-link">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>