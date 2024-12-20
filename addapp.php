<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user's active appointments
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE user_id = ? AND status != 'completed'");
$stmt->execute([$userId]);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <header>
      
    </header>
    <main>
        <div class="container">
            <h1>My Appointments</h1>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Age</th>
                        <th>Address</th>
                        <th>Appointment Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['age']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['address']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="add_appointment.php"><button>Add New Appointment</button></a>
            <a href="logout.php"><button>Log Out</button></a>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Samarinians Clinic. All rights reserved.</p>
    </footer>
</body>
</html>
