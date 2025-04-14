<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/admin_header.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../admin/login.php');
    exit();
}

// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    
    $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $order_id);
    
    if ($stmt->execute()) {
        // Add notification for status update
        $notification_sql = "INSERT INTO notifications (type, message, link) 
                           VALUES ('order', 'Order #" . $order_id . " status updated to " . $new_status . "', 
                           'view_order.php?id=" . $order_id . "')";
        $conn->query($notification_sql);
        
        $success_message = "Order status updated successfully!";
    } else {
        $error_message = "Error updating order status.";
    }
}

// Fetch pending orders with customer details and order items
$sql = "SELECT 
            o.id,
            o.user_id,
            o.total_price,
            o.status,
            o.status,
            o.created_at,
            o.shipping_address,
            u.username,
            u.email,
            COUNT(oi.id) as total_items,
            GROUP_CONCAT(
                JSON_OBJECT(
                    'product_name', COALESCE(p.name, 'Unknown Product'),
                    'quantity', oi.quantity,
                    'price', oi.price
                )
            ) as order_items
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE o.status IN ('pending', 'processing')
        GROUP BY o.id, o.user_id, o.total_price, o.status,  
                 o.created_at, o.shipping_address, u.username, u.email
        ORDER BY o.created_at DESC";

$result = $conn->query($sql);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Pending Orders</h2>
        <div>
            <a href="orders.php" class="btn btn-outline-primary">
                <i class="bi bi-list"></i> All Orders
            </a>
        </div>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($order = $result->fetch_assoc()): ?>
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Order #<?php echo $order['id']; ?></h5>
                            <span class="badge bg-light text-primary">
                                <?php echo date('d M Y, h:i A', strtotime($order['created_at'] ?? 'now')); ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Customer Details -->
                                <div class="col-md-4">
                                    <h6 class="text-muted mb-3">Customer Information</h6>
                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['username'] ?? 'N/A'); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></p>
                                    <p><strong>Shipping Address:</strong><br>
                                        <?php echo nl2br(htmlspecialchars($order['shipping_address'] ?? 'No address provided')); ?>
                                    </p>
                                </div>

                                <!-- Order Items -->
                                <div class="col-md-5">
                                    <h6 class="text-muted mb-3">Order Items</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Quantity</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $items = [];
                                                if (!empty($order['order_items'])) {
                                                    $items = json_decode('[' . rtrim($order['order_items'], ',') . ']', true) ?? [];
                                                }
                                                if (!empty($items)):
                                                    foreach ($items as $item):
                                                ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($item['product_name'] ?? 'Unknown Product'); ?></td>
                                                        <td><?php echo intval($item['quantity'] ?? 0); ?></td>
                                                        <td>₹<?php echo number_format(floatval($item['price'] ?? 0), 2); ?></td>
                                                    </tr>
                                                <?php 
                                                    endforeach;
                                                else: 
                                                ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center">No items found</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                                    <td><strong>₹<?php echo number_format(floatval($order['total_price'] ?? 0), 2); ?></strong></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <!-- Order Status -->
                                <div class="col-md-3">
                                    <h6 class="text-muted mb-3">Order Status</h6>
                                    <div class="mb-3">
                                        <span class="badge bg-warning text-dark">
                                            Status: <?php echo ucfirst($order['status'] ?? 'unknown'); ?>
                                        </span>
                                        <span class="badge bg-<?php echo ($order['payment_status'] ?? '') == 'paid' ? 'success' : 'danger'; ?>">
                                            Payment: <?php echo ucfirst($order['payment_status'] ?? 'pending'); ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Update Status Form -->
                                    <form method="POST" class="mt-3">
                                        <input type="hidden" name="order_id" value="<?php echo intval($order['id']); ?>">
                                        <div class="mb-3">
                                            <select name="new_status" class="form-select form-select-sm" required>
                                                <option value="">Update Status</option>
                                                <option value="processing">Processing</option>
                                                <option value="shipped">Shipped</option>
                                                <option value="delivered">Delivered</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
                                        </div>
                                        <button type="submit" name="update_status" class="btn btn-primary btn-sm w-100">
                                            Update Status
                                        </button>
                                    </form>

                                    <div class="mt-3">
                                        <a href="view_order.php?id=<?php echo intval($order['id']); ?>" 
                                           class="btn btn-outline-primary btn-sm w-100">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle me-2"></i>
                    No pending orders found.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Orders Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Orders Summary</h5>
                </div>
                <div class="card-body">
                    <?php
                    // Get orders summary
                    $summary_sql = "SELECT 
                                    COUNT(*) as total_pending,
                                    SUM(total_price) as total_value,
                                    COUNT(CASE WHEN payment_status = 'completed' THEN 1 END) as paid_orders,
                                    COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as unpaid_orders
                                FROM orders 
                                WHERE status IN ('pending', 'processing')";
                    $summary_result = $conn->query($summary_sql);
                    $summary = $summary_result->fetch_assoc();
                    ?>
                    <div class="row g-4">
                        <div class="col-md-3 col-sm-6">
                            <div class="text-center">
                                <h3 class="text-primary"><?php echo $summary['total_pending']; ?></h3>
                                <p class="text-muted mb-0">Pending Orders</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="text-center">
                                <h3 class="text-success">₹<?php echo number_format($summary['total_value'], 2); ?></h3>
                                <p class="text-muted mb-0">Total Order Value</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="text-center">
                                <h3 class="text-info"><?php echo $summary['paid_orders']; ?></h3>
                                <p class="text-muted mb-0">Paid Orders</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="text-center">
                                <h3 class="text-warning"><?php echo $summary['unpaid_orders']; ?></h3>
                                <p class="text-muted mb-0">Unpaid Orders</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>

<?php require_once '../includes/footer.php'; ?> 