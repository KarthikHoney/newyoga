<?php

include "../conn.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// $input = json_decode(file_get_contents('php://input'));

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['action']) && $_POST['action'] === 'create') {

    if (isset($_POST['roll'], $_POST['enroll'], $_POST['name'], $_POST['parentname'], $_POST['gmail'], $_POST['dob'], $_POST['password'], $_POST['wnumber'], $_POST['number'], $_POST['address'])) {


        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

            $imageName = uniqid() . "-" . basename($_FILES['image']['name']);
            $targetFolder = 'uploads/';
            $folderPath = '/admin/';
            $targetFile = $targetFolder . $imageName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = $folderPath . $targetFile;
            } else {
                $response = ['status' => 0, 'message' => 'Failed to upload image.'];
                exit;
            }
        } else {
            $response = ['status' => 0, 'message' => 'Image uploaded failed or invalid'];
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
        $admin = "admin";


        $sql = "INSERT INTO individual_student (image,roll,enroll,name, registeredBy, parentname, gmail, dob, password, wnumber, number, address) VALUES (:image,:roll,:enroll,:name, :registeredBy, :parentname, :gmail, :dob, :password, :wnumber, :number, :address)";

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
        $stmt->bindParam(':registeredBy', $admin, PDO::PARAM_STR);
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
            echo json_encode($response);
        } else {
            echo json_encode(['message' => 'Failed to insert data.']);
        }

    } else {
        // Missing parameters
        echo json_encode(['status' => 0, 'message' => 'Missing parameters']);
    }
} else {
    echo json_encode(['status' => 0, 'message' => 'Invalid request']);
}

?>
