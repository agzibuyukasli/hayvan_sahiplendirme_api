<?php
// Oturum kontrolü
require_once "session.php";
require_once "config.php";

// İlan ID'si kontrolü
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("location: tum_ilanlar.php");
    exit;
}

$ilan_id = $_GET["id"];

// İlan bilgilerini al
$ilan_sql = "SELECT i.*, u.username, u.email
            FROM ilanlar i 
            JOIN userss u ON i.kullanici_id = u.id 
            WHERE i.id = ?";

$stmt = mysqli_prepare($conn, $ilan_sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $ilan_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 1) {
            $ilan = mysqli_fetch_assoc($result);
        } else {
            // İlan bulunamadı
            header("location: tum_ilanlar.php");
            exit;
        }
    } else {
        echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
        exit;
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
    exit;
}

// Sahiplenme işlemi
$message = "";
$error = "";

if (isset($_POST["sahiplen"]) && $_POST["sahiplen"] == "1") {
    // İlan kullanıcının kendi ilanı mı kontrol et
    if ($ilan["kullanici_id"] == $_SESSION["id"]) {
        $error = "Kendi ilanınızı sahiplenemezsiniz!";
    } else if ($ilan["sahiplenildiMi"] == 1) {
        $error = "Bu hayvan zaten sahiplenilmiş!";
    } else {
        // İlanı sahiplenildi olarak işaretle
        $update_sql = "UPDATE ilanlar SET sahiplenildiMi = 1, sahiplenen_id = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        
        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "ii", $_SESSION["id"], $ilan_id);
            
            if (mysqli_stmt_execute($update_stmt)) {
                $message = "Hayvan başarıyla sahiplenildi! İlan sahibi ile iletişime geçebilirsiniz.";
                // İlan bilgisini güncelle
                $ilan["sahiplenildiMi"] = 1;
                $ilan["sahiplenen_id"] = $_SESSION["id"];
            } else {
                $error = "Sahiplenme işlemi sırasında bir hata oluştu.";
            }
            
            mysqli_stmt_close($update_stmt);
        } else {
            $error = "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $ilan["isim"]; ?> | İlan Detayı</title>
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
        .main-image {
            height: 400px;
            object-fit: contain;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .detail-card {
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
            margin-bottom: 20px;
        }
        .navbar {
            margin-bottom: 30px;
            background-color: #28a745;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-danger {
            background-color: #dc3545;
        }
        .detail-row {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .contact-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
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
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Çıkış Yap</a>
                    </li>
                </ul>
            </div>
        </nav>
        
        <div class="container">
            <!-- Bildirim Mesajları -->
            <?php if(!empty($message)): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <img src="<?php echo $ilan['resim_url']; ?>" class="img-fluid main-image" alt="<?php echo $ilan['isim']; ?>">
                </div>
                <div class="col-md-6">
                    <div class="card detail-card">
                        <div class="card-header">
                            <h2><?php echo $ilan['isim']; ?> 
                                <?php if($ilan['sahiplenildiMi'] == 1): ?>
                                    <span class="badge badge-danger">Sahiplenildi</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Sahiplendirme İlanı Aktif</span>
                                <?php endif; ?>
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="detail-row">
                                <strong>Hayvan Türü:</strong> <?php echo ucfirst($ilan['hayvan_turu']); ?>
                            </div>
                            <div class="detail-row">
                                <strong>Cinsi:</strong> <?php echo $ilan['cins']; ?>
                            </div>
                            <div class="detail-row">
                                <strong>Yaş:</strong> <?php echo $ilan['yas']; ?>
                            </div>
                            <div class="detail-row">
                                <strong>Cinsiyet:</strong> <?php echo ucfirst($ilan['cinsiyet']); ?>
                            </div>
                            <div class="detail-row">
                                <strong>Şehir:</strong> <?php echo $ilan['city']; ?>
                            </div>
                            <!-- <div class="detail-row">
                                <strong>Aşı Durumu:</strong> <?php echo ($ilan['asi_durumu'] == 1) ? 'Aşıları Tam' : 'Eksik Aşı Var'; ?>
                            </div>
                            <div class="detail-row">
                                <strong>Kısırlaştırma:</strong> <?php echo ($ilan['kisirlastirma'] == 1) ? 'Kısırlaştırılmış' : 'Kısırlaştırılmamış'; ?>
                            </div> -->
                            <div class="detail-row">
                                <strong>İlan Tarihi:</strong> <?php echo date('d.m.Y', strtotime($ilan['eklenme_tarihi'])); ?>
                            </div>
                            <div class="detail-row">
                                <strong>İlan Sahibi:</strong> <?php echo $ilan['username']; ?>
                            </div>
                            
                            <div class="mt-4">
                                <h5>Açıklama:</h5>
                                <p><?php echo nl2br(htmlspecialchars($ilan['aciklama'])); ?></p>
                            </div>
                            
                            <?php if($ilan['sahiplenildiMi'] == 0 && $ilan['kullanici_id'] != $_SESSION['id']): ?>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $ilan_id; ?>" method="post" class="mt-3">
                                    <input type="hidden" name="sahiplen" value="1">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">Sahiplen</button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if($ilan['sahiplenildiMi'] == 1 && ($ilan['sahiplenen_id'] == $_SESSION['id'] || $ilan['kullanici_id'] == $_SESSION['id'])): ?>
                                <div class="contact-details mt-3">
                                    <h5>İletişim Bilgileri:</h5>
                                    <p><strong>İlan Sahibi:</strong> <?php echo $ilan['username']; ?></p>
                                    <p><strong>E-posta:</strong> <?php echo $ilan['email']; ?></p>
                                    <?php if(!empty($ilan['phone'])): ?>
                                        <p><strong>Telefon:</strong> <?php echo $ilan['phone']; ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="tum_ilanlar.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Tüm İlanlara Dön</a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>