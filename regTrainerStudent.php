<?php

include "conn.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// $input = json_decode(file_get_contents('php://input'));
$trainerId = isset($_POST['trainerId']) ? intval($_POST['trainerId']) : null;
$trainerName = $_POST['trainerName'] ?? null;

$response = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'create') {
    if (
        !empty($_POST['name']) && !empty($_POST['parentname']) && !empty($_POST['gmail']) &&
        !empty($_POST['dob']) && !empty($_POST['password']) && !empty($_POST['wnumber']) &&
        !empty($_POST['number']) && !empty($_POST['address'])
    ) {


        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $maxFileSize  = 800 * 1024;
            if($_FILES['image']['size']>$maxFileSize){
                $response = ['status'=>0,'message'=>'Image File greater than 800KB'];
            }
            $imageName = uniqid() . "-" . basename($_FILES['image']['name']);
            $targetFolder = 'uploads/';
            $targetFile = $targetFolder . $imageName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = $targetFile;
                $response = ['status' => 1, 'message' => $imagePath];

            } else {
                $response = ['status' => 0, 'message' => 'Failed to upload image.'];
                echo json_encode($response);
                exit;
            }
        } else {
            $response = ['status' => 0, 'message' => 'Image upload failed or invalid'];
            echo json_encode($response);
            exit;
        }

        $roll = $_POST['roll'];
        $enroll = $_POST['enroll'];
        $name = $_POST['name'];
        $parentname = $_POST['parentname'];
        $gmail = $_POST['gmail'];
        $dob = $_POST['dob'];
        $password = $_POST['password'];
        $wnumber = $_POST['wnumber'];
        $number = $_POST['number'];
        $address = $_POST['address'];
        $trainerId = $_POST['trainerId'];
        $trainerName = $_POST['trainerName'];

        try {
            $sql = "INSERT INTO individual_student 
                (image,roll, enroll, registeredBy, name, parentname, trainer_id, gmail, dob, password, wnumber, number, address) 
                VALUES 
                ( :image,:roll, :enroll, :registeredBy, :name, :parentname, :trainerId, :gmail, :dob, :password, :wnumber, :number, :address)";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':image', $imagePath, PDO::PARAM_STR);
            $stmt->bindParam(':roll', $roll, PDO::PARAM_STR);
            $stmt->bindParam(':enroll', $enroll, PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':parentname', $parentname, PDO::PARAM_STR);
            $stmt->bindParam(':gmail', $gmail, PDO::PARAM_STR);
            $stmt->bindParam(':dob', $dob, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':wnumber', $wnumber, PDO::PARAM_INT);
            $stmt->bindParam(':number', $number, PDO::PARAM_INT);
            $stmt->bindParam(':address', $address, PDO::PARAM_STR);
            $stmt->bindParam(':trainerId', $trainerId, PDO::PARAM_INT);
            $stmt->bindParam(':registeredBy', $trainerName, PDO::PARAM_STR);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $response = [
                    'status' => 1,
                    'newStudent' => [
                        'date' => date('Y-m-d'),
                        'name' => $name,
                        'parentname' => $parentname,
                        'gmail' => $gmail,
                        'dob' => $dob,
                        'password' => $password,
                        'number' => $number,
                        'wnumber' => $wnumber,
                        'address' => $address,
                        'roll' => $roll,
                        'enroll' => $enroll,
                        'image' => $imagePath
                    ]
                ];
            } else {
                $response = ['status' => 0, 'message' => 'Failed to insert data.'];
            }
        } catch (PDOException $e) {
            $response = ['status' => 0, 'message' => 'Database error: ' . $e->getMessage()];
        }
    } else {
        $response = ['status' => 0, 'message' => 'Missing or empty parameters'];
    }
} else if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($trainerId)) {
    try {
        $sql = "SELECT * FROM individual_student WHERE trainer_id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $trainerId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            $response = ['status' => 1, 'newStudent' => $result];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to select data'];
        }
    } catch (Exception $e) {
        $response = ['status' => 0, 'message' => 'Error: ' . $e->getMessage()];
    }
} else {
    $response = ['status' => 0, 'message' => 'Invalid Request'];
}

echo json_encode($response);
?>
