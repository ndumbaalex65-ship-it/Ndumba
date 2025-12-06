<?php
function checkAuth($required_type = null) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../admin-login.php");
        exit();
    }
    
    if ($required_type && $_SESSION['user_type'] !== $required_type) {
        // Redirect to appropriate dashboard
        if ($_SESSION['user_type'] == 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../teacher/dashboard.php");
        }
        exit();
    }
}

function logout() {
    session_destroy();
    header("Location: ../admin-login.php");
    exit();
}
?>
