<?php
// No whitespace or output before this PHP tag
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/styles.css" rel="stylesheet">
    <script src="/assets/js/admin.js"></script>

    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 70px;
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --dark-color: #5a5c69;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
        }

        body {
            background-color: #f8f9fc;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-brand {
            height: 70px;
            padding: 1.5rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand h4 {
            color: white;
            margin: 0;
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s;
        }

        .collapsed .sidebar-brand h4 {
            opacity: 0;
            display: none;
        }

        .nav-item {
            position: relative;
            margin: 0.5rem 0;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            position: relative;
            white-space: nowrap;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link i {
            font-size: 1.1rem;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.35rem;
            margin-right: 0.5rem;
            transition: all 0.3s;
        }

        .collapsed .nav-link span {
            opacity: 0;
            display: none;
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-link.active i {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Submenu Styles */
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: rgba(0, 0, 0, 0.1);
        }

        .submenu.show {
            max-height: 1000px;
        }

        .submenu .nav-link {
            padding-left: 4rem;
            font-size: 0.9rem;
        }

        /* Main Content Wrapper */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            transition: all 0.3s;
        }

        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Top Navigation */
        .top-nav {
            background: white;
            height: 70px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            margin-bottom: 1.5rem;
        }

        .top-nav .toggle-sidebar {
            background: none;
            border: none;
            color: var(--secondary-color);
            font-size: 1.5rem;
            padding: 0.5rem;
            cursor: pointer;
            transition: color 0.3s;
        }

        .top-nav .toggle-sidebar:hover {
            color: var(--primary-color);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.expanded {
                margin-left: 0;
            }
        }

        /* User Profile Section */
        .user-profile {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: auto;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 1rem;
        }

        .user-info {
            color: white;
        }

        .user-info small {
            display: block;
            color: rgba(255, 255, 255, 0.6);
        }

        /* Notification Badge */
        .notification-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .notification-dropdown {
            width: 320px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .notification-item {
            transition: background-color 0.3s;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-item.unread {
            background-color: #f0f7ff;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-icon i {
            font-size: 1.2rem;
            color: #6c757d;
        }

        #markAllRead:hover {
            text-decoration: underline !important;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h4>Admin Panel</h4>
            <button class="toggle-sidebar d-none d-md-block">
                <i class="bi bi-chevron-left"></i>
            </button>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" 
                   href="../admin/dashboard.php">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Products Dropdown -->
            <li class="nav-item">
                <a class="nav-link dropdown-toggle" href="#productsSubmenu" data-bs-toggle="collapse">
                    <i class="bi bi-box"></i>
                    <span>Products</span>
                </a>
                <div class="submenu collapse" id="productsSubmenu">
                    <a class="nav-link" href="../admin/products.php">
                        <i class="bi bi-circle"></i>
                        <span>All Products</span>
                    </a>
                    <a class="nav-link" href="../admin/products.php">
                        <i class="bi bi-plus-circle"></i>
                        <span>Add Product</span>
                    </a>
                    <a class="nav-link" href="../admin/categories.php">
                        <i class="bi bi-grid"></i>
                        <span>Categories</span>
                    </a>
                </div>
            </li>

            <!-- Orders Dropdown -->
            <li class="nav-item">
                <a class="nav-link dropdown-toggle" href="#ordersSubmenu" data-bs-toggle="collapse">
                    <i class="bi bi-cart"></i>
                    <span>Orders</span>
                    <span class="notification-badge">5</span>
                </a>
                <div class="submenu collapse" id="ordersSubmenu">
                    <a class="nav-link" href="../admin/orders.php">
                        <i class="bi bi-circle"></i>
                        <span>All Orders</span>
                    </a>
                    <a class="nav-link" href="../admin/pending_orders.php">
                        <i class="bi bi-clock"></i>
                        <span>Pending Orders</span>
                    </a>
                    <a class="nav-link" href="../admin/completed_orders.php">
                        <i class="bi bi-check-circle"></i>
                        <span>Completed</span>
                    </a>
                </div>
            </li>

            <!-- Users Section -->
            <li class="nav-item">
                <a class="nav-link dropdown-toggle" href="#usersSubmenu" data-bs-toggle="collapse">
                    <i class="bi bi-people"></i>
                    <span>Users</span>
                </a>
                <div class="submenu collapse" id="usersSubmenu">
                    <a class="nav-link" href="../admin/users.php">
                        <i class="bi bi-circle"></i>
                        <span>All Users</span>
                    </a>
                    <a class="nav-link" href="../admin/create_user.php">
                        <i class="bi bi-person-plus"></i>
                        <span>Add User</span>
                    </a>
                </div>
            </li>

            <!-- Payments Section -->
            <li class="nav-item">
                <a class="nav-link dropdown-toggle" href="#paymentsSubmenu" data-bs-toggle="collapse">
                    <i class="bi bi-credit-card"></i>
                    <span>Payments</span>
                </a>
                <div class="submenu collapse" id="paymentsSubmenu">
                    <a class="nav-link" href="../admin/payments.php">
                        <i class="bi bi-circle"></i>
                        <span>All Payments</span>
                    </a>
                    <a class="nav-link" href="../admin/payment_reports.php">
                        <i class="bi bi-graph-up"></i>
                        <span>Reports</span>
                    </a>
                </div>
            </li>

            <!-- Settings -->
            <li class="nav-item">
                <a class="nav-link" href="../admin/settings.php">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>

        <!-- User Profile Section -->
        <div class="user-profile d-flex align-items-center">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRkkCM82V9-rngvGCj8DdegNCm_jtoM2QaAEw&s" alt="Admin">
            <div class="user-info">
                <span>Admin User</span>
                <small>Super Admin</small>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content" id="main-content">
        <!-- Top Navigation -->
        <div class="top-nav">
            <button class="toggle-sidebar d-md-none" id="mobile-toggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="ms-auto d-flex align-items-center">
                <!-- Notifications Dropdown -->
                <div class="dropdown me-3">
                    <button class="btn btn-link position-relative" type="button" id="notificationsDropdown" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell fs-5"></i>
                        <?php
                        // Get unread notifications count
                        $notify_sql = "SELECT COUNT(*) as count FROM notifications WHERE is_read = 0";
                        $notify_result = $conn->query($notify_sql);
                        $notify_count = $notify_result->fetch_assoc()['count'];
                        if ($notify_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $notify_count; ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0" aria-labelledby="notificationsDropdown">
                        <div class="notification-header d-flex justify-content-between align-items-center p-3 border-bottom">
                            <h6 class="mb-0">Notifications</h6>
                            <?php if ($notify_count > 0): ?>
                                <a href="#" class="text-decoration-none small" id="markAllRead">Mark all as read</a>
                            <?php endif; ?>
                        </div>
                        <div class="notification-body" style="max-height: 300px; overflow-y: auto;">
                            <?php
                            $notifications_sql = "SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5";
                            $notifications_result = $conn->query($notifications_sql);
                            
                            if ($notifications_result->num_rows > 0):
                                while ($notification = $notifications_result->fetch_assoc()):
                            ?>
                                <a class="dropdown-item notification-item p-3 border-bottom <?php echo $notification['is_read'] ? '' : 'unread'; ?>"
                                   href="<?php echo $notification['link']; ?>">
                                    <div class="d-flex">
                                        <div class="notification-icon me-3">
                                            <?php
                                            $icon_class = 'bi-bell';
                                            switch($notification['type']) {
                                                case 'order':
                                                    $icon_class = 'bi-cart';
                                                    break;
                                                case 'payment':
                                                    $icon_class = 'bi-credit-card';
                                                    break;
                                                case 'user':
                                                    $icon_class = 'bi-person';
                                                    break;
                                            }
                                            ?>
                                            <i class="bi <?php echo $icon_class; ?>"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                            <small class="text-muted">
                                                <?php echo time_elapsed_string($notification['created_at']); ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <div class="p-3 text-center text-muted">
                                    No notifications
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="notification-footer p-2 text-center border-top">
                            <a href="../admin/notifications.php" class="text-decoration-none small">View all notifications</a>
                        </div>
                    </div>
                </div>

                <!-- User Account Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link d-flex align-items-center text-decoration-none" type="button" 
                            id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo isset($_SESSION['admin_avatar']) ? $_SESSION['admin_avatar'] : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRkkCM82V9-rngvGCj8DdegNCm_jtoM2QaAEw&s'; ?>" 
                             alt="Admin" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                        <span class="d-none d-md-block text-dark">
                            <?php echo isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Admin'; ?>
                        </span>
                        <i class="bi bi-chevron-down ms-2"></i>
            </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <div class="dropdown-item-text">
                                <small class="text-muted">Signed in as</small><br>
                                <strong><?php echo isset($_SESSION['admin_email']) ? htmlspecialchars($_SESSION['admin_email']) : 'admin@email.com'; ?></strong>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../admin/profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="../admin/settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../admin/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
        </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const toggleButtons = document.querySelectorAll('.toggle-sidebar');
    const mobileToggle = document.getElementById('mobile-toggle');

    // Desktop sidebar toggle
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (window.innerWidth >= 768) {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                // Rotate chevron icon
                const chevron = this.querySelector('.bi-chevron-left');
                if (chevron) {
                    chevron.classList.toggle('bi-chevron-right');
                }
            }
        });
    });

    // Mobile sidebar toggle
    mobileToggle.addEventListener('click', function() {
        sidebar.classList.toggle('mobile-active');
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 768) {
            const isClickInside = sidebar.contains(event.target) || 
                                mobileToggle.contains(event.target);
            
            if (!isClickInside && sidebar.classList.contains('mobile-active')) {
                sidebar.classList.remove('mobile-active');
            }
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            sidebar.classList.remove('mobile-active');
        }
    });

    // Submenu toggle animation
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const submenu = document.querySelector(this.getAttribute('href'));
            submenu.classList.toggle('show');
            this.classList.toggle('active');
        });
    });

    // Mark all notifications as read
    const markAllReadBtn = document.getElementById('markAllRead');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            fetch('../admin/mark_notifications_read.php', {
                method: 'POST',
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove unread class and badge
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                    });
                    const badge = document.querySelector('#notificationsDropdown .badge');
                    if (badge) badge.remove();
                }
            });
        });
    }
});

// Helper function for time elapsed string
<?php
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );

    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>
</script>

<?php
// Add this function to get the current page name
function isCurrentPage($pageName) {
    return basename($_SERVER['PHP_SELF']) === $pageName;
}
?>

    <!-- Add these scripts before closing body tag -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 