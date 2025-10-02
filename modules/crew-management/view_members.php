<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['crew_id']) || !is_numeric($_GET['crew_id'])) {
    $_SESSION['error'] = "Invalid crew selection";
    echo "<script>window.location.href = 'index.php?route=modules/crew-management';</script>";
    exit();
}

$crew_id = (int)$_GET['crew_id'];

try {
    $crew = DB::queryFirstRow("SELECT * FROM crew WHERE crew_id = %i", $crew_id);
    if (!$crew) {
        throw new Exception("Crew not found in database");
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    echo "<script>window.location.href = 'index.php?route=modules/crew-management';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] === 'add_members' && isset($_POST['user_ids'])) {
            $added_count = 0;
            foreach ($_POST['user_ids'] as $user_id) {
                $user_id = (int)$user_id;

                // Get current crew_ids for user
                $current_crew_ids = DB::queryFirstField(
                    "SELECT crew_id FROM users WHERE user_id = %i",
                    $user_id
                );

                // Convert to array
                $crew_ids = $current_crew_ids ? explode(',', $current_crew_ids) : [];

                // Add new crew_id if not already present
                if (!in_array($crew_id, $crew_ids)) {
                    $crew_ids[] = $crew_id;
                    $new_crew_ids = implode(',', $crew_ids);

                    DB::update('users', [
                        'crew_id' => $new_crew_ids
                    ], "user_id = %i", $user_id);

                    $added_count++;
                }
            }

            if ($added_count > 0) {
                $_SESSION['success'] = "Added $added_count members to crew";
            } else {
                $_SESSION['info'] = "No new members were added (users may already be in this crew)";
            }
        } elseif ($_POST['action'] === 'remove_member' && isset($_POST['user_id'])) {
            $user_id = (int)$_POST['user_id'];

            // Get current crew_ids for user
            $current_crew_ids = DB::queryFirstField(
                "SELECT crew_id FROM users WHERE user_id = %i",
                $user_id
            );

            if ($current_crew_ids) {
                $crew_ids = explode(',', $current_crew_ids);
                $updated_crew_ids = array_diff($crew_ids, [$crew_id]);

                DB::update('users', [
                    'crew_id' => implode(',', $updated_crew_ids)
                ], "user_id = %i", $user_id);
            }

            $_SESSION['success'] = "Member removed successfully";
        }

        echo "<script>window.location.href = 'index.php?route=modules/crew-management/view_members&crew_id=" . $crew_id . "';</script>";
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        echo "<script>window.location.href = 'index.php?route=modules/crew-management/view_members&crew_id=" . $crew_id . "';</script>";
        exit();
    }
}

// Fetch current crew members (using FIND_IN_SET for comma-separated values)
$members = DB::query(
    "SELECT u.user_id, u.first_name, u.last_name, u.email, 
            u.phone, u.picture
     FROM users u
     WHERE FIND_IN_SET(%i, u.crew_id)
     ORDER BY u.last_name, u.first_name",
    $crew_id
);

// Fetch available users not in this crew
$available_users = DB::query(
    "SELECT u.user_id, u.user_name, u.first_name, u.last_name, u.email, u.phone, u.picture, u.crew_id,
            (SELECT GROUP_CONCAT(c.crew_name) 
             FROM crew c 
             WHERE FIND_IN_SET(c.crew_id, u.crew_id)) as other_crews
     FROM users u
     WHERE u.role_id = 5
     AND (u.crew_id IS NULL OR NOT FIND_IN_SET(%i, u.crew_id))
     ORDER BY u.last_name, u.first_name",
    $crew_id
);

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
$info = $_SESSION['info'] ?? '';
unset($_SESSION['success'], $_SESSION['error'], $_SESSION['info']);

include 'includes/page-parts/header.php';
?>

<div class="main-content app-content mt-0 h-100">
    <div class="side-app h-100">
        <div class="main-container container-fluid h-100 p-0">
            <!-- Page Header -->
            <div class="page-header mb-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="page-title" style="color: #fe5500;">
                            <i class="fas fa-users me-2"></i>
                            <?= htmlspecialchars($crew['crew_name']) ?> <?php echo lang("members"); ?>
                        </h4>
                    </div>
                    <div class="col-auto">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="index.php"><i class="fas fa-home"></i> <?php echo lang("home"); ?></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="index.php?route=modules/crew-management"><?php echo lang("crew_management"); ?></a>
                            </li>
                            <li class="breadcrumb-item active"><?php echo lang("manage_members"); ?></li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Status Messages -->
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo lang("close"); ?>"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo lang("close"); ?>"></button>
                </div>
            <?php endif; ?>

            <?php if ($info): ?>
                <div class="alert alert-info alert-dismissible fade show">
                    <?= htmlspecialchars($info) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo lang("close"); ?>"></button>
                </div>
            <?php endif; ?>

            <!-- Main Content Card -->
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user-friends me-2"></i>
                                <?php echo lang("crew_members"); ?>
                                <span class="badge" style="background-color: #fe5500;"><?= count($members) ?></span>
                            </h5>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-sm" style="background-color: #fe5500; color: white;" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                                <i class="fas fa-user-plus me-1"></i> <?php echo lang("add_members"); ?>
                            </button>
                            <a href="index.php?route=modules/crew-management/manage_crew" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> <?php echo lang("back"); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <?php if (empty($members)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <?php echo lang("no_crew_members"); ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th><?php echo lang("member"); ?></th>
                                        <th><?php echo lang("contact"); ?></th>
                                        <th><?php echo lang("actions"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($members as $member): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($member['picture'])): ?>
                                                        <img src="<?= htmlspecialchars($member['picture']) ?>"
                                                            class="rounded-circle me-2" width="36" height="36"
                                                            alt="<?= htmlspecialchars($member['first_name']) ?>">
                                                    <?php else: ?>
                                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2"
                                                            style="width:36px;height:36px;">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <strong class="small"><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></strong>
                                                        <div class="text-muted small"><?php echo lang("id"); ?>: <?= $member['user_id'] ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small"><?= htmlspecialchars($member['email']) ?></div>
                                                <div class="text-muted small"><?= htmlspecialchars($member['phone']) ?></div>
                                            </td>
                                            <td>
                                                <form method="POST" class="d-inline remove-member-form" data-user-name="<?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>">
                                                    <input type="hidden" name="action" value="remove_member">
                                                    <input type="hidden" name="user_id" value="<?= $member['user_id'] ?>">
                                                    <button type="button" class="btn btn-sm btn-outline-danger remove-member-btn">
                                                        <i class="fas fa-user-minus"></i> <?php echo lang("remove"); ?>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal - Fixed Layout -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST">
                <div class="modal-header" style="background-color: #fe5500; color: white;">
                    <h5 class="modal-title" id="addMemberModalLabel">
                        <i class="fas fa-user-plus me-2"></i>
                        <?php echo lang("add_members_to"); ?> <?= htmlspecialchars($crew['crew_name']) ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?php echo lang("close"); ?>"></button>
                </div>
                <div class="modal-body p-4">
                    <?php if (empty($available_users)): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo lang("no_available_users"); ?>
                        </div>
                    <?php else: ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-2"><?php echo lang("search_members"); ?>:</label>
                            <div class="select2-container" style="width: 100%;">
                                <select name="user_ids[]" class="form-select select2" id="user_ids" multiple="multiple" required style="width: 100%">
                                    <?php foreach ($available_users as $user): ?>
                                        <option value="<?= $user['user_id'] ?>"
                                            data-other-crews="<?= htmlspecialchars($user['other_crews'] ?? '') ?>">
                                            <?= htmlspecialchars($user['user_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="selected-users-container mt-4">
                            <label class="form-label fw-bold mb-2"><?php echo lang("selected_members"); ?>:</label>
                            <div id="selectedUsersList" class="d-flex flex-wrap gap-2 p-3 bg-light rounded border"></div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="action" value="add_members">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo lang("cancel"); ?></button>
                    <button type="submit" class="btn" style="background-color: #fe5500; color: white;">
                        <i class="fas fa-save me-1"></i> <?php echo lang("add_selected"); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize Select2 with proper container
        $('.select2').select2({
            placeholder: "<?php echo lang('search_by_name'); ?>...",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#addMemberModal'),
            templateResult: function(option) {
                if (!option.id) return option.text;
                return option.text;
            },
            templateSelection: function(option) {
                if (!option.id) return option.text;
                return option.text;
            }
        });

        // Handle selected users display
        $('#user_ids').on('change', function() {
            let userIds = $(this).val();
            $('#selectedUsersList').empty();

            if (userIds) {
                userIds.forEach(function(userId) {
                    let option = $('#user_ids option[value="' + userId + '"]');
                    let userName = option.text();

                    let userBadge = `
                    <div class="selected-user-badge">
                        ${userName}
                        <span class="remove-user" data-user-id="${userId}">×</span>
                    </div>`;
                    $('#selectedUsersList').append(userBadge);
                });
            }
        });

        // Remove user from selected list
        $(document).on('click', '.remove-user', function() {
            let userId = $(this).data('user-id');
            $(this).parent().remove();
            $('#user_ids option[value="' + userId + '"]').prop('selected', false);
            $('#user_ids').trigger('change');
        });

        // Clear selections when modal is closed
        $('#addMemberModal').on('hidden.bs.modal', function() {
            $('#user_ids').val(null).trigger('change');
        });

        // Confirm member removal
        $(document).on('click', '.remove-member-btn', function() {
            const form = $(this).closest('form');
            const userName = form.data('user-name');

            Swal.fire({
                title: "<?php echo lang('remove_member_confirm_title'); ?>",
                html: `<?php echo lang('remove_member_confirm_text'); ?> <strong>${userName}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FE5500',
                confirmButtonText: "<?php echo lang('remove'); ?>",
                cancelButtonText: "<?php echo lang('cancel'); ?>"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>

<?php
ob_end_flush();
?>