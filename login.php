<?php 

require_once 'config/db.php';  
require 'config/validator.php';

$error = "";

if (isset($_COOKIE['remember_user']) && !isset($_SESSION['userID'])) {
    $cookieID = $_COOKIE['remember_user'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE userID = ?");
    $stmt->bind_param("i", $cookieID);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        $_SESSION['userID'] = $user['userID'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        redirectIfLoggedIn();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);

    if (empty($email) || empty($password)) {
        $error = "Email and Password must be filled.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['userID'] = $user['userID'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($remember) {
                setcookie("remember_user", $user['userID'], time() + (7 * 24 * 60 * 60), "/");
            }

            redirectIfLoggedIn();
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}

include 'utils/header.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <h2 class="auth-title">Login</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <label class="form-label">Email</label>
            <input type="email" name="email" placeholder="example@gmail.com" required>

            <label class="form-label">Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <label class="form-label checkbox-label">
                <input type="checkbox" name="remember"> Remember me
            </label>

            <button type="submit" class="btn">Login</button>
        </form>

        <p class="auth-footer">Don't have an account? <a class="small-link" href="register.php">Register here</a></p>
    </div>
</div>      

<?php include 'utils/footer.php'; ?>