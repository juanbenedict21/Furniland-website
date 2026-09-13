<?php 
 require_once 'config/db.php';
 require 'config/validator.php';

 redirectIfLoggedIn();

 $error = "";
 $success = "";
 
 if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $gender = $_POST['gender'] ?? '';
    $dob = $_POST['dob'];

    if (strlen($username) < 4 || strlen($username) > 20) {
        $error = "Username must be 4 - 20 characters.";
    } elseif (!str_ends_with($email, '@gmail.com')) {
        $error = "Email must end with '@gmail.com'.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Password confirmation does not match.";
    } elseif (empty($gender)) {
        $error = "Please select a gender.";
    } elseif (empty($dob) || strtotime($dob) >= time()) {
        $error = "Date of Birth must be in the past.";
    } else {
        $stmt = $conn->prepare("SELECT userID FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Username or Email already taken!";
        } else {
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $role = 'Member';

            $insertStmt = $conn->prepare("INSERT INTO users (username, email, password, gender, dob, role) VALUES (?, ?, ?, ?, ?, ?)");
            $insertStmt->bind_param("ssssss", $username, $email, $hashed_pass, $gender, $dob, $role);

            if ($insertStmt->execute()) {
                $success = "<script>alert('Registration Successful! Please Login.'); window.location.href='login.php';</script>";
                $_SESSION['userID'] = $conn->insert_id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;
                header("Location: product/catalog.php");
                exit;
            } else {
                $error = "Database Error: " . $conn->error;
            }
        }
    }
}

include 'utils/header.php';
?>

<div class="auth-page">
  <div class="auth-card">
    <h2 class="auth-title">Register</h2>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <label class="form-label">Username</label>
      <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>

      <label class="form-label">Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="@gmail.com" required>

      <label class="form-label">Password</label>
      <input type="password" name="password" placeholder="Min 8 chars" required>

      <label class="form-label">Re-enter Password</label>
      <input type="password" name="confirm_password" required>

            <label class="form-label">Gender</label>
            <div class="radio-group">
                <label class="radio-label"><input type="radio" name="gender" value="Male" <?= (isset($gender) && $gender == 'Male') ? 'checked' : '' ?>> Male</label>
                <label class="radio-label"><input type="radio" name="gender" value="Female" <?= (isset($gender) && $gender == 'Female') ? 'checked' : '' ?>> Female</label>
            </div>

      <label class="form-label">Date of Birth</label>
      <input type="date" name="dob" value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>" required>

      <button type="submit" class="btn">Register</button>
    </form>

    <p class="auth-footer">Already have an account? <a class="small-link" href="login.php">Login here</a></p>
  </div>
</div>

<?php include 'utils/footer.php'; ?>