<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // MySQL kullanıcı adınız
define('DB_PASSWORD', ''); // MySQL şifreniz
define('DB_NAME', 'hayvan_sahiplendirme'); // Veritabanı adı

// Veritabanı bağlantısı
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Bağlantıyı kontrol et
if($conn === false){
    die("HATA: Veritabanına bağlanılamadı. " . mysqli_connect_error());
}

// Türkçe karakter desteği için
mysqli_set_charset($conn, "utf8");
?>