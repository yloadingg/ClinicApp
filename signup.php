<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email is empty
    if (empty($email)) {
        echo "Email cannot be empty!";
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format!";
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Check if the email already exists in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        echo "This email is already registered!";
        exit();
    }

    // Insert the new user into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$username, $email, $hashedPassword]);
        echo "Sign up successful! You can now log in.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Samarinians Clinic - Sign Up</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="images/Slogo.png" alt="Samarinians Clinic" height="50" width="55">
            <span class="title">SAMARINIANS DENTAL CLINIC</span>
        </div>
    </header>
    <main>
        <div class="container">
            <form method="POST" action="signup.php">
                <h2>Sign Up</h2>
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Sign Up</button>
            </form>
            <a href="login.php"><button class="login-btn">Log In</button></a>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Samarinians Clinic. All rights reserved.</p>
    </footer>
</body>
</html>