<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    echo "<script>window.location.href = '../pages/login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['order_id'];

// Get order details with items
$sql = "SELECT o.*, oi.quantity, oi.price as item_price, p.name as product_name, p.image
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.id = ? AND o.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order_items = $result->fetch_all(MYSQLI_ASSOC);

if (empty($order_items)) {
    echo "<script>window.location.href = '../pages/shop.php';</script>";
    exit();
}

$shipping_address = json_decode($order_items[0]['shipping_address'], true);
?>

<style>
@media print {
    .navbar, .btn, footer {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
}
</style>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title text-center text-success">
                        <i class="bi bi-check-circle"></i> Order Placed Successfully!
                    </h2>
                    <hr>
                    <div class="text-center mb-4">
                        <h5>Order ID: #<?php echo $order_id; ?></h5>
                        <p class="text-muted">Thank you for your order!</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Shipping Address</h5>
                            <p>
                                <?php echo htmlspecialchars($shipping_address['full_name']); ?><br>
                                <?php echo htmlspecialchars($shipping_address['address']); ?><br>
                                <?php echo htmlspecialchars($shipping_address['city']); ?>, 
                                <?php echo htmlspecialchars($shipping_address['state']); ?> - 
                                <?php echo htmlspecialchars($shipping_address['pincode']); ?><br>
                                Phone: <?php echo htmlspecialchars($shipping_address['phone']); ?><br>
                                Email: <?php echo htmlspecialchars($shipping_address['email']); ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5>Order Summary</h5>
                            <p>
                                Payment Method: <?php echo ucfirst($order_items[0]['payment_method']); ?><br>
                                Order Status: <?php echo ucfirst($order_items[0]['status']); ?><br>
                                Order Date: <?php echo date('d M Y', strtotime($order_items[0]['created_at'])); ?>
                            </p>
                        </div>
                    </div>

                    <h5>Order Items</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total = 0;
                                foreach ($order_items as $item): 
                                    $item_total = $item['item_price'] * $item['quantity'];
                                    $total += $item_total;
                                ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($item['image'])): ?>
                                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($item['image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                                         style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                                <?php endif; ?>
                                                <?php echo htmlspecialchars($item['product_name']); ?>
                                            </div>
                                        </td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>₹<?php echo number_format($item['item_price'], 2); ?></td>
                                        <td>₹<?php echo number_format($item_total, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                    <td><strong>₹<?php echo number_format($total, 2); ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-4">
                        <a href="../pages/orders.php" class="btn btn-primary">View All Orders</a>
                        <a href="../pages/shop.php" class="btn btn-secondary">Continue Shopping</a>
                        <button onclick="window.print();" class="btn btn-info">Print Receipt</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>  