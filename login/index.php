<?php
session_start();
include '../includes/config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard/");
    exit();
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: ../dashboard/");
        exit();
    } else {
        $error = "Invalid username or password!";
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
    <title>CoreFinance - Login</title>
</head>

<body class="auth-body">
    <div class="auth-card">
        <div class="auth-header">
            <h2><span style="color:var(--primary);">Core</span><span style="color:var(--accent);">Finance</span></h2>
            <p>Sign in to manage your daily expenses</p>
        </div>

        <?php
        if (isset($_GET['registered']) && $_GET['registered'] == 'true') {
            echo "<div class='global-alert' style='display:block; background-color:#d1fae5; color:#065f46; border:none;'>Account created successfully! Please log in to continue.</div>";
        }
        ?>

        <form method="POST" id="loginForm">
            <div id="loginGlobalError" class="global-alert" style="<?php if (isset($error)) echo 'display:block;'; ?>">
                <?php if (isset($error)) echo $error; ?>
            </div>
            <div class="form-group">
                <label style="display:block; margin-bottom:0.5rem; font-weight:500; font-size:0.9rem;">Username</label>
                <input type="text" name="username" placeholder="Enter your username">
                <div class="error-message" id="usernameError"></div>
            </div>

            <div class="form-group password-group">
                <label style="display:block; margin-bottom:0.5rem; font-weight:500; font-size:0.9rem;">Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" placeholder="Enter your password">
                    <span class="password-toggle" onclick="togglePassword('password', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span>
                </div>
                <div class="error-message" id="loginPasswordError"></div>
            </div>

            <button type="submit" name="login" style="margin-top:1rem;">Sign In</button>
            <div style="text-align: center; margin-top: 1rem;">
                <a href="../forgot_password/" style="font-size: 0.9rem; color: var(--accent);">Forgot Password?</a>
            </div>
        </form>

        <p style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem; color: var(--text-muted);">
            Don't have an account? <a href="../register/" style="font-weight:600;">Create Account</a>
        </p>
    </div>
    <script src="../assets/js/script.js?v=<?php echo time(); ?>"></script>
    <script src="script.js?v=<?php echo time(); ?>"></script>
</body>

</html>
