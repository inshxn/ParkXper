<?php
session_start();
include '../includes/header.php';
require '../db/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/admin_login.php");
    exit;
}

if (!isset($_GET['location_id'])) {
    echo "Invalid request!";
    exit;
}

$location_id = (int) $_GET['location_id'];

// Fetch location name
$loc_stmt = $conn->prepare("SELECT name FROM locations WHERE id = ?");
$loc_stmt->bind_param("i", $location_id);
$loc_stmt->execute();
$loc_result = $loc_stmt->get_result();
$location = $loc_result->fetch_assoc();
?>

<h2>Manage Spots for: <?= htmlspecialchars($location['name']) ?></h2>
<a href="dashboard.php">← Back to Dashboard</a>
<br><br>

<table border="1" cellpadding="10">
    <tr>
        <th>Spot Number</th>
        <th>Status</th>
        <th>Booked By</th>
        <th>Start</th>
        <th>End</th>
        <th>Fine</th>
    </tr>

<?php
$spot_stmt = $conn->prepare("
    SELECT s.*, u.name AS user_name 
    FROM spots s 
    LEFT JOIN users u ON s.user_id = u.id 
    WHERE s.location_id = ?
    ORDER BY s.id ASC
");
$spot_stmt->bind_param("i", $location_id);
$spot_stmt->execute();
$spots_result = $spot_stmt->get_result();

while ($row = $spots_result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['spot_number']}</td>";
    echo "<td>" . ucfirst($row['status']) . "</td>";
    echo "<td>" . ($row['user_name'] ?? '—') . "</td>";
    echo "<td>" . ($row['booking_start'] ?? '—') . "</td>";
    echo "<td>" . ($row['booking_end'] ?? '—') . "</td>";
    echo "<td>" . ($row['fine_applied'] ? 'Yes' : 'No') . "</td>";
    echo "</tr>";
}
?>

</table>

<?php include '../includes/footer.php'; ?>
