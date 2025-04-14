<?php
session_start();
require_once 'functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Fetch user data from database using $_SESSION['user_id']
try {
    $conn = new PDO("mysql:host=localhost;dbname=your_db", "username", "password");
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        <!-- Add more profile information here -->
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html> 