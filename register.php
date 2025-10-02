<?php
// register.php
global $mysqli, $AVAILABLE_IMAGES;

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "CSRF token mismatch.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $secret_image = $_POST['secret_image'] ?? '';

        if (empty($username) || empty($email) || empty($password) || empty($secret_image)) {
            $errors[] = "All fields are required.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email.";
        }
        if ($password !== $confirm) {
            $errors[] = "Passwords do not match.";
        }
        if (!password_strong_enough($password)) {
            $errors[] = "Password must be at least 8 chars and include upper, lower, digit and special char.";
        }
        if (!in_array($secret_image, $AVAILABLE_IMAGES, true)) {
            $errors[] = "Invalid secret image.";
        }

        // check uniqueness
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Username or email already exists.";
        }
        $stmt->close();

        if (empty($errors)) {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $ins = $mysqli->prepare("INSERT INTO users (username, email, password_hash, secret_image) VALUES (?, ?, ?, ?)");
            $ins->bind_param("ssss", $username, $email, $hashed, $secret_image);
            if ($ins->execute()) {
                $success = "Registration successful. You may now login.";
                log_event($mysqli, $ins->insert_id, $username, 'register', 'New user registered');
            } else {
                $errors[] = "DB error: " . $ins->error;
            }
            $ins->close();
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Register</title></head>
<body>
<h2>Register</h2>

<?php foreach ($errors as $e): ?><div style="color:red;"><?php echo htmlspecialchars($e); ?></div><?php endforeach; ?>
<?php if ($success): ?><div style="color:green;"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

<form method="post">
  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
  Username: <input name="username" required><br>
  Email: <input name="email" type="email" required><br>
  Password: <input name="password" type="password" required><br>
  Confirm Password: <input name="confirm_password" type="password" required><br>

  <h4>Select your secret image</h4>
  <?php foreach ($AVAILABLE_IMAGES as $img): ?>
    <label style="display:inline-block;margin:8px;text-align:center;">
      <input type="radio" name="secret_image" value="<?php echo $img; ?>" required><br>
      <img src="images/<?php echo $img; ?>" width="100" style="display:block;"><br>
      <?php echo htmlspecialchars(pathinfo($img, PATHINFO_FILENAME)); ?>
    </label>
  <?php endforeach; ?>
  <br>
  <button type="submit">Register</button>
</form>
</body>
</html>
