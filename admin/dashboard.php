<?php
session_start();
require '../db/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/admin_login.php");
    exit;
}

echo "<h2>Admin Dashboard</h2>";
echo "<a href='add_location.php'>+ Add New Location</a><br><br>";

$result = $conn->query("SELECT * FROM locations ORDER BY created_at DESC");

while ($row = $result->fetch_assoc()) {
    echo "<div>";
    echo "<h3>" . $row['name'] . "</h3>";
    echo "<p>" . $row['address'] . "</p>";
    echo "<p>Total Spots: " . $row['total_spots'] . "</p>";
    echo "<a href='manage_spots.php?location_id=" . $row['id'] . "'>Manage Spots</a>";
    echo "</div><hr>";
}

include '../includes/footer.php';
?>
