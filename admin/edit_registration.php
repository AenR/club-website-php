<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gerekli alanları al
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $full_name = isset($_POST['full_name']) ? $conn->real_escape_string($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : null;

    if ($id > 0 && $full_name && $email) {
        $sql = "UPDATE event_registrations SET full_name='$full_name', email='$email', phone=" . ($phone ? "'$phone'" : "NULL") . " WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            header("Location: index.php");
            exit;
        } else {
            echo "Hata: " . $conn->error;
        }
    } else {
        echo "Eksik ya da geçersiz veri.";
    }
} else {
    echo "Geçersiz istek yöntemi.";
}
?>
