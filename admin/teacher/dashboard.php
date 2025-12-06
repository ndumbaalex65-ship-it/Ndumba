<?php
require_once '../includes/header.php';
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

// Check if user is teacher
checkAuth('teacher');

$db = new Database();
$conn = $db->getConnection();

$page_title = "Teacher Dashboard";

// Get teacher's assigned subjects (you'll need to create an assignments table)
$teacher_id = $_SESSION['user_id'];
?>

<main class="dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h2>Welcome, <?php echo $_SESSION['full_name']; ?></h2>
            <p>Teacher Dashboard</p>
        </div>
        
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3><i class="fas fa-tasks"></i> Quick Actions</h3>
                <div class="action-buttons">
                    <a href="enter-marks.php" class="action-btn">
                        <i class="fas fa-edit"></i>
                        <span>Enter Marks</span>
                    </a>
                    <a href="view-class.php" class="action-btn">
                        <i class="fas fa-list"></i>
                        <span>View Class Lists</span>
                    </a>
                    <a href="messages.php" class="action-btn">
                        <i class="fas fa-comments"></i>
                        <span>Messages</span>
                    </a>
                </div>
            </div>
            
            <div class="dashboard-card">
                <h3><i class="fas fa-bell"></i> Notifications</h3>
                <div class="notifications-list">
                    <?php
                    // Fetch recent announcements
                    $announceQuery = "SELECT * FROM announcements 
                                     WHERE is_active = 1 
                                     ORDER BY posted_at DESC LIMIT 3";
                    $announceResult = $conn->query($announceQuery);
                    
                    if ($announceResult->num_rows > 0):
                        while($announce = $announceResult->fetch_assoc()):
                    ?>
                        <div class="notification-item">
                            <div class="notification-icon">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <div class="notification-details">
                                <p><strong><?php echo htmlspecialchars($announce['title']); ?></strong></p>
                                <p><?php echo substr(htmlspecialchars($announce['content']), 0, 100); ?>...</p>
                                <small><?php echo date('M j', strtotime($announce['posted_at'])); ?></small>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    else:
                        echo "<p>No new notifications</p>";
                    endif;
                    ?>
                </div>
            </div>
        </div>
        
        <div class="dashboard-card">
            <h3><i class="fas fa-chart-line"></i> Recent Marks Entered</h3>
            <div class="recent-marks">
                <?php
                $marksQuery = "SELECT m.*, s.full_name, sub.subject_name 
                              FROM marks m 
                              JOIN students s ON m.student_number = s.student_number
                              JOIN subjects sub ON m.subject_code = sub.subject_code
                              WHERE m.teacher_id = ?
                              ORDER BY m.entered_at DESC LIMIT 5";
                $stmt = $conn->prepare($marksQuery);
                $stmt->bind_param("i", $teacher_id);
                $stmt->execute();
                $marksResult = $stmt->get_result();
                
                if ($marksResult->num_rows > 0):
                ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Marks</th>
                                <th>Class</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($mark = $marksResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($mark['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($mark['subject_name']); ?></td>
                                    <td><?php echo $mark['marks_obtained']; ?>/100</td>
                                    <td><?php echo htmlspecialchars($mark['class']); ?></td>
                                    <td><?php echo date('M j', strtotime($mark['entered_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No marks entered yet. <a href="enter-marks.php">Enter marks now</a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
