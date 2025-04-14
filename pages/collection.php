<?php
require_once '../includes/config.php';
require_once '../includes/header.php';
?>

<section class="collection-section py-5">
    <div class="container">
        <h1 class="text-center mb-5 animate__animated animate__fadeInDown">Our Collection</h1>
        <div class="row g-4">
            <!-- Sofa Section -->
            <div class="col-12">
                <h2 class="mb-4 animate__animated animate__fadeIn">Home's Furniture</h2>
            </div>
            <!-- Sofa 1 -->
            <div class="col-md-4 col-sm-6">
                <div class="card h-100 shadow animate__animated animate__fadeInUp">
                    <div class="card-img-container">
                        <img src="../images/sofa.seet.jpg" class="card-img-top" alt="Modern Sofa">
                        <div class="overlay">
                            <button class="btn btn-primary view-btn" onclick="location.href='shop.php';">View Details</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Modern Sofa</h5>
                        <p class="card-text">Elegant and comfortable modern sofa design.</p>
                        <p class="text-muted">₹499.99</p>
                    </div>
                </div>
            </div>
            <!-- Sofa 2 -->
            <div class="col-md-4 col-sm-6">
                <div class="card h-100 shadow animate__animated animate__fadeInUp">
                    <div class="card-img-container">
                        <img src="../images/leather.jpg" class="card-img-top" alt="Leather Sofa">
                        <div class="overlay">
                            <button class="btn btn-primary view-btn" onclick="location.href='shop.php';">View Details</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Leather Sofa</h5>
                        <p class="card-text">Luxurious leather sofa for your living room.</p>
                        <p class="text-muted">₹799.99</p>
                    </div>
                </div>
            </div>

            <!-- Chairs Section -->
            <div class="col-12 mt-5">
                <h2 class="mb-4 animate__animated animate__fadeIn">Office's Furniture</h2>
            </div>
            <!-- Chair 1 -->
            <div class="col-md-4 col-sm-6">
                <div class="card h-100 shadow animate__animated animate__fadeInUp">
                    <div class="card-img-container">
                        <img src="../images/ochair.jpg" class="card-img-top" alt="Office Chair">
                        <div class="overlay">
                            <button class="btn btn-primary view-btn" onclick="location.href='shop.php';">View Details</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Ergonomic Office Chair</h5>
                        <p class="card-text">Comfortable chair for long working hours.</p>
                        <p class="text-muted">₹299.99</p>
                    </div>
                </div>
            </div>
            <!-- Chair 2 -->
            <div class="col-md-4 col-sm-6">
                <div class="card h-100 shadow animate__animated animate__fadeInUp">
                    <div class="card-img-container">
                        <img src="../images/otable.jpg" class="card-img-top" alt="Dining Chair">
                        <div class="overlay">
                            <button class="btn btn-primary view-btn" onclick="location.href='shop.php';">View Details</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Modern Office Table</h5>
                        <p class="card-text">Stylish chairs for your dining area.</p>
                        <p class="text-muted">₹149.99</p>
                    </div>
                </div>
            </div>

            <!-- Tables Section -->
            <div class="col-12 mt-5">
                <h2 class="mb-4 animate__animated animate__fadeIn">Kitchen's Furniture</h2>
            </div>
            <!-- Table 1 -->
            <div class="col-md-4 col-sm-6">
                <div class="card h-100 shadow animate__animated animate__fadeInUp">
                    <div class="card-img-container">
                        <img src="../images/kitchen.jpg" class="card-img-top" alt="Coffee Table">
                        <div class="overlay">
                            <button class="btn btn-primary view-btn" onclick="location.href='shop.php';">View Details</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Modern Kitchen Furniture</h5>
                        <p class="card-text">Modern glass table for your living room.</p>
                        <p class="text-muted">₹199.99</p>
                    </div>
                </div>
            </div>
            <!-- Table 2 -->
            <div class="col-md-4 col-sm-6">
                <div class="card h-100 shadow animate__animated animate__fadeInUp">
                    <div class="card-img-container">
                        <img src="../images/dining.jpg" class="card-img-top" alt="Dining Table">
                        <div class="overlay">
                            <button class="btn btn-primary view-btn" onclick="location.href='shop.php';">View Details</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Wooden Dining Table</h5>
                        <p class="card-text">Elegant dining table for family gatherings.</p>
                        <p class="text-muted">₹599.99</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .collection-section {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    }
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    .card-img-container {
        position: relative;
        overflow: hidden;
    }
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card:hover .overlay {
        opacity: 1;
    }
    .view-btn {
        transform: translateY(20px);
        transition: transform 0.3s ease;
    }
    .card:hover .view-btn {
        transform: translateY(0);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add scroll animations
    const cards = document.querySelectorAll('.card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate__fadeInUp');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    cards.forEach(card => {
        observer.observe(card);
    });
});
</script>

<?php
require_once '../includes/footer.php';
?> 