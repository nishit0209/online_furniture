<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

$products = getProducts();
?>

<div class="container mt-5">
    <h2>Shop</h2>
    <div class="row">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <?php if (!empty($product['image'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($product['image']); ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
                    <?php else: ?>
                        <img src="path/to/default/image.jpg" class="card-img-top" alt="Default Image">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $product['name']; ?></h5>
                        <p class="card-text"><?php echo $product['description']; ?></p>
                        <p class="price">₹<?php echo $product['price']; ?></p>
                        <form method="POST" action="../pages/add_to_cart.php">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1" class="form-control mb-2" style="width: 100px;">
                            <button type="submit" class="btn btn-primary">Add to Cart</button>
                            <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary">View Details</a>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 