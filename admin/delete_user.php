<?php
session_start();
require_once '../includes/config.php';


if (!isset($_GET['id'])) {
    header('Location:../admin/users.php');
    exit();
}

$user_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    header('Location: /pages/admin/users.php?message=User+deleted+successfully');
} else {
    header('Location: /pages/admin/users.php?error=Error+deleting+user');
}
exit();
?> 