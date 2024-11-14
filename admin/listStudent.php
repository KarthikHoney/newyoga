<?php

include "../conn.php";

header("Content-Type: Application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$input = json_decode(file_get_contents("php://input"));


if($_SERVER["REQUEST_METHOD"] === "POST" && isset($input->action) && $input->action ==="listStudent" ){

    $sql = "SELECT * FROM individual_student";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if($result){
        echo json_encode(["status" => 1,"listStudent" => $result]);
    }else{
        echo json_encode(["status" => 0,"message" => "Failed to fetch Student"]);
    }

}else{
    echo json_encode(["error" => "Invalid request"]);
}

?>