<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['event_image'], $_POST['event_id'])) {
    $eventId = intval($_POST['event_id']);

    $fileTmpPath = $_FILES['event_image']['tmp_name'];
    $fileName = basename($_FILES['event_image']['name']);
    $newFileName = time() . '_' . $fileName;

    // Gerçek dosya yolu (sunucu tarafı için)
    $uploadDirServer = __DIR__ . '/../uploads/';
    $destPathServer = $uploadDirServer . $newFileName;

    // Veritabanına kaydedilecek yol (tarayıcıdan erişilebilir)
    $imageURL = 'uploads/' . $newFileName;

    // Yükleme işlemi
    if (move_uploaded_file($fileTmpPath, $destPathServer)) {
        $stmt = $conn->prepare("UPDATE events SET image=? WHERE id=?");
        $stmt->bind_param("si", $imageURL, $eventId);
        $stmt->execute();
        header("Location: index.php");
        exit;
    } else {
        echo "Image upload failed.";
    }
} else {
    echo "Invalid request.";
}
?>
