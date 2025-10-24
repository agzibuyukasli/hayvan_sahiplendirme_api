<?php
session_start();
include("config.php");

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$action = $_GET['action'] ?? '';
$listing_id = $_GET['id'] ?? 0;

switch ($action) {
    case 'approve':
        if ($listing_id > 0) {
            $sql = "UPDATE ilanlar SET status = 'approved' WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $listing_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "İlan başarıyla onaylandı.";
            } else {
                $_SESSION['error'] = "İlan onaylanırken bir hata oluştu.";
            }
        }
        header("Location: admin_panel.php");
        break;

    case 'delete':
        if ($listing_id > 0) {
            $sql = "SELECT resim_url FROM ilanlar WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $listing_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $listing = $result->fetch_assoc();
                $image_path = $listing['resim_url']; // uploads/ kaldırıldı

                if (file_exists($image_path)) {
                    unlink($image_path);
                }

                $delete_sql = "DELETE FROM ilanlar WHERE id = ?";
                $delete_stmt = $conn->prepare($delete_sql);
                $delete_stmt->bind_param("i", $listing_id);
                if ($delete_stmt->execute()) {
                    $_SESSION['message'] = "İlan başarıyla silindi.";
                } else {
                    $_SESSION['error'] = "İlan silinirken bir hata oluştu.";
                }
            }
        }
        header("Location: admin_panel.php");
        break;

    case 'mark_adopted':
        if ($listing_id > 0) {
            $sql = "UPDATE ilanlar SET sahiplenildiMi = 1 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $listing_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Hayvan sahiplendirildi olarak işaretlendi.";
            } else {
                $_SESSION['error'] = "İşlem sırasında bir hata oluştu.";
            }
        }
        header("Location: admin_panel.php");
        break;

    case 'add_listing':
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = trim($_POST['name'] ?? '');
            $animal_type = trim($_POST['animal_type'] ?? '');
            $age = trim($_POST['age'] ?? '');
            $gender = trim($_POST['gender'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $city = trim($_POST['city'] ?? '');
            $district = trim($_POST['district'] ?? '');

            if (isset($_FILES['resim_url']) && $_FILES['resim_url']['error'] == 0) {
                $file_name = time() . '_' . basename($_FILES['resim_url']['name']);
                $target_file = $file_name; // uploads/ kaldırıldı
                $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                $check = getimagesize($_FILES['resim_url']['tmp_name']);
                if ($check !== false) {
                    if ($_FILES['resim_url']['size'] < 5000000) {
                        if (in_array($file_type, ["jpg", "jpeg", "png", "gif"])) {
                            if (move_uploaded_file($_FILES['resim_url']['tmp_name'], $target_file)) {
                                $sql = "INSERT INTO ilanlar (isim, hayvan_turu, yas, cinsiyet, aciklama, city, district, resim_url, status, kullanici_id, eklenme_tarihi) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'approved', '6', NOW())";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("ssssssss", $name, $animal_type, $age, $gender, $description, $city, $district, $file_name);

                                if ($stmt->execute()) {
                                    $_SESSION['message'] = "İlan başarıyla eklendi.";
                                } else {
                                    $_SESSION['error'] = "İlan eklenirken bir hata oluştu: " . $stmt->error;
                                }
                            } else {
                                $_SESSION['error'] = "Dosya yüklenirken bir hata oluştu.";
                            }
                        } else {
                            $_SESSION['error'] = "Sadece JPG, JPEG, PNG ve GIF dosyaları yükleyebilirsiniz.";
                        }
                    } else {
                        $_SESSION['error'] = "Dosya boyutu çok büyük (max 5MB).";
                    }
                } else {
                    $_SESSION['error'] = "Yüklenen dosya bir resim değil.";
                }
            } else {
                $_SESSION['error'] = "Lütfen bir resim seçin.";
            }
        }
        header("Location: admin_panel.php");
        break;

    default:
        header("Location: admin_panel.php");
}
?>
