<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/admin_header.php';

use Intervention\Image\ImageManager;



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0.0;
    $category = $_POST['category'] ?? '';
    
    // Validate inputs
    if (empty($name) || empty($description) || $price <= 0 || empty($category)) {
        echo '<div class="alert alert-danger">Please fill all required fields correctly</div>';
        return;
    }

    // Handle image upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Validate image
        $max_size = 5 * 1024 * 1024; // 5MB
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        
        // Check file size
        if ($_FILES['image']['size'] > $max_size) {
            echo '<div class="alert alert-danger">Image size must be less than 5MB</div>';
            return;
        }
        
        // Check file type
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($file_info, $_FILES['image']['tmp_name']);
        finfo_close($file_info);
        
        if (!in_array($mime_type, $allowed_types)) {
            echo '<div class="alert alert-danger">Only JPG, PNG, and GIF images are allowed</div>';
            return;
        }
        
        // Read image file
        $image = file_get_contents($_FILES['image']['tmp_name']);
    }

    try {
        // Initialize the statement
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, category) VALUES (?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        // Bind parameters
        $stmt->bind_param("ssdss", $name, $description, $price, $image, $category);
        
        // Execute the statement
        if ($stmt->execute()) {
            echo '<div class="alert alert-success">Product added successfully!</div>';
        } else {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    } finally {
        // Close the statement if it exists
        if (isset($stmt)) {
            $stmt->close();
        }
    }
}

// Image compression function
function compressImage($source, $quality) {
    $info = getimagesize($source);
    
    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
    } elseif ($info['mime'] == 'image/gif') {
        $image = imagecreatefromgif($source);
    } else {
        return null;
    }
    
    // Create temporary file
    $temp_file = tempnam(sys_get_temp_dir(), 'img');
    
    // Save compressed image
    imagejpeg($image, $temp_file, $quality);
    imagedestroy($image);
    
    // Read compressed image
    $compressed_image = file_get_contents($temp_file);
    
    // Clean up
    unlink($temp_file);
    
    return $compressed_image;
}

function getDBConnection() {
    static $conn = null;
    
    if ($conn === null || !$conn->ping()) {
        require_once '../includes/config.php';
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    }
    return $conn;
}

function executeQuery($sql, $retries = 3) {
    $conn = getDBConnection();
    
    for ($i = 0; $i < $retries; $i++) {
        try {
            $result = $conn->query($sql);
            if ($result) {
                return $result;
            }
        } catch (mysqli_sql_exception $e) {
            if ($i === $retries - 1) {
                throw $e;
            }
            // Reconnect and try again
            $conn = getDBConnection();
        }
    }
    return false;
}
?>

<div class="container mt-5">
    <h2>Manage Products</h2>
    <div class="row">
        <div class="col-md-6">
            <h3>Add New Product</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-control" id="category" name="category" required>
                        <option value="">Select a category</option>
                        <option value="Home's Furniture">Home's Furniture</option>
                        <option value="Kitchen Furniture">Kitchen Furniture</option>
                        <option value="Office Furniture">Office Furniture</option>
                        
                    </select>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Product Image</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="validateImage(this)">
                </div>
                <button type="submit" class="btn btn-primary">Add Product</button>
            </form>
        </div>
        <div class="col-md-6">
            <h3>Product List</h3>
            <table class="table table-bordered" width=100%>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $result = executeQuery("SELECT id, name, description, price, image,category FROM products LIMIT 100");
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '  <td>' . ($row['id'] ?? '') . '</td>';
                                echo '  <td>' . ($row['name'] ?? '') . '</td>';
                                echo '  <td>' . ($row['description'] ?? 'No description available') . '</td>';
                                echo '  <td>₹' . ($row['price'] ?? '0.00') . '</td>';
                                
                                echo '  <td>';
                                if (!empty($row['image'])) {
                                    echo '<img src="data:image/jpeg;base64,'.base64_encode($row['image']).'" alt="Product Image" style="max-width: 100px;">';
                                } else {
                                    echo 'No Image';
                                }
                                echo '  <td>' . ($row['category'] ?? '') . '</td>';
                                echo '  </td>';
                                echo '  <td>';
                                echo '    <a href="edit_product.php?id=' . ($row['id'] ?? '') . '" class="btn btn-sm btn-warning">Edit</a>';
                                echo '    <a href="delete_product.php?id=' . ($row['id'] ?? '') . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</a>';
                                echo '  </td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="6">No products found</td></tr>';
                        }
                    } catch (mysqli_sql_exception $e) {
                        echo '<div class="alert alert-danger">Database error: ' . $e->getMessage() . '</div>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function validateImage(input) {
    if (input.files && input.files[0]) {
        const maxSize = 10 * 5000 * 5000; // 5MB
        if (input.files[0].size > maxSize) {
            alert('Image size must be less than 5MB');
            input.value = ''; // Clear the input
        }
    }
}
</script>

