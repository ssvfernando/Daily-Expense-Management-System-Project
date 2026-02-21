<?php
session_start();
include '../includes/config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard/");
    exit();
}

$step = 1;
$error = "";
$success = "";

if (isset($_POST['verify_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $query = "SELECT user_id FROM users WHERE username='$username' AND email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['reset_user_id'] = $user['user_id'];
        $step = 2;
    } else {
        $error = "Invalid username or email combination.";
    }
}

if (isset($_POST['reset_password'])) {
    if (!isset($_SESSION['reset_user_id'])) {
        header("Location: index.php");
        exit();
    }

    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
        $step = 2;
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
        $step = 2;
    } else {
        $user_id = $_SESSION['reset_user_id'];

        $update_query = "UPDATE users SET password='$password' WHERE user_id='$user_id'";
        if (mysqli_query($conn, $update_query)) {
            unset($_SESSION['reset_user_id']);
            header("Location: ../login/?reset=success");
            exit();
        } else {
            $error = "Error updating password: " . mysqli_error($conn);
            $step = 2;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <title>CoreFinance - Forgot Password</title>
</head>

<body class="auth-body">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Forgot Password</h2>
            <p><?php echo ($step === 1) ? "Enter your details to verify your account" : "Create a new password"; ?></p>
        </div>

        <?php if ($error): ?>
            <div class="global-alert" style="display:block;"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <form method="POST" id="forgotPasswordForm" novalidate>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Enter your username" required>
                    <div class="error-message" id="fpUsernameError"></div>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your registered email" required>
                    <div class="error-message" id="fpEmailError"></div>
                </div>

                <button type="submit" name="verify_user" style="margin-top:1rem;">Verify Account</button>
            </form>
        <?php else: ?>
            <form method="POST" id="resetPasswordForm" novalidate>
                <div class="form-group password-group">
                    <label>New Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" placeholder="New password (min 6 chars)" required>
                        <span class="password-toggle" onclick="togglePassword('password', this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </span>
                    </div>
                    <div class="error-message" id="fpPasswordError"></div>
                </div>

                <div class="form-group password-group">
                    <label>Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="confirm_password" placeholder="Confirm new password" required>
                        <span class="password-toggle" onclick="togglePassword('confirm_password', this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </span>
                    </div>
                    <div class="error-message" id="fpConfirmPasswordError"></div>
                </div>

                <button type="submit" name="reset_password" style="margin-top:1rem;">Reset Password</button>
            </form>
        <?php endif; ?>

        <p style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem; color: var(--text-muted);">
            Remember your password? <a href="../login/" style="font-weight:600;">Sign In</a>
        </p>
    </div>
    <script src="../assets/js/script.js?v=<?php echo time(); ?>"></script>
    <script src="script.js?v=<?php echo time(); ?>"></script>
</body>

</html>
