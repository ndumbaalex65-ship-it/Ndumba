<?php
require_once 'includes/header.php';
$page_title = "Home";
?>

<main>
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h2>Welcome to <?php echo SCHOOL_NAME; ?></h2>
                <p>Excellence in Education Since 1995</p>
                <p class="motto">"Knowledge, Discipline, Success"</p>
            </div>
        </div>
    </section>

    <section class="results-search">
        <div class="container">
            <h2>Check Your Results</h2>
            <div class="search-box">
                <input type="text" id="studentNumber" placeholder="Enter your Student Number" required>
                <button onclick="searchResults()">Search Results</button>
            </div>
            <div id="resultsContainer" style="display: none;">
                <div id="resultsContent">
                    <!-- Results will be loaded here -->
                </div>
                <button onclick="downloadResultsPDF()" class="download-btn">
                    <i class="fas fa-download"></i> Download as PDF
                </button>
            </div>
        </div>
    </section>

    <section class="quick-info">
        <div class="container">
            <div class="info-grid">
                <div class="info-card">
                    <i class="fas fa-graduation-cap"></i>
                    <h3>Quality Education</h3>
                    <p>Primary and Secondary education with qualified teachers</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-book"></i>
                    <h3>Modern Curriculum</h3>
                    <p>Comprehensive curriculum aligned with national standards</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-users"></i>
                    <h3>Student Support</h3>
                    <p>Individual attention and guidance for all students</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Excellent Results</h3>
                    <p>Consistently high pass rates in national examinations</p>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
function searchResults() {
    const studentNumber = document.getElementById('studentNumber').value.trim();
    if (!studentNumber) {
        alert('Please enter your student number');
        return;
    }

    fetch(`api/search-results.php?student_number=${studentNumber}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('resultsContainer');
            const content = document.getElementById('resultsContent');
            
            if (data.error) {
                content.innerHTML = `<div class="error">${data.error}</div>`;
            } else {
                content.innerHTML = generateResultsHTML(data);
                container.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while fetching results');
        });
}

function generateResultsHTML(data) {
    return `
        <div class="results-header">
            <h3>${data.school_name}</h3>
            <h4>Academic Results - Term ${data.term}, ${data.year}</h4>
            <div class="student-info">
                <p><strong>Student:</strong> ${data.student.full_name}</p>
                <p><strong>Class:</strong> ${data.student.class}</p>
                <p><strong>Student No:</strong> ${data.student.student_number}</p>
            </div>
        </div>
        <table class="results-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Marks</th>
                    <th>Grade</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                ${data.subjects.map(subject => `
                    <tr>
                        <td>${subject.subject_name}</td>
                        <td>${subject.marks_obtained}/${subject.maximum_marks}</td>
                        <td>${subject.grade}</td>
                        <td>${getRemarks(subject.grade)}</td>
                    </tr>
                `).join('')}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: center;">
                        <strong>Overall Performance:</strong> ${data.overall_performance}
                    </td>
                </tr>
            </tfoot>
        </table>
    `;
}

function getRemarks(grade) {
    const remarks = {
        'A': 'Excellent',
        'B': 'Very Good',
        'C': 'Good',
        'D': 'Satisfactory',
        'F': 'Needs Improvement'
    };
    return remarks[grade] || '--';
}

function downloadResultsPDF() {
    const element = document.getElementById('resultsContent');
    const opt = {
        margin:       1,
        filename:     'student_results.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    
    html2pdf().set(opt).from(element).save();
}
</script>

<?php require_once 'includes/footer.php
