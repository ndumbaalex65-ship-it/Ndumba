<?php
require_once 'includes/header.php';
$page_title = "Contact Us";
?>

<main>
    <section class="contact-section">
        <div class="container">
            <h2>Contact Us</h2>
            
            <div class="contact-grid">
                <div class="contact-info">
                    <h3>School Information</h3>
                    
                    <div class="contact-details">
                        <p><i class="fas fa-school"></i> 
                            <strong>School Name:</strong> <?php echo SCHOOL_NAME; ?>
                        </p>
                        <p><i class="fas fa-map-marker-alt"></i> 
                            <strong>Address:</strong> <?php echo SCHOOL_ADDRESS; ?>
                        </p>
                        <p><i class="fas fa-envelope"></i> 
                            <strong>Postal Address:</strong> P.O. Box 14002, Manyinga
                        </p>
                        <p><i class="fas fa-phone"></i> 
                            <strong>Phone:</strong> <?php echo SCHOOL_PHONE; ?>
                        </p>
                        <p><i class="fas fa-envelope"></i> 
                            <strong>Email:</strong> <?php echo SCHOOL_EMAIL; ?>
                        </p>
                    </div>
                    
                    <h3>School Hours</h3>
                    <div class="contact-details">
                        <p><strong>Monday - Friday:</strong> 7:30 AM - 3:30 PM</p>
                        <p><strong>Saturday:</strong> 8:00 AM - 12:00 PM (Extra classes)</p>
                        <p><strong>Sunday:</strong> Closed</p>
                    </div>
                    
                    <h3>Location</h3>
                    <div class="map-placeholder">
                        <p><i class="fas fa-map"></i> Map of Manyinga District</p>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h3>Send us a Message</h3>
                    <form id="contactForm">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <select id="subject" name="subject" required>
                                <option value="">Select a subject</option>
                                <option value="admission">Admission Inquiry</option>
                                <option value="academic">Academic Matters</option>
                                <option value="results">Results Inquiry</option>
                                <option value="general">General Information</option>
                                <option value="feedback">Feedback/Suggestions</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn-login">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // In a real application, you would send this data to a server
    // For now, we'll just show a success message
    alert('Thank you for your message! We will get back to you soon.');
    this.reset();
});
</script>

<?php require_once 'includes/footer.php'; ?>
