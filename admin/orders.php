<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/admin_header.php';


$sql = "SELECT orders.id, users.username, products.name, orders.quantity, orders.total_price, orders.status, orders.created_at 
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        JOIN products ON orders.product_id = products.id";
$result = $conn->query($sql);
?>

<div class="container mt-5">
    <h2>Manage Orders</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '
                    <tr>
                        <td>' . $row['id'] . '</td>
                        <td>' . $row['username'] . '</td>
                        <td>' . $row['name'] . '</td>
                        <td>' . $row['quantity'] . '</td>
                        <td>₹' . $row['total_price'] . '</td>
                        <td>' . $row['status'] . '</td>
                        <td>' . $row['created_at'] . '</td>
                        <td>
                            <a href="edit_order.php?id=' . $row['id'] . '" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_order.php?id=' . $row['id'] . '" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>';
                }
            } else {
                echo '<tr><td colspan="8">No orders found.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

 