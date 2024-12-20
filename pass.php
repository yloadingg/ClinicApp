<?php
include 'db.php';  // Assuming you have a db.php file that connects to the database

// Set admin details
$username = 'admin';
$email = 'admin@example.com';
$password = 'adminpassword'; // Replace with a strong password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT); // Hash the password

// Prepare SQL query to insert the admin user
$stmt = $pdo->prepare("INSERT INTO users (username, password, role) 
                       VALUES (?, ?, ?, ?)");
$stmt->execute([$username, $email, $hashedPassword, 'admin']);

echo "Admin account created successfully!";
?>
