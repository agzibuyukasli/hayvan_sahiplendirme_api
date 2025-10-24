<?php
// Oturum kontrolü
require_once "session.php";
require_once "config.php";

// Değişkenler
$username = $email = $phone = "";
$username_err = $email_err = $phone_err = $password_err = $current_password_err = "";
$success_message = "";

// Kullanıcı bilgilerini çek
$sql = "SELECT username, email FROM userss WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $username, $email);
            mysqli_stmt_fetch($stmt);
        }
    }
    
    mysqli_stmt_close($stmt);
}

// Form işleme
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Profil bilgileri güncelleme
    if (isset($_POST["update_profile"])) {
        
        // Kullanıcı adını doğrula
        if (empty(trim($_POST["username"]))) {
            $username_err = "Lütfen kullanıcı adı girin.";
        } else {
            // Kullanıcı adı başka biri tarafından kullanılıyor mu kontrol et
            $check_sql = "SELECT id FROM userss WHERE username = ? AND id != ?";
            $check_stmt = mysqli_prepare($conn, $check_sql);
            
            if ($check_stmt) {
                $param_username = trim($_POST["username"]);
                mysqli_stmt_bind_param($check_stmt, "si", $param_username, $_SESSION["id"]);
                
                if (mysqli_stmt_execute($check_stmt)) {
                    mysqli_stmt_store_result($check_stmt);
                    
                    if (mysqli_stmt_num_rows($check_stmt) > 0) {
                        $username_err = "Bu kullanıcı adı zaten alınmış.";
                    } else {
                        $username = trim($_POST["username"]);
                    }
                }
                
                mysqli_stmt_close($check_stmt);
            }
        }
        
        // Email adresini doğrula
        if (empty(trim($_POST["email"]))) {
            $email_err = "Lütfen e-posta adresinizi girin.";
        } else {
            // E-posta formatını kontrol et
            if (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
                $email_err = "Geçersiz e-posta formatı.";
            } else {
                // E-posta başka biri tarafından kullanılıyor mu kontrol et
                $check_sql = "SELECT id FROM userss WHERE email = ? AND id != ?";
                $check_stmt = mysqli_prepare($conn, $check_sql);
                
                if ($check_stmt) {
                    $param_email = trim($_POST["email"]);
                    mysqli_stmt_bind_param($check_stmt, "si", $param_email, $_SESSION["id"]);
                    
                    if (mysqli_stmt_execute($check_stmt)) {
                        mysqli_stmt_store_result($check_stmt);
                        
                        if (mysqli_stmt_num_rows($check_stmt) > 0) {
                            $email_err = "Bu e-posta adresi zaten kullanılıyor.";
                        } else {
                            $email = trim($_POST["email"]);
                        }
                    }
                    
                    mysqli_stmt_close($check_stmt);
                }
            }
        }
        
        // Telefon numarasını doğrula (opsiyonel)
        if (!empty(trim($_POST["phone"]))) {
            // Basit bir telefon formatı kontrolü
            if (!preg_match("/^[0-9]{10,11}$/", trim($_POST["phone"]))) {
                $phone_err = "Geçersiz telefon numarası formatı. Lütfen sadece rakam kullanın (10-11 haneli).";
            } else {
                $phone = trim($_POST["phone"]);
            }
        } else {
            $phone = ""; // Telefon boş bırakılabilir
        }
        
        // Hata kontrolü
        if (empty($username_err) && empty($email_err) && empty($phone_err)) {
            // Profil bilgilerini güncelle
            $query = "UPDATE userss SET username = ?, email = ?, phone_number = ? WHERE id = ?";


            $update_stmt = mysqli_prepare($conn, $update_sql);
            
            if ($update_stmt) {
                mysqli_stmt_bind_param($update_stmt, "sssi", $username, $email, $phone, $_SESSION["id"]);
                
                if (mysqli_stmt_execute($update_stmt)) {
                    $success_message = "Profil bilgileriniz başarıyla güncellendi.";
                } else {
                    echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
                }
                
                mysqli_stmt_close($update_stmt);
            }
        }
    }
    
    // Şifre değiştirme
    if (isset($_POST["change_password"])) {
        // Mevcut şifreyi doğrula
        if (empty(trim($_POST["current_password"]))) {
            $current_password_err = "Lütfen mevcut şifrenizi girin.";
        } else {
            // Mevcut şifrenin doğruluğunu kontrol et
            $check_sql = "SELECT password FROM userss WHERE id = ?";
            $check_stmt = mysqli_prepare($conn, $check_sql);
            
            if ($check_stmt) {
                mysqli_stmt_bind_param($check_stmt, "i", $_SESSION["id"]);
                
                if (mysqli_stmt_execute($check_stmt)) {
                    mysqli_stmt_store_result($check_stmt);
                    
                    if (mysqli_stmt_num_rows($check_stmt) == 1) {
                        mysqli_stmt_bind_result($check_stmt, $hashed_password);
                        if (mysqli_stmt_fetch($check_stmt)) {
                            if (!password_verify(trim($_POST["current_password"]), $hashed_password)) {
                                $current_password_err = "Girdiğiniz şifre hatalı.";
                            }
                        }
                    }
                }
                
                mysqli_stmt_close($check_stmt);
            }
        }
        
        // Yeni şifreyi doğrula
        if (empty(trim($_POST["new_password"]))) {
            $password_err = "Lütfen yeni şifrenizi girin.";
        } elseif (strlen(trim($_POST["new_password"])) < 6) {
            $password_err = "Şifre en az 6 karakter olmalıdır.";
        }
        
        // Şifre onayını doğrula
        if (empty(trim($_POST["confirm_password"]))) {
            $password_err = "Lütfen şifre onayını girin.";
        } else {
            if (trim($_POST["new_password"]) != trim($_POST["confirm_password"])) {
                $password_err = "Şifreler eşleşmiyor.";
            }
        }
        
        // Hata kontrolü
        if (empty($current_password_err) && empty($password_err)) {
            // Şifreyi güncelle
            $update_sql = "UPDATE userss SET password = ? WHERE id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            
            if ($update_stmt) {
                $param_password = password_hash(trim($_POST["new_password"]), PASSWORD_DEFAULT);
                mysqli_stmt_bind_param($update_stmt, "si", $param_password, $_SESSION["id"]);
                
                if (mysqli_stmt_execute($update_stmt)) {
                    $success_message = "Şifreniz başarıyla güncellendi.";
                } else {
                    echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
                }
                
                mysqli_stmt_close($update_stmt);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profil | Hayvan Sahiplendirme</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body { 
            font: 14px sans-serif; 
            padding: 20px;
        }
        .wrapper{ 
            max-width: 1200px; 
            margin: 0 auto;
        }
        .form-card {
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        h2 {
            color: #28a745;
            margin-bottom: 30px;
        }
        .navbar {
            margin-bottom: 30px;
            background-color: #28a745;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <a class="navbar-brand" href="index.php">Hayvan Sahiplendirme</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tum_ilanlar.php">Tüm İlanlar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ilan_ekle.php">İlan Ekle</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ilanlarim.php">İlanlarım</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sahiplenmelerim.php">Sahiplenmelerim</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="profile.php">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Çıkış Yap</a>
                    </li>
                </ul>
            </div>
        </nav>
        
        <div class="container">
            <h2>Profil Bilgilerim</h2>
            
            <?php if(!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <div class="row">
                <!-- Profil Bilgileri Güncelleme -->
                <div class="col-md-6">
                    <div class="card form-card">
                        <div class="card-header">
                            <h5>Profil Bilgilerimi Güncelle</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="form-group">
                                    <label>Kullanıcı Adı</label>
                                    <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <label>E-posta Adresi</label>
                                    <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <label>Telefon Numarası <small class="text-muted">(İsteğe bağlı)</small></label>
                                    <input type="text" name="phone" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $phone; ?>" placeholder="Örn: 5551234567">
                                    <span class="invalid-feedback"><?php echo $phone_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="update_profile" value="1">
                                    <button type="submit" class="btn btn-primary">Bilgileri Güncelle</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Şifre Değiştirme -->
                <div class="col-md-6">
                    <div class="card form-card">
                        <div class="card-header">
                            <h5>Şifremi Değiştir</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="form-group">
                                    <label>Mevcut Şifre</label>
                                    <input type="password" name="current_password" class="form-control <?php echo (!empty($current_password_err)) ? 'is-invalid' : ''; ?>">
                                    <span class="invalid-feedback"><?php echo $current_password_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <label>Yeni Şifre</label>
                                    <input type="password" name="new_password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                                    <small class="form-text text-muted">Şifreniz en az 6 karakter olmalıdır.</small>
                                </div>
                                <div class="form-group">
                                    <label>Yeni Şifre (Tekrar)</label>
                                    <input type="password" name="confirm_password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="change_password" value="1">
                                    <button type="submit" class="btn btn-primary">Şifremi Değiştir</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- İstatistikler -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Hesap İstatistikleri</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php
                        // Toplam ilan sayısını al
                        $ilan_sql = "SELECT COUNT(*) as total_ilanlar FROM ilanlar WHERE kullanici_id = ?";
                        $ilan_stmt = mysqli_prepare($conn, $ilan_sql);
                        
                        $total_ilanlar = 0;
                        if ($ilan_stmt) {
                            mysqli_stmt_bind_param($ilan_stmt, "i", $_SESSION["id"]);
                            
                            if (mysqli_stmt_execute($ilan_stmt)) {
                                $ilan_result = mysqli_stmt_get_result($ilan_stmt);
                                $ilan_row = mysqli_fetch_assoc($ilan_result);
                                $total_ilanlar = $ilan_row["total_ilanlar"];
                            }
                            
                            mysqli_stmt_close($ilan_stmt);
                        }
                        
                        // Sahiplenilen hayvan sayısını al
                        $sahiplenilen_sql = "SELECT COUNT(*) as total_adopted FROM ilanlar WHERE sahiplenen_id = ? AND sahiplenildiMi = 1";
                        $sahiplenilen_stmt = mysqli_prepare($conn, $sahiplenilen_sql);
                        
                        $total_adopted = 0;
                        if ($sahiplenilen_stmt) {
                            mysqli_stmt_bind_param($sahiplenilen_stmt, "i", $_SESSION["id"]);
                            
                            if (mysqli_stmt_execute($sahiplenilen_stmt)) {
                                $sahiplenilen_result = mysqli_stmt_get_result($sahiplenilen_stmt);
                                $sahiplenilen_row = mysqli_fetch_assoc($sahiplenilen_result);
                                $total_adopted = $sahiplenilen_row["total_adopted"];
                            }
                            
                            mysqli_stmt_close($sahiplenilen_stmt);
                        }
                        
                        // Sahiplendirilen hayvan sayısını al
                        $sahiplendirilen_sql = "SELECT COUNT(*) as total_given FROM ilanlar WHERE kullanici_id = ? AND sahiplenildiMi = 1";
                        $sahiplendirilen_stmt = mysqli_prepare($conn, $sahiplendirilen_sql);
                        
                        $total_given = 0;
                        if ($sahiplendirilen_stmt) {
                            mysqli_stmt_bind_param($sahiplendirilen_stmt, "i", $_SESSION["id"]);
                            
                            if (mysqli_stmt_execute($sahiplendirilen_stmt)) {
                                $sahiplendirilen_result = mysqli_stmt_get_result($sahiplendirilen_stmt);
                                $sahiplendirilen_row = mysqli_fetch_assoc($sahiplendirilen_result);
                                $total_given = $sahiplendirilen_row["total_given"];
                            }
                            
                            mysqli_stmt_close($sahiplendirilen_stmt);
                        }
                        ?>
                        
                        <div class="col-md-4 text-center">
                            <div class="p-3">
                                <h1 class="display-4"><?php echo $total_ilanlar; ?></h1>
                                <p class="lead">Toplam İlanım</p>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-center">
                            <div class="p-3">
                                <h1 class="display-4"><?php echo $total_adopted; ?></h1>
                                <p class="lead">Sahiplendiğim Hayvan</p>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-center">
                            <div class="p-3">
                                <h1 class="display-4"><?php echo $total_given; ?></h1>
                                <p class="lead">Sahiplendirdiğim Hayvan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>