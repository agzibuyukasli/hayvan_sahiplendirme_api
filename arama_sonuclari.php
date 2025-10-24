
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// config.php dosyasını dahil ederek veritabanı bağlantısını al
include("config.php");

// Form verilerini al
$hayvan_turu = isset($_GET['hayvan_turu']) ? mysqli_real_escape_string($conn, $_GET['hayvan_turu']) : '';
$cins = isset($_GET['cins']) ? mysqli_real_escape_string($conn, $_GET['cins']) : '';

// SQL sorgusu oluştur
$sql = "SELECT * FROM ilanlar WHERE status = 'approved' AND sahiplenildiMi = 0";

if (!empty($hayvan_turu)) {
    $sql .= " AND hayvan_turu = '$hayvan_turu'";
}

if (!empty($cins)) {
    $sql .= " AND cins = '$cins'";
}

// Sonuçları en yeni eklenenler önce gelecek şekilde sırala
$sql .= " ORDER BY eklenme_tarihi DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Arama Sonuçları - Evcil Hayvanlar</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Arama Sonuçları</h1>

        <!-- Arama Formu -->
        <form method="GET" action="">
            <label for="hayvan_turu">Hayvan Türü</label>
            <select name="hayvan_turu" id="hayvan_turu">
                <option value="">Seçiniz...</option>
                <option value="Kedi" <?php echo (isset($_GET['hayvan_turu']) && $_GET['hayvan_turu'] == 'Kedi') ? 'selected' : ''; ?>>Kedi</option>
                <option value="Köpek" <?php echo (isset($_GET['hayvan_turu']) && $_GET['hayvan_turu'] == 'Köpek') ? 'selected' : ''; ?>>Köpek</option>
            </select>

            <label for="cins">Tür</label>
            <select name="cins" id="cins">
                <option value="">Seçiniz...</option>
                <?php
                if (isset($_GET['hayvan_turu'])) {
                    $hayvan_turu = $_GET['hayvan_turu'];

                    // Veritabanı bağlantısı ile cinsleri al
                    $sql = "SELECT DISTINCT cins FROM ilanlar WHERE hayvan_turu = '$hayvan_turu'";
                    $result_cins = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result_cins) > 0) {
                        while ($row = mysqli_fetch_assoc($result_cins)) {
                            echo '<option value="' . $row['cins'] . '">' . $row['cins'] . '</option>';
                        }
                    } else {
                        echo '<option value="">Cins bulunamadı</option>';
                    }
                }
                ?>
            </select>

            <button type="submit" class="btn btn-success">Ara</button>

        </form>

        <!-- Arama Sonuçları -->
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="row">
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <?php if (!empty($row['resim_url'])): ?>
                                <img src="<?php echo htmlspecialchars($row['resim_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['isim']); ?>">
                            <?php else: ?>
                                <img src="default-hayvan.jpg" class="card-img-top" alt="Varsayılan Resim">
                            <?php endif; ?>

                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['isim']); ?></h5>
                                <p class="card-text">
                                    <strong>Tür:</strong> <?php echo htmlspecialchars($row['hayvan_turu']); ?><br>
                                    <strong>Cins:</strong> <?php echo htmlspecialchars($row['cins']); ?><br>
                                    <strong>Yaş:</strong> <?php echo htmlspecialchars($row['yas']); ?><br>
                                    <strong>Şehir:</strong> <?php echo htmlspecialchars($row['city']); ?><br>
                                    <strong>İlçe:</strong> <?php echo htmlspecialchars($row['district']); ?><br>
                                    <strong>Cinsiyet:</strong> <?php echo htmlspecialchars($row['cinsiyet']); ?>
                                </p>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars(substr($row['aciklama'], 0, 100))); ?>...</p>
                                <a href="ilan-detay.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Detayları Gör</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Aramanıza uygun ilan bulunamadı.
            </div>
        <?php endif; ?>

        <a href="index.php" class="btn btn-secondary mt-3">Ana Sayfaya Dön</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Veritabanı bağlantısını kapat
mysqli_close($conn);
?>
