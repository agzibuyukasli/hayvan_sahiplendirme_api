<?php
session_start();
require_once "config.php";

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mevcut = $_POST["mevcut"];
    $yeni = $_POST["yeni"];

    $stmt = mysqli_prepare($conn, "SELECT sifre FROM userss WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row && $row["sifre"] == $mevcut) {
        $stmt = mysqli_prepare($conn, "UPDATE userss SET sifre = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $yeni, $_SESSION["id"]);
        mysqli_stmt_execute($stmt);
        $msg = "Şifre başarıyla güncellendi.";
    } else {
        $msg = "Mevcut şifreniz yanlış.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Şifre Değiştir</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Şifre Değiştir</h2>
    <form method="post">
        <div class="form-group">
            <label>Mevcut Şifre</label>
            <input type="password" name="mevcut" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Yeni Şifre</label>
            <input type="password" name="yeni" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Şifreyi Güncelle</button>
    </form>
    <br>
    <?php if ($msg): ?>
        <div class="alert alert-info"><?php echo $msg; ?></div>
    <?php endif; ?>
</body>
</html>

