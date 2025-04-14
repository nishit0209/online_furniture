<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/admin_header.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /pages/admin/login.php');
    exit();
}
?>

<div class="container mt-5">
    <h2>Admin Dashboard</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
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
                    <h5 class="card-title">Total Orders</h5>
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
                    <h5 class="card-title">Total Users</h5>
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

<?php require_once '../../includes/footer.php'; ?> 