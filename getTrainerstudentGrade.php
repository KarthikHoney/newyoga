<?php
include "conn.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$input = json_decode(file_get_contents('php://input'));
$studentId = isset($input->studentId) ? intval($input->studentId) : null;
$trainerId = isset($input->trainerId) ? intval($input->trainerId) : null;

if ($_SERVER["REQUEST_METHOD"] === "POST" || $studentId || $trainerId) {
    try {
        $sql = "SELECT * FROM individual_student WHERE id = :studentId AND trainer_id = :trainerId";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":studentId", $studentId, PDO::PARAM_INT);
        $stmt->bindParam(":trainerId", $trainerId, PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            echo json_encode(["status" => 1, "student" => $result]);
        } else {
            echo json_encode(["status" => 0, "message" => "Student not found"]);
        }
    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => 0, "message" => "Invalid Request"]);
}
?>
