<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../user/timer.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$spot_id = intval($_POST['spot_id']);

// Reset spot
$update = $conn->prepare("UPDATE spots SET status = 'available', user_id = NULL, booking_start = NULL, booking_end = NULL WHERE id = ? AND user_id = ?");
$update->bind_param("ii", $spot_id, $user_id);

if ($update->execute()) {
    $_SESSION['message'] = "✅ Parking session ended.";
} else {
    $_SESSION['message'] = "❌ Failed to end parking.";
}

header("Location: ../user/timer.php");
exit();
