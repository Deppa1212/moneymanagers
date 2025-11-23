<?php
// proses_pemasukan.php
session_start();

if (isset($_POST['tambah'])) {
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];
    
    // Initialize session array jika belum ada
    if (!isset($_SESSION['pemasukan'])) {
        $_SESSION['pemasukan'] = [];
    }
    
    // Tambahkan data pemasukan
    $_SESSION['pemasukan'][] = [
        'jumlah' => $jumlah,
        'keterangan' => $keterangan,
        'tanggal' => date('Y-m-d H:i:s'),
        'tipe' => 'pemasukan'
    ];
    
    header('Location: index.php');
    exit;
} else {
    // Jika tidak ada data POST, redirect ke form
    header('Location: pemasukan.php');
    exit;
}
?>