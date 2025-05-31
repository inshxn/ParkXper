<?php
$year = date('Y');
?>
<link rel="stylesheet" href="../assets/css/footer.css">
<footer class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h3 class="footer-logo">ParkXpert</h3>
            <p>Smart parking solutions for a hassle-free experience.</p>
            <div class="social-links">
                <a href="https://twitter.com" target="_blank"><i class="fab fa-x-twitter"></i></a>
                <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <div class="footer-section">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="../index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="../user/book.php"><i class="fas fa-parking"></i> Book a Spot</a></li>
                <li><a href="../user/profile.php"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="../contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Contact Us</h4>
            <p><i class="fas fa-envelope"></i> support@parkxpert.com</p>
            <p><i class="fas fa-phone"></i> +91 123-456-7890</p>
            <p><i class="fas fa-map-marker-alt"></i> 01, Lal Chowk, Srinagar, Republic of Kashmir</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo $year; ?> ParkXpert. All rights reserved.</p>
    </div>
</footer>