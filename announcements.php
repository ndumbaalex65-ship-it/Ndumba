<?php
require_once 'includes/header.php';
require_once 'includes/db_connect.php';

$db = new Database();
$conn = $db->getConnection();

$page_title = "Announcements";

// Fetch announcements
$query = "SELECT a.*, u.full_name 
          FROM announcements a 
          LEFT JOIN users u ON a.posted_by = u.id 
          WHERE a.is_active = 1 
          ORDER BY a.posted_at DESC";
$result = $conn->query($query);
?>

<main>
    <section class="announcements">
        <div class="container">
            <h2>School Announcements</h2>
            
            <?php if ($result->num_rows > 0): ?>
                <div class="announcements-list">
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="announcement-card">
                            <div class="announcement-header">
                                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                                <span class="announcement-date">
                                    <?php echo date('F j, Y, g:i a', strtotime($row['posted_at'])); ?>
                                </span>
                            </div>
                            <div class="announcement-body">
                                <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                            </div>
                            <?php if($row['full_name']): ?>
                                <div class="announcement-footer">
                                    <small>Posted by: <?php echo htmlspecialchars($row['full_name']); ?></small>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-announcements">
                    <p>No announcements available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
