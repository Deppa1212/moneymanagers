<?php
session_start();
include "db.php";

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $query = "INSERT INTO users (username, email, password)
              VALUES ('$username', '$email', '$password')";

    if (mysqli_query($conn, $query)) {
        

        $user_id = mysqli_insert_id($conn);
        mysqli_query($conn, "INSERT INTO saldo (user_id, total_saldo) VALUES ($user_id, 0)");

        echo "Registrasi berhasil!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>


