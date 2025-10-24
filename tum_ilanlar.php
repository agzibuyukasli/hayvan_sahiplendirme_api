<?php
// Oturum kontrolü
require_once "session.php";
require_once "config.php";

// Filtre için değişkenler
$hayvan_turu_filter = $city_filter = "";

// Arama ve filtreleme sorgusu için başlangıç
$where_conditions = [];
$params = [];
$types = "";

// Filtre formunu işle
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Hayvan türü filtresi
    if (isset($_GET["hayvan_turu"]) && !empty($_GET["hayvan_turu"])) {
        $hayvan_turu_filter = $_GET["hayvan_turu"];
        $where_conditions[] = "LOWER(REPLACE(i.hayvan_turu, ' ', '-')) = ?";
        $params[] = strtolower(str_replace(' ', '-', $hayvan_turu_filter));
        $types .= "s";
    }
    
    // Şehir filtresi
    if (isset($_GET["city"]) && !empty($_GET["city"])) {
        $city_filter = $_GET["city"];
        $where_conditions[] = "LOWER(REPLACE(i.city, ' ', '-')) = ?";
        $params[] = strtolower(str_replace(' ', '-', $city_filter));
        $types .= "s";
    }
}

// Sadece sahiplenilmemiş ilanları göster
$where_conditions[] = "i.sahiplenildiMi = 0";

// WHERE koşulunu oluştur
$where_clause = count($where_conditions) > 0 ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Şehirleri al (filtre için)
$cities = [];
$city_sql = "SELECT DISTINCT city FROM ilanlar ORDER BY city";
$city_result = mysqli_query($conn, $city_sql);

if ($city_result) {
    while ($row = mysqli_fetch_assoc($city_result)) {
        $cities[] = $row["city"];
    }
}

// İlanları al
$sql = "SELECT i.*, u.username FROM ilanlar i 
        JOIN userss u ON i.kullanici_id = u.id 
        $where_clause 
        ORDER BY i.eklenme_tarihi DESC";

// Veritabanı sorgusu için hazırlık
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    // Parametreleri bağla (eğer varsa)
    if (count($params) > 0) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    // Sorguyu çalıştır
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
    } else {
        echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
    }

    // Sorguyu kapat
    mysqli_stmt_close($stmt);
} else {
    echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tüm İlanlar | miHav - Hayvan Sahiplendirme</title>
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
        /* Filtre kartı */
        .filter-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
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
            margin-bottom: 8px;
        }
        .text-muted {
            color: #888 !important;
            font-size: 13px;
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
        .auth-buttons .btn-danger {
            background-color: #e53935;
            border-color: #e53935;
        }
        /* Sayfa başlığı */
        h2 {
            color: #333;
            margin-bottom: 30px;
            font-weight: bold;
        }
        /* Alert stileri */
        .alert-info {
            background-color: #e3f2fd;
            border-color: #bbdefb;
            color: #0d47a1;
            border-radius: 8px;
            padding: 15px 20px;
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
                    <li class="nav-item active">
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
            <div class="search-form">
                <form method="get" action="tum_ilanlar.php" class="row">
                    <div class="col-md-4 mb-3">
                        <select name="hayvan_turu" class="form-control">
                            <option value="">Tür Seçiniz</option>
                            <option value="kedi">Kedi</option>
                            <option value="kopek">Köpek</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <select name="city" class="form-control">
                            <option value="">Şehir Seçiniz</option>
                            <option value="istanbul">İstanbul</option>
                            <option value="ankara">Ankara</option>
                            <option value="izmir">İzmir</option>
                            <!-- Diğer şehirler -->
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <button type="submit" class="search-btn btn-block">Ara</button>
                    </div>
                </form>
            </div>
            
            <h2>Sahiplendirme İlanları</h2>
            
            <!-- İlanlar Bölümü -->
            <div class="row">
                <?php 
                if(isset($result) && mysqli_num_rows($result) > 0):
                    while($row = mysqli_fetch_assoc($result)): 
                ?>
                    <div class="col-md-4">
                        <div class="card">
                            <img src="<?php echo $row['resim_url']; ?>" class="card-img-top" alt="<?php echo $row['isim']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['isim']; ?> (<?php echo ucfirst($row['hayvan_turu']); ?>)</h5>
                                <p class="card-text"><strong>Cinsi:</strong> <?php echo $row['cins']; ?></p>
                                <p class="card-text"><strong>Yaş:</strong> <?php echo $row['yas']; ?></p>
                                <p class="card-text"><strong>Şehir:</strong> <?php echo $row['city']; ?></p>
                                <p class="card-text"><small class="text-muted">İlan Sahibi: <?php echo $row['username']; ?></small></p>
                                <p class="card-text"><small class="text-muted">Eklenme Tarihi: <?php echo date('d.m.Y', strtotime($row['eklenme_tarihi'])); ?></small></p>
                                <a href="ilan_detay.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Detayları Gör</a>
                            </div>
                        </div>
                    </div>
                <?php 
                    endwhile; 
                else: 
                ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            Aradığınız kriterlere uygun ilan bulunamadı.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>