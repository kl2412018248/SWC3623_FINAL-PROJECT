<?php
// --- START OF PHP BLOCK ---

// This file has a lot of logic before any HTML, so we start the session and include db.php first.
// The header.php will be included later, after the logic is done.
require_once(dirname(__FILE__) . '/includes/db.php');

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $user_found = false;
        
        // 1. Check in 'admins' table
        $sql_admin = "SELECT id, username, password FROM admins WHERE username = ?";
        if ($stmt_admin = $conn->prepare($sql_admin)) {
            $stmt_admin->bind_param("s", $username);
            if ($stmt_admin->execute()) {
                $stmt_admin->store_result();
                if ($stmt_admin->num_rows == 1) {
                    $stmt_admin->bind_result($id, $username, $hashed_password);
                    if ($stmt_admin->fetch() && password_verify($password, $hashed_password)) {
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;
                        $_SESSION["role"] = "admin";
                        $user_found = true;
                        header("location: admin/dashboard.php");
                        exit;
                    }
                }
            }
            $stmt_admin->close();
        }

        // 2. If not found, check in 'users' table
        if (!$user_found) {
            $sql_user = "SELECT id, username, password FROM users WHERE username = ?";
            if ($stmt_user = $conn->prepare($sql_user)) {
                $stmt_user->bind_param("s", $username);
                if ($stmt_user->execute()) {
                    $stmt_user->store_result();
                    if ($stmt_user->num_rows == 1) {
                        $stmt_user->bind_result($id, $username, $hashed_password);
                        if ($stmt_user->fetch() && password_verify($password, $hashed_password)) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["role"] = "user";
                            $user_found = true;
                            header("location: index.php");
                            exit;
                        }
                    }
                }
                $stmt_user->close();
            }
        }

        if(!$user_found) {
            $error = "Invalid username or password.";
        }
    }
}

// Now that the logic is done, we can start the HTML by including the header.
require_once(dirname(__FILE__) . '/includes/header.php');

// --- END OF PHP BLOCK, HTML BEGINS BELOW ---
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0" data-aos="fade-up">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Welcome Back!</h2>
                        <p class="text-muted">Sign in to continue to Eventura</p>
                    </div>

                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="login.php" method="post">
                        <div class="form-group mb-3">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" name="username" class="form-control" id="username" required>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" name="password" class="form-control" id="password" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary w-100 btn-lg">Login</button>
                        </div>
                    </form>
                    <p class="mt-4 text-center text-muted">Don't have an account? <a href="register.php">Sign up now</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Include the footer to close the HTML document
require_once(dirname(__FILE__) . '/includes/footer.php'); 
?>