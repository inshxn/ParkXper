<?php include '../includes/header.php'; ?>
<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">
    <h2>Admin Registration</h2>
    <form method="POST" action="../actions/admin_register.php">
        <input type="text" name="name" placeholder="Full Name" required><br>
        <input type="email" name="email" placeholder="Email Address" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Register</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
