<?php
$plainPassword = "eren123";

// Generate Hash
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// Print passwords
echo "Plain Password: " . $plainPassword . "<br>";
echo "Hashlenmiş Şifre: " . $hashedPassword;
?>
