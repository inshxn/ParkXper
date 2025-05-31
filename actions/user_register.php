<?php
require '../db/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name        = trim($_POST['name']);
    $email       = trim($_POST['email']);
    $password    = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $address     = trim($_POST['address']);
    $aadhaar     = trim($_POST['aadhaar']);
    $car_number  = trim($_POST['car_number']);
    $card_number = trim($_POST['card_number']);

    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "User already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, address, aadhaar, car_number, card_number)
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $email, $password, $address, $aadhaar, $car_number, $card_number);
        if ($stmt->execute()) {
            header("Location: login.php?success=1");
        } else {
            echo "Registration failed!";
        }
    }
}
?>
