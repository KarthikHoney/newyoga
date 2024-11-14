<?php

include '../conn.php';

header('Content-Type: Application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$input = json_decode(file_get_contents('php://input'));

$admin = "admin";

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (isset($input->queryType)) {
            if ($input->queryType === 'student') {
                // Query to count students registered by admin
                $sql = "SELECT count(*) as al_student FROM individual_student";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

                if ($result) {
                    echo json_encode(['status' => 1, 'total_student' => $result]);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'No students found']);
                }
            } else if ($input->queryType === 'trainer') {
                // Query to count trainers registered by admin
                $sql = "SELECT count(*) as al_trainer FROM trainer";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_COLUMN);

                if ($result) {
                    echo json_encode(['status' => 1, 'total_trainer' => $result]);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'No trainers found']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid query type']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'No query type provided']);
        }
    } else {
        echo json_encode(['status' => 0, 'message' => 'Invalid request method']);
    }
} catch (Exception $th) {
    echo json_encode(['errorMsg' => 'Error fetching details']);
}

?>
