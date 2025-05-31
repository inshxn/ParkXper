<?php
session_start();
include '../db/config.php';

if (isset($_GET['spot_id']) && isset($_SESSION['user_id'])) {
    $spot_id = $_GET['spot_id'];
    $user_id = $_SESSION['user_id'];

    // Check if the booking belongs to the current user and if it's still within the allowed time
    $query = "SELECT * FROM spots WHERE id = $spot_id AND user_id = $user_id AND status = 'booked'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $spot = $result->fetch_assoc();
        $current_end_time = new DateTime($spot['booking_end']);
        $current_end_time->modify('+30 minutes');
        $new_end_time = $current_end_time->format('Y-m-d H:i:s');

        // Update the booking end time
        $update_query = "UPDATE spots SET booking_end = '$new_end_time' WHERE id = $spot_id";
        if ($conn->query($update_query)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Error updating booking time."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid spot or user."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>
