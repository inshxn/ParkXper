<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/admin_login.php");
    exit;
}
?>

<h2>Add Parking Location</h2>
<form method="POST" action="../actions/add_location_action.php">
    <input type="text" name="name" placeholder="Location Name" required><br>
    <textarea name="address" placeholder="Full Address" required></textarea><br>
    <input type="number" name="total_spots" placeholder="Total Spots" required><br>
    <button type="submit">Add Location</button>
</form>

<?php include '../includes/footer.php'; ?>
