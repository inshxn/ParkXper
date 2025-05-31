<?php include '../includes/header.php'; ?>
<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">
    <h2>Admin Login</h2>
    <form method="POST" action="../actions/admin_login.php">
        <input type="email" name="email" placeholder="Email Address" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
