<?php
session_start();
include '../db.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? '';
    $image = $_POST['image'] ?? '';
    $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 0;

    if (!$id) {
        http_response_code(400);
        echo "ID is required";
        exit;
    }

    $stmt = $conn->prepare("UPDATE events SET name = ?, image = ?, is_active = ? WHERE id = ?");
    $stmt->bind_param("ssii", $name, $image, $is_active, $id);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        http_response_code(500);
        echo "Error updating event";
    }
    exit;
}

http_response_code(405);
echo "Method not allowed";
?>
