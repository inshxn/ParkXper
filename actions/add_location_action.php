<?php
require '../db/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name        = trim($_POST['name']);
    $address     = trim($_POST['address']);
    $total_spots = (int) $_POST['total_spots'];

    $stmt = $conn->prepare("INSERT INTO locations (name, address, total_spots) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $address, $total_spots);
    
    if ($stmt->execute()) {
        $location_id = $stmt->insert_id;
        for ($i = 1; $i <= $total_spots; $i++) {
            $spot_number = "Spot-$i";
            $insert_spot = $conn->prepare("INSERT INTO spots (location_id, spot_number) VALUES (?, ?)");
            $insert_spot->bind_param("is", $location_id, $spot_number);
            $insert_spot->execute();
        }

        header("Location: ../admin/dashboard.php?success=1");
    } else {
        echo "Error adding location.";
    }
}
?>
