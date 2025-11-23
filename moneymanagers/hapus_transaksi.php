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

if ($transaksi) {
    $jumlah = $transaksi['jumlah'];
    

    mysqli_query($conn, "DELETE FROM $table WHERE id = '$id' AND user_id = '$user_id'");
    

    if ($tipe == 'pemasukan') {
        mysqli_query($conn, "UPDATE saldo SET total_saldo = total_saldo - $jumlah WHERE user_id = '$user_id'");
    } else {
        mysqli_query($conn, "UPDATE saldo SET total_saldo = total_saldo + $jumlah WHERE user_id = '$user_id'");
    }
    
    echo "<script>
        alert('Transaksi berhasil dihapus!');
        window.location.href = 'index.php';
    </script>";
} else {
    echo "<script>
        alert('Transaksi tidak ditemukan!');
        window.location.href = 'index.php';
    </script>";
}
?>