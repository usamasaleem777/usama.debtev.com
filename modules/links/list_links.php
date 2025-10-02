<?php
/************** START SECURITY CHECK ***********************/
// Define allowed roles - make sure these variables are defined somewhere in your application
$allowedRoles = array(
					$admin_role 
					); // You need to define these variables

// Check if role is allowed
if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], $allowedRoles)) {
    // User does not have access, redirect to home
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => "You do not have permission to view this page."
    ];
    echo '<script>window.location.href = "index.php";</script>';
    die();
}
/**************** END SECURITY CHECK ***********************/
 
if (isset($_GET['delete_id'])) {
    $link_id = intval($_GET['delete_id']);

    try {
        // Update status in applicants table

        // Delete user
        DB::delete('applicant_links', "id=%i", $link_id);

        $_SESSION['message'] = [
            'type' => 'success',
            'text' => "User deleted successfully."
        ];
        echo '<script>window.location.href = "index.php?route=modules/links/list_links";</script>';
    } catch (MeekroDBException $e) {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => "Delete failed: " . $e->getMessage()
        ];
        echo '<script>window.location.href = "index.php?route=modules/links/list_links";</script>';
    }
}


$links = DB::query("
    SELECT applicant_links.*, 
           CONCAT(applicants.first_name, ' ', applicants.last_name) AS Name 
    FROM applicant_links 
    LEFT JOIN applicants ON applicant_links.applicant_id = applicants.id
        ORDER BY applicant_links.id DESC  

");
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Font Awesome CSS for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="main-content app-content mt-0">
    <!-- Page header with breadcrumb navigation -->
    <div class="page-header d-flex align-items-center justify-content-end mt-2 mb-2">
        <div style="margin-top: 25px;">
            <ol class="breadcrumb float-sm-right mt-2">
                <!-- Home breadcrumb -->
                <li class="breadcrumb-item">
                    <a href="index.php" style="color: #fe5500"><i
                            class="fas fa-home me-1"></i><?php echo lang("user_home"); ?></a>
                </li>
                <!-- Position breadcrumb -->
                <li class="breadcrumb-item">
                    <a href="#" style="color: #fe5500"><?php echo lang(key: "links_links"); ?></a>
                </li>
                <!-- View position breadcrumb -->
                <li class="breadcrumb-item">
                    <a href="#" style="color: #fe5500"><?php echo lang("links_view_links"); ?></a>
                </li>
            </ol>
        </div>
    </div>

    <?php if (isset($message)): ?>
        <div class="alert alert-<?= $message['type'] ?> alert-dismissible fade show">
            <?= $message['text'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Users Table Section -->
    <div class="row1">
        <div class="row">
            <div class="col-12 mt-4">
                <div class="card rounded-4">
                    <div class="card-body m-2">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="card-title fw-bold m-0" style="font-size: 1.2rem;">
                                <?php echo lang("link_list"); ?>
                            </h3>

                        </div>

                        <!-- Role Filter  -->
                        <!-- <div class="col-md-3 mb-3 px-0">
                        <label style="font-size: 0.9rem;"><?php echo lang("user_filter_role"); ?></label>
                        <select id="roleFilter" class="form-select square-filter">
                            <option value=""><?php echo lang("user_all_roles"); ?></option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= htmlspecialchars($role['name']) ?>">
                                    <?= htmlspecialchars($role['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div> -->

                        <!-- Desktop Table (hidden on mobile) -->
                        <div class="d-none d-md-block">
                            <div class="table-responsive">
                                <table class="table align-middle table-hover" id="users-datatable">
                                    <thead class="table-light">
                                        <tr>
                                            <th><?php echo lang("link_id"); ?></th>
                                            <th><?php echo lang("link_applicant"); ?></th>
                                            <th><?php echo lang("link_form"); ?></th>
                                            <th><?php echo lang("link_token"); ?></th>
                                            <th><?php echo lang("link_expires_at"); ?></th>
                                            <th><?php echo lang("link_generated_date"); ?></th>
                                            <th style="text-align: center;"><?php echo lang("user_Action"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($links as $link): ?>
                                            <tr>
                                                <td><?= $link['id'] ?></td>
                                                <td><?= $link['Name'] ?></td>
                                                <td><?= $link['form_link'] ?></td>
                                                <td>
                                                    <?php if (isset($link['token']) && $link['token'] !== ''): ?>
                                                        <?= $link['token'] ?>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($link['expires_at'])): ?>
                                                        <?php
                                                        $date = new DateTime($link['expires_at']);
                                                        echo $date->format('D, M j, Y'); // Example: Mon, Apr 27, 2025
                                                        ?>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <?php if (!empty($link['generated_date'])): ?>
                                                        <?php
                                                        $date = new DateTime($link['generated_date']);
                                                        echo $date->format('D, M j, Y'); // Example: Mon, Apr 27, 2025
                                                        ?>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <div class="d-flex gap-1 flex-wrap justify-content-center">
                                                        <!-- <a href="?route=modules/users/view&id=<?= $link['id'] ?>"
                                                        class="btn btn-sm rounded-3 px-2 action-btn"
                                                        style="background: #FE5505; color: white; font-size: 0.75rem;"
                                                        title="<?php echo lang("user_view"); ?>">
                                                        <i class="fas fa-eye"></i>
                                                        <span class="action-text"><?php echo lang("user_view"); ?></span>
                                                    </a> -->
                                                        <!-- <a href="?route=modules/users/edituser&id=<?= $link['id'] ?>"
                                                        class="btn btn-sm rounded-3 px-2 action-btn"
                                                        style="background: #FE5505; color: white; font-size: 0.75rem;"
                                                        title="<?php echo lang("user_edit"); ?>">
                                                        <i class="fas fa-edit"></i>
                                                        <span class="action-text"><?php echo lang("user_edit"); ?></span>
                                                    </a> -->
                                                        <a href="javascript:void(0);"
                                                            class="btn btn-sm rounded-3 px-2 copy-btn action-btn"
                                                            style="background-color: #FE5505; color: #fff; font-size: 0.75rem;"
                                                            data-clipboard-text="<?= $link['form_link'] ?>"
                                                            title="<?php echo lang("link_copy"); ?>">
                                                            <i class="fas fa-copy"></i>
                                                            <span
                                                                class="action-text"><?php echo lang("link_copy"); ?></span>
                                                        </a>
                                                        <a href="?route=modules/links/list_links&delete_id=<?= $link['id'] ?>"
                                                            class="btn btn-sm btn-danger rounded-3 delete-btn px-2 action-btn"
                                                            style="font-size: 0.75rem;"
                                                            title="<?php echo lang("user_delete"); ?>">
                                                            <i class="fas fa-trash"></i>
                                                            <span
                                                                class="action-text"><?php echo lang("user_delete"); ?></span>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Cards (hidden on desktop) -->
                        <div class="d-block d-md-none">
                            <div id="users-cards-container">
                                <div class="row" id="users-cards-row">
                                    <?php foreach ($links as $link): ?>
                                        <div class="col-12 mb-3 user-card">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h5 class="card-title mb-0"><?= $link['id'] ?></h5>
                                                        <span class="badge bg-orange">
                                                            <?php if (isset($link['role_name']) && $link['role_name'] !== ''): ?>
                                                                <?= htmlspecialchars($link['role_name']) ?>
                                                            <?php else: ?>
                                                                N/A
                                                            <?php endif; ?>
                                                        </span>
                                                    </div>
                                                    <div class="user-details">
                                                        <div class="detail-item">
                                                            <span
                                                                class="detail-label"><?php echo lang("user_user_id"); ?>:</span>
                                                            <span class="detail-value"><?= $link['id'] ?></span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span
                                                                class="detail-label"><?php echo lang("list_applicant"); ?>:</span>
                                                            <span
                                                                class="detail-value"><?= htmlspecialchars($link['Name']) ?></span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span
                                                                class="detail-label"><?php echo lang("link_form"); ?>:</span>
                                                            <span
                                                                class="detail-value"><?= isset($link['form_link']) && $link['form_link'] !== '' ? htmlspecialchars($link['form_link']) : 'N/A' ?></span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span
                                                                class="detail-label"><?php echo lang("link_token"); ?>:</span>
                                                            <span
                                                                class="detail-value"><?= isset($link['token']) && $link['token'] !== '' ? htmlspecialchars($link['token']) : 'N/A' ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex gap-1 flex-wrap justify-content-center mt-3">
                                                        <!-- <a href="?route=modules/users/view&id=<?= $user['user_id'] ?>"
                                                        class="btn btn-sm rounded-3 px-2"
                                                        style="background: #FE5505; color: white; font-size: 0.75rem;">
                                                        <i class="fas fa-eye me-1"></i> <?php echo lang("user_view"); ?>
                                                    </a> -->
                                                        <!-- <a href="?route=modules/users/edituser&id=<?= $user['user_id'] ?>"
                                                        class="btn btn-sm rounded-3 px-2"
                                                        style="background: #FE5505; color: white; font-size: 0.75rem;">
                                                        <i class="fas fa-edit me-1"></i> <?php echo lang("user_edit"); ?>
                                                    </a> -->
                                                        <a href="javascript:void(0);"
                                                            class="btn btn-sm rounded-3 px-2 copy-btn"
                                                            style="background: #FE5505; color: #fff !important; font-size: 0.75rem;"
                                                            data-clipboard-text="<?= $link['form_link'] ?>"
                                                            title="<?php echo lang("link_copy"); ?>">
                                                            <i class="fa fa-link me-1"></i>
                                                            <?php echo lang("link_copy"); ?>
                                                        </a>
                                                        <a href="?route=modules/link/list_links&delete_id=<?= $link['id'] ?>"
                                                            class="btn btn-sm btn-danger rounded-3 delete-btn px-2"
                                                            style="font-size: 0.75rem;">
                                                            <i class="fas fa-trash me-1"></i>
                                                            <?php echo lang("user_delete"); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                <nav aria-label="User cards pagination">
                                    <ul class="pagination" id="cards-pagination">
                                        <!-- Pagination will be added by JavaScript -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .row {
        margin-top: 10px;
    }

    .btn-orange {
        background-color: #FE5500;
        border-color: #FE5500;
        color: white;
    }



    .bg-orange {
        background-color: #FE5500 !important;
    }

    #users-datatable_filter input {
        border-radius: 20px !important;
        border: 1px solid #FE5505 !important;
        padding: 5px 15px !important;
        margin-bottom: 15px;
    }

    #users-datatable th {
        background: #FE5505 !important;
        color: white !important;
        font-size: 0.85rem;
        padding: 8px 5px;
    }

    #users-datatable td {
        vertical-align: middle;
        padding: 8px 5px;
        font-size: 0.85rem;
    }

    .page-item.active .page-link {
        background: #FE5505 !important;
        border-color: #FE5505 !important;
    }

    /* Search Filter Styles */
    .square-filter {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.9rem;
    }

    .square-filter:focus {
        border-color: #adb5bd;
        box-shadow: 0 0 0 3px rgba(108, 117, 125, 0.15);
    }

    /* Mobile Card Styles */
    .user-card .card {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
    }

    .user-card .card-title {
        font-size: 1rem;
        font-weight: 600;
        color: #333;
    }

    .detail-item {
        display: flex;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
    }

    .detail-label {
        font-weight: 600;
        color: #555;
        min-width: 100px;
    }

    .detail-value {
        color: #333;
        flex-grow: 1;
    }

    /* Action button styles for desktop */
    @media (min-width: 768px) {
        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
        }

        .action-text {
            display: none;
        }



    }

    /* Extra small devices (phones, 360px and down) */
    @media screen and (max-width: 360px) {
        .page-header {
            margin-top: 10px !important;
            margin-bottom: 10px !important;
        }

        .row1 {
            margin-left: -30px;
            margin-right: -30px;
            margin-top: -25px;
        }

        .breadcrumb {
            padding: 0.5rem 0.75rem;
            margin-right: -30px !important;
            font-size: 0.75rem;
        }

        .breadcrumb-item i {
            font-size: 0.7rem;
            margin-right: 0.25rem !important;
        }

        .card-title {
            font-size: 1rem !important;
        }

        .btn-orange.btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .user-card {
            padding: 0 5px;
        }

        .user-card .card-body {
            padding: 1rem;
        }

        .detail-item {
            flex-direction: column;
        }

        .detail-label {
            min-width: auto;
            margin-bottom: 0.1rem;
        }

        #users-cards-container {
            margin: 0 -5px;
        }

        .alert {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }
    }

    @media screen and (max-width: 430px) {
        .page-header {
            margin-top: 10px !important;
            margin-bottom: 10px !important;
        }

        .row1 {
            margin-left: -30px;
            margin-right: -30px;
            margin-top: -25px;
        }

        .breadcrumb {
            padding: 0.5rem 0.75rem;
            margin-right: -30px !important;
            font-size: 0.75rem;
        }

        .breadcrumb-item i {
            font-size: 0.7rem;
            margin-right: 0.25rem !important;
        }

        .card-title {
            font-size: 1rem !important;
        }

        .btn-orange.btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .user-card {
            padding: 0 5px;
        }

        .user-card .card-body {
            padding: 1rem;
        }

        .detail-item {
            flex-direction: column;
        }

        .detail-label {
            min-width: auto;
            margin-bottom: 0.1rem;
        }

        #users-cards-container {
            margin: 0 -5px;
        }

        .alert {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }
    }

    /* Small devices (phones, 576px and down) */
    @media screen and (max-width: 576px) {
        .card-body {
            padding: 1rem !important;
        }

        .card-title {
            font-size: 1.1rem !important;
        }

        .user-card .card-body {
            padding: 1.25rem;
        }

        .detail-item {
            font-size: 0.9rem;
        }
    }

    /* Medium devices (tablets, 768px and up) */
    @media screen and (min-width: 768px) and (max-width: 991px) {

        /* Adjust filter width */
        #roleFilter {
            max-width: 200px;
        }
    }
</style>

<!-- Add SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        // Initialize DataTable for desktop view
        const table = $('#users-datatable').DataTable({
            searching: true,
            paging: true,
            ordering: true,
            order: [[0, 'desc']],  // Add this line for initial sorting
            info: false,
            caseInsensitive: true,
            dom: 'rtip',
            lengthMenu: [],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search users...",
            },
            columnDefs: [
                {
                    orderable: false,
                    targets: [6],
                    searchable: false
                }
            ],
            initComplete: function () {
                $('#roleFilter').on('change', function () {
                    const selected = $(this).val();
                    const roleColIndex = table.column(':contains("<?php echo lang("user_role"); ?>")').index();
                    if (roleColIndex !== undefined) {
                        table.column(roleColIndex).search('^' + selected + '$', true, false).draw();
                    }
                });
            }
        });

        // Initialize card pagination for mobile view
        function initCardPagination() {
            const cardsPerPage = 3;
            const $cards = $('.user-card');
            const totalCards = $cards.length;
            const totalPages = Math.ceil(totalCards / cardsPerPage);
            let currentPage = 1;

            // Hide all cards initially
            $cards.hide();

            // Show first page
            $cards.slice(0, cardsPerPage).show();

            // Create pagination buttons
            const $pagination = $('#cards-pagination');
            $pagination.empty();

            // Previous button
            $pagination.append('<li class="page-item"><a class="page-link" href="#" aria-label="Previous" id="prev-page"><span aria-hidden="true">&laquo;</span></a></li>');

            // Current page button (initially 1)
            $pagination.append(`<li class="page-item active"><a class="page-link" href="#">${currentPage}</a></li>`);

            // Next button
            $pagination.append('<li class="page-item"><a class="page-link" href="#" aria-label="Next" id="next-page"><span aria-hidden="true">&raquo;</span></a></li>');

            // Handle previous page click
            $('#prev-page').on('click', function (e) {
                e.preventDefault();
                if (currentPage > 1) {
                    currentPage--;
                    updatePagination();
                }
            });

            // Handle next page click
            $('#next-page').on('click', function (e) {
                e.preventDefault();
                if (currentPage < totalPages) {
                    currentPage++;
                    updatePagination();
                }
            });

            function updatePagination() {
                // Update the displayed page number
                $pagination.find('li.page-item:not(:first-child):not(:last-child)').remove();
                $pagination.find('li.page-item:first-child').after(`<li class="page-item active"><a class="page-link" href="#">${currentPage}</a></li>`);

                // Show the current page's cards
                const startIndex = (currentPage - 1) * cardsPerPage;
                const endIndex = startIndex + cardsPerPage;

                $cards.hide();
                $cards.slice(startIndex, endIndex).show();

                // Disable previous/next buttons when at first/last page
                $('#prev-page').parent().toggleClass('disabled', currentPage === 1);
                $('#next-page').parent().toggleClass('disabled', currentPage === totalPages);
            }
        }

        // Initialize card pagination if on mobile view
        if ($(window).width() < 768) {
            initCardPagination();
        }

        // Reinitialize on window resize
        $(window).resize(function () {
            if ($(window).width() < 768 && $('#cards-pagination').children().length === 0) {
                initCardPagination();
            }
        });

        // SweetAlert2 Delete Confirmation
        $(document).on('click', '.delete-btn', function (e) {
            e.preventDefault();
            const deleteUrl = $(this).attr('href');

            Swal.fire({
                title: '<?php echo lang("user_delete_confirmation_title"); ?>',
                text: '<?php echo lang("user_delete_confirmation_text"); ?>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FE5500',
                cancelButtonColor: '#d33',
                confirmButtonText: '<?php echo lang("user_delete_confirm_button"); ?>',
                cancelButtonText: '<?php echo lang("user_delete_cancel_button"); ?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = deleteUrl;
                }
            });
        });
    });
    // Copy to Clipboard Functionality
    $(document).on('click', '.copy-btn', function () {
        const textToCopy = $(this).data('clipboard-text');

        navigator.clipboard.writeText(textToCopy).then(() => {
            Swal.fire({
                icon: 'success',
                title: '<?php echo lang("link_copied"); ?>',
                text: '<?php echo lang("link_copied_success"); ?>',
                timer: 2000,
                showConfirmButton: false
            });
        }).catch((err) => {
            Swal.fire({
                icon: 'error',
                title: '<?php echo lang("link_copy_error"); ?>',
                text: '<?php echo lang("link_copy_error_msg"); ?>'
            });
        });
    });
</script>