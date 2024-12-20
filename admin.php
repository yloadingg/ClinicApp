<?php
session_start();
include 'db.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all appointments
$stmt = $pdo->query("SELECT appointments.*, users.username FROM appointments 
                     JOIN users ON appointments.user_id = users.id");
$appointments = $stmt->fetchAll();

// Handle actions (approve, reschedule, complete)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $appointmentId = $_POST['appointment_id'];

        if ($action == 'approve') {
            $stmt = $pdo->prepare("UPDATE appointments SET status = 'approved' WHERE id = ?");
            $stmt->execute([$appointmentId]);
        } elseif ($action == 'reschedule') {
            $newSchedule = $_POST['new_schedule'];
            $stmt = $pdo->prepare("UPDATE appointments SET status = 'rescheduled', new_schedule = ? WHERE id = ?");
            $stmt->execute([$newSchedule, $appointmentId]);
        } elseif ($action == 'complete') {
            $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
            $stmt->execute([$appointmentId]);
        }
        header("Location: admin.php");
        exit();
    }

    // Create new admin
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new admin into the users table
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hashedPassword, 'admin']);

        echo "Admin account created successfully!";
    }
}
?>

<h1>Admin Panel</h1>
<table border="1">
    <tr>
        <th>User</th>
        <th>Name</th>
        <th>Number</th>
        <th>Age</th>
        <th>Address</th>
        <th>Appointment Date</th>
        <th>New Schedule</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($appointments as $appointment): ?>
        <tr>
            <td><?= $appointment['username']; ?></td>
            <td><?= $appointment['name']; ?></td>
            <td><?= $appointment['phone_number']; ?></td>
            <td><?= $appointment['age']; ?></td>
            <td><?= $appointment['address']; ?></td>
            <td><?= $appointment['appointment_date']; ?></td>
            <td><?= $appointment['new_schedule'] ?: 'N/A'; ?></td>
            <td><?= ucfirst($appointment['status']); ?></td>
            <td>
                <?php if ($appointment['status'] == 'pending'): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="appointment_id" value="<?= $appointment['id']; ?>">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit">Approve</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="appointment_id" value="<?= $appointment['id']; ?>">
                        <input type="hidden" name="action" value="reschedule">
                        <input type="datetime-local" name="new_schedule" required>
                        <button type="submit">Reschedule</button>
                    </form>
                <?php elseif ($appointment['status'] == 'approved' || $appointment['status'] == 'rescheduled'): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="appointment_id" value="<?= $appointment['id']; ?>">
                        <input type="hidden" name="action" value="complete">
                        <button type="submit">Mark as Completed</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>Create Admin</h2>
<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Create Admin</button>
</form>

<a href="logout.php">Log Out</a>
<html>
    <head>
    <link rel="stylesheet" href="admin.css">
    </head>
</html>