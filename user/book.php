<?php
session_start();
include '../db/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$locationId = isset($_GET['location_id']) ? (int)$_GET['location_id'] : 0;
$sql = "SELECT * FROM locations WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $locationId);
$stmt->execute();
$location = $stmt->get_result()->fetch_assoc();

if (!$location) {
    header("Location: index.php");
    exit();
}

$totalSpots = $location['total_spots'];
$bookedSql = "SELECT COUNT(*) AS booked FROM bookings WHERE location_id = ? AND status = 'active'";
$stmt = $conn->prepare($bookedSql);
$stmt->bind_param("i", $locationId);
$stmt->execute();
$booked = $stmt->get_result()->fetch_assoc()['booked'];
$available = $totalSpots - $booked;

$pricePerHour = 20;
$maxHours = 4;
$durationOptions = range(0.5, $maxHours, 0.5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book a Spot - ParkXpert</title>
    <link rel="stylesheet" href="../assets/css/book.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="icon" type="image/png" href="/ParkXpert/assets/images/logo.png" sizes="50x50">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main>
        <section class="booking-form-section animate__animated animate__fadeInUp">
            <h2>Book a Parking Spot at <span class="location-highlight"><?= htmlspecialchars($location['name']) ?></span></h2>
            <p class="location-address"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($location['address']) ?></p>
            
            <div class="spot-info">
                <h3>Spot Availability</h3>
                <p><i class="fas fa-parking"></i> Total Spots: <span id="total-spots"><?= $totalSpots ?></span></p>
                <p><i class="fas fa-check-circle"></i> Available Spots: <span id="available-spots"><?= $available ?></span></p>
                <p><i class="fas fa-car"></i> Booked Spots: <span id="booked-spots"><?= $booked ?></span></p>
            </div>

            <div class="pricing-info">
                <h3>Pricing</h3>
                <p><i class="fas fa-rupee-sign"></i> ₹<?= $pricePerHour ?> per hour</p>
            </div>

            <form id="booking-form" class="booking-form">
                <input type="hidden" name="location_id" value="<?= $locationId ?>">
                
                <div class="form-group">
                    <label for="duration"><i class="fas fa-clock"></i> Select Duration:</label>
                    <select name="duration_minutes" id="duration" required>
                        <option value="" disabled selected>Choose duration</option>
                        <?php foreach ($durationOptions as $hours): ?>
                            <option value="<?= $hours * 60 ?>"><?= $hours ?> hour<?= $hours > 1 ? 's' : '' ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-rupee-sign"></i> Total Cost:</label>
                    <span id="total-price">₹0.00</span>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-book" <?php if ($available <= 0) echo 'disabled title="No spots available"'; ?>>
                        <i class="fas fa-parking"></i> Confirm Booking
                    </button>
                    <a href="profile.php" class="btn-manage"><i class="fas fa-user-cog"></i> Manage Parking</a>
                </div>
            </form>
        </section>

        <div id="booking-modal" class="modal">
            <div class="modal-content">
                <span class="modal-close">×</span>
                <p id="modal-message"></p>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script>
        function updatePrice() {
            try {
                const durationSelect = document.getElementById('duration');
                const totalPrice = document.getElementById('total-price');
                const pricePerHour = <?= $pricePerHour ?>;
                const minutes = parseFloat(durationSelect.value);
                if (!isNaN(minutes) && minutes > 0) {
                    const hours = minutes / 60;
                    const total = hours * pricePerHour;
                    totalPrice.textContent = `₹${total.toFixed(2)}`;
                } else {
                    totalPrice.textContent = '₹0.00';
                }
            } catch (error) {
                console.error('Error in updatePrice:', error);
            }
        }

        const durationSelect = document.getElementById('duration');
        durationSelect.addEventListener('change', updatePrice);
        updatePrice();

        const form = document.getElementById('booking-form');
        const modal = document.getElementById('booking-modal');
        const modalMessage = document.getElementById('modal-message');
        const modalClose = document.querySelector('.modal-close');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);

            fetch('../actions/book_spot.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP ${response.status}: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                modalMessage.textContent = data.message;
                modal.className = `modal ${data.status}-message`;
                modal.style.display = 'block';
                
                if (data.status === 'success') {
                    const availableSpots = document.getElementById('available-spots');
                    const bookedSpots = document.getElementById('booked-spots');
                    const currentAvailable = parseInt(availableSpots.textContent);
                    availableSpots.textContent = currentAvailable - 1;
                    bookedSpots.textContent = parseInt(bookedSpots.textContent) + 1;
                    form.reset();
                    updatePrice();
                    form.querySelector('button[type="submit"]').disabled = currentAvailable - 1 <= 0;
                }
            })
            .catch(error => {
                modalMessage.textContent = `Error: Unable to process booking - ${error.message}`;
                modal.className = 'modal error-message';
                modal.style.display = 'block';
            });
        });

        modalClose.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });

        window.onload = function () {
            setTimeout(() => {
                const preloader = document.getElementById('preloader');
                if (preloader) preloader.style.display = 'none';
            }, 2000);
        };
    </script>
</body>
</html>