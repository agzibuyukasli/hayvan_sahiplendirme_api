<?php 
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>miHav - Evcil Hayvan Sahiplendirme</title>
    <link rel="stylesheet" href="index_style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php"><h1 class="logo">miHav</h1></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Anasayfa</a></li>
                <li class="nav-item"><a class="nav-link" href="hakkimizda.php">Hakkımızda</a></li>
            </ul>
            <div class="d-flex">
                <a href="logout.php" class="btn btn-outline-dark me-2">Çıkış Yap</a>
                <a href="ilan_ekle.php" class="btn btn-dark">Ücretsiz İlan Ver</a>
            </div>
        </div>
    </div>
</nav>

<div class="hero-section">
    <div class="container text-center py-5">
        <h1 class="display-4 text-white">Evcil Hayvanlar</h1>
        <p class="lead text-white">Ücretsiz evcil hayvan sahiplendirme ilanları</p>

        <div class="search-container mt-4">
            <form action="ilanlarim.php" method="GET" class="card p-4">
                <h5>Kategoriler</h5>
                <div class="row justify-content-center">
                    <div class="col-md-4 mb-3">
                        <label for="category" class="form-label">Kategori Seçiniz</label>
                        <select class="form-select" name="kategori" id="category" onchange="guncelleTurSecenekleri()">
                            <option value="">Seçiniz</option>
                            <option value="kedi">Kedi</option>
                            <option value="kopek">Köpek</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="tur" class="form-label">Tür Seçiniz</label>
                        <select class="form-select" name="tur" id="tur">
                            <option value="">Önce kategori seçiniz</option>
                        </select>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">Ara</button>
                </div>
            </form>
        </div>
    </div>
</div>

<footer class="bg-dark text-white py-4">
    <div class="container text-center">
        <p>© 2025 miHav - Tüm Hakları Saklıdır</p>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const turSecenekleri = {
    kedi: ["British Shorthair", "Van Kedisi", "Scottish Fold", "Tekir"],
    kopek: ["Golden Retriever", "Labrador", "Pomeranian", "Sokak Köpeği"]
};

function guncelleTurSecenekleri() {
    const kategori = document.getElementById("category").value;
    const turSelect = document.getElementById("tur");

    turSelect.innerHTML = '<option value="">Tür Seçiniz</option>';

    if (kategori && turSecenekleri[kategori]) {
        turSecenekleri[kategori].forEach(tur => {
            const option = document.createElement("option");
            option.value = tur.toLowerCase().replace(/\s+/g, '-');
            option.textContent = tur;
            turSelect.appendChild(option);
        });
    }
}
</script>
</body>
</html>
