<?php
session_start();
include("config.php");

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// İlan ID kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_panel.php");
    exit();
}

$ilan_id = intval($_GET['id']);
$errors = [];
$success_message = "";

// İlan bilgilerini çek
$ilan_query = "SELECT * FROM ilanlar WHERE id = ?";
$stmt = $conn->prepare($ilan_query);
$stmt->bind_param("i", $ilan_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: admin_panel.php");
    exit();
}

$ilan = $result->fetch_assoc();

// Form gönderildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form verilerini alma
    $isim = trim($_POST['name']);
    $hayvan_turu = trim($_POST['animal_type']);
    $yas = trim($_POST['age']);
    $cinsiyet = trim($_POST['gender']);
    $aciklama = trim($_POST['description']);
    $city = trim($_POST['city']);
    $district = trim($_POST['district']);
    
    // Hata kontrolü
    if (empty($isim)) {
        $errors[] = "Hayvan adı alanı boş bırakılamaz.";
    }
    
    if (empty($hayvan_turu)) {
        $errors[] = "Lütfen hayvan türünü seçin.";
    }
    
    if (empty($aciklama)) {
        $errors[] = "Açıklama alanı boş bırakılamaz.";
    }
    
    // Resim yükleme işlemi (eğer yeni resim yüklendiyse)
    $resim_url = $ilan['resim_url']; // Varsayılan olarak mevcut resim yolunu koru
    
    if (isset($_FILES['resim_url']) && $_FILES['resim_url']['size'] > 0) {
        $izin_verilen_uzantilar = ['jpg', 'jpeg', 'png', 'gif'];
        $dosya_adi = $_FILES['resim_url']['name'];
        $dosya_uzantisi = strtolower(pathinfo($dosya_adi, PATHINFO_EXTENSION));
        
        if (!in_array($dosya_uzantisi, $izin_verilen_uzantilar)) {
            $errors[] = "Sadece JPG, JPEG, PNG ve GIF uzantılı dosyalar yükleyebilirsiniz.";
        } else {
            $yeni_dosya_adi = "hayvan_" . $ilan_id . "_" . time() . "." . $dosya_uzantisi;
            $hedef_yol = "uploads/" . $yeni_dosya_adi;
            
            if (move_uploaded_file($_FILES['resim_url']['tmp_name'], $hedef_yol)) {
                // Eski resmi silme (varsayılan resim değilse)
                if (!empty($ilan['resim_url']) && file_exists($ilan['resim_url']) && basename($ilan['resim_url']) != "no-image.jpg") {
                    unlink($ilan['resim_url']);
                }
                $resim_url = $hedef_yol;
            } else {
                $errors[] = "Resim yüklenirken bir hata oluştu.";
            }
        }
    }
    
    // Hata yoksa güncelleme işlemi yap
    if (empty($errors)) {
        $update_query = "UPDATE ilanlar SET 
                        isim = ?, 
                        hayvan_turu = ?, 
                        yas = ?, 
                        cinsiyet = ?, 
                        aciklama = ?, 
                        city = ?,
                        district = ?,
                        resim_url = ?,
                        eklenme_tarihi = NOW() 
                        WHERE id = ?";
                        
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssssssssi", $isim, $hayvan_turu, $yas, $cinsiyet, $aciklama, $city, $district, $resim_url,$ilan_id);
        
        if ($update_stmt->execute()) {
            $success_message = "İlan başarıyla güncellendi!";
            
            // Başarıyla güncellenince yönlendir (3 saniye sonra)
            header("refresh:3;url=admin_panel.php");
        } else {
            $errors[] = "İlan güncellenirken bir hata oluştu: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>miHav - İlan Düzenle</title>
    <link rel="stylesheet" href="admin_panel_style.css">
    <style>
        .edit-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .form-group textarea {
            height: 150px;
        }
        
        .buttons {
            margin-top: 30px;
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        
        .btn-secondary {
            background-color: #f1f1f1;
            color: #333;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .current-image {
            margin-top: 10px;
        }
        
        .current-image img {
            max-width: 200px;
            border: 1px solid #ddd;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <header>
            <h1>miHav Admin Paneli</h1>
            <div class="admin-info">
                Merhaba, <?php echo htmlspecialchars($_SESSION['admin_username']); ?> | 
                <a href="admin_logout.php">Çıkış Yap</a>
            </div>
        </header>
        
        <div class="edit-container">
            <h2>İlan Düzenle</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                    <p>Admin paneline yönlendiriliyorsunuz...</p>
                </div>
            <?php endif; ?>
            
            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Hayvan Adı</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($ilan['isim']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="animal_type">Hayvan Türü</label>
                    <select id="animal_type" name="animal_type" required>
                        <option value="">Seçiniz</option>
                        <option value="Kedi" <?php if ($ilan['hayvan_turu'] == 'Kedi') echo 'selected'; ?>>Kedi</option>
                        <option value="Köpek" <?php if ($ilan['hayvan_turu'] == 'Köpek') echo 'selected'; ?>>Köpek</option>
                        <option value="Diğer" <?php if ($ilan['hayvan_turu'] == 'Diğer') echo 'selected'; ?>>Diğer</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="age">Yaş</label>
                    <input type="text" id="age" name="age" value="<?php echo htmlspecialchars($ilan['yas']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="gender">Cinsiyet</label>
                    <select id="gender" name="gender" required>
                        <option value="">Seçiniz</option>
                        <option value="Erkek" <?php if ($ilan['cinsiyet'] == 'Erkek') echo 'selected'; ?>>Erkek</option>
                        <option value="Dişi" <?php if ($ilan['cinsiyet'] == 'Dişi') echo 'selected'; ?>>Dişi</option>
                        <option value="Bilinmiyor" <?php if ($ilan['cinsiyet'] == 'Bilinmiyor') echo 'selected'; ?>>Bilinmiyor</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="city">Şehir</label>
                    <input type="text" id="city" name="city" value="<?php echo isset($ilan['sehir']) ? htmlspecialchars($ilan['sehir']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="district">İlçe</label>
                    <input type="text" id="district" name="district" value="<?php echo isset($ilan['ilce']) ? htmlspecialchars($ilan['ilce']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Açıklama</label>
                    <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($ilan['aciklama']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="resim_url">Hayvan Fotoğrafı</label>
                    <?php if (!empty($ilan['resim_url'])): ?>
                        <div class="current-image">
                            <p>Mevcut Fotoğraf:</p>
                            <img src="<?php echo htmlspecialchars($ilan['resim_url']); ?>" alt="<?php echo htmlspecialchars($ilan['isim']); ?>">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="resim_url" name="resim_url" accept="image/*">
                    <small>Yeni fotoğraf yüklemezseniz mevcut fotoğraf korunacaktır.</small>
                </div>
                
                <div class="buttons">
                    <button type="submit" class="btn btn-primary">İlanı Güncelle</button>
                    <a href="admin_panel.php" class="btn btn-secondary">İptal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>