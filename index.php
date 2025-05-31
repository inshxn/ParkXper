<?php
session_start();
include '../db/config.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$sql = "SELECT * FROM locations";
$locationsResult = $conn->query($sql);

$user_id = $_SESSION['user_id'];
$bookingSql = "SELECT * FROM spots WHERE user_id = $user_id AND status = 'booked'";
$activeBooking = $conn->query($bookingSql)->fetch_assoc();
?>

<div id="preloader">
    <div class="loader-text">Welcome to <span>ParkXpert</span></div>
</div>


<div class="stars"></div>
<div class="solar-container">
    <div class="sun"></div>
    <div class="planet planet1"></div>
    <div class="planet planet2"></div>
</div>

<div class="container">
    <h2 class="page-title"><i class="fas fa-map-marker-alt"></i> Available Parking Locations</h2>

    <?php if ($activeBooking): ?>
        <div class="booking-alert">
            <p>🚗 You have an active booking at <strong><?= $activeBooking['spot_number'] ?></strong></p>
            <a href="timer.php" class="manage-btn">Manage Booking</a>
        </div>
    <?php endif; ?>

    <?php if ($locationsResult->num_rows > 0): ?>
        <div class="locations-list">
            <?php while ($location = $locationsResult->fetch_assoc()): ?>
                <div class="location-box">
                    <div class="icon"><i class="fas fa-parking"></i></div>
                    <h3><?= $location['name'] ?></h3>
                    <p class="address"><i class="fas fa-map-marker-alt"></i> <?= $location['address'] ?></p>
                    <div class="details">
                        <p><strong>Total Spots:</strong> <?= $location['total_spots'] ?></p>
                        <p><strong>Available:</strong> <?= $location['available_spots'] ?></p>
                    </div>
                    <a href="book.php?location_id=<?= $location['id'] ?>" class="book-btn">Book Spot</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="no-location">🚫 No locations available for booking at the moment.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

<!-- Scripts -->
<link rel="stylesheet" href="../assets/css/header.css">
<link rel="stylesheet" href="../assets/css/solar.css">
<link rel="stylesheet" href="../assets/css/style.css">
<script src="https://kit.fontawesome.com/a2d9a34c2c.js" crossorigin="anonymous"></script>
<script>
    // Preloader
    window.addEventListener('load', function() {
        document.getElementById('preloader').style.display = 'none';
    });
</script>
