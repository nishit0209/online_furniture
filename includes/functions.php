<?php
require_once 'db.php';

function getProducts($limit = 10) {
    $db = new Database();
    $conn = $db->getConnection();
    $sql = "SELECT * FROM products LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $db->close();
    return $products;
}

function getProductById($id) {
    $db = new Database();
    $conn = $db->getConnection();
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    $db->close();
    return $product;
}

function addProduct($name, $description, $price, $image) {
    $db = new Database();
    $conn = $db->getConnection();
    $sql = "INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssds", $name, $description, $price, $image);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    return $success;
}

function updateProduct($id, $name, $description, $price, $image) {
    $db = new Database();
    $conn = $db->getConnection();
    $sql = "UPDATE products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsi", $name, $description, $price, $image, $id);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    return $success;
}

function deleteProduct($id) {
    $db = new Database();
    $conn = $db->getConnection();
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();
    $db->close();
    return $success;
}

function isLoggedIn() {
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}
?> 