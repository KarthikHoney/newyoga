<?php

include "conn.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

function generateUniqueNumber($prefix, $table, $column, $conn)
{
    do {
        $number = $prefix . rand(1000, 9999);
        $stmt = $conn->prepare("SELECT COUNT(*) FROM $table WHERE $column = ?");
        $stmt->execute([$number]);
        $exists = $stmt->fetchColumn() > 0;
    } while ($exists);
    return $number;
}

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getNumbers') {
    $rollNumber = generateUniqueNumber('YGR', 'individual_student', 'roll', $conn);
    $enrollNumber = generateUniqueNumber('YGE', 'individual_student', 'enroll', $conn);

    $response = [
        'status' => 1,
        'message' => 'Number generated',
        'roll' => $rollNumber,
        'enroll' => $enrollNumber
    ];
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    if (
        isset(
        $_POST['name'],
        $_POST['roll'],
        $_POST['enroll'],
        $_POST['parentname'],
        $_POST['gmail'],
        $_POST['dob'],
        $_POST['password'],
        $_POST['wnumber'],
        $_POST['number'],
        $_POST['address']
    )
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
        $registeredBy = 'Individual';
        $parentname = $_POST['parentname'];
        $gmail = $_POST['gmail'];
        $dob = $_POST['dob'];
        $password = $_POST['password'];
        $wnumber = $_POST['wnumber'];
        $number = $_POST['number'];
        $address = $_POST['address'];

        $sql = "INSERT INTO individual_student 
                (image,roll, enroll, name, registeredBy, parentname, gmail, dob, password, wnumber, number, address) 
                VALUES (:image,:roll, :enroll, :name, :registeredBy, :parentname, :gmail, :dob, :password, :wnumber, :number, :address)";

        $stmt = $conn->prepare($sql);

        $result = $stmt->execute([
            "roll" => $roll,
            "enroll" => $enroll,
            "name" => $name,
            "registeredBy" => $registeredBy,
            "parentname" => $parentname,
            "gmail" => $gmail,
            "dob" => $dob,
            "password" => $password,
            "wnumber" => $wnumber,
            "number" => $number,
            "address" => $address,
            "image" => $imagePath
        ]);
        if ($result) {
            $response = ['status' => 1, 'message' => 'Record created successfully'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to create record'];
        }
    } else {
        $response = ['status' => 0, 'message' => 'Missing parameters'];
    }
} else {
    $response = ['status' => 0, 'message' => 'Invalid request'];
}

echo json_encode($response);
exit();
?>
