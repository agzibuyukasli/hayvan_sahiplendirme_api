<?php

session_start();


ini_set('display_errors', 1);
error_reporting(E_ALL);


 
// Kullanıcı zaten giriş yapmışsa ana sayfaya yönlendir
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}
 
// Veritabanı bağlantısı
require_once "config.php";
 
// Değişkenleri tanımla ve temizle
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Form gönderildiğinde işlem yap
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Kullanıcı adını kontrol et
    if(empty(trim($_POST["username"]))){
        $username_err = "Lütfen kullanıcı adınızı girin.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Şifreyi kontrol et
    if(empty(trim($_POST["password"]))){
        $password_err = "Lütfen şifrenizi girin.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Giriş bilgilerini doğrula
    if(empty($username_err) && empty($password_err)){
        // Select sorgusu hazırla
        $sql = "SELECT id, username, password, role FROM userss WHERE username = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Parametreleri bağla
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Parametreleri ayarla
            $param_username = $username;
            
            // Sorguyu çalıştır
            if(mysqli_stmt_execute($stmt)){
                // Sonuçları sakla
                mysqli_stmt_store_result($stmt);
                
                // Kullanıcı adı var mı kontrol et
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Değişkenleri bağla
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $role);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Şifre doğruysa oturum başlat
                            session_start();
                            
                            // Oturum değişkenlerini ayarla
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role"] = $role;                            
                            
                            // Ana sayfaya yönlendir
                            header("Location: index.php");
                            exit;
                        } else{
                            // Şifre yanlışsa hata mesajı göster
                            $login_err = "Geçersiz kullanıcı adı veya şifre.";
                        }
                    }
                } else{
                    // Kullanıcı adı yoksa hata mesajı göster
                    $login_err = "Geçersiz kullanıcı adı veya şifre.";
                }
            } else{
                echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }

            // Sorguyu kapat
            mysqli_stmt_close($stmt);
        }
    }
    
    // Veritabanı bağlantısını kapat
    mysqli_close($conn);
}
?>
 
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş | Hayvan Sahiplendirme</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            display: flex;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 24 24' fill='%23dedede'%3E%3Cpath d='M8.5,2C9.33,2 10,2.67 10,3.5C10,4.33 9.33,5 8.5,5C7.67,5 7,4.33 7,3.5C7,2.67 7.67,2 8.5,2M14.5,2C15.33,2 16,2.67 16,3.5C16,4.33 15.33,5 14.5,5C13.67,5 13,4.33 13,3.5C13,2.67 13.67,2 14.5,2M7,7.5C7,6.67 7.67,6 8.5,6C9.33,6 10,6.67 10,7.5C10,8.33 9.33,9 8.5,9C7.67,9 7,8.33 7,7.5M15,7.5C15,6.67 15.67,6 16.5,6C17.33,6 18,6.67 18,7.5C18,8.33 17.33,9 16.5,9C15.67,9 15,8.33 15,7.5M11,11.5C11,10.67 11.67,10 12.5,10C13.33,10 14,10.67 14,11.5C14,12.33 13.33,13 12.5,13C11.67,13 11,12.33 11,11.5M12.5,15C13.33,15 14,15.67 14,16.5C14,17.33 13.33,18 12.5,18C11.67,18 11,17.33 11,16.5C11,15.67 11.67,15 12.5,15M16.5,11C17.33,11 18,11.67 18,12.5C18,13.33 17.33,14 16.5,14C15.67,14 15,13.33 15,12.5C15,11.67 15.67,11 16.5,11M6.5,11C7.33,11 8,11.67 8,12.5C8,13.33 7.33,14 6.5,14C5.67,14 5,13.33 5,12.5C5,11.67 5.67,11 6.5,11M8.5,15C9.33,15 10,15.67 10,16.5C10,17.33 9.33,18 8.5,18C7.67,18 7,17.33 7,16.5C7,15.67 7.67,15 8.5,15M16.5,15C17.33,15 18,15.67 18,16.5C18,17.33 17.33,18 16.5,18C15.67,18 15,17.33 15,16.5C15,15.67 15.67,15 16.5,15M12.5,7C13.33,7 14,7.67 14,8.5C14,9.33 13.33,10 12.5,10C11.67,10 11,9.33 11,8.5C11,7.67 11.67,7 12.5,7Z'/%3E%3C/svg%3E");
            background-repeat: repeat;
            background-size: 60px;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        
        .wrapper {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 400px;
            max-width: 90%;
            position: relative;
            z-index: 1;
        }
        
        h2 {
            color: #77A649;
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .paw-icon {
            color: #8862b8;
            font-size: 1.2rem;
            margin-left: 5px;
        }
        
        .text-center {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .is-invalid {
            border-color: #dc3545;
        }
        
        .invalid-feedback {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #77A649;
            color: white;
            width: 100%;
            padding: 12px;
        }
        
        .btn-primary:hover {
            background-color: #658f3e;
        }
        
        .btn-warning {
            background-color: #CD7F32;
            color: white;
            text-decoration: none;
            padding: 10px;
            text-align: center;
            margin-top: 15px;
            display: block;
        }
        
        .btn-warning:hover {
            background-color: #b06b2a;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .register-link a {
            color: #77A649;
            text-decoration: none;
            font-weight: 500;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>miHav</h2>
        <p class="text-center">Giriş yaparak devam edin</p>

        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Kullanıcı Adı</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Şifre</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Giriş Yap"><br><br>
              

            </div>
            <!-- BURAYA BAKKKKKKKKKKKK!!!!!!!!!!!!!!!!!!!!!!!!!!! -->
            <!-- <button class="btn btn-secondary" onclick="location.href='admin_login.php';">Admin Girişi</button> -->

            <p class="register-link">Hesabınız yok mu? <a href="register.php">Şimdi Kaydolun</a>.</p>
        </form>
        <a href="admin_login.php" class="btn btn-warning mt-3" style="display:block; text-align:center;">Admin Girişi</a>
    </div>
</body>
</html>