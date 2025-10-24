<?php
// Oturum kontrolü
require_once "session.php";
require_once "config.php";

// Kullanıcının sahiplendiği ilanları al
$sql = "SELECT i.*, u.username FROM ilanlar i 
        JOIN userss u ON i.kullanici_id = u.id 
        WHERE i.sahiplenen_id = ? AND i.sahiplenildiMi = 1
        ORDER BY i.eklenme_tarihi DESC";

$stmt = mysqli_prepare($conn, $sql);

if($stmt){
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
    } else{
        echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sahiplenmelerim | miHav - Hayvan Sahiplendirme</title>
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
        .btn-outline-success {
            color: #6aa84f;
            border-color: #6aa84f;
            border-radius: 4px;
            padding: 8px 20px;
        }
        .btn-outline-success:hover {
            background-color: #6aa84f;
            color: white;
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
        /* Modal Stileri */
        .modal-content {
            border-radius: 8px;
            border: none;
        }
        .modal-header {
            border-bottom: 1px solid #f1f1f1;
            background-color: #6aa84f;
            color: white;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            padding: 15px 20px;
        }
        .modal-title {
            font-weight: bold;
        }
        .modal-body {
            padding: 20px;
        }
        .modal-footer {
            border-top: 1px solid #f1f1f1;
            padding: 15px 20px;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        /* Boş durum (empty state) */
        .empty-state {
            text-align: center;
            padding: 60px 0;
        }
        .empty-state i {
            font-size: 70px;
            color: #6aa84f;
            margin-bottom: 25px;
            opacity: 0.8;
        }
        .empty-state h4 {
            margin-bottom: 15px;
            color: #333;
            font-weight: bold;
            font-size: 24px;
        }
        .empty-state p {
            color: #666;
            max-width: 500px;
            margin: 0 auto 25px;
            font-size: 16px;
        }
        /* Sayfa başlığı */
        h2 {
            color: #333;
            margin-bottom: 30px;
            font-weight: bold;
        }
        /* Badge stileri */
        .badge-success {
            background-color: #6aa84f;
            padding: 6px 10px;
            font-size: 12px;
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
                    <li class="nav-item">
                        <a class="nav-link" href="ilan_ekle.php">İlan Ekle</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ilanlarim.php">İlanlarım</a>
                    </li>
                    <li class="nav-item active">
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
            <h2>Sahiplendiğim Hayvanlar</h2>
            
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
                                <p class="card-text">
                                    <small class="text-muted">İlan Sahibi: <?php echo $row['username']; ?></small>
                                </p>
                                <p class="card-text">
                                    <small class="text-muted">Sahiplenme Tarihi: <?php echo date('d.m.Y', strtotime($row['eklenme_tarihi'])); ?></small>
                                </p>
                                <div class="d-flex justify-content-between">
                                    <a href="ilan_detay.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">
                                        Detayları Gör
                                    </a>
                                    <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#contactModal<?php echo $row['id']; ?>">
                                        <i class="fas fa-phone-alt"></i> İletişim
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- İletişim Bilgileri Modal -->
                    <div class="modal fade" id="contactModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="contactModalLabel<?php echo $row['id']; ?>">İlan Sahibi İletişim Bilgileri</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <?php
                                    // İlan sahibi bilgilerini al
                                    $contact_sql = "SELECT username, email, phone FROM userss WHERE id = ?";
                                    $contact_stmt = mysqli_prepare($conn, $contact_sql);
                                    
                                    if($contact_stmt){
                                        mysqli_stmt_bind_param($contact_stmt, "i", $row["kullanici_id"]);
                                        
                                        if(mysqli_stmt_execute($contact_stmt)){
                                            $contact_result = mysqli_stmt_get_result($contact_stmt);
                                            $contact_info = mysqli_fetch_assoc($contact_result);
                                            
                                            if($contact_info){
                                                echo "<p><strong>İsim:</strong> " . $contact_info["username"] . "</p>";
                                                echo "<p><strong>E-posta:</strong> " . $contact_info["email"] . "</p>";
                                                
                                                if(!empty($contact_info["phone"])){
                                                    echo "<p><strong>Telefon:</strong> " . $contact_info["phone"] . "</p>";
                                                } else {
                                                    echo "<p><strong>Telefon:</strong> Belirtilmemiş</p>";
                                                }
                                            }
                                        }
                                        
                                        mysqli_stmt_close($contact_stmt);
                                    }
                                    ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                    endwhile; 
                else: 
                ?>
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-paw"></i>
                            <h4>Henüz sahiplendiğiniz hayvan bulunmuyor</h4>
                            <p>Tüm ilanları inceleyerek bir can dostuna yuva olabilirsiniz.</p>
                            <a href="tum_ilanlar.php" class="btn btn-primary">Tüm İlanları Görüntüle</a>
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