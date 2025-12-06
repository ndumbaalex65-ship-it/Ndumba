<?php
require_once 'includes/header.php';
require_once 'includes/db_connect.php';

$db = new Database();
$conn = $db->getConnection();

$page_title = "Admin Login";
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password (for demo, using simple check - in production use password_verify)
        if ($password == 'admin123' || password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Log activity
            $logQuery = "INSERT INTO activity_logs (user_id, activity_type, description) VALUES (?, 'login', 'User logged in')";
            $logStmt = $conn->prepare($logQuery);
            $logStmt->bind_param("i", $user['id']);
            $logStmt->execute();
            
            // Redirect based on user type
            if ($user['user_type'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: teacher/dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "Invalid username or password";
    }
}
?>

<main>
    <section class="login-section">
        <div class="container">
            <div class="login-box">
                <h2>Administration Login</h2>
                <p class="login-subtitle">Teachers and Administrators only</p>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required 
                               placeholder="Enter your username">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required 
                               placeholder="Enter your password">
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
                
                <div class="login-note">
                    <p><strong>Default Credentials:</strong></p>
                    <p>Admin: username: <code>admin</code>, password: <code>admin123</code></p>
                    <p>Teacher: username: <code>teacher1</code>, password: <code>teacher123</code></p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
