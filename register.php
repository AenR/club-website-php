<?php
include 'db.php';

$event_id = $_POST['event_id'];
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];

$stmt = $conn->prepare("INSERT INTO event_registrations (event_id, full_name, email, phone) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $event_id, $full_name, $email, $phone);
$stmt->execute();

header("Location: index.php");
exit;
?>
