<?php

include "../conn.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$input = json_decode(file_get_contents('php://input'));

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['action']) && $_POST['action'] === 'create') {

    if (isset($_POST['name'], $_POST['studio'], $_POST['gmail'], $_POST['password'], $_POST['wnumber'], $_POST['number'], $_POST['address'])) {

        if (isset($_FILES['timage']) && $_FILES['timage']['error'] === UPLOAD_ERR_OK) {

            $imageName = uniqid() . "-" . basename($_FILES['timage']['name']);
            $targetFolder = 'tuploads/';
            $folderPath = '/admin/';
            $targetFile = $targetFolder . $imageName;

            if (move_uploaded_file($_FILES['timage']['tmp_name'], $targetFile)) {
                $imagePath = $folderPath . $targetFile;
            } else {
                $response = ['status' => 0, 'message' => 'Failed to upload image.'];
                exit;
            }
        } else {
            $response = ['status' => 0, 'message' => 'Image uploaded failed or invalid'];
        }

        $name = $_POST['name'];
        $studio = $_POST['studio'];
        $gmail = $_POST['gmail'];
        $password = $_POST['password'];
        $wnumber = $_POST['wnumber'];
        $number = $_POST['number'];
        $address = $_POST['address'];
        $admin = "admin";

        $sql = "INSERT INTO trainer (timage,name, registeredBy, studio, gmail,  password, wnumber, number, address) VALUES (:timage,:name, :registeredBy, :studio, :gmail, :password, :wnumber, :number, :address)";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':timage', $imagePath, PDO::PARAM_STR);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':studio', $studio, PDO::PARAM_STR);
        $stmt->bindParam(':gmail', $gmail, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':wnumber', $wnumber, PDO::PARAM_INT);
        $stmt->bindParam(':number', $number, PDO::PARAM_INT);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':registeredBy', $admin, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $response = [
                'status' => 1,
                'newTrainer' => [
                    'date' => date('Y-m-d'),
                    'name' => $name,
                    'studio' => $studio,
                    'gmail' => $gmail,
                    'password' => $password,
                    'number' => $number,
                    'wnumber' => $wnumber,
                    'address' => $address,
                    'timage' => $imagePath
                ]
            ];
            echo json_encode($response);
        } else {
            echo json_encode(['message' => 'Failed to insert data.']);
        }

    } else {
        echo json_encode(['status' => 0, 'message' => 'Missing parameters']);
    }
} else {
    echo json_encode(['status' => 0, 'message' => 'Invalid request']);
}

?>
