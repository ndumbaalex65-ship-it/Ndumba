<?php
header('Content-Type: application/json');
require_once '../includes/db_connect.php';

$db = new Database();
$conn = $db->getConnection();

$student_number = isset($_GET['student_number']) ? $conn->real_escape_string($_GET['student_number']) : '';

if (empty($student_number)) {
    echo json_encode(['error' => 'Student number is required']);
    exit;
}

// Get student details
$studentQuery = "SELECT * FROM students WHERE student_number = '$student_number'";
$studentResult = $conn->query($studentQuery);

if ($studentResult->num_rows == 0) {
    echo json_encode(['error' => 'Student not found']);
    exit;
}

$student = $studentResult->fetch_assoc();

// Get latest term and year
$latestQuery = "SELECT term, year FROM marks 
                WHERE student_number = '$student_number' 
                ORDER BY year DESC, term DESC LIMIT 1";
$latestResult = $conn->query($latestQuery);

if ($latestResult->num_rows == 0) {
    echo json_encode(['error' => 'No results found for this student']);
    exit;
}

$latest = $latestResult->fetch_assoc();
$term = $latest['term'];
$year = $latest['year'];

// Get marks for the latest term
$marksQuery = "SELECT m.*, s.subject_name 
               FROM marks m 
               JOIN subjects s ON m.subject_code = s.subject_code
               WHERE m.student_number = '$student_number' 
               AND m.term = '$term' 
               AND m.year = '$year'";
$marksResult = $conn->query($marksQuery);

$subjects = [];
$total_marks = 0;
$count = 0;

while($row = $marksResult->fetch_assoc()) {
    // Calculate grade
    $percentage = ($row['marks_obtained'] / $row['maximum_marks']) * 100;
    $row['grade'] = calculateGrade($percentage);
    $row['percentage'] = $percentage;
    
    $subjects[] = $row;
    $total_marks += $row['marks_obtained'];
    $count++;
}

// Calculate overall performance
$average = $count > 0 ? $total_marks / $count : 0;
$overall_performance = calculateGrade(($average / 100) * 100);

$response = [
    'school_name' => 'Mangana Primary/Secondary School',
    'school_address' => 'Manyinga District, P.O. Box 14002',
    'term' => $term,
    'year' => $year,
    'student' => $student,
    'subjects' => $subjects,
    'average' => round($average, 2),
    'overall_performance' => $overall_performance
];

echo json_encode($response);

function calculateGrade($percentage) {
    if ($percentage >= 80) return 'A';
    if ($percentage >= 70) return 'B';
    if ($percentage >= 60) return 'C';
    if ($percentage >= 50) return 'D';
    return 'F';
}
?>
