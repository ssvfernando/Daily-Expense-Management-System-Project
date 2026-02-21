<?php
session_start();
include '../includes/config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard/");
    exit();
}

if (isset($_POST['register'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = 'user';

    $check_query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $existing_user = mysqli_fetch_assoc($check_result);
        if ($existing_user['username'] === $username) {
            $error = "Username already taken. Please choose another.";
        } else {
            $error = "Email already registered. Please login.";
        }
    } elseif (empty($username) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($username) < 3) {
        $error = "Username must be at least 3 characters long.";
    } elseif (!preg_match("/^[a-zA-Z0-9]+$/", $username)) {
        $error = "Username can only contain letters and numbers.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $_POST['confirm_password']) {
        $error = "Passwords do not match.";
    } else {
        $status = 'pending';
        $query = "INSERT INTO users (email, username, password, role, status) VALUES ('$email', '$username', '$password', '$role', '$status')";
        if (mysqli_query($conn, $query)) {
            header("Location: ../login/?registered=true");
            exit();
        } else {
            $error = "Registration failed: " . mysqli_error($conn);
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
    <title>CoreFinance - Register</title>
</head>

<body class="auth-body">
    <div class="auth-card">
        <div class="auth-header">
            <h2>Create Account</h2>
            <p>Start tracking your expenses today</p>
        </div>

        <?php
        if (isset($success)) echo "<div class='global-alert' style='display:block; background-color:#d1fae5; color:#065f46; border:none;'>$success</div>";
        ?>

        <form method="POST" id="registerForm">
            <div id="registerGlobalError" class="global-alert" style="<?php if (isset($error)) echo 'display:block;'; ?>">
                <?php if (isset($error)) echo $error; ?>
            </div>

            <div class="form-group">
                <label style="display:block; margin-bottom:0.5rem; font-weight:500; font-size:0.9rem;">Username</label>
                <input type="text" name="username" placeholder="Choose a username">
                <div class="error-message" id="regUsernameError"></div>
            </div>

            <div class="form-group">
                <label style="display:block; margin-bottom:0.5rem; font-weight:500; font-size:0.9rem;">Email Address</label>
                <input type="text" name="email" placeholder="Enter email address">
                <div class="error-message" id="emailError"></div>
            </div>

            <div class="form-group password-group">
                <label>Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" placeholder="Choose a password (min 6 chars)">
                    <span class="password-toggle" onclick="togglePassword('password', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span>
                </div>
                <div class="error-message" id="regPasswordError"></div>
            </div>

            <div class="form-group password-group">
                <label>Confirm Password</label>
                <div class="password-wrapper">
                    <input type="password" name="confirm_password" placeholder="Confirm your password">
                    <span class="password-toggle" onclick="togglePassword('confirm_password', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span>
                </div>
                <div class="error-message" id="regConfirmPasswordError"></div>
            </div>

            <button type="submit" name="register" style="margin-top:1rem;">Sign Up</button>
        </form>

        <p style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem; color: var(--text-muted);">
            Already have an account? <a href="../login/" style="font-weight:600;">Sign In</a>
        </p>
    </div>
    <script src="../assets/js/script.js?v=<?php echo time(); ?>"></script>
    <script src="script.js?v=<?php echo time(); ?>"></script>
</body>

</html>
