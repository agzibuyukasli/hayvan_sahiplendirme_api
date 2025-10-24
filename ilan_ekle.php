<?php
// Oturum kontrolü
require_once "session.php";
require_once "config.php";

// Değişkenleri tanımla
$hayvan_turu = $isim = $yas = $city = $district = $cinsiyet = $cins = $aciklama = "";
$hayvan_turu_err = $isim_err = $yas_err = $city_err = $district_err = $cinsiyet_err = $cins_err = $aciklama_err = $resim_err = "";

// Form gönderildiğinde işlem yap
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Hayvan türünü doğrula
    if(empty(trim($_POST["hayvan_turu"]))){
        $hayvan_turu_err = "Lütfen hayvan türünü seçin.";
    } else{
        $hayvan_turu = trim($_POST["hayvan_turu"]);
    }
    
    // İsmi doğrula
    if(empty(trim($_POST["isim"]))){
        $isim_err = "Lütfen hayvanın ismini girin.";
    } else{
        $isim = trim($_POST["isim"]);
    }
    
    // Yaşı doğrula
    if(empty(trim($_POST["yas"]))){
        $yas_err = "Lütfen hayvanın yaşını girin.";
    } elseif(!is_numeric(trim($_POST["yas"])) || trim($_POST["yas"]) < 0){
        $yas_err = "Yaş pozitif bir sayı olmalıdır.";
    } else{
        $yas = trim($_POST["yas"]);
    }
    
    // Şehri doğrula
    if(empty(trim($_POST["city"]))){
        $city_err = "Lütfen şehir girin.";
    } else{
        $city = trim($_POST["city"]);
    }
    
    // İlçeyi doğrula
    if(empty(trim($_POST["district"]))){
        $district_err = "Lütfen ilçe girin.";
    } else{
        $district = trim($_POST["district"]);
    }
    
    // Cinsiyeti doğrula
    if(empty(trim($_POST["cinsiyet"]))){
        $cinsiyet_err = "Lütfen cinsiyet seçin.";
    } else{
        $cinsiyet = trim($_POST["cinsiyet"]);
    }
    
    // Cinsi doğrula
    if(empty(trim($_POST["cins"]))){
        $cins_err = "Lütfen hayvanın cinsini girin.";
    } else{
        $cins = trim($_POST["cins"]);
    }
    
    // Açıklamayı doğrula
    if(empty(trim($_POST["aciklama"]))){
        $aciklama_err = "Lütfen açıklama girin.";
    } else{
        $aciklama = trim($_POST["aciklama"]);
    }
    
    // Resim yükleme işlemi
    $resim_url = "";
    if(isset($_FILES["resim"]) && $_FILES["resim"]["error"] == 0){
        $izin_verilen_uzantilar = array("jpg", "jpeg", "png", "gif");
        $dosya_uzantisi = pathinfo($_FILES["resim"]["name"], PATHINFO_EXTENSION);
        
        // Uzantı kontrol
        if(in_array(strtolower($dosya_uzantisi), $izin_verilen_uzantilar)){
            // Dosya boyutu kontrol (5MB)
            if($_FILES["resim"]["size"] < 5000000){
                $yeni_dosya_adi = uniqid() . "." . $dosya_uzantisi;
                $yukleme_yolu = "uploads/" . $yeni_dosya_adi;
                
                // Uploads klasörü yoksa oluştur
                if(!file_exists("uploads")){
                    mkdir("uploads", 0777, true);
                }
                
                if(move_uploaded_file($_FILES["resim"]["tmp_name"], $yukleme_yolu)){
                    $resim_url = $yukleme_yolu;
                } else {
                    $resim_err = "Resim yüklenirken bir hata oluştu.";
                }
            } else {
                $resim_err = "Dosya boyutu çok büyük. Maksimum 5MB olmalıdır.";
            }
        } else {
            $resim_err = "Sadece JPG, JPEG, PNG ve GIF dosyaları yükleyebilirsiniz.";
        }
    }
    
    // Hata kontrolü
    if(empty($hayvan_turu_err) && empty($isim_err) && empty($yas_err) && empty($city_err) && 
       empty($district_err) && empty($cinsiyet_err) && empty($cins_err) && empty($aciklama_err) && empty($resim_err)){
        
        // Insert sorgusu hazırla
        $sql = "INSERT INTO ilanlar (kullanici_id, hayvan_turu, isim, yas, city, district, cinsiyet, cins, aciklama, resim_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Parametreleri bağla
            mysqli_stmt_bind_param($stmt, "isisssssss", $param_kullanici_id, $param_hayvan_turu, $param_isim, $param_yas, 
                                   $param_city, $param_district, $param_cinsiyet, $param_cins, $param_aciklama, $param_resim_url);
            
            // Parametreleri ayarla
            $param_kullanici_id = $_SESSION["id"];
            $param_hayvan_turu = $hayvan_turu;
            $param_isim = $isim;
            $param_yas = $yas;
            $param_city = $city;
            $param_district = $district;
            $param_cinsiyet = $cinsiyet;
            $param_cins = $cins;
            $param_aciklama = $aciklama;
            $param_resim_url = $resim_url;
            
            // Sorguyu çalıştır
            if(mysqli_stmt_execute($stmt)){
                // İlanlar sayfasına yönlendir
                header("location: ilanlarim.php");
                exit();
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
    <title>İlan Ekle | Hayvan Sahiplendirme</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body{ 
            font: 14px sans-serif; 
            padding: 20px;
            background-color: #fefefe;
        }
        .wrapper{ 
            max-width: 800px; 
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
            color:rgb(192, 11, 156);
            font-weight: bold;
        }
        h2::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background-color: rgb(192, 11, 156);
            margin: 10px auto 20px;
            border-radius: 10px;
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .fa-paw {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2><i class="fas fa-paw"></i>Yeni İlan Ekle</h2>
        <p class="text-center">Sahiplendirmek istediğiniz hayvan için ilan oluşturun</p>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Hayvan Türü</label>
                <select name="hayvan_turu" class="form-control <?php echo (!empty($hayvan_turu_err)) ? 'is-invalid' : ''; ?>">
                    <option value="">Seçiniz</option>
                    <option value="kedi" <?php echo ($hayvan_turu == "kedi") ? 'selected' : ''; ?>>Kedi</option>
                    <option value="kopek" <?php echo ($hayvan_turu == "kopek") ? 'selected' : ''; ?>>Köpek</option>
                </select>
                <span class="invalid-feedback"><?php echo $hayvan_turu_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>İsim</label>
                <input type="text" name="isim" class="form-control <?php echo (!empty($isim_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $isim; ?>">
                <span class="invalid-feedback"><?php echo $isim_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>Yaş</label>
                <input type="number" name="yas" class="form-control <?php echo (!empty($yas_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $yas; ?>">
                <span class="invalid-feedback"><?php echo $yas_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>Şehir</label>
                <input type="text" name="city" class="form-control <?php echo (!empty($city_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $city; ?>">
                <span class="invalid-feedback"><?php echo $city_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>İlçe</label>
                <input type="text" name="district" class="form-control <?php echo (!empty($district_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $district; ?>">
                <span class="invalid-feedback"><?php echo $district_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>Cinsiyet</label>
                <select name="cinsiyet" class="form-control <?php echo (!empty($cinsiyet_err)) ? 'is-invalid' : ''; ?>">
                    <option value="">Seçiniz</option>
                    <option value="erkek" <?php echo ($cinsiyet == "erkek") ? 'selected' : ''; ?>>Erkek</option>
                    <option value="disi" <?php echo ($cinsiyet == "disi") ? 'selected' : ''; ?>>Dişi</option>
                </select>
                <span class="invalid-feedback"><?php echo $cinsiyet_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>Cins</label>
                <input type="text" name="cins" class="form-control <?php echo (!empty($cins_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $cins; ?>">
                <span class="invalid-feedback"><?php echo $cins_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>Açıklama</label>
                <textarea name="aciklama" class="form-control <?php echo (!empty($aciklama_err)) ? 'is-invalid' : ''; ?>" rows="5"><?php echo $aciklama; ?></textarea>
                <span class="invalid-feedback"><?php echo $aciklama_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>Hayvan Resmi</label>
                <input type="file" name="resim" class="form-control-file <?php echo (!empty($resim_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $resim_err; ?></span>
                <small class="form-text text-muted">İzin verilen dosya türleri: JPG, JPEG, PNG, GIF. Maksimum dosya boyutu: 5MB.</small>
            </div>
            
            <div class="form-group text-center">
                <input type="submit" class="btn btn-primary" value="İlan Ekle">
                <a href="ilanlarim.php" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>    
</body>
</html>



<!--  
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>İlan Ekle | Hayvan Sahiplendirme</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  
    <style>
        body{ 
            font: 14px sans-serif; 
            padding: 20px;
        }
        .wrapper{ 
            max-width: 800px; 
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color:rgb(192, 11, 156);
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Yeni İlan Ekle</h2>
        <p>Sahiplendirmek istediğiniz hayvan için ilan oluşturun</p>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Hayvan Türü</label>
                <select name="hayvan_turu" class="form-control <?php echo (!empty($hayvan_turu_err)) ? 'is-invalid' : ''; ?>">
                    <option value="">Seçiniz</option>
                    <option value="kedi" <?php echo ($hayvan_turu == "kedi") ? 'selected' : ''; ?>>Kedi</option>
                    <option value="kopek" <?php echo ($hayvan_turu == "kopek") ? 'selected' : ''; ?>>Köpek</option>
                </select>
                <span class="invalid-feedback"><?php echo $hayvan_turu_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>İsim</label>
                <input type="text" name="isim" class="form-control <?php echo (!empty($isim_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $isim; ?>">
                <span class="invalid-feedback"><?php echo $isim_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>Yaş</label>
                <input type="number" name="yas" class="form-control <?php echo (!empty($yas_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $yas; ?>">
                <span class="invalid-feedback"><?php echo $yas_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>Şehir</label>
                <input type="text" name="city" class="form-control <?php echo (!empty($city_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $city; ?>">
                <span class="invalid-feedback"><?php echo $city_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>İlçe</label>
                <input type="text" name="district" class="form-control <?php echo (!empty($district_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $district; ?>">
                <span class="invalid-feedback"><?php echo $district_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>Cinsiyet</label>
                <select name="cinsiyet" class="form-control <?php echo (!empty($cinsiyet_err)) ? 'is-invalid' : ''; ?>">
                    <option value="">Seçiniz</option>
                    <option value="erkek" <?php echo ($cinsiyet == "erkek") ? 'selected' : ''; ?>>Erkek</option>
                    <option value="disi" <?php echo ($cinsiyet == "disi") ? 'selected' : ''; ?>>Dişi</option>
                </select>
                <span class="invalid-feedback"><?php echo $cinsiyet_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>Cins</label>
                <input type="text" name="cins" class="form-control <?php echo (!empty($cins_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $cins; ?>">
                <span class="invalid-feedback"><?php echo $cins_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>Açıklama</label>
                <textarea name="aciklama" class="form-control <?php echo (!empty($aciklama_err)) ? 'is-invalid' : ''; ?>" rows="5"><?php echo $aciklama; ?></textarea>
                <span class="invalid-feedback"><?php echo $aciklama_err; ?></span>
            </div>
            
            <div class="form-group">
                <label>Hayvan Resmi</label>
                <input type="file" name="resim" class="form-control-file <?php echo (!empty($resim_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $resim_err; ?></span>
                <small class="form-text text-muted">İzin verilen dosya türleri: JPG, JPEG, PNG, GIF. Maksimum dosya boyutu: 5MB.</small>
            </div>
            
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="İlan Ekle">
                <a href="ilanlarim.php" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>    
</body>
</html> -->