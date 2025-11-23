<?php
include "db.php";


$cek_reset = mysqli_query($conn, "
    SELECT * FROM reset_log 
    WHERE bulan = MONTH(NOW()) AND tahun = YEAR(NOW())
");

if (mysqli_num_rows($cek_reset) == 0) {

    mysqli_query($conn, "UPDATE saldo SET total_saldo = 0");
    mysqli_query($conn, "DELETE FROM pemasukan");
    mysqli_query($conn, "DELETE FROM pengeluaran");
    

    mysqli_query($conn, "
        INSERT INTO reset_log (bulan, tahun, tanggal_reset) 
        VALUES (MONTH(NOW()), YEAR(NOW()), NOW())
    ");
    
    echo "Auto reset executed for " . date('F Y');
}
?>