<?php
session_start();
include '../db/config.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Set timezone to avoid date parsing issues
date_default_timezone_set('Asia/Kolkata'); // Adjust to your timezone

// Query to fetch active booking
$activeBooking = $conn->prepare("SELECT * FROM spots WHERE user_id = ? AND status = 'booked'");
$activeBooking->bind_param("i", $user_id);
$activeBooking->execute();
$result = $activeBooking->get_result();

// Debug: Log the user_id and query result
error_log("User ID: $user_id, Number of rows: " . $result->num_rows);
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
        $rawEnd = trim($spot['booking_end']); // Trim to remove potential whitespace
        $formattedEnd = null;

        // Debug: Log raw booking_end value
        error_log("Raw booking_end for spot ID {$spot['id']}: " . var_export($rawEnd, true));

        // Try parsing booking_end with multiple formats
        $date = false;
        if (!empty($rawEnd)) {
            // Try standard MySQL DATETIME format
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $rawEnd);
            if ($date === false) {
                // Try alternative format (e.g., if stored as d-m-Y H:i:s)
                $date = DateTime::createFromFormat('d-m-Y H:i:s', $rawEnd);
            }
            if ($date !== false) {
                $formattedEnd = $date->format('Y-m-d\TH:i:s');
            }
        }

        // Fallback: If booking_end is invalid, set a temporary future time for testing
        if (!$formattedEnd) {
            error_log("Invalid booking_end for spot ID {$spot['id']}. Using fallback time.");
            $formattedEnd = date('Y-m-d\TH:i:s', strtotime('+1 hour')); // Fallback to 1 hour from now
        }
    ?>
        <div class="card active-parking-info">
            <p><strong>🅿️ Spot:</strong> <?= htmlspecialchars($spot['spot_number']) ?></p>
            <p><strong>⏰ Booking Ends At:</strong> <?= $formattedEnd ? htmlspecialchars($formattedEnd) : 'Not Set' ?></p>
            <p><strong>⏳ Time Remaining:</strong> <span id="countdown">Loading...</span></p>

            <form action="../actions/end_parking.php" method="POST">
                <input type="hidden" name="spot_id" value="<?= $spot['id'] ?>">
                <button type="submit" class="btn-end">End Parking</button>
            </form>
        </div>

        <script>
            const endTime = new Date("<?= $formattedEnd ?>").getTime();
            console.log("JavaScript endTime:", endTime); // Debug JavaScript date parsing

            function updateCountdown() {
                const now = new Date().getTime();
                const distance = endTime - now;

                if (isNaN(distance) || distance <= 0) {
                    document.getElementById("countdown").innerHTML = "⛔ Expired or Invalid";
                    console.error("Invalid countdown distance:", distance);
                    return;
                }

                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById("countdown").innerHTML =
                    `${hours}h ${minutes}m ${seconds}s`;
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);
        </script>

    <?php else: ?>
        <div class="card no-active">
            <p>You don’t have an active parking session.</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>