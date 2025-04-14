<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/admin_header.php';



if (!isset($_GET['id'])) {
    header('Location: ../admin/products.php');
    exit();
}

$product_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    header('Location: ../admin/products.php?message=Product+deleted+successfully');
} else {
    header('Location: ../admin/products.php?error=Error+deleting+product');
}
exit();
?> 