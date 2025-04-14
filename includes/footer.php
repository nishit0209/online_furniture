    <footer class="bg-light text-dark pt-5">
        <div class="container">
            <div class="row g-4">
                <!-- About Section -->
                <div class="col-md-4">
                    <h5 class="mb-4">About Us</h5>
                    <p class="text-muted">We are dedicated to providing the finest furniture pieces, crafted with precision and passion.</p>
                    <div class="social-icons mt-4">
                        <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-md-2">
                    <h5 class="mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-muted text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="collections.php" class="text-muted text-decoration-none">Collections</a></li>
                        <li class="mb-2"><a href="about.php" class="text-muted text-decoration-none">About Us</a></li>
                        <li class="mb-2"><a href="contact.php" class="text-muted text-decoration-none">Contact</a></li>
                        <li class="mb-2"><a href="privacy.php" class="text-muted text-decoration-none">Privacy Policy</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="col-md-3">
                    <h5 class="mb-4">Contact Info</h5>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-3">
                            <i class="bi bi-geo-alt me-2"></i>
                            123 Luxury Street, Furniture City
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-telephone me-2"></i>
                            +1 (234) 567-8900
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-envelope me-2"></i>
                            info@luxuryfurniture.com
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div class="col-md-3">
                    <h5 class="mb-4">Newsletter</h5>
                    <p class="text-muted">Subscribe to get updates on new collections and special offers.</p>
                    <form class="d-flex">
                        <input type="email" class="form-control me-2" placeholder="Your email">
                        <button type="submit" class="btn btn-outline-light">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-top pt-4 mt-4 text-center">
                <p class="text-muted mb-0">&copy; <?php echo date('Y'); ?> Luxury Furniture. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/main.js"></script>
    <!-- Include Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>

    <style>
        /* Updated Footer Styles */
        footer {
            background: #f8f9fa !important; /* Light background */
        }

        footer a {
            color: #6c757d !important; /* Darker text for links */
        }

        footer a:hover {
            color: #000 !important; /* Black on hover */
        }

        .social-icons a {
            color: #6c757d !important; /* Darker social icons */
        }

        .social-icons a:hover {
            color: #000 !important; /* Black on hover */
        }

        .form-control {
            background: rgba(0,0,0,0.05);
            color: #000;
        }

        .form-control:focus {
            background: rgba(0,0,0,0.1);
            color: #000;
        }

        .btn-outline-light {
            border-color: #dee2e6;
            color: #6c757d;
        }

        .btn-outline-light:hover {
            background: #fff;
            color: #000;
        }
    </style>
</body>
</html> 