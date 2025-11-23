<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$tipe = $_GET['tipe'];
$id = $_GET['id'];

if ($tipe == 'pemasukan') {
    $table = 'pemasukan';
} else {
    $table = 'pengeluaran';
}

$query = mysqli_query($conn, "SELECT * FROM $table WHERE id = '$id' AND user_id = '$user_id'");
$transaksi = mysqli_fetch_assoc($query);

if (!$transaksi) {
    echo "<script>alert('Transaksi tidak ditemukan!'); window.location='index.php';</script>";
    exit();
}

if (isset($_POST['update'])) {
    $jumlah_baru = $_POST['jumlah'];
    $keterangan_baru = $_POST['keterangan'];
    

    $jumlah_lama = $transaksi['jumlah'];
    

    mysqli_query($conn, "
        UPDATE $table 
        SET jumlah = '$jumlah_baru', keterangan = '$keterangan_baru' 
        WHERE id = '$id' AND user_id = '$user_id'
    ");
    

    $selisih = $jumlah_baru - $jumlah_lama;
    
    if ($tipe == 'pemasukan') {
        mysqli_query($conn, "UPDATE saldo SET total_saldo = total_saldo + $selisih WHERE user_id = '$user_id'");
    } else {
        mysqli_query($conn, "UPDATE saldo SET total_saldo = total_saldo - $selisih WHERE user_id = '$user_id'");
    }
    
    echo "<script>
        alert('Transaksi berhasil diupdate!');
        window.location.href = 'index.php';
    </script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Transaksi</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div class="form-container">
        <h2>✏️ Edit Transaksi <?php echo ucfirst($tipe); ?></h2>
        <form method="post">
            <div class="form-group">
                <label>Jumlah:</label>
                <input type="number" name="jumlah" value="<?php echo $transaksi['jumlah']; ?>" required>
            </div>
            <div class="form-group">
                <label>Keterangan:</label>
                <input type="text" name="keterangan" value="<?php echo htmlspecialchars($transaksi['keterangan']); ?>" required>
            </div>
            <button type="submit" name="update" class="btn">Update Transaksi</button>
        </form>
        <a href="index.php" class="back-link">Kembali ke Dashboard</a>
    </div>
</body>
</html>