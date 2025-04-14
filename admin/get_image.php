<?php
require_once '../includes/config.php';

if (!isset($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit();
}

$product_id = $_GET['id'];
$stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if ($product && $product['image']) {
    header('Content-Type: image/jpeg');
    echo $product['image'];
} else {
    header('HTTP/1.1 404 Not Found');
} 