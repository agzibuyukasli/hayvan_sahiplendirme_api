<?php
$orijinal_sifre = 'admin1234';
$hash = '$2y$10$HkHmgzmQH.RP05FBxtpSD.nBk7c6SQVKyw.m5ofVFRf4VvaWOavR2'; // Veritabanındaki hash

if (password_verify($orijinal_sifre, $hash)) {
    echo "Şifre DOĞRU!";
} else {
    echo "Şifre YANLIŞ!";
}
