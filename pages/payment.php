<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/header.php';

// Check if user is logged in and has pending order
if (!isset($_SESSION['user_id']) || !isset($_SESSION['pending_order'])) {
    header('Location: login.php');
    exit();
}

$pending_order = $_SESSION['pending_order'];
$payment_method = $pending_order['payment_method'];
$total_amount = $pending_order['total_amount'];

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_status = false;
    $transaction_id = 'TXN' . time() . rand(1000, 9999); // Generate unique transaction ID
    $upi_id = '';
    
    if ($payment_method === 'cod') {
        // For Cash on Delivery, automatically mark as pending
        $payment_status = true;
        $payment_status_text = 'pending';
    } else if ($payment_method === 'upi') {
        // Simulate UPI payment verification
        // In real application, integrate with actual payment gateway
        $upi_id = $_POST['upi_id'] ?? '';
        if (!empty($upi_id)) {
            $payment_status = true;
            $payment_status_text = 'completed';
        }
    }

    if ($payment_status) {
        $conn->begin_transaction();

        try {
            $user_id = $_SESSION['user_id'];
            $cart_items = $pending_order['cart_items'];
            $shipping_address = json_encode($pending_order['shipping_details']);

            // Create the order first
            $order_sql = "INSERT INTO orders (user_id, product_id, quantity, total_price, status, shipping_address, payment_method) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";

            foreach ($cart_items as $item) {
                $stmt = $conn->prepare($order_sql);
                $product_id = $item['id'];
                $quantity = $item['quantity'];
                $item_total = $item['price'] * $item['quantity'];
                $status = $payment_method === 'cod' ? 'pending' : 'completed';
                
                $stmt->bind_param("iiidsss", 
                    $user_id,
                    $product_id,    
                    $quantity,   
                    $item_total,
                    $status,
                    $shipping_address, 
                    $payment_method
                );
                $stmt->execute();

                if (!isset($order_id)) {
                    $order_id = $conn->insert_id;
                }

                // Add to order_items
                $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                            VALUES (?, ?, ?, ?)";
                
                $stmt = $conn->prepare($item_sql);
                $stmt->bind_param("iiid", 
                    $order_id,
                    $item['id'],      
                    $item['quantity'], 
                    $item['price']     
                );
                $stmt->execute();
            }

            // Store payment information
            $payment_sql = "INSERT INTO payments (order_id, amount, payment_method, payment_status, 
                           transaction_id, upi_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($payment_sql);
            $stmt->bind_param("idssss", 
                $order_id,
                $total_amount,
                $payment_method,
                $payment_status_text,
                $transaction_id,
                $upi_id
            );
            $stmt->execute();

            // Clear cart
            $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
            $stmt = $conn->prepare($clear_cart_sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $conn->commit();
            
            // Clear pending order from session
            unset($_SESSION['pending_order']);
            
            // Redirect to order confirmation
            echo "<script>window.location.href = 'order_confirmation.php?order_id=" . $order_id . "';</script>";
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $error = "An error occurred while processing your payment. Please try again.";
        }
    } else {
        $error = "Payment verification failed. Please try again.";
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Details</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <h5>Order Summary</h5>
                        <p>Total Amount: ₹<?php echo number_format($total_amount, 2); ?></p>
                        <p>Payment Method: <?php echo ucfirst($payment_method); ?></p>
                        <?php if (isset($transaction_id)): ?>
                            <p>Transaction ID: <?php echo $transaction_id; ?></p>
                        <?php endif; ?>
                    </div>

                    <form method="POST">
                        <?php if ($payment_method === 'upi'): ?>
                            <div class="mb-3">
                                <label for="upi_id" class="form-label">UPI ID</label>
                                <input type="text" class="form-control" id="upi_id" name="upi_id" required 
                                       placeholder="Enter your UPI ID">
                                <small class="text-muted">Example: yourname@upi</small>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Cash on Delivery will be collected at the time of delivery.
                            </div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <?php echo $payment_method === 'cod' ? 'Confirm Order' : 'Pay Now'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 