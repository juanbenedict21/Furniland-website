<?php
require_once 'config/db.php';
require 'config/validator.php';

isLogin();

$userID = $_SESSION['userID'];
$msg = '';
$err = '';

$stmt = $conn->prepare("SELECT username, email, gender, dob, password FROM users WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_info'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $gender = $_POST['gender'] ?? '';
        $dob = $_POST['dob'] ?? '';

        if (strlen($username) < 4 || strlen($username) > 20) {
            $err = 'Username must be 4 - 20 characters.';
        } elseif (!str_ends_with($email, '@gmail.com')) {
            $err = "Email must end with '@gmail.com'.";
        } elseif (empty($gender)) {
            $err = 'Please select a gender.';
        } elseif (empty($dob) || strtotime($dob) >= time()) {
            $err = 'Date of Birth must be in the past.';
        } else {
            $check = $conn->prepare("SELECT userID FROM users WHERE (username = ? OR email = ?) AND userID != ?");
            $check->bind_param("ssi", $username, $email, $userID);
            $check->execute();
            $res = $check->get_result();
            if ($res->num_rows > 0) {
                $err = 'Username or Email already taken.';
            } else {
                $upd = $conn->prepare("UPDATE users SET username = ?, email = ?, gender = ?, dob = ? WHERE userID = ?");
                $upd->bind_param("ssssi", $username, $email, $gender, $dob, $userID);
                if ($upd->execute()) {
                    $_SESSION['username'] = $username;
                    $msg = 'Profile updated successfully.';
                    $user['username'] = $username;
                    $user['email'] = $email;
                    $user['gender'] = $gender;
                    $user['dob'] = $dob;
                } else {
                    $err = 'Database error while updating profile.';
                }
            }
        }
    }

    if (isset($_POST['update_password'])) {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($current) || empty($new) || empty($confirm)) {
            $err = 'All password fields are required.';
        } elseif (!password_verify($current, $user['password'])) {
            $err = 'Current password is incorrect.';
        } elseif (strlen($new) < 8) {
            $err = 'New password must be at least 8 characters.';
        } elseif ($new !== $confirm) {
            $err = 'Password confirmation does not match.';
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $upd = $conn->prepare("UPDATE users SET password = ? WHERE userID = ?");
            $upd->bind_param("si", $hash, $userID);
            if ($upd->execute()) {
                $msg = 'Password updated successfully.';
                $user['password'] = $hash;
            } else {
                $err = 'Database error while updating password.';
            }
        }
    }

    if (isset($_POST['delete_account'])) {
        $confirm = $_POST['confirm_password_delete'] ?? '';
        if (empty($confirm)) {
            $err = 'Please provide your password to confirm account deletion.';
        } elseif (!password_verify($confirm, $user['password'])) {
            $err = 'Password incorrect.';
        } else {
            $delCart = $conn->prepare("DELETE FROM cart WHERE userID = ?");
            $delCart->bind_param("i", $userID);
            $delCart->execute();

            $delUser = $conn->prepare("DELETE FROM users WHERE userID = ?");
            $delUser->bind_param("i", $userID);
            if ($delUser->execute()) {
                session_unset();
                session_destroy();
                header('Location: register.php');
                exit;
            } else {
                $err = 'Database error while deleting account.';
            }
        }
    }
}

include 'utils/header.php';
?>

<div class="profile-container">         
    <h1 class="auth-title">Your Profile</h1>

    <div class="profile-card">

        <?php if ($msg): ?>
            <div class="alert alert-success"><?= $msg ?></div>
        <?php endif; ?>
        <?php if ($err): ?>
            <div class="alert alert-error"><?= $err ?></div>
        <?php endif; ?>

        <form method="POST">
            <h3 class="profile-section-title">Update Profile Information</h3>
            <label class="form-label">Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

            <label class="form-label">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label class="form-label">Gender</label>
            <select name="gender">
                <option value="">Select</option>
                <option value="Male" <?= ($user['gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= ($user['gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
            </select>

            <label class="form-label">Date of Birth</label>
            <input type="date" name="dob" value="<?= htmlspecialchars($user['dob']) ?>" required>

            <button type="submit" name="update_info" class="btn btn-block">Save Changes</button>
        </form>
    </div>

    <div class="profile-card">
        <form method="POST">
            <h3 class="profile-section-title">Update Password</h3>
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password">

            <label class="form-label">New Password</label>
            <input type="password" name="new_password">

            <label class="form-label">Confirm New Password</label>
            <input type="password" name="confirm_password">

            <button type="submit" name="update_password" class="btn btn-block">Update Password</button>
        </form>
    </div>

    <div class="profile-cardz">
        <form method="POST">
            <h3 class="profile-section-title profile-delete-title">Delete Account</h3>
            <p class="profile-delete-note">Once deleted, your account cannot be recovered.</p>

            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password_delete">

            <button type="submit" name="delete_account" class="btn btn-block btn-danger">Delete Account</button>
        </form>
    </div>

</div>

<?php include 'utils/footer.php'; ?>
