<?php

include 'conn.php';

header('Content-Type: Application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');


$input = json_decode(file_get_contents('php://input'));
$studentId = isset($input->studentId) ? intval($input->studentId) : null;


try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $sql = "SELECT  count(*) as al_grade  FROM  grade WHERE student_id=:studentID  ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':studentID', $studentId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_COLUMN);

        if ($result) {
            echo json_encode(['status' => 1, 'total_grade' => $result]);
        }
    } else {
        echo json_encode(['message' => 'No studentId Provided']);
    }
} catch (Exception $th) {
    echo json_encode(['errorMsg:' => 'Not Getting Details']);
}


?>