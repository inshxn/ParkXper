<?php
session_start();
include '../db/config.php';

header('Content-Type: application/json');
ob_start();

if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Please log in to book a spot.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

$userId = $_SESSION['user_id'];
$locationId = isset($_POST['location_id']) ? (int)$_POST['location_id'] : 0;
$duration = isset($_POST['duration_minutes']) ? (int)$_POST['duration_minutes'] : 0;

if ($locationId <= 0 || $duration <= 0) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Invalid location or duration.']);
    exit();
}

if ($conn->connect_error) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit();
}

$tables = ['spots', 'bookings', 'locations'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows === 0) {
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => "Table `$table` does not exist."]);
        exit();
    }
}

$requiredBookingColumns = ['user_id', 'location_id', 'spot_id', 'start_time', 'duration_minutes', 'status', 'booking_time'];
$columns = $conn->query("SHOW COLUMNS FROM bookings");
$existingColumns = [];
while ($col = $columns->fetch_assoc()) {
    $existingColumns[] = $col['Field'];
}
foreach ($requiredBookingColumns as $col) {
    if (!in_array($col, $existingColumns)) {
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => "Missing column `$col` in bookings table."]);
        exit();
    }
}

$requiredSpotColumns = ['id', 'location_id', 'status', 'user_id', 'booking_start'];
$columns = $conn->query("SHOW COLUMNS FROM spots");
$existingColumns = [];
while ($col = $columns->fetch_assoc()) {
    $existingColumns[] = $col['Field'];
}
foreach ($requiredSpotColumns as $col) {
    if (!in_array($col, $existingColumns)) {
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => "Missing column `$col` in spots table."]);
        exit();
    }
}

$statusColumn = $conn->query("SHOW COLUMNS FROM spots LIKE 'status'")->fetch_assoc();
$statusType = $statusColumn['Type'];
$statusValue = 'occupied';
if (strpos($statusType, "enum") !== false) {
    preg_match_all("/'([^']+)'/", $statusType, $matches);
    $enumValues = $matches[1];
    if (!in_array('occupied', $enumValues)) {
        $statusValue = $enumValues[0] === 'available' && count($enumValues) > 1 ? $enumValues[1] : 'available';
    }
}

$sql = "SELECT id FROM spots WHERE location_id = ? AND status = 'available' LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Database error: Unable to prepare spots query.']);
    exit();
}
$stmt->bind_param("i", $locationId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $spot = $result->fetch_assoc();
    $spotId = $spot['id'];

    $conn->begin_transaction();
    try {
        $startTime = date("Y-m-d H:i:s");
        $bookingSql = "INSERT INTO bookings (user_id, location_id, spot_id, start_time, duration_minutes, status, booking_time) 
                       VALUES (?, ?, ?, ?, ?, 'active', NOW())";
        $stmt = $conn->prepare($bookingSql);
        if (!$stmt) {
            throw new Exception("Prepare failed for booking insert: " . $conn->error);
        }
        $stmt->bind_param("iiisi", $userId, $locationId, $spotId, $startTime, $duration);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed for booking insert: " . $stmt->error);
        }

        $updateSpotSql = "UPDATE spots SET status = ?, user_id = ?, booking_start = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSpotSql);
        if (!$stmt) {
            throw new Exception("Prepare failed for spot update: " . $conn->error);
        }
        $stmt->bind_param("sisi", $statusValue, $userId, $startTime, $spotId);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed for spot update: " . $stmt->error);
        }

        $conn->commit();
        ob_end_clean();
        echo json_encode([
            'status' => 'success',
            'message' => "Spot booked successfully for " . ($duration / 60) . " hour" . ($duration > 60 ? 's' : '') . "!"
        ]);
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => "Error booking spot: " . $e->getMessage()]);
        exit();
    }
} else {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => "No available spots at this location."]);
    exit();
}
?>