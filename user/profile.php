<?php
session_start();
require_once '../auth/auth_check.php';
require_once '../db/config.php';

$user_id = $_SESSION['user_id'];

$user_query = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();

$active_query = $conn->prepare("SELECT b.*, l.name AS location_name, s.spot_number 
    FROM bookings b 
    JOIN locations l ON b.location_id = l.id 
    JOIN spots s ON b.spot_id = s.id 
    WHERE b.user_id = ? AND b.status = 'active'");
$active_query->bind_param("i", $user_id);
$active_query->execute();
$active_result = $active_query->get_result();
$active_booking = $active_result->fetch_assoc();

$history_query = $conn->prepare("SELECT b.*, l.name AS location_name, s.spot_number 
    FROM bookings b 
    JOIN locations l ON b.location_id = l.id 
    JOIN spots s ON b.spot_id = s.id 
    WHERE b.user_id = ? ORDER BY b.booking_time DESC");
$history_query->bind_param("i", $user_id);
$history_query->execute();
$history_result = $history_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Profile - ParkXpert</title>
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>
<body>

<?php include '../includes/header.php'; ?>
<link rel="icon" type="image/png" href="/ParkXpert/assets/images/logo.png" sizes="50x50">

<div class="profile-container">
    <h2>Welcome, <?= htmlspecialchars($user['name']) ?></h2>

    <div class="card profile-details">
        <h3>Your Details</h3>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?></p>
        <p><strong>Aadhaar:</strong> <?= htmlspecialchars($user['aadhaar']) ?></p>
        <p><strong>Car Number:</strong> <?= htmlspecialchars($user['car_number']) ?></p>
    </div>

    <div class="card update-profile">
        <h3>Update Profile</h3>
        <form action="../actions/update_profile.php" method="POST">
            <input type="hidden" name="user_id" value="<?= $user_id ?>">
            <input type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($user['name']) ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($user['email']) ?>" required>
            <input type="text" name="address" placeholder="Address" value="<?= htmlspecialchars($user['address']) ?>">
            <input type="text" name="aadhaar" placeholder="Aadhaar" value="<?= htmlspecialchars($user['aadhaar']) ?>">
            <input type="text" name="car_number" placeholder="Car Number" value="<?= htmlspecialchars($user['car_number']) ?>">
            <button type="submit">Update</button>
        </form>
    </div>

    <?php if ($active_booking): ?>
    <div class="card active-booking">
        <h3>Active Booking</h3>
        <p><strong>Location:</strong> <?= htmlspecialchars($active_booking['location_name']) ?></p>
        <p><strong>Spot:</strong> <?= htmlspecialchars($active_booking['spot_number']) ?></p>
        <p><strong>Start Time:</strong> <?= date("d M Y, h:i A", strtotime($active_booking['start_time'])) ?></p>
        <p><strong>Duration:</strong> <?= $active_booking['duration_minutes'] ?> mins</p>
        <p><strong>Ends At:</strong> 
            <?= date("d M Y, h:i A", strtotime($active_booking['start_time'] . " + {$active_booking['duration_minutes']} minutes")) ?>
        </p>
        <a href="timer.php?booking_id=<?= $active_booking['id'] ?>" class="btn">View Timer</a>
        <a href="../actions/end_parking.php?booking_id=<?= $active_booking['id'] ?>" class="btn danger">End Parking</a>
    </div>
    <?php else: ?>
    <div class="card">
        <p>No active booking currently.</p>
    </div>
    <?php endif; ?>

    <div class="card booking-history">
        <h3>Booking History</h3>
        <?php if ($history_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>Spot</th>
                        <th>Start Time</th>
                        <th>Duration</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = $history_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['location_name']) ?></td>
                        <td><?= htmlspecialchars($booking['spot_number']) ?></td>
                        <td><?= date("d M Y, h:i A", strtotime($booking['start_time'])) ?></td>
                        <td><?= $booking['duration_minutes'] ?> mins</td>
                        <td><?= ucfirst($booking['status']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No bookings found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
