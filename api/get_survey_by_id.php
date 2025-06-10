<?php
header('Content-Type: application/json');

// Direct DB connection (without db.php)
$host = 'localhost';
$db   = 'eduvote';         // Replace with your actual DB name
$user = 'root';        // Default XAMPP user
$pass = '';            // Default XAMPP password (empty)
$conn = new mysqli($host, $user, $pass, $db);

// Connection error handling
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$surveyId = isset($_GET['survey_id']) ? intval($_GET['survey_id']) : 0;
if ($surveyId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid survey ID']);
    exit;
}

// Fetch survey
$surveyResult = $conn->query("SELECT * FROM surveys WHERE id = $surveyId");
if ($surveyResult && $surveyResult->num_rows > 0) {
    $survey = $surveyResult->fetch_assoc();

    // Fetch questions
    $questions = [];
    $qResult = $conn->query("SELECT * FROM survey_questions WHERE survey_id = $surveyId");
    while ($qRow = $qResult->fetch_assoc()) {
        $qId = $qRow['id'];

        // Fetch options for each question
        $optResult = $conn->query("SELECT * FROM survey_options WHERE question_id = $qId");
        $options = [];
        while ($optRow = $optResult->fetch_assoc()) {
            $options[] = [
                'id' => $optRow['id'],
                'option_text' => $optRow['option_text']
            ];
        }

        $questions[] = [
            'id' => $qId,
            'question_text' => $qRow['question_text'],
            'options' => $options
        ];
    }

    echo json_encode([
        'success' => true,
        'survey' => [
            'id' => $survey['id'],
            'title' => $survey['title'],
            'created_at' => $survey['created_at'],
            'questions' => $questions
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Survey not found']);
}
$conn->close();
?>
