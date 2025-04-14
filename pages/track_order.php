<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : null;

// Fetch user's recent orders
$orders_sql = "SELECT 
                o.id,
                o.status,
                o.created_at,
                o.total_price,
                p.payment_status,
                COUNT(oi.id) as total_items
            FROM orders o
            LEFT JOIN payments p ON o.id = p.order_id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.user_id = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC
            LIMIT 5";

$stmt = $conn->prepare($orders_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_orders = $stmt->get_result();
?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Track Your Order</h4>
                </div>
                <div class="card-body">
                    <!-- Order Tracking Form -->
                    <form method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="number" name="order_id" class="form-control" 
                                   placeholder="Enter Order ID" 
                                   value="<?php echo $order_id; ?>"
                                   required>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i> Track
                            </button>
                        </div>
                    </form>

                    <?php if ($order_id): ?>
                        <?php
                        // Fetch specific order details
                        $track_sql = "SELECT 
                                        o.*,
                                        p.payment_status,
                                        p.payment_method
                                    FROM orders o
                                    LEFT JOIN payments p ON o.id = p.order_id
                                    WHERE o.id = ? AND o.user_id = ?";
                        $track_stmt = $conn->prepare($track_sql);
                        $track_stmt->bind_param("ii", $order_id, $user_id);
                        $track_stmt->execute();
                        $order = $track_stmt->get_result()->fetch_assoc();

                        if ($order):
                        ?>
                            <!-- Order Status Timeline -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Order #<?php echo $order_id; ?></h5>
                                    <div class="timeline mt-4">
                                        <?php
                                        $statuses = ['pending', 'processing', 'shipped', 'delivered'];
                                        $current_status = array_search($order['status'], $statuses);
                                        
                                        foreach ($statuses as $index => $status):
                                            $is_active = $index <= $current_status;
                                            $status_date = $is_active ? date('M d, Y', strtotime($order['created_at'])) : '';
                                        ?>
                                            <div class="timeline-item <?php echo $is_active ? 'active' : ''; ?>">
                                                <div class="timeline-icon">
                                                    <i class="bi bi-<?php echo $is_active ? 'check-circle-fill' : 'circle'; ?>"></i>
                                                </div>
                                                <div class="timeline-content">
                                                    <h6><?php echo ucfirst($status); ?></h6>
                                                    <?php if ($status_date): ?>
                                                        <small class="text-muted"><?php echo $status_date; ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Details -->
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-3 text-muted">Order Details</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Order Date:</strong><br>
                                                <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?>
                                            </p>
                                        </div>
                                        <div class="col-6">
                                            <p><strong>Payment Status:</strong><br>
                                                <span class="badge bg-<?php echo $order['payment_status'] == 'paid' ? 'success' : 'warning'; ?>">
                                                    <?php echo ucfirst($order['payment_status']); ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-6">
                                            <p><strong>Total Amount:</strong><br>
                                                ₹<?php echo number_format($order['total_price'], 2); ?>
                                            </p>
                                        </div>
                                        <div class="col-6">
                                            <p><strong>Payment Method:</strong><br>
                                                <?php echo ucfirst($order['payment_method'] ?? 'Not specified'); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Order not found or you don't have permission to view it.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Recent Orders -->
                    <div class="mt-4">
                        <h5>Recent Orders</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $order['status'] == 'delivered' ? 'success' : 
                                                        ($order['status'] == 'processing' ? 'warning' : 
                                                        ($order['status'] == 'shipped' ? 'info' : 'secondary')); 
                                                ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td>₹<?php echo number_format($order['total_price'], 2); ?></td>
                                            <td>
                                                <a href="?order_id=<?php echo $order['id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    Track
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    left: 15px;
    height: 100%;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 20px;
}

.timeline-icon {
    position: absolute;
    left: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    text-align: center;
    line-height: 30px;
    background: #fff;
}

.timeline-item.active .timeline-icon {
    color: #0d6efd;
}

.timeline-content {
    padding: 5px 0;
}

.timeline-content h6 {
    margin: 0;
}
</style>

<?php require_once '../includes/footer.php'; ?> 