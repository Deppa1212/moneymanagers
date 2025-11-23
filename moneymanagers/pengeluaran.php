<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];

if (isset($_POST['tambah'])) {
    $jumlah = $_POST['jumlah'];
    $ket    = $_POST['keterangan'];
    
    $saldo_query = mysqli_query($conn, "SELECT total_saldo FROM saldo WHERE user_id = '$user_id'");
    
    if ($saldo_query && mysqli_num_rows($saldo_query) > 0) {
        $saldo_data = mysqli_fetch_assoc($saldo_query);
        $saldo_sekarang = $saldo_data['total_saldo'];
        
        if ($saldo_sekarang >= $jumlah) {

            $insert_pengeluaran = mysqli_query($conn, "
                INSERT INTO pengeluaran (user_id, jumlah, keterangan, tanggal)
                VALUES ('$user_id', '$jumlah', '$ket', NOW())
            ");
            
            if ($insert_pengeluaran) {

                $update_saldo = mysqli_query($conn, "
                    UPDATE saldo SET total_saldo = total_saldo - $jumlah 
                    WHERE user_id = '$user_id'
                ");
                
                echo "<script>
                    alert('Pengeluaran berhasil ditambahkan!');
                    window.location.href = 'index.php';
                </script>";
            } else {
                echo "<script>alert('Gagal menambah pengeluaran!');</script>";
            }
        } else {
            echo "<script>alert('Saldo tidak cukup! Saldo Anda: Rp " . number_format($saldo_sekarang, 0, ',', '.') . "');</script>";
        }
    } else {

        mysqli_query($conn, "INSERT INTO saldo (user_id, total_saldo) VALUES ('$user_id', 0)");
        echo "<script>alert('Data saldo belum ada. Silakan coba lagi.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Pengeluaran</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
   <div class="form-container">
        <div class="pemasukan-items">
            <div class="pemasukan-items2">
                <h2>Tambah Pengeluaran</h2>
                <form method="post">
                    <div class="form-group2">
                        <small>Jumlah:</small>
                        <input type="number" name="jumlah" placeholder="Masukkan jumlah" required>
                    </div>
                    <div class="form-group2">
                        <small>Keterangan:</small>
                        <input type="text" name="keterangan" placeholder="Masukkan keterangan" required>
                    </div>
                    <button type="submit" name="tambah" class="btn">Tambah Pengeluaran</button>
                </form>
                <a href="index.php" class="back-link">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>