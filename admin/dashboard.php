<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/admin_header.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location:../admin/login.php');
    exit();
}
?>
<style>
.card {
    margin-bottom: 20px;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
.card-title {
    color: #333;
    font-weight: 600;
    margin-bottom: 20px;
}
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
.text-success {
    color: #28a745;
}
.text-warning {
    color: #ffc107;
}
.text-danger {
    color: #dc3545;
}
</style>
<!-- Bootstrap CSS -->

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
<div class="container mt-5">
    <h2>Admin Dashboard</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-shopping-bag"></i>Total Products</h5>
                    <p class="card-text">
                        <?php
                        $sql = "SELECT COUNT(*) as total FROM products";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-file-invoice"></i>Total Orders</h5>
                    <p class="card-text">
                        <?php
                        $sql = "SELECT COUNT(*) as total FROM orders";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-users me-2"></i>Total Users</h5>
                    <p class="card-text">
                        <?php
                        $sql = "SELECT COUNT(*) as total FROM users";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-money-bill-wave me-2"></i>Total Revenue</h5>
                    <p class="card-text">
                        ₹<?php
                        $sql = "SELECT SUM(amount) as total FROM payments WHERE payment_status = 'completed'";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        echo number_format($row['total'] ?? 0, 2);
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
