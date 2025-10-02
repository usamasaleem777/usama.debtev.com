<?php



require_once(__DIR__ . '/../../db_config.php');
require_once(__DIR__ . '/../../classes/db.class.php');

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $have_signature = DB::queryFirstRow(
        "SELECT * FROM application_signatures 
         WHERE user_id = %i",
        $user_id
    );
}
// Handle signature submission
// Modify the signature submission handling part
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signature_data'])) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['error' => 'Invalid CSRF token']);
        exit;
    }

    $signatureData = $_POST['signature_data'];

    // Validate signature data
    if (!preg_match('/^data:image\/(png|jpeg);base64,/', $signatureData)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Invalid signature format']);
        exit;
    }

    try {
        // Check for existing signature
        $existingSignature = DB::queryFirstRow(
            "SELECT applicant_id FROM application_signatures WHERE user_id = %i",
            $user_id
        );

        if ($existingSignature) {
            // Update existing record
            DB::update('application_signatures', [
                'signature' => $signatureData,
            ], "user_id = %i", $user_id);
        } else {
            // Insert new record
            DB::insert('application_signatures', [
                'user_id' => $user_id,
                'signature' => $signatureData,
            ]);
        }

        // For AJAX requests, return JSON response
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        } else {
            $success = 'Signature ' . ($existingSignature ? 'updated' : 'saved') . ' successfully!';
        }
    } catch (Exception $e) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        } else {
            throw $e;
        }
    }
}
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid CSRF token');
        }





        // Process profile picture upload
        if (!empty($_FILES['profilePicInput']['name'])) {
            // Use absolute path for upload directory
            $uploadDir = __DIR__ . '/../../../uploads/profileImages/';

            if ($_FILES['profilePicInput']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Upload error: ' . $_FILES['profilePicInput']['error']);
            }

            // Create directory if missing
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Validate file
              $allowedTypes = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/heic' => 'heic',
                    'image/heif' => 'heif'
                ];

            $detectedType = mime_content_type($_FILES['profilePicInput']['tmp_name']);

            if (!array_key_exists($detectedType, $allowedTypes)) {
                throw new Exception('Only JPG and PNG files are allowed.');
            }

            if ($_FILES['profilePicInput']['size'] > 20 * 1024 * 1024) {
                throw new Exception('File size must be less than 20MB');
            }

            // Delete old picture if exists
            $user = DB::queryFirstRow("SELECT picture FROM users WHERE user_id = %i", $user_id);
            if (!empty($user['picture']) && file_exists(__DIR__ . '/../../../' . $user['picture'])) {
                unlink(__DIR__ . '/../../../' . $user['picture']);
            }

            // Generate unique filename
            $extension = $allowedTypes[$detectedType];
            $filename = "user_{$user_id}_" . time() . ".$extension";
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['profilePicInput']['tmp_name'], $targetPath)) {
                $relativePath = "uploads/profileImages/$filename";
                DB::update('users', [
                    'picture' => $relativePath
                ], "user_id = %i", $user_id);
                // Output a JavaScript redirect
                echo '<script>window.location.href = "./index.php";</script>';
                exit();
            } else {
                throw new Exception('Error uploading file');
            }
        }


        $success = 'Profile updated successfully!';
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$currentUserId = $_SESSION['user_id'];

// Query to fetch the tasks
$assignedTasksCount = DB::queryFirstField("SELECT COUNT(*) FROM user_jobs WHERE user_id = %i", $currentUserId);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tool Manager Dashboard</title>

    <style>
        :root {
            --primary-color: #fe5500;
            --secondary-color: #6c757d;
            --light-bg: #f8f9fa;
            --border-radius: 8px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        /* Keep all original CSS styles from admins_dashboard.php */
        .page-breadcrumb {
            margin-bottom: 1rem;
        }

        .breadcrumb-title {
            font-weight: 600;
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
            position: relative;
            left: 8px;
            transform: translate(-25%);
            background-color: #04A96C;
            color: white;
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
            background-color: #04A96C;
            color: white;
            transform: translate(-25%) scale(1.1);
        }

        .card {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
            height: 150px;
        }

        .card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .btn-orange {
            background-color: #FE5505;
            color: #fff;
        }

        /* Add to your CSS */
        .upload-button.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .bg-light-green {
            background-color: #e6ffed;
        }

        .upload-button.loading i {
            animation: spin 1s linear infinite;
        }

        .upload-button.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .upload-button.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .profile-pic-container {
            margin-top: 10px;
            width: 70px;
            height: 70px;
            margin-bottom: 0;
        }

        .bg-orange-50 {
            background-color: #FFEFE6;
        }

        .material-icons-outlined {
            font-size: 24px;
        }

        .card-icon-sm {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-icon-xs {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bullet {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        .bg-orange {
            background-color: #FE5505;
        }

        .bg-warning {
            background-color: #FFC107;
        }

        .bg-danger {
            background-color: #DC3545;
        }

        .bg-success {
            background-color: #28A745;
        }
    </style>
</head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<body>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center py-2">
            <div class="breadcrumb-title pe-3 small"><?php echo lang("dashboard_title"); ?></div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0 small">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active small" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Top Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-3">
            <!-- Projects Card -->
            <div class="col">
                <div class="card rounded-3 h-80">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h6 class="mb-0 small"><?php echo lang("add_tool_card"); ?></h6>
                        </div>


                        <div class="mb-3" style="height: 1.7rem;"></div>

                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <a href="index.php?route=modules/tools/add_tool"
                                    class="btn btn-success rounded-4 px-3 btn-sm small">Add Tool</a>
                            </div>
                            <div class="bg-orange-50 rounded-2 card-icon-sm">
                                <span class="material-icons-outlined text-orange">assignment</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Signature Form -->

            <div class="col">
                <div class="card rounded-3 h-80">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h6 class="mb-0 small">Digital <span class="fw-500">Signature</span></h6>
                        </div>
                        <h6 style="font-size: 0.7rem; color: red;">I authorize my signature to be used on CRAFT
                            forms.</h6>
                        <form id="signatureForm" method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="signature_data" id="signatureInput">
                            <div class="d-flex align-items-center justify-content-between">
                                <button type="button" class="btn btn-success rounded-4 px-3 btn-sm"
                                    onclick="openSignaturePad()">
                                    <?= isset($user['signature']) ? 'Update Signature' : 'Input Signature' ?>
                                </button>
                                <div class="">
                                    <img src="<?= htmlspecialchars($have_signature['signature']) ?>" alt="Signature"
                                        style="background: transparent; max-width: 150px; max-height: 60px; margin-top: 5px; margin-right: -10px;">
                                </div>
                            </div>
                        </form>
                        <!-- Success message container (initially hidden) -->
                        <div id="successMessage" class="text-success small" style="display:none; margin-top: -12px;">
                            Signature Updated!
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signature Pad Modal -->
            <div id="signatureModal"
                style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:white; padding:20px; z-index:1000; box-shadow:0 0 20px rgba(0,0,0,0.2); border-radius:10px;">
                <div style="text-align:right;">
                    <button onclick="closeSignaturePad()" class="btn btn-danger btn-sm mb-2">&times;</button>
                </div>
                <canvas id="signaturePad" width="350" height="200" style="border:1px solid #ddd;"></canvas>
                <div class="mt-2">
                    <button onclick="clearSignature()" class="btn btn-warning btn-sm">Clear</button>
                    <button onclick="saveAndSubmitSignature()" class="btn btn-success btn-sm">Save</button>
                </div>
            </div>

            <!-- Profile Picture Upload Form -->
            <div class="col">
                <div class="card rounded-3 h-80">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h6 class="mb-0 small">Upload <span class="fw-500">Profile Picture</span></h6>
                        </div>
                        <form id="profilePicForm" method="POST" enctype="multipart/form-data" action="">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                            <div class="d-flex align-items-center justify-content-between">
                                <div class="profile-pic-container">
                                    <img id="profileImage"
                                        src="<?= !empty($user['picture']) ? htmlspecialchars($user['picture']) : 'https://placehold.co/120x120.png?text=User' ?>"
                                        alt="Profile Picture" class="profile-pic"
                                        onerror="this.src='https://placehold.co/120x120.png?text=User'">
                                    <input type="file" id="profilePicInput" name="profilePicInput" accept="image/*"
                                        capture="user" style="display: none;">
                                </div>
                                <div class="bg-light-green rounded-2 card-icon-sm">
                                    <label for="profilePicInput" class="upload-button">
                                        <i class="fas fa-camera"></i>
                                    </label>
                                </div>
                            </div>

                            <!-- Loading indicator (hidden by default) -->
                            <div id="uploadLoading" class="text-center mt-2" style="display: none;">
                                <div class="spinner-border text-primary spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="small ms-2">Uploading...</span>
                            </div>

                            <!-- Success message (hidden by default) -->
                            <div id="uploadSuccess" class="text-center text-success small mt-2" style="display: none;">
                                <i class="fas fa-check-circle"></i> Picture uploaded successfully
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Team Members Card -->
            <div class="col">
                <div class="card rounded-3 h-80">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h6 class="mb-0 small">Change <span class="fw-500">Password</span></h6>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="mb-3" style="height: 1.7rem;"></div>
                                <a href="index.php?route=modules/profile/profile"
                                    class="btn btn-orange rounded-4 px-3 btn-sm small">Change Here</a>
                            </div>
                            <div class="bg-orange-50 rounded-2 card-icon-sm">
                                <span class="material-icons-outlined text-orange">lock</span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks Assigned Card -->
            <div class="col">
                <div class="card rounded-3 h-80">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h6 class="mb-0 small">Assigned <span class="fw-500">Tasks</span></h6>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 id="tasks-count" class="mb-2 text-indigo"><?php echo $assignedTasksCount; ?></h4>
                                <a href="" class="btn btn-orange rounded-4 px-3 btn-sm small">
                                    Manage Tasks
                                </a>
                            </div>
                            <div class="bg-orange-50 rounded-2 card-icon-sm">
                                <span class="material-icons-outlined text-orange">assignment</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Signature Pad Modal -->
            <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
            <script>
                let signaturePad = null;

                function openSignaturePad() {
                    document.getElementById('signatureModal').style.display = 'block';
                    if (!signaturePad) {
                        const canvas = document.getElementById('signaturePad');
                        signaturePad = new SignaturePad(canvas, {
                            backgroundColor: 'rgb(255, 255, 255)',
                            penColor: '#FE5505',
                            minWidth: 1,
                            maxWidth: 3
                        });

                        // If there's an existing signature, load it
                        const existingSig = document.getElementById('signatureInput').value;
                        if (existingSig) {
                            signaturePad.fromDataURL(existingSig);
                        }
                    }
                }

                function closeSignaturePad() {
                    document.getElementById('signatureModal').style.display = 'none';
                }

                function clearSignature() {
                    signaturePad.clear();
                }

                // Modify the saveAndSubmitSignature function to use AJAX
                function saveAndSubmitSignature() {
                    if (!signaturePad.isEmpty()) {
                        const signatureData = signaturePad.toDataURL('image/png');

                        // Show loading state
                        const saveBtn = document.querySelector('#signatureModal button.btn-success');
                        const originalBtnText = saveBtn.innerHTML;
                        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving';
                        saveBtn.disabled = true;

                        // Prepare form data
                        const formData = new FormData();
                        formData.append('signature_data', signatureData);
                        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

                        // Send via AJAX
                        fetch(window.location.href, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.text();
                            })
                            .then(() => {
                                // Update the displayed signature
                                const signatureImg = document.querySelector('#signatureForm img');
                                if (signatureImg) {
                                    signatureImg.src = signatureData;
                                }

                                // Show success message
                                const successMsg = document.getElementById('successMessage');
                                successMsg.style.display = 'block';

                                // Hide message after 5 seconds
                                setTimeout(() => {
                                    successMsg.style.display = 'none';
                                }, 5000);

                                // Close modal
                                closeSignaturePad();

                                // Reload the page to ensure everything is in sync
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Error saving signature. Please try again.');
                            })
                            .finally(() => {
                                // Restore button state
                                saveBtn.innerHTML = originalBtnText;
                                saveBtn.disabled = false;
                            });
                    } else {
                        alert('Please provide a signature first.');
                    }
                }
            </script>
            <!-- Upcoming Meetings Card -->
            <div class="col">
                <div class="card rounded-3 h-80">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h6 class="mb-0 small">Upcoming <span class="fw-500">Meetings</span></h6>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-2 text-indigo">5</h4>
                                <a href="#" class="btn btn-orange rounded-4 px-3 btn-sm small">View Schedule</a>
                            </div>
                            <div class="bg-orange-50 rounded-2 card-icon-sm">
                                <span class="material-icons-outlined text-orange">event</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('mini-calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: false,
                    fixedWeekCount: false,
                    height: 'auto'
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
                    saveBtn.innerHTML =
                        '<i class="fas fa-spinner fa-spin me-2"></i><?php echo lang("profile_saving"); ?>';
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
        <!-- Add this library for HEIC conversion -->
        <script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.3/dist/heic2any.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const profilePicInput = document.getElementById('profilePicInput');
                const profilePicForm = document.getElementById('profilePicForm');
                const uploadLoading = document.getElementById('uploadLoading');
                const uploadSuccess = document.getElementById('uploadSuccess');
                const profileImage = document.getElementById('profileImage');
                const uploadButton = document.querySelector('.upload-button');

                // Supported image types including HEIC
                const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/heic', 'image/heif'];
                const maxFileSize = 20 * 1024 * 1024; // 20MB

                profilePicInput.addEventListener('change', async function(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    // Client-side validation
                    if (!validTypes.includes(file.type.toLowerCase())) {
                        alert('Only JPG, PNG, WEBP, and HEIC files are allowed.');
                        return;
                    }

                    if (file.size > maxFileSize) {
                        alert('File size must be less than 20MB');
                        return;
                    }

                    // Show loading state
                    uploadLoading.style.display = 'block';
                    uploadSuccess.style.display = 'none';
                    uploadButton.classList.add('loading');
                    uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    try {
                        let processedFile = file;

                        // Check if file is HEIC/HEIF and needs conversion
                        if (file.type.toLowerCase() === 'image/heic' || file.type.toLowerCase() === 'image/heif' ||
                            file.name.toLowerCase().endsWith('.heic') || file.name.toLowerCase().endsWith('.heif')) {

                            // Convert HEIC to JPG
                            const conversionResult = await heic2any({
                                blob: file,
                                toType: 'image/jpeg',
                                quality: 0.8 // Adjust quality as needed
                            });

                            // Create new file object from the conversion result
                            processedFile = new File([conversionResult],
                                file.name.replace(/\.[^/.]+$/, '.jpg'), {
                                    type: 'image/jpeg',
                                    lastModified: new Date().getTime()
                                });
                        }

                        // Show preview (works for both original and converted files)
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            profileImage.src = event.target.result;
                        };
                        reader.readAsDataURL(processedFile);

                        // Prepare FormData for upload
                        const formData = new FormData();
                        formData.append('profilePicInput', processedFile);
                        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

                        // Upload the file (original or converted)
                        const response = await fetch(window.location.href, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Upload failed with status ' + response.status);
                        }

                        // Show success message
                        uploadLoading.style.display = 'none';
                        uploadSuccess.style.display = 'block';
                        uploadButton.classList.remove('loading');
                        uploadButton.innerHTML = '<i class="fas fa-camera"></i>';

                        // Hide success message after 3 seconds
                        setTimeout(() => {
                            uploadSuccess.style.display = 'none';
                        }, 3000);

                        // Refresh the page to show the new image
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);

                    } catch (error) {
                        console.error('Error:', error);
                        uploadLoading.style.display = 'none';
                        uploadButton.classList.remove('loading');
                        uploadButton.innerHTML = '<i class="fas fa-camera"></i>';
                        alert('Error uploading picture. Please try again.');
                        profileImage.src = profileImage.dataset.fallback ||
                            'https://placehold.co/120x120.png?text=User';
                    }
                });

                // Handle potential errors with the image loading
                profileImage.onerror = function() {
                    this.src = this.dataset.fallback || 'https://placehold.co/120x120.png?text=User';
                };
            });
        </script>

</body>

</html>