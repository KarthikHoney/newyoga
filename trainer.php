<?php
include 'conn.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['action']) && $_POST['action'] === 'create') {
    try {
        if (isset($_POST['name'], $_POST['studio'], $_POST['gmail'], $_POST['password'], $_POST['number'], $_POST['wnumber'], $_POST['address'])) {
            if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK ){
                $maxFileSize  = 800 * 1024;
            if($_FILES['image']['size']>$maxFileSize){
                $response = ['status'=>0,'message'=>'Image File greater than 800KB'];
            }
                $imageName = uniqid()."-".basename($_FILES['image']['name']);
                $uploadPath = "tuploads/";
                $targetFile = $uploadPath.$imageName;
                if(move_uploaded_file($_FILES['image']['tmp_name'],$targetFile)){
                    $imagePath = $targetFile;
                }else{
                    echo json_encode(["status" => 0, "message" => "can't upload image"]);
                }
            }else{
                echo json_encode(["status" => 0,"message" => "invalid File"]);
            }

            $name = $_POST['name'];
            $studio = $_POST['studio'];
            $gmail = $_POST['gmail'];
            $password = $_POST['password'];
            $number = $_POST['number'];
            $wnumber = $_POST['wnumber'];
            $address = $_POST['address'];

            $sql = "INSERT INTO trainer (timage, name,studio,gmail,password,number,wnumber,address) VALUES (:timage, :name,:studio,:gmail,:password,:number,:wnumber,:address)";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute(['timage' => $imagePath, 'name' => $name, 'studio' => $studio, 'address' => $address, 'number' => $number, 'wnumber' => $wnumber, 'gmail' => $gmail, 'password' => $password]);

            if ($result) {
                $response = ['status' => 1, 'message' => 'Record Created'];

            } else {
                $response = ['status' => 0, 'message' => ' Record Not Created'];
            }

            echo json_encode($response);


        } else {
            echo json_encode(['error' => 'Missing Parameter']);
        }
    } catch (Exception $e) {
        echo json_encode('message' . $e->getMessage());
    }
} else {
    echo json_encode(['message' => 'Invalid Request']);
}


?>