

<?php

include '../conn.php';


header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$input = json_decode(file_get_contents("php://input"));
$gradeId = isset($input->gradeId) ? intval($input->gradeId) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input->action) && $input->action === "getGrade") {

    $sql = "SELECT * FROM gradePrice ";
    $stmt = $conn->prepare($sql);

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($result) {
        echo json_encode(["status" => 1, "grade" => $result]);
    } else {
        echo json_encode(["status" => 0, "message" => "Failed to fetched"]);
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input->action) && $input->action === "getGradeId") {

    $sql = "SELECT * FROM gradePrice WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $gradeId, PDO::PARAM_INT);

    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        echo json_encode(["status" => 1, "grade" => $result]);
    } else {
        echo json_encode(["status" => 0, "message" => "Failed to fetched"]);
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input->action) && $input->action === "updateGrade") {
    $gradeId = $input->gradeId;
    $studentPrice = $input->studentPrice;
    $trainerPrice = $input->TrainerPrice;
    $grade = $input->grade;

    $sql = "UPDATE gradePrice SET studentPrice=:studentPrice, TrainerPrice=:TrainerPrice, grade=:grade WHERE id=:id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':studentPrice', $studentPrice, PDO::PARAM_INT);
    $stmt->bindParam(':TrainerPrice', $trainerPrice, PDO::PARAM_INT);
    $stmt->bindParam(':grade', $grade, PDO::PARAM_STR);
    $stmt->bindParam(':id', $gradeId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["status" => 1, "data" => [
            "id" => $gradeId,
            "studentPrice" => $studentPrice,
            "TrainerPrice" => $trainerPrice,
            "grade" => $grade
        ]]);
    } else {
        echo json_encode(["status" => 0, "message" => "Update failed"]);
    }
}
 else {
    echo json_encode(['message' => 'invalid method you entered']);
}




?>