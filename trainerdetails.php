<?php

include 'conn.php';

header('Content-Type:application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$input = json_decode(file_get_contents('php://input'));

$trainerId = isset($input->userId) ? intval($input->userId) : null;

if (!$trainerId) {
    echo json_encode(['message' => 'No Trainer Id']);
    exit();
}

try {

    $sql = "SELECT * FROM trainer WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $trainerId, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $trainerData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($trainerData) {
            echo json_encode($trainerData);
        } else {
            echo json_encode(['message' => 'No Trainer Data']);
        }
    } else {
        echo json_encode(['message' => 'Query Execution Failed']);
    }

} catch (Exception $e) {
    echo json_encode(['message' => $e->getMessage()]);
}






?>