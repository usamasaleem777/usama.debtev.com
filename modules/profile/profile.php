<?php

// Initialize variables
$error = '';
$success = '';
$user = [];
$formattedName = 'Admin';
$roleText = 'User';

try {
    // Check authentication
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    $user_id = (int) $_SESSION['user_id'];

    // Generate CSRF token
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // Handle form submission
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid CSRF token');
        }

        // Handle AJAX profile picture upload
        if (isset($_POST['isAjaxUpload'])) {
            try {
                if (empty($_FILES['profilePicInput']['name'])) {
                    throw new Exception('No file uploaded');
                }

                $uploadDir = __DIR__ . '/../../uploads/profileImages/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $allowedTypes = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/heic' => 'heic',
                    'image/heif' => 'heif'
                ];

                $detectedType = mime_content_type($_FILES['profilePicInput']['tmp_name']);

                if (!array_key_exists($detectedType, $allowedTypes)) {
                    throw new Exception('Only JPG, PNG, and HEIC files are allowed.');
                }

                if ($_FILES['profilePicInput']['size'] > 20 * 1024 * 1024) {
                    throw new Exception('File size must be less than 20MB');
                }

                // Delete old picture if exists
                $user = DB::queryFirstRow("SELECT picture FROM users WHERE user_id = %i", $user_id);
                if (!empty($user['picture']) && file_exists(__DIR__ . '/../../' . $user['picture'])) {
                    unlink(__DIR__ . '/../../' . $user['picture']);
                }

                // Generate unique filename
                $extension = $allowedTypes[$detectedType];
                $filename = "user_{$user_id}_" . time() . ".$extension";
                $targetPath = $uploadDir . $filename;

                if (!move_uploaded_file($_FILES['profilePicInput']['tmp_name'], $targetPath)) {
                    throw new Exception('Error uploading file');
                }

                $relativePath = "uploads/profileImages/$filename";
                DB::update('users', ['picture' => $relativePath], "user_id = %i", $user_id);

                echo json_encode(['success' => true]);
                exit;
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        }

        // Process regular form submission
        $name = filter_input(INPUT_POST, 'fullNameInput', FILTER_SANITIZE_SPECIAL_CHARS);
        $username = filter_input(INPUT_POST, 'usernameInput', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'emailInput', FILTER_SANITIZE_EMAIL);
        $phone = filter_input(INPUT_POST, 'phoneInput', FILTER_SANITIZE_SPECIAL_CHARS);

        // Validate required fields
        if (empty($name) || empty($username) || empty($email)) {
            throw new Exception('All required fields must be filled');
        }

        // Update user data
        DB::update('users', [
            'name' => $name,
            'user_name' => $username,
            'email' => $email,
            'phone' => $phone
        ], "user_id = %i", $user_id);

        $success = 'Profile updated successfully!';
    }
    // Get updated user data
    $user = DB::queryFirstRow("SELECT * FROM users WHERE user_id = %i", $user_id);

    // Format data for display
    $formattedName = isset($user['name']) ? ucfirst($user['name']) : 'Admin';
    $formattedUsername = isset($user['first_name']) && !empty($user['first_name'])
        ? strtolower(trim($user['first_name'] . ' ' . ($user['last_name'] ?? '')))
        : (isset($user['user_name']) ? strtolower($user['user_name']) : 'N/A');
    if ($user['role_id'] == 1) {
        $roleText = 'Admin';
    } elseif ($user['role_id'] == 2) {
        $roleText = 'Manager';
    } elseif ($user['role_id'] == 5) {
        $roleText = 'Craftman';
    } elseif ($user['role_id'] == 3) {
        $roleText = 'Foreman';
    } elseif ($user['role_id'] == 6) {
        $roleText = 'Superindendent';
    } elseif ($user['role_id'] == 7) {
        $roleText = 'Tool Manager';
    } elseif ($user['role_id'] == 8) {
        $roleText = 'HR';
    } else {
        $roleText = 'User';
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<style>
    :root {
        --primary-color: #fe5500;
        --secondary-color: #6c757d;
        --light-bg: #f8f9fa;
        --border-radius: 8px;
        --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
    }

    body {
        background-color: #f5f7fa;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        color: #333;
        line-height: 1.6;
        padding-bottom: 2rem;
    }

    .profile-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #ff7b25 100%);
        margin-top: 10px;
        padding: 1.5rem;
        color: white;
        border-radius: var(--border-radius);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .profile-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
    }

    .banner {
        background-color: #ffffff;
        color: var(--primary-color);
        text-align: center;
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
        font-weight: 600;
        border-radius: var(--border-radius);
        margin-top: 0.75rem;
        box-shadow: var(--box-shadow);
        display: inline-block;
    }

    .profile-pic-container {
        position: relative;
        width: 100px;
        height: 100px;
        flex-shrink: 0;
        margin-bottom: 1rem;
    }

    .profile-pic {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.2);
        object-fit: cover;
        background-color: #f0f0f0;
        transition: var(--transition);
    }

    .profile-pic-container:hover .profile-pic {
        border-color: rgba(255, 255, 255, 0.5);
    }

    .upload-button {
        position: absolute;
        bottom: 0;
        right: 0;
        transform: translate(-25%);
        background-color: white;
        color: var(--primary-color);
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: var(--transition);
        font-size: 0.8rem;
    }

    .upload-button:hover {
        background-color: var(--primary-color);
        color: white;
        transform: translate(-25%) scale(1.1);
    }

    .profile-info {
        z-index: 1;
        text-align: center;
    }

    .profile-info h3 {
        font-weight: 700;
        margin-bottom: 0.25rem;
        font-size: 1.5rem;
    }

    .profile-info p {
        opacity: 0.9;
        font-size: 0.95rem;
    }

    .card-custom {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        overflow: hidden;
        background: white;
        margin-bottom: 1.5rem;
    }

    .card-custom:hover {
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }

    .card-custom h5 {
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-size: 1.25rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--secondary-color);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-control {
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 0.65rem 1rem;
        transition: var(--transition);
        font-size: 0.95rem;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(254, 85, 0, 0.15);
    }

    .save-btn {
        background-color: var(--primary-color);
        color: white;
        font-weight: 600;
        padding: 0.65rem 1.5rem;
        border-radius: 6px;
        border: none;
        transition: var(--transition);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
        width: 100%;
    }

    .save-btn:hover {
        background-color: #d94700;
        color: rgb(255, 255, 255);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 255, 255, 0.3);
    }

    .about-list li {
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        font-size: 0.95rem;
    }

    .about-list li:last-child {
        border-bottom: none;
    }

    .about-list strong {
        min-width: 80px;
        color: var(--secondary-color);
        font-weight: 500;
        font-size: 0.9rem;
    }

    .alert {
        border-radius: var(--border-radius);
        padding: 0.85rem 1.25rem;
        margin-bottom: 1.25rem;
        border: none;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .alert-danger {
        background-color: rgb(255, 255, 255);
        color: #d32f2f;
    }

    .alert-success {
        background-color: #e8f5e9;
        color: #388e3c;
    }

    .breadcrumb {
        background: transparent;
        padding: 0.5rem 0;
        font-size: 0.9rem;
        flex-wrap: nowrap;
        overflow-x: auto;
        white-space: nowrap;
    }

    .breadcrumb-item {
        display: inline-block;
    }

    .breadcrumb-item a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
    }

    .breadcrumb-item a:hover {
        color: #d94700;
        text-decoration: underline;
    }

    .breadcrumb-item.active {
        color: var(--secondary-color);
    }

    .btn-outline-primary {
        background-color: #fe5500;
        border-color: #fe5500;
        color: rgb(255, 255, 255);
        font-size: 0.9rem;
        padding: 0.6rem 1rem;
    }

    .btn-outline-primary:hover {
        background-color: #d94700;
        border-color: #d94700;
        color: rgb(255, 255, 255);
    }

    .page-header {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
    }

    .page-header h2 {
        font-size: 1.5rem;
        margin-bottom: 0.75rem;
    }

    .input-group-text {
        font-size: 0.9rem;
        padding: 0.65rem 0.75rem;
    }

    /* Tablet and larger */
    @media (min-width: 768px) {
        .profile-header {
            flex-direction: row;
            text-align: left;
            padding: 2rem;
        }

        .profile-info {
            margin-left: 1.5rem;
            text-align: left;
        }

        .profile-pic-container {
            width: 120px;
            height: 120px;
            margin-bottom: 0;
        }

        .save-btn {
            width: auto;
        }

        /* .page-header {
            flex-direction: row;
        align-items: center;
        justify-content: space-between;
        } */

        .page-header h2 {
            font-size: 1.75rem;
            margin-bottom: 0;
        }
    }

    /* Desktop and larger */
    @media (min-width: 992px) {
        .profile-header {
            padding: 2.5rem;
        }

        .profile-info h3 {
            font-size: 1.75rem;
        }

        .card-custom h5 {
            font-size: 1.35rem;
        }
    }

    /* Extra small devices (phones, 480px and down) */
    @media (max-width: 480px) {
        .profile-pic-container {
            width: 90px;
            height: 90px;
        }

        .profile-info h3 {
            font-size: 1.3rem;
        }

        .banner {
            font-size: 0.8rem;
            padding: 0.4rem 0.6rem;
        }

        .card-custom {
            padding: 1.25rem;
        }

        .card-custom h5 {
            font-size: 1.15rem;
            margin-bottom: 1rem;
        }

        .about-list li {
            flex-direction: column;
        }

        .about-list strong {
            min-width: 100%;
            margin-bottom: 0.25rem;
        }
    }
</style>

<!-- Font Awesome CSS for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Google Fonts - Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Page header with breadcrumb navigation -->
<div class="container">
    <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
        <div class="ms-md-auto" style="margin-top: 10px;">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="index.php" style="color: #fe5500"><i
                            class="fas fa-home me-1"></i><?php echo lang("user_home"); ?></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="#" style="color: #fe5500"><?php echo lang("profile_profile"); ?></a>
                </li>
                <li class="breadcrumb-item active">
                    <?php echo lang("profile_edit_profile"); ?>
                </li>
            </ol>
        </div>
    </div>

    <!-- Status messages -->
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Profile header section -->
    <h2 class="mb-3 mb-md-0" style="color: var(--primary-color); font-weight: 700; margin-top: -5px;">
        <?php echo lang("profile_edit_profile"); ?>
    </h2>
    <div class="profile-header">
        <div class="profile-pic-container">
            <img id="profileImage"
                src="<?= htmlspecialchars(!empty($user['picture']) ? $user['picture'] : 'https://placehold.co/120x120.png?text=' . substr($formattedName, 0, 1)) ?>"
                alt="Profile Picture" class="profile-pic"
                onerror="this.src='https://placehold.co/120x120.png?text=<?= substr($formattedName, 0, 1) ?>'">
            <input type="file" id="profilePicInput" name="profilePicInput" accept="image/*" style="display: none;">
            <label for="profilePicInput" class="upload-button">
                <i class="fas fa-camera"></i>
            </label>
        </div>
        <div class="profile-info">
            <h3 id="displayName"><?= htmlspecialchars($formattedName) ?></h3>
            <p id="displayUsername">@<?= htmlspecialchars($formattedUsername) ?></p>
            <div class="banner mt-2">
                <i class="fas fa-user-tag me-2"></i><?php echo lang("profile_role"); ?>:
                <?= htmlspecialchars($roleText) ?>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Edit Profile Form -->
        <div class="col-lg-8 order-lg-1 order-2">
            <div class="card card-custom p-3 p-md-4 mb-4">
                <h5><i class="fas fa-user-edit me-2"></i><?php echo lang("profile_edit_profile"); ?></h5>
                <form id="editProfileForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="mb-3">
                        <label for="fullNameInput" class="form-label"><?php echo lang("profile_full_name"); ?></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="fullNameInput" class="form-control"
                                placeholder="<?php echo lang("profile_full_name"); ?>"
                                value="<?= htmlspecialchars(isset($user['name']) ? $user['name'] : 'Admin') ?>"
                                required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="usernameInput" class="form-label"><?php echo lang("profile_username"); ?></label>
                        <div class="input-group">
                            <span class="input-group-text">@</span>
                            <input type="text" name="usernameInput" class="form-control"
                                placeholder="<?php echo lang("profile_username"); ?>"
                                value="<?= htmlspecialchars(isset($user['user_name']) ? $user['user_name'] : 'admin') ?>"
                                required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="emailInput" class="form-label"><?php echo lang("profile_email"); ?></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="emailInput" class="form-control"
                                placeholder="<?php echo lang("profile_email"); ?>"
                                value="<?= htmlspecialchars(isset($user['email']) ? $user['email'] : '') ?>" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="phoneInput" class="form-label"><?php echo lang("profile_phone"); ?></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" name="phoneInput" class="form-control"
                                placeholder="<?php echo lang("profile_phone"); ?>"
                                value="<?= htmlspecialchars(isset($user['phone']) ? $user['phone'] : '-') ?>">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn save-btn">
                            <i class="fas fa-save me-2"></i><?php echo lang("profile_save"); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- About Section -->
        <div class="col-lg-4 order-lg-2 order-1 mb-4 mb-lg-0">
            <div class="card card-custom p-3 p-md-4 mb-4">
                <h5><i class="fas fa-info-circle me-2"></i><?php echo lang("profile_about"); ?></h5>
                <ul class="list-unstyled about-list mt-3">
                    <li>
                        <strong><?php echo lang("profile_name"); ?></strong>
                        <span><?= htmlspecialchars(isset($user['name']) ? $user['name'] : 'Admin') ?></span>
                    </li>
                    <li>
                        <strong><?php echo lang("profile_username"); ?></strong>
                        <span><?= htmlspecialchars(isset($user['user_name']) ? $user['user_name'] : 'admin') ?></span>
                    </li>
                    <li>
                        <strong><?php echo lang("profile_email"); ?></strong>
                        <span><?= htmlspecialchars(isset($user['email']) ? $user['email'] : '-') ?></span>
                    </li>
                    <li>
                        <strong><?php echo lang("profile_phone"); ?></strong>
                        <span><?= htmlspecialchars(isset($user['phone']) ? $user['phone'] : '-') ?></span>
                    </li>
                    <li>
                        <strong><?php echo lang("profile_role"); ?></strong>
                        <span><?= htmlspecialchars($roleText) ?></span>
                    </li>
                    <li>
                        <strong>Kiosk ID</strong>
                        <span><?= htmlspecialchars(isset($user['kioskID']) ? $user['kioskID'] : 'N/A') ?></span>
                    </li>
                </ul>
            </div>

            <!-- Additional Card (optional) -->
            <div class="card card-custom p-3 p-md-4">
                <h5><i class="fas fa-shield-alt me-2"></i><?php echo lang("profile_security"); ?></h5>
                <div class="d-grid gap-2 mt-3">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                        data-bs-target="#changePasswordModal">
                        <i class="fas fa-key me-2"></i><?php echo lang("profile_change_password"); ?>
                    </button>
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="fas fa-question-circle me-2"></i><?php echo lang("profile_help"); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add this modal HTML right before the closing </div> tag of the main container -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content"
            style="border-radius: var(--border-radius); border: none; box-shadow: var(--box-shadow);">
            <div class="modal-header"
                style="background: linear-gradient(135deg, var(--primary-color) 0%, #ff7b25 100%); color: white; border-top-left-radius: var(--border-radius); border-top-right-radius: var(--border-radius);">
                <h5 class="modal-title" id="changePasswordModalLabel" style="font-weight: 700;">
                    <i class="fas fa-key me-2"></i>Change Password
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="changePasswordForm">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="currentPassword" name="current_password"
                                required>
                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                data-target="currentPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="currentPasswordFeedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="newPassword" name="new_password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                data-target="newPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="newPasswordFeedback"></div>
                        <small class="form-text text-muted">Password must be at least 8 characters long</small>
                    </div>

                    <div class="mb-4">
                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password"
                                required>
                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                data-target="confirmPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="confirmPasswordFeedback"></div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn save-btn">
                            <i class="fas fa-save me-2"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modal
        const changePasswordModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));

        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const target = document.getElementById(this.getAttribute('data-target'));
                const icon = this.querySelector('i');

                if (target.type === 'password') {
                    target.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    target.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Password validation
        const currentPassword = document.getElementById('currentPassword');
        const newPassword = document.getElementById('newPassword');
        const confirmPassword = document.getElementById('confirmPassword');

        function validatePassword() {
            let isValid = true;

            // Validate new password is different from current password
            if (currentPassword.value && newPassword.value &&
                currentPassword.value === newPassword.value) {
                document.getElementById('newPasswordFeedback').textContent = 'New password must be different from current password';
                newPassword.classList.add('is-invalid');
                isValid = false;
            }
            // Validate new password length
            else if (newPassword.value.length < 8) {
                document.getElementById('newPasswordFeedback').textContent = 'Password must be at least 8 characters long';
                newPassword.classList.add('is-invalid');
                isValid = false;
            } else {
                newPassword.classList.remove('is-invalid');
            }

            // Validate password match
            if (newPassword.value !== confirmPassword.value && confirmPassword.value.length > 0) {
                document.getElementById('confirmPasswordFeedback').textContent = 'New password and confirm password do not match';
                confirmPassword.classList.add('is-invalid');
                isValid = false;
            } else {
                confirmPassword.classList.remove('is-invalid');
            }

            return isValid;
        }

        currentPassword.addEventListener('input', validatePassword);
        newPassword.addEventListener('input', validatePassword);
        confirmPassword.addEventListener('input', validatePassword);

        // Form submission
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validatePassword()) {
                return;
            }

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
            submitBtn.disabled = true;

            fetch('ajax_helpers/update_password.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Show success message with SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: 'Password Updated!',
                            text: 'Your password has been changed successfully',
                            timer: 3000,
                            showConfirmButton: false
                        });

                        // Close modal and reset form
                        changePasswordModal.hide();
                        this.reset();
                    } else {
                        // Show error message with SweetAlert only for current password mismatch
                        if (data.message.includes('Current password')) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Incorrect Password',
                                text: 'The current password you entered is incorrect',
                                timer: 3000,
                                showConfirmButton: false
                            });

                            document.getElementById('currentPasswordFeedback').textContent = data.message;
                            document.getElementById('currentPassword').classList.add('is-invalid');
                        } else {
                            // For other errors, show inline feedback
                            const errorAlert = document.createElement('div');
                            errorAlert.className = 'alert alert-danger alert-dismissible fade show mt-3';
                            errorAlert.innerHTML = `
                        <i class="fas fa-exclamation-circle me-2"></i>
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                            document.getElementById('changePasswordForm').appendChild(errorAlert);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'The current password you entered is incorrect',
                        timer: 3000,
                        showConfirmButton: false
                    });
                })
                .finally(() => {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                });
        });
    });
</script>

<script>
    // Enhanced JavaScript with better UX
    document.addEventListener('DOMContentLoaded', function() {
        const editProfileForm = document.getElementById('editProfileForm');

        // Form submission handler
        editProfileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitFormWithFile();
        });

        function submitFormWithFile() {
            const formData = new FormData(editProfileForm);

            // Show loading state
            const saveBtn = editProfileForm.querySelector('button[type="submit"]');
            const originalBtnText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><?php echo lang("profile_saving"); ?>';
            saveBtn.disabled = true;

            fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        return response.text();
                    }
                    throw new Error('Network response was not ok.');
                })
                .then(() => {
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    saveBtn.innerHTML = originalBtnText;
                    saveBtn.disabled = false;
                    alert('<?php echo lang("profile_error_saving"); ?>');
                });
        }
    });
</script>
<!-- Add this with your other script includes -->
<script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.3/dist/heic2any.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Profile picture upload handler with HEIC conversion
        document.getElementById('profilePicInput').addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Client-side validation
            if (file.size > 20 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File too large',
                    text: 'File size must be less than 20MB',
                    timer: 3000
                });
                this.value = '';
                return;
            }

            try {
                // Show loading indicator
                const swalInstance = Swal.fire({
                    title: 'Processing Image',
                    html: 'Please wait while we prepare your image...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                // Convert HEIC to JPG if needed
                const processedFile = await processImageFile(file);

                // Update preview
                await updateImagePreview(processedFile);

                // Upload via AJAX
                await uploadProfilePicture(processedFile);

                // Success notification
                await swalInstance.close();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Profile picture updated successfully',
                    timer: 2000,
                    showConfirmButton: false
                });
                // Add this line to reload the page after upload
                setTimeout(() => {
                    window.location.reload();
                }, 2100); // Slight delay to let the success message show
            } catch (error) {
                console.error('Upload error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed',
                    text: error.message || 'An error occurred while uploading the image',
                    timer: 3000
                });
                this.value = '';
            }
        });

        // Regular form submission handler
        const editProfileForm = document.getElementById('editProfileForm');
        if (editProfileForm) {
            editProfileForm.addEventListener('submit', function(e) {
                // Prevent default if we're handling an image upload
                if (document.getElementById('profilePicInput').files.length > 0) {
                    e.preventDefault();
                    return;
                }

                // Show loading state for regular form submission
                const saveBtn = this.querySelector('button[type="submit"]');
                const originalBtnText = saveBtn.innerHTML;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><?php echo lang("profile_saving"); ?>';
                saveBtn.disabled = true;
            });
        }

        // Helper functions remain the same as before
        async function processImageFile(file) {
            const isHEIC = file.name.toLowerCase().endsWith('.heic') ||
                file.type === 'image/heic' ||
                file.type === 'image/heif';

            if (!isHEIC) return file;

            try {
                const conversionResult = await heic2any({
                    blob: file,
                    toType: 'image/jpeg',
                    quality: 0.8
                });

                return new File([conversionResult], file.name.replace(/\.[^/.]+$/, '.jpg'), {
                    type: 'image/jpeg',
                    lastModified: new Date().getTime()
                });
            } catch (error) {
                console.error('HEIC conversion failed:', error);
                throw new Error('Failed to convert HEIC image. Please try another file.');
            }
        }

        function updateImagePreview(file) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('profileImage').src = event.target.result;
                    resolve();
                };
                reader.readAsDataURL(file);
            });
        }

        async function uploadProfilePicture(file) {
            const formData = new FormData();
            formData.append('profilePicInput', file);
            formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
            formData.append('isAjaxUpload', 'true');

            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Upload failed');
            }
        }
    });
</script>