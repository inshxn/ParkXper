<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ParkXpert</title>
    <link rel="stylesheet" href="/ParkXpert/assets/css/header.css">
</head>
<body>
    <header class="main-header">
        <div class="logo">
            <a href="/ParkXpert/user/index.php">
                <img src="/ParkXpert/assets/images/logo.png" alt="ParkXpert Logo" height="50">
            </a>
        </div>
        <link rel="icon" type="image/png" href="/ParkXpert/assets/images/logo.png" sizes="50x50">
        <nav class="navbar">
            <ul>
                <li><a href="/ParkXpert/user/index.php">Home</a></li>
                <li><a href="/ParkXpert/user/profile.php">Profile</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="/ParkXpert/GameHeaven/index.html" class="Games">RTO Services</a></li>
                    <li class="greeting">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></li>
                    <li><a href="/ParkXpert/auth/logout.php" class="btn-logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="/ParkXpert/auth/login.php" class="btn-login">Login</a></li>
                    <li><a href="/ParkXpert/auth/register.php" class="btn-register">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
</body>
</html>
