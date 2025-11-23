<?php
session_start();
include "db.php";

$error = "";

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    
    if(mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if ($password == $user['password']) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];

            if (isset($user['username'])) {
                $_SESSION['username'] = $user['username'];
            } else if (isset($user['name'])) {
                $_SESSION['username'] = $user['name'];
            } else if (isset($user['full_name'])) {
                $_SESSION['username'] = $user['full_name'];
            } else {

                $_SESSION['username'] = explode('@', $user['email'])[0];
            }
            
            header("Location: index.php");
            exit();
        } else {
            $error = "Email/Pw salah!";
        }
    } else {
        $error = "Email/Pw salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style/style.css">
    <style>
        .error-message {
            color: red;
            background: #ffe6e6;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #ff9999;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <form action="" method="post">
            <h4>Login</h4>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" required placeholder="enter email" class="box" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" required placeholder="enter password" class="box">
            </div>
            
            <input type="submit" name="submit" class="btn" value="Access My Account">
        </form>
    </div>
</body>
</html>