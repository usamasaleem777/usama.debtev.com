<?php
// login.php
global $mysqli, $AVAILABLE_IMAGES;

$step = $_SESSION['login_step'] ?? 1;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Step 1: username + password
    if (isset($_POST['step']) && $_POST['step'] == '1') {
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
            $errors[] = "CSRF token mismatch.";
        } else {
            $identifier = trim($_POST['identifier'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = get_user_by_username_or_email($mysqli, $identifier);
            if (!$user) {
                $errors[] = "Invalid username or password.";
                log_event($mysqli, null, $identifier, 'login_fail', 'user not found');
            } else {
                // Check lock
                if ($user['is_locked'] && !empty($user['lock_expires']) && strtotime($user['lock_expires']) > time()) {
                    $errors[] = "Account is locked until " . $user['lock_expires'];
                    log_event($mysqli, $user['id'], $user['username'], 'login_fail', 'account locked');
                } else {
                    // Verify password
                    if (password_verify($password, $user['password_hash'])) {
                        // reset failed attempts
                        $stmt = $mysqli->prepare("UPDATE users SET failed_attempts=0, last_attempt=NULL, is_locked=0, lock_expires=NULL WHERE id=?");
                        $stmt->bind_param("i", $user['id']); $stmt->execute(); $stmt->close();

                        // optionally rehash
                        if (password_needs_rehash($user['password_hash'], PASSWORD_BCRYPT)) {
                            $newHash = password_hash($password, PASSWORD_BCRYPT);
                            $upd = $mysqli->prepare("UPDATE users SET password_hash=? WHERE id=?");
                            $upd->bind_param("si", $newHash, $user['id']); $upd->execute(); $upd->close();
                        }

                        // store pending user in session
                        $_SESSION['pending_user'] = ['id'=>$user['id'],'username'=>$user['username'],'email'=>$user['email'],'secret_image'=>$user['secret_image']];
                        $_SESSION['login_step'] = 2;
                        // redirect to avoid form re-post
                        header("Location: login.php");
                        exit;
                    } else {
                        // increment failed attempts
                        $stmt = $mysqli->prepare("UPDATE users SET failed_attempts = failed_attempts + 1, last_attempt = NOW() WHERE id = ?");
                        $stmt->bind_param("i", $user['id']); $stmt->execute(); $stmt->close();

                        // fetch updated
                        $user = get_user_by_username_or_email($mysqli, $identifier);
                        if ($user['failed_attempts'] >= MAX_FAILED_ATTEMPTS) {
                            $lock_until = date('Y-m-d H:i:s', time() + LOCK_MINUTES * 60);
                            $l = $mysqli->prepare("UPDATE users SET is_locked=1, lock_expires=? WHERE id=?");
                            $l->bind_param("si", $lock_until, $user['id']); $l->execute(); $l->close();
                            $errors[] = "Account locked due to multiple failed attempts. Try again after " . $lock_until;
                            log_event($mysqli, $user['id'], $user['username'], 'account_locked', 'too many failed attempts');
                        } else {
                            $errors[] = "Invalid username or password.";
                            log_event($mysqli, $user['id'], $user['username'], 'login_fail', 'wrong password');
                        }
                    }
                }
            }
        }
    }

    // Step 2: captcha + secret image verification -> send OTP
    elseif (isset($_POST['step']) && $_POST['step'] == '2') {
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
            $errors[] = "CSRF token mismatch.";
        } else {
            if (empty($_SESSION['pending_user'])) {
                $errors[] = "Session expired. Start login again.";
            } else {
                $user = $_SESSION['pending_user'];
                $captcha = $_POST['captcha'] ?? '';
                $selected = $_POST['secret_image'] ?? '';

                if (!isset($_SESSION['captcha_code']) || $captcha !== $_SESSION['captcha_code']) {
                    $errors[] = "Incorrect captcha.";
                    log_event($mysqli, $user['id'], $user['username'], 'login_fail', 'captcha_failed');
                } elseif ($selected !== $user['secret_image']) {
                    $errors[] = "Wrong secret image.";
                    log_event($mysqli, $user['id'], $user['username'], 'login_fail', 'image_mismatch');
                } else {
                    // success of step 2: send OTP
                    if (create_and_send_otp($mysqli, $user['id'], $user['email'], $user['username'])) {
                        $_SESSION['login_step'] = 3;
                        log_event($mysqli, $user['id'], $user['username'], 'otp_sent', 'otp emailed');
                        header("Location: login.php");
                        exit;
                    } else {
                        $errors[] = "Failed to send OTP email. Contact admin.";
                    }
                }
            }
        }
    }

    // Step 3: OTP verification
    elseif (isset($_POST['step']) && $_POST['step'] == '3') {
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
            $errors[] = "CSRF token mismatch.";
        } else {
            if (empty($_SESSION['pending_user'])) {
                $errors[] = "Session expired. Start login again.";
            } else {
                $user = $_SESSION['pending_user'];
                $otp = trim($_POST['otp'] ?? '');
                if (verify_otp($mysqli, $user['id'], $otp)) {
                    // finalize login
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    unset($_SESSION['pending_user']);
                    unset($_SESSION['login_step']);
                    unset($_SESSION['captcha_code']);
                    log_event($mysqli, $user['id'], $user['username'], 'login_success', 'user logged in');
                    header("Location: protected.php"); // or dashboard
                    exit;
                } else {
                    $errors[] = "Invalid or expired OTP.";
                    log_event($mysqli, $user['id'], $user['username'], 'login_fail', 'otp_invalid');
                }
            }
        }
    }
}

// Prepare display variables
$step = $_SESSION['login_step'] ?? 1;
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Login</title></head><body>
<h2>Login</h2>
<?php foreach ($errors as $e): ?><div style="color:red;"><?php echo htmlspecialchars($e); ?></div><?php endforeach; ?>

<?php if ($step == 1): ?>
<form method="post">
  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
  <input type="hidden" name="step" value="1">
  Username or Email: <input name="identifier" required><br>
  Password: <input name="password" type="password" required><br>
  <button type="submit">Next</button>
</form>

<?php elseif ($step == 2 && !empty($_SESSION['pending_user'])): 
    // generate captcha
    $captcha_number = generate_numeric_captcha();
    // prepare images include the user's secret plus random others
    $imgs = $AVAILABLE_IMAGES;
    // ensure secret image included and shuffle
    if (!in_array($_SESSION['pending_user']['secret_image'], $imgs)) {
        array_unshift($imgs, $_SESSION['pending_user']['secret_image']);
    }
    shuffle($imgs);
    // limit to 6 choices for UI
    $imgs = array_slice($imgs, 0, 6);
    if (!in_array($_SESSION['pending_user']['secret_image'], $imgs)) {
        $imgs[0] = $_SESSION['pending_user']['secret_image'];
        shuffle($imgs);
    }
?>
<form method="post">
  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
  <input type="hidden" name="step" value="2">
  <div>
    <strong>Captcha:</strong> <?php echo htmlspecialchars($captcha_number); ?> <br>
    Enter captcha: <input name="captcha" required><br>
  </div>
  <div>
    <h4>Select your secret image</h4>
    <?php foreach ($imgs as $img): ?>
      <label style="display:inline-block;margin:8px;text-align:center;">
        <input type="radio" name="secret_image" value="<?php echo htmlspecialchars($img); ?>" required><br>
        <img src="images/<?php echo htmlspecialchars($img); ?>" width="100"><br>
      </label>
    <?php endforeach; ?>
  </div>
  <button type="submit">Verify</button>
</form>

<?php elseif ($step == 3 && !empty($_SESSION['pending_user'])): ?>
<form method="post">
  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
  <input type="hidden" name="step" value="3">
  <div>
    <p>An OTP has been sent to your email: <strong><?php echo htmlspecialchars($_SESSION['pending_user']['email']); ?></strong></p>
    Enter OTP: <input name="otp" required><br>
  </div>
  <button type="submit">Verify OTP & Login</button>
</form>

<?php else: ?>
<p>Session expired. <a href="login.php">Start again</a></p>
<?php endif; ?>

</body></html>
