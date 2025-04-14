<?php
require_once '../includes/config.php';
require_once '../includes/header.php';
?>

<section class="contact-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h1 class="display-4 mb-4 animate__animated animate__fadeInLeft">Contact Us</h1>
                <p class="lead animate__animated animate__fadeInLeft animate__delay-1s">
                    We'd love to hear from you!
                </p>
                <div class="contact-info mt-4 animate__animated animate__fadeInLeft animate__delay-2s">
                    <p><i class="fas fa-map-marker-alt me-2"></i> 123 Furniture St, Design City</p>
                    <p><i class="fas fa-phone me-2"></i> (123) 456-7890</p>
                    <p><i class="fas fa-envelope me-2"></i> info@furniturestore.com</p>
                </div>
                <div class="social-links mt-4 animate__animated animate__fadeInLeft animate__delay-3s">
                    <a href="#" class="me-3"><i class="fab fa-facebook fa-2x"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-twitter fa-2x"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-instagram fa-2x"></i></a>
                </div>
            </div>
            <div class="col-md-6">
                <form class="animate__animated animate__fadeInRight">
                    <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Your Name" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" class="form-control" placeholder="Your Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Subject" required>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" rows="5" placeholder="Your Message" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
    .contact-section {
        background: linear-gradient(135deg, #f8f9fa, #ffffff);
    }
    .contact-info i {
        width: 20px;
        text-align: center;
    }
    .social-links a {
        color: #333;
        transition: color 0.3s ease;
    }
    .social-links a:hover {
        color: #0d6efd;
    }
    form {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
</style>

<?php
require_once '../includes/footer.php';
?> 