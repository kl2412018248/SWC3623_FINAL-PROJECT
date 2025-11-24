<?php
include 'includes/header.php';

$username_err = $email_err = $password_err = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role']; // This variable is now used to CHOOSE the table, not be a column
    
    // Simple validation
    if (empty($username)) $username_err = "Please enter a username.";
    if (empty($email)) $email_err = "Please enter an email.";
    if (empty($password)) $password_err = "Please enter a password.";
    
    if (empty($username_err) && empty($email_err) && empty($password_err)) {
        // ---- THIS IS THE FIX ----
        // Choose the table based on the selected role
        $table_name = ($role === 'admin') ? 'admins' : 'users';
        
        // The SQL no longer has a 'role' column
        $sql = "INSERT INTO $table_name (username, email, password) VALUES (?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // The bind_param no longer has a 4th parameter
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $success_msg = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                 if ($conn->errno == 1062) { // 1062 is the error code for duplicate entry
                    $username_err = "This username or email is already taken.";
                } else {
                    echo "Error: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h2>Register</h2></div>
            <div class="card-body">
                <?php if(!empty($success_msg)): ?>
                    <div class="alert alert-success"><?php echo $success_msg; ?></div>
                <?php endif; ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" id="username" required>
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                     <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Register as</label>
                        <select name="role" id="role" class="form-select">
                            <option value="user">Attendee</option>
                            <option value="admin">Event Organizer</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Register</button>
                    <p class="mt-3">Already have an account? <a href="login.php">Login here</a>.</p>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>