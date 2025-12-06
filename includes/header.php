<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SCHOOL_NAME; ?> - <?php echo $page_title ?? 'Home'; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h1><?php echo SCHOOL_NAME; ?></h1>
                <p>Manyinga District</p>
            </div>
            <div class="nav-links">
                <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="announcements.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'announcements.php' ? 'active' : ''; ?>">
                    <i class="fas fa-bullhorn"></i> Announcements
                </a>
                <a href="admin-login.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin-login.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-shield"></i> Admin Login
                </a>
                <a href="contact.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">
                    <i class="fas fa-address-book"></i> Contact
                </a>
            </div>
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>
