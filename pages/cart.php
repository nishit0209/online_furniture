        
        <?php
session_start();
require_once '../includes/config.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $product_id = $_POST['product_id'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
}

$sql = "SELECT products.id, products.name, products.price, products.image, cart.quantity 
        FROM cart 
        JOIN products ON cart.product_id = products.id 
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}
?>

<div class="container mt-5">
    <h2>Shopping Cart</h2>
    <div class="row">
        <div class="col-md-8">
            <?php if (count($cart_items) > 0): ?>
                <?php foreach ($cart_items as $item): ?>
                    <div class="card mb-3">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($item['image']); ?>" class="img-fluid rounded-start" alt="<?php echo $item['name']; ?>">
                                <?php else: ?>
                                    <img src="../images/default-product.jpg" class="img-fluid rounded-start" alt="Default Image">
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $item['name']; ?></h5>
                                    <p class="card-text">₹<?php echo $item['price']; ?></p>
                                    <p class="card-text">Quantity: <?php echo $item['quantity']; ?></p>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" name="remove_item" class="btn btn-danger">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Order Summary</h5>
                    <p class="card-text">Total: ₹<?php echo $total_amount; ?></p>
                    <a href="../pages/checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 