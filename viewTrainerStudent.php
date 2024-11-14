<?php

include "conn.php";

header("Content-Type: Application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$input = json_decode(file_get_contents('php://input'));
$trainerId = isset($input->trainerId) ? intval($input->trainerId) : null;

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    try {
        $sql = "SELECT * FROM individual_student WHERE trainer_id =:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $trainerId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode(['status' => 1, 'message' => $result]);
        } else {
            echo json_encode(['error' => 'failed to select data']);
        }

    } catch (Exception $e) {
        echo json_encode('Error:' . $e->getMessage());
    }

} else {
    echo json_encode(['message' => 'Invalid Request']);
}




?>