<?php

include "conn.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type,Authorization');

$input = json_decode(file_get_contents('php://input'));

$studentId = isset($input->studentId) ? intval($input->studentId) : null;
$trainerId = isset($input->trainerId) ? intval($input->trainerId) : null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($input->action) && $input->action === 'grade') {
    if (isset($input->grade, $input->payment)) {
        $grade = intval($input->grade);
        $payment = intval($input->payment);

        try {
            $sql = "INSERT INTO grade (student_id, trainer_id, grade, payment) VALUES (:studentId, :trainerId, :grade, :payment)";
            
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(":studentId", $studentId, PDO::PARAM_INT);
            $stmt->bindParam(":trainerId", $trainerId, PDO::PARAM_INT);
            $stmt->bindParam(":grade", $grade, PDO::PARAM_INT);
            $stmt->bindParam(":payment", $payment, PDO::PARAM_INT);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Returning a single JSON response with all required details
                $response = [
                    'status' => 1,
                    'data' => [
                        'date' => date('Y-m-d'),
                        'grade' => $grade,
                        'payment' => $payment
                    ]
                ];
                echo json_encode($response);
            } else {
                echo json_encode(['status' => 0, 'message' => 'Failed to insert data.']);
            }

        } catch (Exception $e) {
            echo json_encode(['status' => 0, 'error' => $e->getMessage()]);
        }
    } 
} else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($input->action) && $input->action === 'fetchGrades') {
    // Make sure to extract the studentId and trainerId from $input
    $studentId = $input->studentId;
    $trainerId = $input->trainerId;

    if ($studentId && $trainerId) {
        $sql = "SELECT * FROM grade WHERE student_id = :studentId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":studentId", $studentId, PDO::PARAM_INT);
        $stmt->execute();
        $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ensure proper naming convention for 'grades'
        echo json_encode(['status' => 1, 'grades' => $grades]);
    } else {
        // Handle error if IDs are not provided
        echo json_encode(['status' => 0, 'message' => 'Missing studentId or trainerId']);
    }
} else {
    echo json_encode(['status' => 0, 'message' => 'Required fields are missing.']);
}

?>