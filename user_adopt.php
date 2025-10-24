<?php
session_start();
include("db_connection.php");

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if listing ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$listing_id = $_GET['id'];

// Get listing details
$sql = "SELECT * FROM ilanlar WHERE id = ? AND status = 'approved' AND adopted = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $listing_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    $_SESSION['error'] = "İlan bulunamadı veya bu hayvan zaten sahiplendirilmiş.";
    header("Location: index.php");
    exit;
}

$listing = $result->fetch_assoc();

// Process adoption request
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $reason = trim($_POST['reason']);
    
    // Insert adoption request
    $sql = "INSERT INTO adoption_requests (listing_id, user_id, name, email, phone, reason, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissss", $listing_id, $_SESSION['user_id'], $name, $email, $phone, $reason);
    
    if($stmt->execute()) {
        $_SESSION['message'] = "Sahiplenme talebiniz başarıyla gönderildi. İlan sahibi sizinle iletişime geçecektir.";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['error'] = "Talebiniz gönderilirken bir hata oluştu.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>miHav - Sahiplenme Talebi</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Sahiplenme Talebi</h1>
        
        <div class="adoption-details">
            <div class="animal-image">
                <img src="<?php echo htmlspecialchars($listing['resim_url']); ?>" alt="<?php echo htmlspecialchars($listing['name']); ?>">
            </div>
            
            <div class="animal-info">
                <h2><?php echo htmlspecialchars($listing['name']); ?></h2>
                <p><strong>Tür:</strong> <?php echo htmlspecialchars($listing['animal_type']); ?></p>
                <p><strong>Yaş:</strong> <?php echo htmlspecialchars($listing['age']); ?></p>
                <p><strong>Cinsiyet:</strong> <?php echo htmlspecialchars($listing['gender']); ?></p>
                <p><strong>Açıklama:</strong> <?php echo htmlspecialchars($listing['description']); ?></p>
            </div>
        </div>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="name">Adınız Soyadınız</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">E-posta Adresiniz</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Telefon Numaranız</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            
            <div class="form-group">
                <label for="reason">Neden Bu Hayvanı Sahiplenmek İstiyorsunuz?</label>
                <textarea id="reason" name="reason" rows="5" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Sahiplenme Talebini Gönder</button>
            <a href="index.php" class="btn btn-secondary">Vazgeç</a>
        </form>
    </div>
</body>
</html>