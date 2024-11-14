<?php


include "conn.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$input = json_decode(file_get_contents('php://input'));

$studentId = isset(
    $input->userId
) ? intval($input->userId) : null;

if (!$studentId) {
    echo json_encode(['error' => 'No student ID Provided']);
    exit();
}

try {
    $sql = "SELECT * FROM individual_student WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $studentId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $studentdata = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($studentdata) {
            echo json_encode($studentdata);
        } else {
            echo json_encode(['error' => 'No Student Data']);
        }
    } else {
        echo json_encode(['error' => 'Query execution failed']);
    }
} catch (Exception $e) {

    echo json_encode(['error' => $e->getMessage()]);
}
?>
