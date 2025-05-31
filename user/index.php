<?php
session_start();
include '../db/config.php';

$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/x-icon" href="/ParkXpert/assets/fav.png">
    <link rel="icon" type="image/x-icon" href="../assets/Images/fav.png">
   
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ParkXpert - Home</title>
  <link rel="stylesheet" href="../assets/css/header.css" />
  <link rel="stylesheet" href="../assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <link rel="icon" type="image/png" href="/ParkXpert/assets/images/logo.png" sizes="50x50">
</head>
<body>

<div id="preloader">
  <div class="preloader-content">
    <h2>🚗 Loading ParkXpert...</h2>
  </div>
</div>

<?php include '../includes/header.php'; ?>

<?php
if (isset($_SESSION['success_message'])) {
    echo '<div class="success-message">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']);
}
?>

<main>

  <section class="hero-section">
    <h1 class="animate__animated animate__fadeInDown">Welcome to ParkXpert</h1>
    <p class="animate__animated animate__fadeInUp">Smart Parking System for Kashmir</p>
    <a href="#locations" class="cta-btn animate__animated animate__bounceIn">Get Started <i class="fas fa-arrow-down"></i></a>
  </section>

  <section id="locations" class="locations-section">
    <center><h2 class="animate__animated animate__fadeInUp">Available Parking Locations</h2></center>
    <?php
    $sql = "SELECT * FROM locations";
    $locationsResult = $conn->query($sql);

    if ($locationsResult->num_rows > 0): ?>
      <div class="locations-list">
        <?php while ($location = $locationsResult->fetch_assoc()):
          $locationId = $location['id'];
          $totalSpots = $location['total_spots'];
          
          $bookedSql = "SELECT COUNT(*) AS booked FROM bookings WHERE location_id = ? AND status = 'active'";
          $stmt = $conn->prepare($bookedSql);
          $stmt->bind_param("i", $locationId);
          $stmt->execute();
          $booked = $stmt->get_result()->fetch_assoc()['booked'];
          $available = $totalSpots - $booked;
        ?>
          <div class="location-card animate__animated animate__zoomIn" id="location-<?= $locationId ?>">
            <div class="location-icon"><i class="fas fa-parking"></i></div>
            <div class="location-info">
              <h3><?= $location['name'] ?></h3>
              <p><i class="fas fa-map-marker-alt"></i> <?= $location['address'] ?></p>
              <p><i class="fas fa-car"></i> Total Spots: <?= $totalSpots ?></p>
              <p class="available-spots"><i class="fas fa-check-circle"></i> Available: <?= $available ?></p>
            </div>
            <a href="book.php?location_id=<?= $locationId ?>" class="btn-book">Book Spot</a>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p class="no-locations">No locations available right now.</p>
    <?php endif; ?>
  </section>

  <?php if ($isLoggedIn): ?>
  <section class="user-bookings-section">
    <center><h2>Your Active Bookings</h2></center>
    <?php
      $userId = $_SESSION['user_id'];
      $bookingSql = "SELECT b.*, l.name AS location_name, l.address, s.spot_number FROM bookings b
                     JOIN locations l ON b.location_id = l.id
                     LEFT JOIN spots s ON b.spot_id = s.id
                     WHERE b.user_id = ? AND b.status = 'active'";
      $stmt = $conn->prepare($bookingSql);
      $stmt->bind_param("i", $userId);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0): ?>
        <div class="bookings-list">
        <?php while ($booking = $result->fetch_assoc()):
          $start = strtotime($booking['start_time']);
          $duration = (int)$booking['duration_minutes'];
          $end = $start + ($duration * 60);
          $remaining = max(0, $end - time());
          $hrs = floor($remaining / 3600);
          $min = floor(($remaining % 3600) / 60);
          $sec = $remaining % 60;
        ?>
          <div class="booking-card animate__animated animate__fadeInLeft">
            <div class="booking-info">
              <h3><?= $booking['location_name'] ?> - Spot #<?= $booking['spot_number'] ?? 'N/A' ?></h3>
              <p><i class="fas fa-map-marker-alt"></i> <?= $booking['address'] ?></p>
              <p><i class="fas fa-clock"></i> Start: <?= date("d M Y, h:i A", $start) ?></p>
              <p><i class="fas fa-hourglass-half"></i> Remaining: <?= sprintf("%02d:%02d:%02d", $hrs, $min, $sec) ?></p>
            </div>
            <a href="../actions/end_parking.php?booking_id=<?= $booking['id'] ?>" class="btn-end">End</a>
          </div>
        <?php endwhile; ?>
        </div>
    <?php else: ?>
      <p class="no-bookings">You have no active bookings.</p>
    <?php endif; ?>
  </section>
  <?php endif; ?>

  <section class="districts-section">
    <center><h2 class="animate__animated animate__fadeInUp">Explore by District</h2></center>
    <div class="districts-grid">
      <?php
        $districts = ['Srinagar', 'Baramulla', 'Anantnag', 'Pulwama', 'Budgam', 'Kupwara', 'Shopian', 'Kulgam', 'Bandipora', 'Ganderbal'];
        foreach ($districts as $d): ?>
          <div class="district-card animate__animated animate__zoomIn">
            <i class="fas fa-map-marked-alt"></i>
            <h3><?= $d ?></h3>
          </div>
      <?php endforeach; ?>
    </div>
  </section>
  <section class="feedback-section">
    <center><h2 class="animate__animated animate__fadeIn">We Value Your Feedback</h2></center>
    <form action="../actions/submit_feedback.php" method="POST" class="feedback-form animate__animated animate__fadeInUp">
      <input type="text" name="name" placeholder="Your Name" required />
      <input type="email" name="email" placeholder="Your Email" required />
      <textarea name="message" placeholder="Your Message..." rows="5" required></textarea>
      <button type="submit" class="btn-feedback">Submit</button>
    </form>
  </section>

</main>

<?php include '../includes/footer.php'; ?>

<script>
  window.onload = function () {
    setTimeout(() => {
      document.getElementById('preloader').style.display = 'none';
    }, 2000);
  };
</script>

<script src="../assets/js/script.js"></script>

</body>
</html>