<?php
session_start();
include("config.php");

// Check if admin is already logged in
if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_panel.php");
    exit;
}

// Process login form
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Query to check admin credentials
    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        if(password_verify($password, $admin['password'])) {
            // Set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            // Redirect to admin panel
            header("Location: admin_panel.php");
            exit;
        } else {
            $error_message = "Yanlış şifre!";
        }
    } else {
        $error_message = "Kullanıcı adı bulunamadı!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>miHav - Admin Girişi</title>
    <link rel="stylesheet" href="admin_login_style.css">
</head>
<body>
    <div class="container">
        <div class="login-form">
            <h1>miHav</h1>
            <h2>Admin Girişi</h2>
            
            <?php if(isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form method="post" action="admin_login.php">
                <div class="form-group">
                    <label for="username">Admin Kullanıcı Adı</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Şifre</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Giriş Yap</button>
            </form>
            
            <p><a href="index.php">Ana Sayfaya Dön</a></p>
        </div>
    </div>
</body>
</html>