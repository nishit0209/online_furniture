<?php
// Start session and include config first
session_start();

// Check if admin is logged in - do this before any output
if (!isset($_SESSION['admin_id'])) {
    // Store any messages in session
    $_SESSION['error_message'] = "Please login to access the admin panel.";
    header('Location: login.php');
    exit();
}

// Include database configuration
require_once '../includes/config.php';

// Fetch admin details with error handling
$sql = "SELECT * FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Admin account not found.";
    session_destroy();
    header('Location: login.php');
    exit();
}

$admin = $result->fetch_assoc();

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_username'])) {
        $username = trim($_POST['username']);
        
        // Check if username already exists
        $check_sql = "SELECT id FROM admins WHERE username = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $username, $_SESSION['admin_id']);
        $check_stmt->execute();
        $exists = $check_stmt->get_result()->num_rows > 0;

        if ($exists) {
            $error_message = "Username already taken.";
        } else {
            $update_sql = "UPDATE admins SET username = ? WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("si", $username, $_SESSION['admin_id']);
            
            if ($stmt->execute()) {
                $_SESSION['admin_username'] = $username;
                $success_message = "Username updated successfully!";
            } else {
                $error_message = "Error updating username.";
            }
        }
    }

    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password === $confirm_password) {
            if (password_verify($current_password, $admin['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE admins SET password = ? WHERE id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("si", $hashed_password, $_SESSION['admin_id']);
                
                if ($stmt->execute()) {
                    $success_message = "Password changed successfully!";
                } else {
                    $error_message = "Error changing password.";
                }
            } else {
                $error_message = "Current password is incorrect.";
            }
        } else {
            $error_message = "New passwords do not match.";
        }
    }

    // Handle avatar upload
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (in_array($_FILES['avatar']['type'], $allowed_types) && $_FILES['avatar']['size'] <= $max_size) {
            $upload_dir = '../uploads/admin/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $filename = 'admin_' . $_SESSION['admin_id'] . '_' . time() . '.' . $file_extension;
            $target_path = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_path)) {
                $update_sql = "UPDATE admins SET avatar = ? WHERE id = ?";
                $stmt = $conn->prepare($update_sql);
                $avatar_path = 'uploads/admin/' . $filename;
                $stmt->bind_param("si", $avatar_path, $_SESSION['admin_id']);
                
                if ($stmt->execute()) {
                    $_SESSION['admin_avatar'] = $avatar_path;
                    $success_message = "Profile picture updated successfully!";
                }
            } else {
                $error_message = "Error uploading profile picture.";
            }
        } else {
            $error_message = "Invalid file type or size too large.";
        }
    }
}

// Now include the header template
require_once '../includes/admin_header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Admin Profile</h2>
        </div>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-muted mb-0">Username</h6>
                            <button class="btn btn-sm btn-outline-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editUsernameModal">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                        </div>
                        <p class="h5">
                            <?php echo htmlspecialchars($admin['username'] ?? 'N/A'); ?>
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Account Created</h6>
                        <p class="h6">
                            <i class="bi bi-calendar-event me-2"></i>
                            <?php 
                            if (!empty($admin['created_at'])) {
                                echo date('F d, Y', strtotime($admin['created_at']));
                            } else {
                                echo 'Not available';
                            }
                            ?>
                        </p>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-outline-primary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#changePasswordModal">
                            <i class="bi bi-key me-2"></i>
                            Change Password
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Security Tips</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="bi bi-shield-lock me-2"></i>Password Security</h6>
                        <ul class="mb-0">
                            <li>Use a strong password with at least 8 characters</li>
                            <li>Include numbers, symbols, and capital letters</li>
                            <li>Change your password regularly</li>
                            <li>Don't share your password with others</li>
                        </ul>
                    </div>
                    <div class="alert alert-warning">
                        <h6><i class="bi bi-exclamation-triangle me-2"></i>Account Security</h6>
                        <ul class="mb-0">
                            <li>Always log out when finished</li>
                            <li>Don't use public computers for admin access</li>
                            <li>Keep your login credentials secure</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Username Modal -->
<div class="modal fade" id="editUsernameModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Username</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">New Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($admin['username'] ?? ''); ?>" 
                               required>
                        <small class="text-muted">Username must be unique.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_username" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" 
                               name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" 
                               name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" 
                               name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Avatar Upload Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Choose Image</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" 
                               accept="image/*" required>
                        <small class="text-muted">Max file size: 5MB. Supported formats: JPG, PNG, GIF</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="upload_avatar" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    position: relative;
    padding-left: 30px;
    margin-bottom: 20px;
}

.timeline-icon {
    position: absolute;
    left: 0;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.timeline-icon i {
    font-size: 8px;
}

.timeline-content {
    padding-left: 15px;
}
</style>

