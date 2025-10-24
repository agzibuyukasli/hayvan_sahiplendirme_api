<?php

$city = "";
$city_err = "";
$district = "";
$district_err = "";


session_start();
include("config.php");

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Get pending ilanlar
$pending_sql = "SELECT * FROM ilanlar WHERE status = 'pending' ORDER BY eklenme_tarihi DESC";
$pending_result = $conn->query($pending_sql);

// Get approved ilanlar
$approved_sql = "SELECT * FROM ilanlar WHERE status = 'approved' ORDER BY eklenme_tarihi DESC";
$approved_result = $conn->query($approved_sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>miHav - Admin Paneli</title>
    <link rel="stylesheet" href="admin_panel_style.css">
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
        
        <div class="admin-tabs">
            <button class="tab-btn active" onclick="openTab('pending')">Onay Bekleyen İlanlar</button>
            <button class="tab-btn" onclick="openTab('approved')">Onaylanmış İlanlar</button>
            <button class="tab-btn" onclick="openTab('add-listing')">Yeni İlan Ekle</button>
        </div>
        
        <div id="pending" class="tab-content active">
            <h2>Onay Bekleyen İlanlar</h2>
            <?php if($pending_result->num_rows > 0): ?>
                <div class="ilanlar-grid">
                    <?php while($listing = $pending_result->fetch_assoc()): ?>
                        <div class="listing-card">
                            <div class="listing-image">
                                <img src="<?php echo htmlspecialchars($listing['resim_url']); ?>" alt="<?php echo htmlspecialchars($listing['isim']); ?>">
                            </div>
                            <div class="listing-details">
                                <h3><?php echo htmlspecialchars($listing['isim']); ?></h3>
                                <p><strong>Tür:</strong> <?php echo htmlspecialchars($listing['hayvan_turu']); ?></p>
                                <p><strong>Yaş:</strong> <?php echo htmlspecialchars($listing['yas']); ?></p>
                                <p><strong>Cinsiyet:</strong> <?php echo htmlspecialchars($listing['cinsiyet']); ?></p>
                                <p><strong>Açıklama:</strong> <?php echo htmlspecialchars($listing['aciklama']); ?></p>
                                <!-- <p><strong>İletişim:</strong> <?php echo htmlspecialchars($listing['contact_info']); ?></p> -->
                                <p><strong>Ekleyen:</strong> <?php echo htmlspecialchars($listing['kullanici_id']); ?></p>
                                <p><strong>Tarih:</strong> <?php echo date('d.m.Y H:i', strtotime($listing['eklenme_tarihi'])); ?></p>
                                
                                <div class="admin-actions">
                                    <a href="admin_actions.php?action=approve&id=<?php echo $listing['id']; ?>" class="btn btn-success">Onayla</a>
                                    <a href="ilan_duzenle.php?id=<?php echo $listing['id']; ?>" class="btn btn-primary">Düzenle</a>
                                    <a href="admin_actions.php?action=delete&id=<?php echo $listing['id']; ?>" class="btn btn-danger" onclick="return confirm('Bu ilanı silmek istediğinizden emin misiniz?')">Sil</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>Onay bekleyen ilan bulunmamaktadır.</p>
            <?php endif; ?>
        </div>
        
        <div id="approved" class="tab-content">
            <h2>Onaylanmış İlanlar</h2>
            <?php if($approved_result->num_rows > 0): ?>
                <div class="ilanlar-grid">
                    <?php while($listing = $approved_result->fetch_assoc()): ?>
                        <div class="listing-card">
                        <div class="listing-image">
                            <?php if(!empty($listing['resim_url'])): ?>
                                <img src="<?php echo htmlspecialchars($listing['resim_url']); ?>" alt="<?php echo htmlspecialchars($listing['isim']); ?>">

                            <?php else: ?>
                                <img src="no-image.jpg" alt="Resim Yok">
                            <?php endif; ?>
                        </div>
                            <div class="listing-details">
                                <h3><?php echo htmlspecialchars($listing['isim']); ?></h3>
                                <p><strong>Tür:</strong> <?php echo htmlspecialchars($listing['hayvan_turu']); ?></p>
                                <p><strong>Yaş:</strong> <?php echo htmlspecialchars($listing['yas']); ?></p>
                                <p><strong>Cinsiyet:</strong> <?php echo htmlspecialchars($listing['cinsiyet']); ?></p>
                                <p><strong>Açıklama:</strong> <?php echo htmlspecialchars($listing['aciklama']); ?></p>
                                <!-- <p><strong>İletişim:</strong> <?php echo htmlspecialchars($listing['contact_info']); ?></p> -->
                                <p><strong>Ekleyen:</strong> <?php echo htmlspecialchars($listing['kullanici_id']); ?></p>
                                <p><strong>Tarih:</strong> <?php echo date('d.m.Y H:i', strtotime($listing['eklenme_tarihi'])); ?></p>
                                
                                <div class="admin-actions">
                                    <?php if(isset($listing['sahiplenildiMi']) && $listing['sahiplenildiMi'] == 0): ?>
                                        <a href="admin_actions.php?action=mark_adopted&id=<?php echo $listing['id']; ?>" class="btn btn-info">Sahiplendirildi İşaretle</a>
                                    <?php else: ?>
                                        <span class="adopted-badge">Sahiplendirildi ✅</span>
                                    <?php endif; ?>
                                    <a href="ilan_duzenle.php?id=<?php echo $listing['id']; ?>" class="btn btn-primary">Düzenle</a>
                                    <a href="admin_actions.php?action=delete&id=<?php echo $listing['id']; ?>" class="btn btn-danger" onclick="return confirm('Bu ilanı silmek istediğinizden emin misiniz?')">Sil</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>Onaylanmış ilan bulunmamaktadır.</p>
            <?php endif; ?>
        </div>
        
        <div id="add-listing" class="tab-content">
            <h2>Yeni İlan Ekle</h2>
            <form method="post" action="admin_actions.php?action=add_listing" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Hayvan Adı</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="animal_type">Hayvan Türü</label>
                    <select id="animal_type" name="animal_type" required>
                        <option value="">Seçiniz</option>
                        <option value="Kedi">Kedi</option>
                        <option value="Köpek">Köpek</option>
                        <option value="Diğer">Diğer</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="age">Yaş</label>
                    <input type="text" id="age" name="age" required>
                </div>
                
                <div class="form-group">
                    <label for="gender">Cinsiyet</label>
                    <select id="gender" name="gender" required>
                        <option value="">Seçiniz</option>
                        <option value="Erkek">Erkek</option>
                        <option value="Dişi">Dişi</option>
                        <option value="Bilinmiyor">Bilinmiyor</option>
                    </select>
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
                    <label for="description">Açıklama</label>
                    <textarea id="description" name="description" rows="5" required></textarea>
                </div>
                
                <!-- <div class="form-group">
                    <label for="contact_info">İletişim Bilgileri</label>
                    <input type="text" id="contact_info" name="contact_info" required>
                </div> -->
                
                <div class="form-group">
                    <label for="resim_url">Hayvan Fotoğrafı</label>
                    <input type="file" id="image" name="resim_url" accept="image/*" required>
                </div>
                
                <button type="submit" class="btn btn-primary">İlanı Ekle</button>
            </form>
        </div>
    </div>
    
    <script>
    function openTab(tabName) {
        // Hide all tab contents
        var tabContents = document.getElementsByClassName("tab-content");
        for (var i = 0; i < tabContents.length; i++) {
            tabContents[i].classList.remove("active");
        }
        
        // Remove active class from all tab buttons
        var tabButtons = document.getElementsByClassName("tab-btn");
        for (var i = 0; i < tabButtons.length; i++) {
            tabButtons[i].classList.remove("active");
        }
        
        // Show the selected tab content and make the button active
        document.getElementById(tabName).classList.add("active");
        event.currentTarget.classList.add("active");
    }
    </script>
</body>
</html>