<?php
session_start();
include '../db/config.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$activeBooking = $conn->prepare("SELECT * FROM spots WHERE user_id = ? AND status = 'booked'");
$activeBooking->bind_param("i", $user_id);
$activeBooking->execute();
$result = $activeBooking->get_result();
?>

<link rel="stylesheet" href="../assets/css/timer.css">
<link rel="icon" type="image/png" href="/ParkXpert/assets/images/logo.png" sizes="50x50">

<div class="container">
    <h2 class="heading">🚗 Your Active Parking</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="notification">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): 
        $spot = $result->fetch_assoc();
    ?>
        <div class="card active-parking-info">
            <p><strong>🅿️ Spot:</strong> <?= htmlspecialchars($spot['spot_number']) ?></p>
            <p><strong>⏰ Booking Ends At:</strong> <?= htmlspecialchars($spot['booking_end']) ?></p>
            <p><strong>⏳ Time Remaining:</strong> <span id="countdown">Loading...</span></p>

            <form action="../actions/end_parking.php" method="POST">
                <input type="hidden" name="spot_id" value="<?= $spot['id'] ?>">
                <button type="submit" class="btn-end">End Parking</button>
            </form>
        </div>

        <script>
            const endTime = new Date("<?= $spot['booking_end'] ?>").getTime();

            function updateCountdown() {
                const now = new Date().getTime();
                const distance = endTime - now;

                if (distance <= 0) {
                    document.getElementById("countdown").innerHTML = "⛔ Expired";
                    return;
                }

                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById("countdown").innerHTML =
                    `${hours}h ${minutes}m ${seconds}s`;
            }

            updateCountdown(); // Initial
            setInterval(updateCountdown, 1000);
        </script>

    <?php else: ?>
        <div class="card no-active">
            <p>You don’t have an active parking session.</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
