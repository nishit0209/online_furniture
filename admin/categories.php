<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/admin_header.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../admin/login.php');
    exit();
}

// Fetch categories with their products
$sql = "SELECT c.*, 
        COUNT(p.id) as product_count,
        GROUP_CONCAT(
            JSON_OBJECT(
                'id', p.id,
                'name', p.name,
                'price', p.price,
                'stock', p.stock,
                'image', p.image
            )
        ) as products
        FROM categories c 
        LEFT JOIN products p ON c.id = p.category_id 
        GROUP BY c.id";
$result = $conn->query($sql);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Category Overview</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-plus-circle"></i> Add New Category
        </button>
    </div>

    <!-- Category Cards -->
    <div class="row">
        <?php while ($category = $result->fetch_assoc()): ?>
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php echo htmlspecialchars($category['name']); ?></h5>
                        <div>
                            <button class="btn btn-sm btn-light edit-category" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editCategoryModal"
                                    data-id="<?php echo $category['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                    data-description="<?php echo htmlspecialchars($category['description']); ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-category"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteCategoryModal"
                                    data-id="<?php echo $category['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($category['name']); ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted"><?php echo htmlspecialchars($category['description']); ?></p>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="badge bg-info">
                                <i class="bi bi-box"></i> <?php echo $category['product_count']; ?> Products
                            </span>
                            <small class="text-muted">Created: <?php echo date('d M Y', strtotime($category['created_at'])); ?></small>
                        </div>

                        <?php if ($category['product_count'] > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $products = json_decode('[' . rtrim($category['products'], ',') . ']', true);
                                        foreach ($products as $product):
                                            if ($product === null) continue;
                                        ?>
                                            <tr>
                                                <td class="d-flex align-items-center">
                                                    <?php if (!empty($product['image'])): ?>
                                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                             class="me-2"
                                                             style="width: 30px; height: 30px; object-fit: cover;">
                                                    <?php endif; ?>
                                                    <?php echo htmlspecialchars($product['name']); ?>
                                                </td>
                                                <td>₹<?php echo number_format($product['price'], 2); ?></td>
                                                <td>
                                                    <?php if ($product['stock'] > 0): ?>
                                                        <span class="badge bg-success"><?php echo $product['stock']; ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Out of stock</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($category['product_count'] > 5): ?>
                                <div class="text-center mt-2">
                                    <a href="products.php?category=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        View All Products
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p>No products in this category</p>
                                <a href="products.php?add=1&category=<?php echo $category['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    Add Product
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_description" class="form-label">Description</label>
                        <textarea class="form-control" id="category_description" name="category_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="category_id" id="edit_category_id">
                    <div class="mb-3">
                        <label for="edit_category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="edit_category_name" name="category_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_category_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_category_description" name="category_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_category" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this category? This action cannot be undone.</p>
                <p class="text-danger">Category: <span id="delete_category_name"></span></p>
            </div>
            <form method="POST">
                <input type="hidden" name="category_id" id="delete_category_id">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_category" class="btn btn-danger">Delete Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit Category
    document.querySelectorAll('.edit-category').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const description = this.dataset.description;

            document.getElementById('edit_category_id').value = id;
            document.getElementById('edit_category_name').value = name;
            document.getElementById('edit_category_description').value = description;
        });
    });

    // Delete Category
    document.querySelectorAll('.delete-category').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;

            document.getElementById('delete_category_id').value = id;
            document.getElementById('delete_category_name').textContent = name;
        });
    });
});
</script>
