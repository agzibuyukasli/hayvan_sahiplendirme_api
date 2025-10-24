<?php
// Mesaj gösterme için kontrol
if (isset($_GET['silmesaj'])): ?>
    <?php if ($_GET['silmesaj'] == 'başarılı'): ?>
        <div class="alert alert-success">
            İlan başarıyla silindi.
        </div>
    <?php elseif ($_GET['silmesaj'] == 'hata'): ?>
        <div class="alert alert-danger">
            İlan silinirken bir hata oluştu.
        </div>
    <?php endif; ?>
<?php endif; 

// Oturum kontrolü ve bağlantılar
require_once "session.php";
require_once "config.php";

// Filtre parametrelerini al
$kategori = $_GET['kategori'] ?? '';
$tur = $_GET['tur'] ?? '';

// Temel SQL ve parametreler
$sql = "SELECT * FROM ilanlar WHERE kullanici_id = ?";
$params = [$_SESSION["id"]];
$types = "i";

// Kategori filtresi varsa ekle
if (!empty($kategori)) {
    $sql .= " AND hayvan_turu = ?";
    $params[] = $kategori;
    $types .= "s";
}

// Tür filtresi varsa ekle
if (!empty($tur)) {
    $sql .= " AND LOWER(REPLACE(cins, ' ', '-')) = ?";
    $params[] = strtolower($tur);
    $types .= "s";
}

// Tarihe göre sırala
$sql .= " ORDER BY eklenme_tarihi DESC";

// Sorguyu hazırla
if ($stmt = $conn->prepare($sql)) {
    // Dinamik parametre bağlama
    $stmt->bind_param($types, ...$params);

    // Sorguyu çalıştır
    if ($stmt->execute()) {
        $result = $stmt->get_result();
    } else {
        echo "<div class='alert alert-danger'>Veritabanı hatası: Sorgu çalıştırılamadı.</div>";
    }

    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>Veritabanı hatası: Sorgu hazırlanamadı.</div>";
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>İlanlarım | miHav - Hayvan Sahiplendirme</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 0;
            margin: 0;
            background-color: #f5f5f5;
        }
        .wrapper {
            width: 100%;
            margin: 0;
            padding: 0;
        }
        /* Navbar Stili */
        .navbar {
            margin-bottom: 0;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px 20px;
        }
        .navbar-brand {
            color: #6aa84f !important;
            font-size: 24px;
            font-weight: bold;
        }
        .nav-link {
            color: #333 !important;
            margin: 0 10px;
        }
        .navbar-nav .active .nav-link {
            color: #6aa84f !important;
            font-weight: bold;
        }
        /* Header Banner */
        .header-banner {
            background-image: url('hayvanlar-bg.jpg');
            background-size: cover;
            background-position: center;
            height: 300px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            margin-bottom: 30px;
        }
        .header-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.4);
            z-index: 1;
        }
        .header-content {
            position: relative;
            z-index: 2;
        }
        .header-content h1 {
            font-size: 40px;
            margin-bottom: 15px;
        }
        .header-content p {
            font-size: 18px;
        }
        /* Arama formu */
        .search-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 40px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .search-form .form-control {
            border-radius: 4px;
            padding: 12px;
            border: 1px solid #ddd;
        }
        .search-btn {
            background-color: #6aa84f;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 4px;
            font-weight: bold;
        }
        /* İlan kartları */
        .card {
            border: none;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            font-size: 18px;
            font-weight: bold;
        }
        .card-text {
            color: #555;
        }
        .card-footer {
            background-color: white;
            border-top: 1px solid #eee;
            padding: 15px 20px;
        }
        /* Butonlar */
        .btn-primary {
            background-color: #6aa84f;
            border-color: #6aa84f;
            border-radius: 4px;
            padding: 8px 20px;
        }
        .btn-primary:hover {
            background-color: #5c9444;
            border-color: #5c9444;
        }
        .btn-warning {
            background-color: #f9a825;
            border-color: #f9a825;
            color: white;
            border-radius: 4px;
            padding: 8px 20px;
        }
        .btn-warning:hover {
            background-color: #e69c22;
            border-color: #e69c22;
            color: white;
        }
        .btn-danger {
            background-color: #e53935;
            border-color: #e53935;
            border-radius: 4px;
            padding: 8px 20px;
        }
        .btn-danger:hover {
            background-color: #d32f2f;
            border-color: #d32f2f;
        }
        .btn-info {
            background-color: #03a9f4;
            border-color: #03a9f4;
            border-radius: 4px;
            padding: 8px 20px;
        }
        /* Header butonları */
        .auth-buttons .btn {
            margin-left: 10px;
            padding: 8px 16px;
            border-radius: 4px;
        }
        .auth-buttons .btn-outline-secondary {
            border-color: #6aa84f;
            color: #6aa84f;
        }
        .auth-buttons .btn-success {
            background-color: #db8836;
            border-color: #db8836;
        }
        /* Rozetler */
        .badge-success {
            background-color: #6aa84f;
            padding: 6px 10px;
            font-size: 12px;
        }
        .badge-danger {
            background-color: #e53935;
            padding: 6px 10px;
            font-size: 12px;
        }
        /* Sayfa başlığı */
        h2 {
            color: #333;
            margin-bottom: 30px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand" href="index.php">miHav</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Anasayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tum_ilanlar.php">Tüm İlanlar</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="ilanlarim.php">İlanlarım</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sahiplenmelerim.php">Sahiplenmelerim</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="hakkimizda.php">Hakkımızda</a>
                    </li>
                </ul>
                <div class="auth-buttons">
                    <a class="btn btn-outline-secondary" href="profile.php">Profil</a>
                    <a class="btn btn-danger" href="logout.php">Çıkış Yap</a>
                </div>
            </div>
        </nav>
        
        <div class="header-banner">
            <div class="header-content">
                <h1>Evcil Hayvanlar</h1>
                <p>Ücretsiz evcil hayvan sahiplendirme ilanları</p>
            </div>
        </div>

        
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>İlanlarım</h2>
                <a href="ilan_ekle.php" class="btn btn-primary"><i class="fas fa-plus"></i> Yeni İlan Ekle</a>
            </div>
            


            <?php if (!empty($kategori) || !empty($tur)): ?>
    <div class="alert alert-info">
        Filtrelenen Sonuçlar: 
        <?php if ($kategori): ?>
            <strong>Tür:</strong> <?= htmlspecialchars($kategori) ?>
        <?php endif; ?>
        <?php if ($tur): ?>
            <strong style="margin-left: 10px;">Cins:</strong> <?= htmlspecialchars($tur) ?>
        <?php endif; ?>
        <a href="ilanlarim.php" class="btn btn-sm btn-outline-secondary float-right">Filtreyi Temizle</a>
    </div>
<?php endif; ?>



            <div class="row">
                <?php
                if(mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)){
                ?>
                <div class="col-md-4">
                    <div class="card">
                        <?php if(!empty($row["resim_url"])): ?>
                            <img src="<?php echo $row["resim_url"]; ?>" class="card-img-top" alt="<?php echo $row["isim"]; ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Resim Yok">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row["isim"]; ?> 
                                <?php if (isset($_POST["sahiplenildiMi"]) && $_POST["sahiplenildiMi"] == "1") {
                                    $sahiplenildiMi = true;
                                } else {
                                    $sahiplenildiMi = false;
                                }
                                ?>
                            </h5>
                            <p class="card-text">
                                <strong>Tür:</strong> <?php echo ($row["hayvan_turu"] == "kedi") ? "Kedi" : "Köpek"; ?><br>
                                <strong>Cins:</strong> <?php echo $row["cins"]; ?><br>
                                <strong>Yaş:</strong> <?php echo $row["yas"]; ?><br>
                                <strong>Cinsiyet:</strong> <?php echo ($row["cinsiyet"] == "erkek") ? "Erkek" : "Dişi"; ?><br>
                                <strong>Konum:</strong> <?php echo $row["district"] . ", " . $row["city"]; ?>
                            </p>
                        </div>
                        <div class="card-footer">
                            <a href="ilan_detay.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-info">Detaylar</a>
                            <!-- <a href="ilan_duzenle.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Düzenle</a> -->
                            <!-- <a href="ilan_sil.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu ilanı silmek istediğinizden emin misiniz?')">Sil</a> -->
                            <!-- <a href="ilan_sil.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Bu ilanı silmek istediğinizden emin misiniz?')">Sil</a> -->
                        </div>
                    </div>
                </div>
                <?php 
                    }
                } else {
                    echo '<div class="col-12"><div class="alert alert-info">Henüz ilan bulunmamaktadır.</div></div>';
                }
                ?>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>