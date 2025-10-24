<?php
// Oturum başlat
session_start();
 
// Kullanıcı giriş yapmış mı kontrol et
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>