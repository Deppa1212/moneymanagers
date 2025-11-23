<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$current_month = date('m');
$current_year = date('Y');

$prev_month = date('m', strtotime('first day of last month'));
$prev_month_name = date('F', strtotime('first day of last month'));
$prev_year = date('Y', strtotime('first day of last month'));

$saldo_bulan_ini_query = mysqli_query($conn, "
    SELECT 
        (SELECT COALESCE(SUM(jumlah), 0) FROM pemasukan 
         WHERE user_id = '$user_id' 
         AND MONTH(tanggal) = '$current_month' 
         AND YEAR(tanggal) = '$current_year') 
        -
        (SELECT COALESCE(SUM(jumlah), 0) FROM pengeluaran 
         WHERE user_id = '$user_id' 
         AND MONTH(tanggal) = '$current_month' 
         AND YEAR(tanggal) = '$current_year') 
    AS saldo_bulan_ini
");
$saldo_data = mysqli_fetch_assoc($saldo_bulan_ini_query);
$total_saldo = $saldo_data['saldo_bulan_ini'];

$pemasukan_query = mysqli_query($conn, "
    SELECT COALESCE(SUM(jumlah), 0) as total 
    FROM pemasukan 
    WHERE user_id = '$user_id' 
    AND MONTH(tanggal) = '$current_month' 
    AND YEAR(tanggal) = '$current_year'
");
$pemasukan_data = mysqli_fetch_assoc($pemasukan_query);
$total_pemasukan = $pemasukan_data['total'];

$pengeluaran_query = mysqli_query($conn, "
    SELECT COALESCE(SUM(jumlah), 0) as total 
    FROM pengeluaran 
    WHERE user_id = '$user_id' 
    AND MONTH(tanggal) = '$current_month' 
    AND YEAR(tanggal) = '$current_year'
");
$pengeluaran_data = mysqli_fetch_assoc($pengeluaran_query);
$total_pengeluaran = $pengeluaran_data['total'];

$saldo_bulan_sebelumnya_query = mysqli_query($conn, "
    SELECT 
        (SELECT COALESCE(SUM(jumlah), 0) FROM pemasukan 
         WHERE user_id = '$user_id' 
         AND MONTH(tanggal) = '$prev_month' 
         AND YEAR(tanggal) = '$prev_year') 
        -
        (SELECT COALESCE(SUM(jumlah), 0) FROM pengeluaran 
         WHERE user_id = '$user_id' 
         AND MONTH(tanggal) = '$prev_month' 
         AND YEAR(tanggal) = '$prev_year') 
    AS saldo_bulan_sebelumnya
");
$saldo_sebelumnya_data = mysqli_fetch_assoc($saldo_bulan_sebelumnya_query);
$saldo_bulan_sebelumnya = $saldo_sebelumnya_data['saldo_bulan_sebelumnya'];

$history_query = mysqli_query($conn, "
    (SELECT 
        id,
        'pemasukan' as tipe,
        jumlah,
        keterangan,
        tanggal,
        'positive' as warna
    FROM pemasukan 
    WHERE user_id = '$user_id'
    ORDER BY tanggal DESC
    LIMIT 10)
    
    UNION ALL
    
    (SELECT 
        id,
        'pengeluaran' as tipe,
        jumlah,
        keterangan,
        tanggal,
        'negative' as warna
    FROM pengeluaran 
    WHERE user_id = '$user_id'
    ORDER BY tanggal DESC
    LIMIT 10)
    
    ORDER BY tanggal DESC
    LIMIT 15
");

if ($saldo_bulan_sebelumnya > 0) {
    $pesan_hemat = "Rp" . number_format($saldo_bulan_sebelumnya, 0, ',', '.');
    $warna_pesan = "positive";
    $icon = "💰";
} else if ($saldo_bulan_sebelumnya < 0) {
    $pesan_hemat = "Rp " . number_format(abs($saldo_bulan_sebelumnya), 0, ',', '.');
    $warna_pesan = "negative";
    $icon = "⚠️";
} else {
    $pesan_hemat = "";
    $warna_pesan = "neutral";
    $icon = "⚖️";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Keuangan</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
   <header class="navbar">
    <nav class="nav-container">
        <div class="nav-brand">
            <h4>MoneyManagers</h4>
        </div>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="index.php" class="nav-link">Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="pemasukan.php" class="nav-link">Pemasukan</a>
            </li>
            <li class="nav-item">
                <a href="pengeluaran.php" class="nav-link">Pengeluaran</a>
            </li>
        </ul>
    </nav>
   </header>
    
   <div class="login-information">
      <div class="login-container">
         <h1>Selamat Datang,
            <strong>
                <?php 
                if (isset($_SESSION['username'])) {
                    echo $_SESSION['username'];
                } else {
                    echo $_SESSION['email'];
                }
                ?>
            </strong>
         </h1>
         <p class="month-info">Laporan Keuangan <?php echo date('F Y'); ?></p>
         <div class="logout-btn">
            <a href="logout.php"><button class="btn-logout">Log Out</button></a>
         </div>
      </div>
   </div>
    
   <div class="money-menu">
        <div class="money-container">
           <div class="total-saldo">
             <h5>Saldo Bulan Ini</h5>
             <p class="amount">Rp <?php echo number_format($total_saldo, 0, ',', '.'); ?></p>
             <small><?php echo date('F Y'); ?></small>
           </div>
           <div class="pemasukan-saldo">
             <a href="pemasukan.php"><h5>Pemasukan</h5></a>
             <p class="amount positive">Rp <?php echo number_format($total_pemasukan, 0, ',', '.'); ?></p>
             <small>Bulan <?php echo date('F'); ?></small>
           </div>
           <div class="pengeluaran-saldo">
             <a href="pengeluaran.php"><h5>Pengeluaran</h5></a>
             <p class="amount negative">Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></p>
             <small>Bulan <?php echo date('F'); ?></small>
           </div>
        </div>
   </div>

   <div class="pesan-hemat">
        <div class="pesan-container <?php echo $warna_pesan; ?>">
            <h5> Saldo Bulan Lalu: </h5>
            <p class="pesan-text"><?php echo $pesan_hemat; ?></p>
            <small>Pencapaian keuangan Anda pada <?php echo $prev_month_name . ' ' . $prev_year; ?></small>
        </div>
   </div>

   <div class="history-section">
        <div class="history-container">
            <h3>Transaksi</h3>
            
            <?php if (mysqli_num_rows($history_query) > 0): ?>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while($transaksi = mysqli_fetch_assoc($history_query)): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <span class="tipe-badge <?php echo $transaksi['tipe']; ?>">
                                <?php echo $transaksi['tipe'] == 'pemasukan' ? 'Pemasukan' : 'Pengeluaran'; ?>
                            </span>
                        </td>
                        <td class="amount <?php echo $transaksi['warna']; ?>">
                            Rp <?php echo number_format($transaksi['jumlah'], 0, ',', '.'); ?>
                        </td>
                        <td><?php echo htmlspecialchars($transaksi['keterangan']); ?></td>
                        <td><?php echo date('d M Y', strtotime($transaksi['tanggal'])); ?></td>
                        <td class="aksi">
                            <a href="edit_transaksi.php?tipe=<?php echo $transaksi['tipe']; ?>&id=<?php echo $transaksi['id']; ?>" class="btn-edit">Edit</a>
                            <a href="hapus_transaksi.php?tipe=<?php echo $transaksi['tipe']; ?>&id=<?php echo $transaksi['id']; ?>" class="btn-hapus" onclick="return confirm('Yakin hapus transaksi ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data">
                <p>Belum ada transaksi. <a href="pemasukan.php">Tambah transaksi pertama Anda!</a></p>
            </div>
            <?php endif; ?>
        </div>
   </div>

</body>
</html>