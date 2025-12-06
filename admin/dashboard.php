<?php
require_once '../includes/header.php';
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

// Check if user is admin
checkAuth('admin');

$db = new Database();
$conn = $db->getConnection();

$page_title = "Admin Dashboard";
?>

<main class="dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h2>Welcome, <?php echo $_SESSION['full_name']; ?></h2>
            <p>Administrator Dashboard</p>
        </div>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <?php
                    $studentCount = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
                    ?>
                    <h3><?php echo $studentCount; ?></h3>
                    <p>Total Students</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-info">
                    <?php
                    $teacherCount = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'teacher'")->fetch_assoc()['count'];
                    ?>
                    <h3><?php echo $teacherCount; ?></h3>
                    <p>Teachers</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-info">
                    <?php
                    $subjectCount = $conn->query("SELECT COUNT(*) as count FROM subjects")->fetch_assoc()['count'];
                    ?>
                    <h3><?php echo $subjectCount; ?></h3>
                    <p>Subjects</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div class="stat-info">
                    <?php
                    $announcementCount = $conn->query("SELECT COUNT(*) as count FROM announcements WHERE is_active = 1")->fetch_assoc()['count'];
                    ?>
                    <h3><?php echo $announcementCount; ?></h3>
                    <p>Active Announcements</p>
                </div>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3><i class="fas fa-user-plus"></i> Quick Actions</h3>
                <div class="action-buttons">
                    <a href="manage-students.php" class="action-btn">
                        <i class="fas fa-user-graduate"></i>
                        <span>Manage Students</span>
                    </a>
                    <a href="manage-subjects.php" class="action-btn">
                        <i class="fas fa-book-open"></i>
                        <span>Manage Subjects</span>
                    </a>
                    <a href="announcements-admin.php" class="action-btn">
                        <i class="fas fa-bullhorn"></i>
                        <span>Post Announcement</span>
                    </a>
                    <a href="send-messages.php" class="action-btn">
                        <i class="fas fa-envelope"></i>
                        <span>Send Messages</span>
                    </a>
                </div>
            </div>
            
            <div class="dashboard-card">
                <h3><i class="fas fa-history"></i> Recent Activity</h3>
                <div class="activity-list">
                    <?php
                    $activityQuery = "SELECT al.*, u.full_name 
                                     FROM activity_logs al 
                                     LEFT JOIN users u ON al.user_id = u.id 
                                     ORDER BY al.created_at DESC LIMIT 5";
                    $activityResult = $conn->query($activityQuery);
                    
                    if ($activityResult->num_rows > 0):
                        while($activity = $activityResult->fetch_assoc()):
                    ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="activity-details">
                                <p><?php echo htmlspecialchars($activity['description']); ?></p>
                                <small>
                                    <?php echo htmlspecialchars($activity['full_name']); ?> • 
                                    <?php echo date('M j, g:i a', strtotime($activity['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    else:
                        echo "<p>No recent activity</p>";
                    endif;
                    ?>
                </div>
            </div>
        </div>
        
        <div class="dashboard-card">
            <h3><i class="fas fa-chart-bar"></i> System Overview</h3>
            <div class="system-overview">
                <div class="overview-item">
                    <h4>Data Management</h4>
                    <ul>
                        <li><a href="manage-students.php">Student Records</a></li>
                        <li><a href="../teacher/view-class.php">Class Lists</a></li>
                        <li><a href="activity-logs.php">View All Logs</a></li>
                    </ul>
                </div>
                <div class="overview-item">
                    <h4>Communication</h4>
                    <ul>
                        <li><a href="announcements-admin.php">Create Announcements</a></li>
                        <li><a href="send-messages.php">Send SMS/Email</a></li>
                        <li><a href="../teacher/messages.php">View Messages</a></li>
                    </ul>
                </div>
                <div class="overview-item">
                    <h4>Reports</h4>
                    <ul>
                        <li><a href="#">Student Performance</a></li>
                        <li><a href="#">Class Reports</a></li>
                        <li><a href="#">Term Results</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
