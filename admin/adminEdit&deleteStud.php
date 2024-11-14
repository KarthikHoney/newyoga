<?php

include "../conn.php";

header("Content-Type: Application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// $input = json_decode(file_get_contents('php://input'));
$studentId = isset($_POST['studentId']) ? intval($_POST['studentId']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {




    if ($_POST['action'] === 'update') {
        $name = $_POST['name'];
        $parentname = $_POST['parentname'];
        $gmail = $_POST['gmail'];
        $wnumber = $_POST['wnumber'];
        $number = $_POST['number'];
        $address = $_POST['address'];
        $password  =$_POST['password'];
        $dob = $_POST['dob'];
        $imagePath = null;


        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image'];
            $targetDir = "uploads/";
            $targetDir1 = "/admin/";
            $targetPath = $targetDir . basename($image['name']);

            if (!move_uploaded_file($image["tmp_name"], $targetPath)) {

                echo json_encode(["status" => 0, "message" => "Failed to upload image"]);
                exit;
            }
            $imagePath = uniqid(). $targetDir1 . $targetPath;


        } else if (isset($_POST['exitingImage'])) {
            $imagePath = $_POST['exitingImage'];
        }




        $sql = "UPDATE  individual_student SET name = :name, 
                    parentname = :parentname, 
                    gmail = :gmail,
                    wnumber = :wnumber,
                    number = :number,
                    address = :address,
                    password= :password,
                    dob = :dob,
                    image  = :imagePath
                    WHERE id = :id ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":parentname", $parentname, PDO::PARAM_STR);
        $stmt->bindParam(":gmail", $gmail, PDO::PARAM_STR);
        $stmt->bindParam(":wnumber", $wnumber, PDO::PARAM_INT);
        $stmt->bindParam(":password", $password, PDO::PARAM_STR);
        $stmt->bindParam(":number", $number, PDO::PARAM_INT);
        $stmt->bindParam(":id", $studentId, PDO::PARAM_INT);
        $stmt->bindParam(":address", $address, PDO::PARAM_STR);
        $stmt->bindParam(":dob", $dob, PDO::PARAM_STR);
        $stmt->bindParam(":imagePath", $imagePath, PDO::PARAM_STR);


        if ($stmt->execute()) {
            echo json_encode(['status' => 1, 'message' => 'successfully updated']);
        } else {
            echo json_encode(['status' => 0, 'message' => 'failed to  updated']);

        }
    } elseif ($_POST['action'] === "delete") {

        $sql = "DELETE FROM individual_student WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $studentId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["status" => 1, "message" => "Student deleted successfully"]);
        } else {
            echo json_encode(["status" => 0, "message" => "Failed to delete student"]);

        }
    } else if ($_POST['action'] === "getStudent") {

        $sql = "SELECT * FROM individual_student WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $studentId, PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            echo json_encode(["status" => 1, "student" => $result]);
        } else {
            echo json_encode(["status" => 0, "message" => "Failed to fetched"]);
        }

    } else {
        echo json_encode(['message' => 'invalid action ']);

    }

} else {
    echo json_encode(['message' => 'invalid Reque']);
}

?>