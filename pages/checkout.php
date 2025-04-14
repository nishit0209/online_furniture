<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = '../pages/login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Get cart items
$sql = "SELECT products.id, products.name, products.price, products.image, cart.quantity 
        FROM cart 
        JOIN products ON cart.product_id = products.id 
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

// Calculate total
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

$order_success = false;
$order_id = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $payment_method = $_POST['payment_method'] ?? '';

    $errors = [];
    if (empty($full_name)) $errors[] = "Full name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($address)) $errors[] = "Address is required";
    if (empty($city)) $errors[] = "City is required";
    if (empty($state)) $errors[] = "State is required";
    if (empty($pincode)) $errors[] = "Pincode is required";
    if (empty($payment_method)) $errors[] = "Payment method is required";

    if (empty($errors)) {
        // Store order details in session for payment processing
        $_SESSION['pending_order'] = [
            'cart_items' => $cart_items,
            'total_amount' => $total_amount,
            'shipping_details' => [
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'pincode' => $pincode
            ],
            'payment_method' => $payment_method
        ];

        // Redirect to payment page
        echo "<script>window.location.href = 'payment.php';</script>";
        exit();
    }
}
?>

<!-- Add this at the top of your HTML section -->
<?php if ($order_success && $order_id): ?>
<script>
    window.location.href = 'order_confirmation.php?order_id=<?php echo $order_id; ?>';
</script>
<?php endif; ?>

<div class="container mt-5">
    <h2>Checkout</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Order Summary -->
        <div class="col-md-4 order-md-2 mb-4">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Order Summary</span>
                <span class="badge bg-primary rounded-pill"><?php echo count($cart_items); ?></span>
            </h4>
            <ul class="list-group mb-3">
                <?php foreach ($cart_items as $item): ?>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                            <small class="text-muted">Quantity: <?php echo $item['quantity']; ?></small>
                        </div>
                        <span class="text-muted">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </li>
                <?php endforeach; ?>
                <li class="list-group-item d-flex justify-content-between">
                    <strong>Total</strong>
                    <strong>₹<?php echo number_format($total_amount, 2); ?></strong>
                </li>
            </ul>
        </div>

        <!-- Checkout Form -->
        <div class="col-md-8 order-md-1">
            <h4 class="mb-3">Shipping Address</h4>
            <form method="POST" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="full_name">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="phone">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required>
                </div>

                <div class="mb-3">
                    <label for="address">Address</label>
                    <input type="text" class="form-control" id="address" name="address" required>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="city">City</label>
                        <input type="text" class="form-control" id="city" name="city" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="state">State</label>
                        <input type="text" class="form-control" id="state" name="state" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="pincode">Pincode</label>
                        <input type="text" class="form-control" id="pincode" name="pincode" required>
                    </div>
                </div>

                <hr class="mb-4">

                <h4 class="mb-3">Payment Method</h4>
                <div class="d-block my-3">
                    <div class="custom-control custom-radio">
                        <input id="cod" name="payment_method" type="radio" class="custom-control-input" value="cod" required>
                        <label class="custom-control-label" for="cod">Cash on Delivery</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input id="upi" name="payment_method" type="radio" class="custom-control-input" value="upi" required>
                        <label class="custom-control-label" for="upi">UPI</label>
                    </div>
                </div>

                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block w-100" type="submit">Place Order</button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 