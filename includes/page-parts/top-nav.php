<?php


// Fetch last 5 applicants (customize as needed)
$applicants = DB::query("SELECT first_name, last_name, created_at FROM applicants ORDER BY created_at DESC LIMIT 5");
if (isset($_SESSION['user_id'])) {
  try {
    $user = DB::queryFirstRow(
      "SELECT picture, role_id FROM users WHERE user_id = %i",
      $_SESSION['user_id']
    );

    if ($user) {
      $_SESSION['role_id'] = $user['role_id']; // Store role in session
      if (!empty($user['picture'])) {
        $userImage = $user['picture'];
      }
    }
  } catch (Exception $e) {
    error_log("Profile image error: " . $e->getMessage());
  }
}

?>

<style>
  .notification-badge {
  position: absolute;
  top: 5px;
  right: 0;
  background: red;
  color: white;
  border-radius: 50%;
  font-size: 12px;
  padding: 2px 6px;
  display: none; /* default hidden */
}

  .dropdown-notifications .dropdown-item {
    background-color: white !important;
  }

  .dropdown-notifications .dropdown-item.bg-light {
    background-color: #f8f9fa !important; /* Slightly off-white for unread items if you want distinction */
  }
 @media (max-width: 575.98px) {
    .dropdown-notifications {
      width: 280px;
      position: fixed !important;
      left: 50% !important;
      transform: translateX(-50%) !important;
      top: 60px !important;
    }
    .dropdown-notifications .dropdown-item {
      white-space: normal !important;
      padding: 10px 15px !important;
    }

    #notification-list-wrapper {
      max-height: 60vh !important;
      /* Use viewport height units */
    }

    .notification-badge {
      top: 0;
      right: -5px;
    }
  }

</style>
<!--start header-->
<header class="top-header">
  <nav class="navbar navbar-expand align-items-center gap-4">
    <div class="btn-toggle">
      <a href="javascript:;"><i class="material-icons-outlined">menu</i></a>
    </div>


    



    <ul class="navbar-nav gap-1 nav-right-links align-items-center ms-auto">

    <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1): ?>

          <!-- Notifications Dropdown -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="javascript:;" data-bs-toggle="dropdown">
          <i class="material-icons-outlined">notifications</i>
          <span class="notification-badge"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-end dropdown-notifications shadow">
          <div class="dropdown-header">
            <h6 class="mb-0">Notifications</h6>
          </div>
          <div id="notification-list-wrapper" style="max-height: 400px; overflow-y: auto;">

          <div class="dropdown-body" id="notification-list">
  <!-- Notifications will be loaded here via JavaScript -->
</div>
</div>

          
        </div>
      </li>
<?php endif; ?>

      <li class="nav-item dropdown ns-auto">
        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" data-bs-toggle="dropdown">
          <img id="selectedLangImg" src="assets/images/county/02.png" width="22" alt="">
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item d-flex align-items-center py-2 <?= (isset($_SESSION['lang']) && $_SESSION['lang'] == 'en') ? 'active' : ''; ?>"
              href="javascript:;" onclick="setActive(this)" data-lang="en">
              <img src="assets/images/county/02.png" width="20" alt="">
              <span class="ms-2">English</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item d-flex align-items-center py-2 <?= (isset($_SESSION['lang']) && $_SESSION['lang'] == 'es') ? 'active' : ''; ?>"
              href="javascript:;" onclick="setActive(this)" data-lang="es">
              <img src="assets/images/county/09.png" width="20" alt="">
              <span class="ms-2">Español</span>
            </a>
          </li>
        </ul>
      </li>

      <div class="notify-list">
        <div class="card-body search-content">
        </div>
      </div>

      <li class="nav-item dropdown">
        <a href="javascrpt:;" class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
          <?php
          // Start the session at the very beginning
          if (session_status() === PHP_SESSION_NONE) {
            session_start();
          }

          $defaultImage = 'https://placehold.co/110x110/png';
          $userImage = $defaultImage;

          if (isset($_SESSION['user_id'])) {
            try {
              // Get user data from database using MeekroDB
              $user = DB::queryFirstRow(
                "SELECT picture FROM users WHERE user_id = %i",
                $_SESSION['user_id']
              );

              if ($user && !empty($user['picture'])) {
                // Assuming the path in the database is relative to your web root
                $userImage = $user['picture'];
              }
            } catch (Exception $e) {
              error_log("Profile image error: " . $e->getMessage());
            }
          }
          ?>
          <img src="<?= htmlspecialchars($userImage) ?>"
            class="rounded-circle p-1 border"
            width="45"
            height="45"
            alt="Profile Picture"
            onerror="this.onerror=null;this.src='<?= htmlspecialchars($defaultImage) ?>'">
        </a>
        <div class="dropdown-menu dropdown-user dropdown-menu-end shadow">
          <a class="dropdown-item  gap-2 py-2" href="javascript:;">
            <div class="text-center">
              <h6 class="user-name mb-0 fw-bold small text-truncate" style="max-width: 150px;">
                <?php echo lang("topnavbar_hello"); ?>, <?= ucfirst($_SESSION['user_name']) ?>
              </h6>
            </div>
          </a>
          <hr class="dropdown-divider">
          <a class="dropdown-item d-flex align-items-center gap-2 py-2"
            href="index.php?route=modules/profile/profile"><i
              class="material-icons-outlined">person_outline</i><?php echo lang("topnavbar_profile"); ?></a>
          <hr class="dropdown-divider">
          <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="index.php?logout=1"><i
              class="material-icons-outlined">power_settings_new</i><?php echo lang("topnavbar_logout"); ?></a>
        </div>
      </li>
    </ul>

  </nav>
</header>
<!--end top header-->
<script>
  function setActive(selectedItem) {
    // Remove 'active' class from all dropdown items
    document.querySelectorAll('.dropdown-item').forEach(item => {
      item.classList.remove('active');
    });


    // Active selected item
    selectedItem.classList.add('active');


    // Get selected item language
    let selectedLang = selectedItem.getAttribute('data-lang');


    // Get selected item image
    let selectedImgSrc = selectedItem.querySelector("img").src;


    // Update dropdown image
    document.getElementById("selectedLangImg").src = selectedImgSrc;


    // Update URL with lang parameter and reload the page
    let url = new URL(window.location.href);
    url.searchParams.set('lang', selectedLang); // Add or update lang parameter
    window.location.href = url.toString(); // Redirect to updated URL
  }


  window.addEventListener('DOMContentLoaded', function() {
    let url = new URL(window.location.href);
    let lang = url.searchParams.get('lang'); // Get lang parameter from URL


    // Remove 'active' class from all dropdown items
    document.querySelectorAll('.dropdown-item').forEach(item => {
      item.classList.remove('active');
    });


    if (lang) {
      // Find the matching dropdown item
      let selectedItem = document.querySelector(`.dropdown-item[data-lang="${lang}"]`);


      if (selectedItem) {
        // Get selected image src
        let selectedImgSrc = selectedItem.querySelector("img").src;


        // Update dropdown image
        document.getElementById("selectedLangImg").src = selectedImgSrc;


        // Mark selected item as active
        selectedItem.classList.add('active');
      }
    }
  });
</script>
<script>
function loadNotifications() {
  fetch('ajax_helpers/get-notifications.php')
    .then(response => response.json())
    .then(data => {
      const container = document.getElementById('notification-list');
      const badge = document.querySelector('.notification-badge');

      container.innerHTML = ''; // Clear previous

      // Handle badge display
      if (data.unread_count > 0) {
        badge.textContent = data.unread_count;
        badge.style.display = 'inline-block';
      } else {
        badge.style.display = 'none';
      }

      // No notifications
      if ((!data.unread || data.unread.length === 0) && (!data.read || data.read.length === 0)) {
        container.innerHTML = `
          <div class="px-3 py-2 text-center text-muted small">
            No notifications yet.
          </div>`;
        return;
      }

      // Section: Unread
      if (data.unread && data.unread.length > 0) {
        container.innerHTML += `
          <div class="dropdown-header text-dark fw-bold px-3 py-1 small">
            New
          </div>`;

        data.unread.forEach(item => {
          const message = item.source === 'applicants'
            ? `${item.first_name} ${item.last_name} has applied`
            : `${item.first_name} ${item.last_name} filled the packet form`;

          const icon = item.source === 'applicants'
            ? `<i class="material-icons-outlined text-info">person_add</i>`
            : `<i class="material-icons-outlined text-warning">assignment_turned_in</i>`;

          container.innerHTML += `
            <a href="#" class="dropdown-item d-flex align-items-center gap-3 py-2 bg-light border-bottom">
              <div class="icon-box bg-light-info rounded-circle p-2">
                ${icon}
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-1 fw-semibold">${message}</h6>
                <p class="small text-muted mb-0">${new Date(item.created_at).toLocaleString()}</p>
              </div>
            </a>`;
        });
      }

      // Section: Read
      if (data.read && data.read.length > 0) {
        container.innerHTML += `
          <div class="dropdown-header text-muted fw-bold px-3 py-1 small mt-2">
            Earlier
          </div>`;

        data.read.forEach(item => {
          const message = item.source === 'applicants'
            ? `${item.first_name} ${item.last_name} has applied`
            : `${item.first_name} ${item.last_name} filled the packet form`;

          const icon = item.source === 'applicants'
            ? `<i class="material-icons-outlined text-secondary">person</i>`
            : `<i class="material-icons-outlined text-warning">assignment_turned_in</i>`;

          container.innerHTML += `
            <a href="#" class="dropdown-item d-flex align-items-center gap-3 py-2 border-bottom">
              <div class="icon-box bg-light-secondary rounded-circle p-2">
                ${icon}
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-1">${message}</h6>
                <p class="small text-muted mb-0">${new Date(item.created_at).toLocaleString()}</p>
              </div>
            </a>`;
        });
      }

      // Footer: Mark all as read
      container.innerHTML += `
        <div class="dropdown-footer text-center p-2 border-top">
          <button onclick="markAllAsRead()" class="btn btn-sm btn-primary w-100">
            Mark all as read
          </button>
        </div>`;
    })
    .catch(error => {
      console.error('Error fetching notifications:', error);
      document.getElementById('notification-list').innerHTML = `
        <p class="text-muted px-3">Unable to load notifications.</p>`;
    });
}

function markAllAsRead() {
  fetch('ajax_helpers/mark-notifications-read.php', {
    method: 'POST'
  }).then(() => {
    loadNotifications(); // Reload updated notifications
  });
}

document.addEventListener('DOMContentLoaded', loadNotifications);
setInterval(loadNotifications, 60000); // Auto-refresh every 60s
</script>

