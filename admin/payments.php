<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/admin_header.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location:../admin/login.php');
    exit();
}

// Handle payment status updates
if (isset($_POST['update_status'])) {
    $payment_id = $_POST['payment_id'];
    $new_status = $_POST['new_status'];
    
    $update_sql = "UPDATE payments SET payment_status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $payment_id);
    
    if ($stmt->execute()) {
        $success_message = "Payment status updated successfully!";
    } else {
        $error_message = "Error updating payment status.";
    }
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$payment_method_filter = isset($_GET['payment_method']) ? $_GET['payment_method'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build the query
$sql = "SELECT p.*, o.user_id, u.username 
        FROM payments p 
        JOIN orders o ON p.order_id = o.id 
        JOIN users u ON o.user_id = u.id 
        WHERE 1=1";

if ($status_filter) {
    $sql .= " AND p.payment_status = '$status_filter'";
}
if ($payment_method_filter) {
    $sql .= " AND p.payment_method = '$payment_method_filter'";
}
if ($search) {
    $sql .= " AND (p.transaction_id LIKE '%$search%' OR u.username LIKE '%$search%')";
}

$sql .= " ORDER BY p.payment_date DESC";
$result = $conn->query($sql);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Payments</h2>
        <div class="d-flex gap-2">
            <button class="btn btn-success" onclick="exportToExcel()">
                <i class="bi bi-file-earmark-excel"></i> Export to Excel
            </button>
            <button class="btn btn-primary" onclick="printReport()">
                <i class="bi bi-printer"></i> Print Report
            </button>
        </div>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Payment Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="failed" <?php echo $status_filter === 'failed' ? 'selected' : ''; ?>>Failed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-select" onchange="this.form.submit()">
                        <option value="">All Methods</option>
                        <option value="cod" <?php echo $payment_method_filter === 'cod' ? 'selected' : ''; ?>>Cash on Delivery</option>
                        <option value="upi" <?php echo $payment_method_filter === 'upi' ? 'selected' : ''; ?>>UPI</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by Transaction ID or Username" 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Transaction ID</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td>
                                    <a href="view_order.php?id=<?php echo $row['order_id']; ?>">
                                        #<?php echo $row['order_id']; ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo $row['transaction_id']; ?></td>
                                <td>₹<?php echo number_format($row['amount'], 2); ?></td>
                                <td><?php echo ucfirst($row['payment_method']); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $row['payment_status'] === 'completed' ? 'success' : 
                                            ($row['payment_status'] === 'pending' ? 'warning' : 'danger'); 
                                    ?>">
                                        <?php echo ucfirst($row['payment_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y H:i', strtotime($row['payment_date'])); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" 
                                                data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="payment_id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="new_status" value="completed">
                                                    <button type="submit" name="update_status" class="dropdown-item">
                                                        Mark as Completed
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="payment_id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="new_status" value="pending">
                                                    <button type="submit" name="update_status" class="dropdown-item">
                                                        Mark as Pending
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="payment_id" value="<?php echo $row['id']; ?>">
                                                    <input type="hidden" name="new_status" value="failed">
                                                    <button type="submit" name="update_status" class="dropdown-item">
                                                        Mark as Failed
                                                    </button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a href="view_payment.php?id=<?php echo $row['id']; ?>" 
                                                   class="dropdown-item">View Details</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function exportToExcel() {
    // Create a timestamp for the filename
    const timestamp = new Date().toISOString().replace(/[^0-9]/g, "");
    const filename = `payments_report_${timestamp}.csv`;

    // Get the table
    const table = document.querySelector('table');
    let csv = [];
    
    // Get all rows
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td, th');
        const rowData = Array.from(cells).map(cell => {
            // Remove HTML and get text content
            let text = cell.textContent.trim();
            // Escape quotes and wrap in quotes
            return `"${text.replace(/"/g, '""')}"`;
        });
        csv.push(rowData.join(','));
    });

    // Create CSV content
    const csvContent = csv.join('\n');
    
    // Create download link
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function printReport() {
    window.print();
}
</script>

<style>
/* Print styles */
@media print {
    .sidebar, .btn, .actions, form, .alert {
        display: none !important;
    }
    .card {
        border: none !important;
    }
    .table {
        width: 100% !important;
    }
}
</style>

