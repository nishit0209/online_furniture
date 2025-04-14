<?php
require_once '../includes/config.php';
require_once '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Furniture Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        /* Custom CSS */
        .hero-section {
            background: url('../images/living-room.jpg') center/cover no-repeat;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        .hero-content {
            animation: slideIn 1.5s ease-out;
        }

        @keyframes slideIn {
            0% { transform: translateY(100px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        .furniture-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .furniture-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .parallax-section {
            background: url('../images/sale.jpg') center/cover fixed;
            height: 60vh;
            position: relative;
            overflow: hidden;
        }

        .parallax-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(-50%, -50%) translateY(0); }
            50% { transform: translate(-50%, -50%) translateY(-20px); }
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .feature-icon:hover {
            transform: scale(1.1);
            box-shadow: 0 0 30px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="hero-section d-flex align-items-center">
        <div class="container">
            <div class="row hero-content">
                <div class="col-md-6">
                    <h1 class="display-1 fw-bold mb-4">Crafting Luxury,<br>Defining Spaces</h1>
                    <p class="lead mb-4">Experience the pinnacle of furniture design with our exclusive collection.</p>
                    <a href="#collections" class="btn btn-dark btn-lg px-5 py-3">Explore Collections</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Collections -->
    <section id="collections" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 mb-3">Our Collections</h2>
                <p class="lead">Discover our handpicked selection of luxury furniture</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card furniture-card h-100">
                        <img src="../images/bunk.jpg" class="card-img-top" alt="Modern Living">
                        <div class="card-body">
                            <h5 class="card-title">Modern Living</h5>
                            <p class="card-text">Sleek designs for contemporary spaces</p>
                        </div>
                    </div>
                </div>
                <!-- Repeat for other collections -->
            </div>
        </div>
    </section>

    <!-- Parallax Section -->
    <section class="parallax-section">
        <div class="parallax-content text-white text-center">
            <h2 class="display-3 mb-4">Craftsmanship Meets Innovation</h2>
            <p class="lead">Experience the perfect blend of tradition and technology</p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 mb-3">Why Choose Us?</h2>
                <p class="lead">Experience the difference with our premium services</p>
            </div>
            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="feature-card p-4 text-center h-100">
                        <div class="feature-icon mx-auto mb-4">
                            <i class="bi bi-truck fs-1"></i>
                        </div>
                        <h3 class="h4 mb-3">Free Delivery</h3>
                        <p class="text-muted">We offer free nationwide delivery on all orders</p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="feature-card p-4 text-center h-100">
                        <div class="feature-icon mx-auto mb-4">
                            <i class="bi bi-shield-check fs-1"></i>
                        </div>
                        <h3 class="h4 mb-3">Quality Guarantee</h3>
                        <p class="text-muted">5-year warranty on all our furniture pieces</p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-md-4">
                    <div class="feature-card p-4 text-center h-100">
                        <div class="feature-icon mx-auto mb-4">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                        <h3 class="h4 mb-3">Expert Support</h3>
                        <p class="text-muted">Our design experts are here to help 24/7</p>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="col-md-4">
                    <div class="feature-card p-4 text-center h-100">
                        <div class="feature-icon mx-auto mb-4">
                            <i class="bi bi-arrow-repeat fs-1"></i>
                        </div>
                        <h3 class="h4 mb-3">Easy Returns</h3>
                        <p class="text-muted">30-day hassle-free return policy</p>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="col-md-4">
                    <div class="feature-card p-4 text-center h-100">
                        <div class="feature-icon mx-auto mb-4">
                            <i class="bi bi-tree fs-1"></i>
                        </div>
                        <h3 class="h4 mb-3">Eco-Friendly</h3>
                        <p class="text-muted">Sustainable materials, responsible sourcing</p>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="col-md-4">
                    <div class="feature-card p-4 text-center h-100">
                        <div class="feature-icon mx-auto mb-4">
                            <i class="bi bi-star fs-1"></i>
                        </div>
                        <h3 class="h4 mb-3">Premium Quality</h3>
                        <p class="text-muted">Handcrafted by skilled artisans</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* Feature Section Styles */
        .feature-card {
            background: #fff;
            border-radius: 15px;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            background: #000;
            color: #fff;
            transform: scale(1.1);
        }
    </style>

    <script>
        // Add animation to feature cards
        document.addEventListener('DOMContentLoaded', function() {
            const featureCards = document.querySelectorAll('.feature-card');
            featureCards.forEach((card, index) => {
                card.style.transitionDelay = `${index * 0.1}s`;
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add JavaScript interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Add scroll animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.furniture-card, .feature-icon').forEach(card => {
                observer.observe(card);
            });
        });
    </script>

<?php include '../includes/footer.php'; ?>
</body>
</html> 