<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id > 0) {
        $sql = "DELETE FROM event_registrations WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            header("Location: index.php");
            exit;
        } else {
            echo "Silme sırasında hata oluştu: " . $conn->error;
        }
    } else {
        echo "Geçersiz ID.";
    }
} else {
    echo "ID belirtilmedi.";
}
?>
