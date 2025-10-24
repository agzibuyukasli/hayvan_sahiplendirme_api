<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hakkımızda - miHav</title>
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .logo {
            font-family: 'Arial Rounded MT Bold', 'Arial', sans-serif;
            color: #ff6b6b;
            margin: 0;
            font-size: 2rem;
        }
        
        .hero-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 40px;
            margin-bottom: 30px;
        }
        
        .mission-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
            height: 100%;
        }
        
        .mission-card:hover {
            transform: translateY(-5px);
        }
        
        .mission-card .card-header {
            background-color: #ff6b6b;
            color: white;
            border-radius: 10px 10px 0 0;
            font-weight: bold;
        }
        
        .icon-box {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .icon-box i {
            font-size: 24px;
            color: #ff6b6b;
            margin-right: 15px;
            width: 40px;
            text-align: center;
        }
        
        .team-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        .team-img-container{
            height: 300px;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
        
        .active-link {
            color: #ff6b6b !important;
            border-bottom: 2px solid #ff6b6b;
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
        
        footer {
            background-color: #343a40;
            color: white;
            padding: 40px 0 20px;
        }
        
        .footer-heading {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #ff6b6b;
        }
        
        .footer-link {
            color: #e1e1e1;
            text-decoration: none;
            transition: color 0.3s ease;
            display: block;
            margin-bottom: 10px;
        }
        
        .footer-link:hover {
            color: #ff6b6b;
        }
        
        .social-icons a {
            display: inline-block;
            width: 36px;
            height: 36px;
            background-color: rgba(255,255,255,0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 36px;
            margin-right: 10px;
            color: white;
            transition: background-color 0.3s ease;
        }
        
        .social-icons a:hover {
            background-color: #ff6b6b;
        }
        
        .stats-box {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #ff6b6b;
            margin-bottom: 0;
        }
        
        .copyright {
            padding-top: 20px;
            margin-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
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
                        <a class="nav-link active-link" href="hakkimizda.php">Hakkımızda</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="hayvanlar.php">Hayvanlar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog.php">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="iletisim.php">İletişim</a>
                    </li> -->
                </ul>
                
                <div class="d-flex">
                    <!-- <a href="login.php" class="btn btn-outline-mihav me-2">Giriş / Kayıt</a> -->
                    <a href="ilan_ekle.php" class="btn btn-dark">Ücretsiz İlan Ver</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="hero-section">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 mb-4">Biz Kimiz?</h1>
                    <p class="lead">miHav, evcil hayvan sahiplendirme sürecini kolaylaştırmak için kurulmuş güvenilir bir platformdur. Amacımız, sahipsiz hayvanların sıcak bir yuvaya kavuşmasını sağlamak ve hayvan severlerin güvenilir bir şekilde can dostlarını bulmalarına yardımcı olmaktır.</p>
                </div>
                <div class="col-lg-4 text-center">
                    <div> 
                        <img src="images/ekipUye.jpeg" alt="miHav Ekibi" class="team-img">
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-lg-4">
                <div class="stats-box">
                    <i class="fas fa-home fa-2x mb-3" style="color: #ff6b6b;"></i>
                    <h2 class="stats-number">5000+</h2>
                    <p class="text-muted">Yuva Bulan Hayvan</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="stats-box">
                    <i class="fas fa-users fa-2x mb-3" style="color: #ff6b6b;"></i>
                    <h2 class="stats-number">15000+</h2>
                    <p class="text-muted">Aktif Kullanıcı</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="stats-box">
                    <i class="fas fa-paw fa-2x mb-3" style="color: #ff6b6b;"></i>
                    <h2 class="stats-number">50+</h2>
                    <p class="text-muted">İş Birliği Yapılan Barınak</p>
                </div>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-lg-6 mb-4">
                <div class="card mission-card">
                    <div class="card-header">Misyonumuz</div>
                    <div class="card-body">
                        <p class="card-text">Her yıl binlerce evcil hayvan, çeşitli sebeplerle sokaklara terk edilmekte veya barınaklarda yaşamaya mahkum edilmektedir. miHav olarak, bu soruna çözüm getirmek ve hayvanların hak ettikleri sevgi dolu yuvalara kavuşmalarını sağlamak için çalışıyoruz.</p>
                        <p class="card-text">Tüm süreç boyunca güvenilir ve şeffaf bir ortam sağlamak önceliğimizdir. Hayvan sahiplendirme sürecini en etik ve sorumlu şekilde yönetmeyi hedefliyoruz.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card mission-card">
                    <div class="card-header">Vizyonumuz</div>
                    <div class="card-body">
                        <p class="card-text">miHav ailesi olarak, her can dostunun sevgi dolu bir yuvayı hak ettiğine inanıyoruz. Vizyonumuz, Türkiye'de sahipsiz hayvan sorununa kalıcı çözümler üretmek ve toplumda hayvan sevgisini yaygınlaştırmaktır.</p>
                        <p class="card-text">Gelecekte daha fazla barınak ve hayvan koruma derneği ile iş birliği yaparak, daha çok hayvanın yuva bulmasına katkı sağlamayı hedefliyoruz.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <h2 class="mb-4">Neden miHav?</h2>
        <div class="row mb-5">
            <div class="col-md-6">
                <div class="icon-box">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <h5>Güvenilir Platform</h5>
                        <p>Tüm ilanlarımız kontrol edilir ve onaylanır, güvenli bir sahiplendirme ortamı sağlarız.</p>
                    </div>
                </div>
                <div class="icon-box">
                    <i class="fas fa-heart"></i>
                    <div>
                        <h5>Sevgi Dolu Yuvalar</h5>
                        <p>Hayvanların uygun şartlarda ve sevgi dolu ailelere sahiplendirilmesini sağlarız.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="icon-box">
                    <i class="fas fa-hand-holding-medical"></i>
                    <div>
                        <h5>Sağlık Kontrolleri</h5>
                        <p>Platformumuzda yer alan hayvanların sağlık durumları hakkında şeffaf bilgiler sunuyoruz.</p>
                    </div>
                </div>
                <div class="icon-box">
                    <i class="fas fa-users"></i>
                    <div>
                        <h5>Topluluk Desteği</h5>
                        <p>Hayvan sahiplerinden oluşan geniş bir topluluğun desteği ve deneyimi.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card p-4 mb-5">
            <div class="row">
                <div class="col-md-8">
                    <h3>Siz de Bu Yolculukta Bize Katılın!</h3>
                    <p class="mb-0">Bir hayvanın hayatını değiştirmek ve miHav ailesinin bir parçası olmak için hemen üye olun veya ilan verin.</p>
                </div>
                <div class="col-md-4 text-md-end d-flex align-items-center justify-content-md-end">
                    <!-- <a href="kayit.php" class="btn btn-mihav">Hemen Üye Ol</a> -->
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h3 class="footer-heading">miHav</h3>
                    <p>Evcil hayvan sahiplendirme sürecini kolaylaştıran güvenilir platform. Her can dostunun sıcak bir yuvayı hak ettiğine inanıyoruz.</p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h5 class="footer-heading">Hızlı Bağlantılar</h5>
                    <a href="index.php" class="footer-link">Anasayfa</a>
                    <a href="hakkimizda.php" class="footer-link">Hakkımızda</a>
                    <a href="hayvanlar.php" class="footer-link">Hayvanlar</a>
                    <a href="blog.php" class="footer-link">Blog</a>
                    <a href="iletisim.php" class="footer-link">İletişim</a>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 class="footer-heading">Yardım & Destek</h5>
                    <a href="sss.php" class="footer-link">Sık Sorulan Sorular</a>
                    <a href="nasil-calisir.php" class="footer-link">Nasıl Çalışır?</a>
                    <a href="kosullar.php" class="footer-link">Kullanım Koşulları</a>
                    <a href="gizlilik.php" class="footer-link">Gizlilik Politikası</a>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 class="footer-heading">İletişim</h5>
                    <p><i class="fas fa-map-marker-alt me-2"></i> Bahçelievler, İstanbul</p>
                    <p><i class="fas fa-phone me-2"></i> (0212) 123 45 67</p>
                    <p><i class="fas fa-envelope me-2"></i> info@mihav.com</p>
                </div>
            </div>
            <div class="text-center copyright">
                <p>© 2025 miHav - Tüm Hakları Saklıdır</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>