<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/register.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="icon" type="image/x-icon" href="/ParkXpert/assets/fav.png">
    <link rel="icon" type="image/x-icon" href="../assets/Images/fav.png">

<section class="background-section">
    <div class="clip-path"></div>
</section>

<div class="form-container">
    <div class="form-box">
        <h2 class="title">Welcome to <span style="color:#007BFF;">ParkXpert</span></h2>
        <p class="subtitle">User Registration</p>

        <form method="POST" action="../actions/user_register.php" class="form">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="name" placeholder="Full Name" required>
            </div>

            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="input-group">
                <i class="fas fa-map-marker-alt"></i>
                <input type="text" name="address" placeholder="Address" required>
            </div>

            <div class="input-group">
                <i class="fas fa-id-card"></i>
                <input type="text" name="aadhaar" placeholder="Aadhaar Number" maxlength="12" required>
            </div>

            <div class="input-group">
                <i class="fas fa-car"></i>
                <input type="text" name="car_number" placeholder="Car Number" required>
            </div>

            <div class="input-group">
                <i class="fas fa-credit-card"></i>
                <input type="text" name="card_number" placeholder="Card Number" maxlength="20" required>
            </div>

            <button type="submit" class="btn-submit">Register</button>
        </form>

        <div class="bottom-text">
            Already have an account? <a href="../user/login.php">Login here</a>
        </div>
    </div>
</div>
