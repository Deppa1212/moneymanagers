<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn, "SELECT total_saldo FROM saldo WHERE user_id=$user_id");
$data   = mysqli_fetch_assoc($result);

echo "Saldo Anda: Rp " . number_format($data['total_saldo'], 0, ',', '.');
?>
