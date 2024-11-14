<?php
include "conn.php";
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$input = json_decode(file_get_contents('php://input'));

$student_Id = isset($input->studentId) ? intval($input->studentId) : null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($input->action) && $input->action === 'grade') {
    if (isset($input->grade, $input->payment)) {
        $grade = intval($input->grade);
        $payment = intval($input->payment);

        try {
            $sql = "INSERT INTO grade (student_id,  grade, payment) VALUES (:studentId,  :grade, :payment)";

            $stmt = $conn->prepare($sql);

            $stmt->bindParam(":studentId", $student_Id, PDO::PARAM_INT);
            $stmt->bindParam(":grade", $grade, PDO::PARAM_INT);
            $stmt->bindParam(":payment", $payment, PDO::PARAM_INT);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $response = [
                    'status' => 1,
                    'newGrade' => [
                        'date' => date('Y-m-d'),
                        'grade' => $grade,
                        'payment' => $payment
                    ]
                ];
                echo json_encode($response);
            } else {
                echo json_encode(['message' => 'Failed to insert data.']);
            }

        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['message' => 'Required fields are missing.']);
    }
} else if ($_SERVER["REQUEST_METHOD"] === "POST" && $student_Id) {
    $sql = "SELECT * FROM grade WHERE student_id = :studentId";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":studentId", $student_Id, PDO::PARAM_INT);
    $stmt->execute();
    $gradess = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 1, 'gradess' => $gradess]);
}
else{
    echo json_encode(["error" => "invalid request"]);
}
?>