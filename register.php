<?php
// Veritabanı bağlantısı
require_once "config.php";
 
// Değişkenleri tanımla ve temizle
$username = $password = $confirm_password = $email = $city = $district = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";
 
// Form gönderildiğinde işlem yap
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Kullanıcı adını doğrula
    if(empty(trim($_POST["username"]))){
        $username_err = "Lütfen bir kullanıcı adı girin.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Kullanıcı adı sadece harf, rakam ve alt çizgi içerebilir.";
    } else{
        // Select sorgusu hazırla
        $sql = "SELECT id FROM userss WHERE username = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Parametreleri bağla
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Parametreleri ayarla
            $param_username = trim($_POST["username"]);
            
            // Sorguyu çalıştır
            if(mysqli_stmt_execute($stmt)){
                // Sonuçları sakla
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Bu kullanıcı adı zaten alınmış.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }

            // Sorguyu kapat
            mysqli_stmt_close($stmt);
        }
    }
    
    // Email doğrula
    if(empty(trim($_POST["email"]))){
        $email_err = "Lütfen email adresinizi girin.";     
    } elseif(!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)){
        $email_err = "Geçersiz email formatı.";
    } else{
        // Email benzersizlik kontrolü
        $sql = "SELECT id FROM userss WHERE email = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = trim($_POST["email"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "Bu email adresi zaten kullanılıyor.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Şifreyi doğrula
    if(empty(trim($_POST["password"]))){
        $password_err = "Lütfen bir şifre girin.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Şifre en az 6 karakter olmalıdır.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Şifre onayını doğrula
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Lütfen şifrenizi onaylayın.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Şifreler eşleşmiyor.";
        }
    }
    
    // Şehir ve ilçe değerlerini al
    $city = trim($_POST["city"]);
    $district = trim($_POST["district"]);
    
    // Hata kontrolü
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)){
        
        // Insert sorgusu hazırla
        $sql = "INSERT INTO userss (username, password, email, city, district, role, is_active) VALUES (?, ?, ?, ?, ?, 'user', 1)";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Parametreleri bağla
            mysqli_stmt_bind_param($stmt, "sssss", $param_username, $param_password, $param_email, $param_city, $param_district);
            
            // Parametreleri ayarla
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Şifreyi hash'le
            $param_email = $email;
            $param_city = $city;
            $param_district = $district;
            
            // Sorguyu çalıştır
            if(mysqli_stmt_execute($stmt)){
                // Giriş sayfasına yönlendir
                header("location: login.php");
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

 <!-- benimseme ekleme -->
<?php if (isset($listing) && isset($listing['adopted']) && $listing['adopted'] == 0): ?>
    <a href="user_adopt.php?id=<?php echo $listing['id']; ?>" class="btn btn-primary">Bu Hayvanı Sahiplenmek İstiyorum</a>
<?php elseif (isset($listing) && isset($listing['adopted'])): ?>
    <div class="adopted-notice">Bu hayvan sahiplendirilmiştir.</div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol | miHav</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            padding: 20px 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .logo {
            font-family: 'Arial Rounded MT Bold', 'Arial', sans-serif;
            color: #ff6b6b;
            margin: 0;
            font-size: 2rem;
        }
        
        .custom-navbar {
            background-color: white !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }
        
        .navbar-toggler {
            border: none;
            outline: none;
        }
        
        .nav-link {
            font-weight: 500;
            padding: 10px 15px !important;
            transition: color 0.3s ease;
        }
        
        .nav-link:hover {
            color: #ff6b6b !important;
        }
        
        .btn-mihav {
            background-color: #ff6b6b;
            color: white;
            border: none;
            padding: 10px 20px;
            transition: background-color 0.3s ease;
        }
        
        .btn-mihav:hover {
            background-color: #ff5252;
            color: white;
        }
        
        .btn-outline-mihav {
            border: 1px solid #ff6b6b;
            color: #ff6b6b;
            background-color: transparent;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-mihav:hover {
            background-color: #ff6b6b;
            color: white;
        }
        
        .form-container {
            max-width: 500px;
            margin: 30px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .form-title {
            color: #ff6b6b;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }
        
        .form-subtitle {
            text-align: center;
            margin-bottom: 30px;
            color: #6c757d;
        }
        
        .form-control {
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 5px;
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #ff6b6b;
            box-shadow: 0 0 0 0.25rem rgba(255, 107, 107, 0.25);
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .btn-submit {
            background-color: #ff6b6b;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            color: white;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            background-color: #ff5252;
            transform: translateY(-2px);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #6c757d;
        }
        
        .login-link a {
            color: #ff6b6b;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            color: #ff5252;
            text-decoration: underline;
        }
        
        .form-group-row {
            display: flex;
            gap: 15px;
        }
        
        .form-group-row .form-group {
            flex: 1;
        }
        
        .input-icon-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            right: 15px;
            color: #6c757d;
        }
        
        .form-text {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .footer {
            margin-top: auto;
            background-color: #343a40;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        
        @media (max-width: 576px) {
            .form-container {
                padding: 20px;
                margin: 20px 15px;
            }
            
            .form-group-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light custom-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <h1 class="logo">miHav</h1>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Anasayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="hakkimizda.php">Hakkımızda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="hayvanlar.php">Hayvanlar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog.php">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="iletisim.php">İletişim</a>
                    </li>
                </ul>
                
                <div class="d-flex">
                    <a href="login.php" class="btn btn-outline-mihav me-2">Giriş Yap</a>
                    <a href="ilan-ver.php" class="btn btn-mihav">Ücretsiz İlan Ver</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2 class="form-title">miHav'a Hoş Geldiniz</h2>
            <p class="form-subtitle">Kayıt olarak can dostlarına yuva bulma macerasına başlayın</p>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="username" class="form-label">Kullanıcı Adı</label>
                    <div class="input-icon-wrapper">
                        <input type="text" name="username" id="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                        <i class="fas fa-user input-icon"></i>
                    </div>
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">E-posta Adresi</label>
                    <div class="input-icon-wrapper">
                        <input type="email" name="email" id="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Şifre</label>
                    <div class="input-icon-wrapper">
                        <input type="password" name="password" id="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    <small class="form-text">En az 8 karakter, büyük-küçük harf ve rakam içermelidir.</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Şifre Onayı</label>
                    <div class="input-icon-wrapper">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                </div>
                
                <div class="form-group-row">
                    <div class="form-group">
                        <label for="city" class="form-label">Şehir</label>
                        <div class="input-icon-wrapper">
                            <input type="text" name="city" id="city" class="form-control" value="<?php echo $city; ?>">
                            <i class="fas fa-map-marker-alt input-icon"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="district" class="form-label">İlçe</label>
                        <div class="input-icon-wrapper">
                            <input type="text" name="district" id="district" class="form-control" value="<?php echo $district; ?>">
                            <i class="fas fa-map input-icon"></i>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            <a href="kosullar.php" target="_blank">Kullanıcı sözleşmesini</a> okudum ve kabul ediyorum
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-submit">Kayıt Ol</button>
                </div>
                
                <p class="login-link">Zaten bir hesabınız var mı? <a href="login.php">Giriş yapın</a></p>
            </form>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>© 2025 miHav - Tüm Hakları Saklıdır</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>