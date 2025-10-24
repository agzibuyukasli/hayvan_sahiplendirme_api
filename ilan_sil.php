<?php
// Oturum başlatma
session_start();

// Kullanıcı girişi kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: login.php");
    exit();
}

// Veritabanı bağlantısı
require_once("baglan.php");

// İlan ID kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // İlan ID geçerli değilse, geri dön
    header("Location: ilanlarim.php");
    exit();
}

$ilan_id = intval($_GET['id']);
$kullanici_id = $_SESSION['kullanici_id'];

// İlan sahibi kontrolü
$kontrol_query = "SELECT * FROM hayvan_ilanlari WHERE ilan_id = ? AND kullanici_id = ?";
$kontrol_stmt = $conn->prepare($kontrol_query);
$kontrol_stmt->bind_param("ii", $ilan_id, $kullanici_id);
$kontrol_stmt->execute();
$kontrol_result = $kontrol_stmt->get_result();

if ($kontrol_result->num_rows == 0) {
    // Kullanıcı bu ilanın sahibi değilse
    header("Location: ilanlarim.php");
    exit();
}

// İlanı silme işlemi
$sil_query = "DELETE FROM hayvan_ilanlari WHERE ilan_id = ? AND kullanici_id = ?";
$sil_stmt = $conn->prepare($sil_query);
$sil_stmt->bind_param("ii", $ilan_id, $kullanici_id);

if ($sil_stmt->execute()) {
    // Silme başarılı
    header("Location: ilanlarim.php?silmesaj=başarılı");
    exit();
} else {
    // Silme işlemi başarısız olursa
    error_log("Silme işlemi başarısız oldu: " . $sil_stmt->error);  // Hata mesajını loglayalım
    header("Location: ilanlarim.php?silmesaj=hata");
    exit();
}
?>
