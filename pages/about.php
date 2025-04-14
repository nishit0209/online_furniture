<?php
require_once '../includes/config.php';
require_once '../includes/header.php';
?>

<section class="about-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 mb-4 animate__animated animate__fadeInLeft">About Us</h1>
                <p class="lead animate__animated animate__fadeInLeft animate__delay-1s">
                    We are passionate about creating beautiful, functional spaces that inspire.
                </p>
                <p class="animate__animated animate__fadeInLeft animate__delay-2s">
                    Founded in 2010, our furniture store has been dedicated to providing high-quality, 
                    stylish furniture that combines comfort and elegance. We work with skilled artisans 
                    and use sustainable materials to craft pieces that last a lifetime.
                </p>
                <div class="mt-4 animate__animated animate__fadeInLeft animate__delay-3s">
                    <a href="collection.php" class="btn btn-primary btn-lg">View Our Collection</a>
                </div>
            </div>
            <div class="col-md-6">
                <img src="../images/about.jpg" alt="About Us" class="img-fluid rounded shadow animate__animated animate__fadeInRight">
            </div>
        </div>
    </div>
</section>

<style>
    .about-section {
        background: linear-gradient(135deg, #ffffff, #f8f9fa);
    }
    .about-section img {
        max-height: 500px;
        object-fit: cover;
    }
</style>

<?php
require_once '../includes/footer.php';
?> 