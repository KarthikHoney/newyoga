<?php

include "../conn.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type,Authorization');

$trainer_Id = isset($_POST['trainer_Id']) ? intval($_POST['trainer_Id']) : null;
$output = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'create') {
    if (isset($_POST['roll'], $_POST['enroll'], $_POST['name'], $_POST['parentname'], $_POST['gmail'], $_POST['dob'], $_POST['password'], $_POST['wnumber'], $_POST['number'], $_POST['address'])) {
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageName = uniqid() . "-" . basename($_FILES['image']['name']);
            $targetFolder = 'uploads/';
            $folderPath = '/admin/';
            $targetFile = $targetFolder . $imageName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = $folderPath . $targetFile;
            } else {
                $output = ['status' => 0, 'message' => 'Failed to upload image.'];
                echo json_encode($output);
                exit;
            }
        } else {
            $output = ['status' => 0, 'message' => 'Image upload failed or invalid'];
            echo json_encode($output);
            exit;
        }
        
        $roll = $_POST['roll'];
        $enroll = $_POST['enroll'];
        $name = $_POST['name'];
        $parentname = $_POST['parentname'];
        $dob = $_POST['dob'];
        $gmail = $_POST['gmail'];
        $password = $_POST['password'];
        $wnumber = $_POST['wnumber'];
        $number = $_POST['number'];
        $address = $_POST['address'];
        $admin = "admin";

        try {
            $sql = "INSERT INTO individual_student (image, roll, enroll, name, registeredBy, trainer_id, dob, parentname, gmail, password, wnumber, number, address)
                    VALUES (:image, :roll, :enroll, :name, :registeredBy, :trainer_Id, :dob, :parentname, :gmail, :password, :wnumber, :number, :address)";
            
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':image', $imagePath, PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':roll', $roll, PDO::PARAM_STR);
            $stmt->bindParam(':enroll', $enroll, PDO::PARAM_STR);
            $stmt->bindParam(':parentname', $parentname, PDO::PARAM_STR);
            $stmt->bindParam(':gmail', $gmail, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':wnumber', $wnumber, PDO::PARAM_INT);
            $stmt->bindParam(':number', $number, PDO::PARAM_INT);
            $stmt->bindParam(':address', $address, PDO::PARAM_STR);
            $stmt->bindParam(':registeredBy', $admin, PDO::PARAM_STR);
            $stmt->bindParam(':dob', $dob, PDO::PARAM_STR);
            $stmt->bindParam(':trainer_Id', $trainer_Id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $output = [
                    'status' => 1,
                    'newStudent' => [
                        'date' => date('Y-m-d'),
                        'name' => $name,
                        'parentname' => $parentname,
                        'dob' => $dob,
                        'gmail' => $gmail,
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
                $output = ['status' => 0, 'message' => 'Failed to insert data.'];
            }
    
        } catch (Exception $e) {
            $output = ['status' => 0, 'error' => $e->getMessage()];
        }
    } 
} else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'fetchStudent') {

    if ($trainer_Id) {
        $sql = "SELECT * FROM individual_student WHERE trainer_id = :trainer_Id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":trainer_Id", $trainer_Id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $output = ['status' => 1, 'viewStudent' => $result];
    } else {
        $output = ['status' => 0, 'message' => 'Missing trainer_Id'];
    }
} else {
    $output = ['status' => 0, 'message' => 'Required fields are missing.'];
}

// Ensure response is output as JSON
echo json_encode($output);
?>
