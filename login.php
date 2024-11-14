<?php

include "conn.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type,Authorization');



if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    $role = $_POST['action'];
    $name = $_POST['name'];
    $password = $_POST['password'];

    try {
        if ($role === 'individualstudent') {
            $sql = "SELECT * FROM individual_student WHERE name = :name";
        } elseif ($role === 'trainer') {
            $sql = "SELECT * FROM trainer WHERE name = :name";
        } else {
            echo json_encode(['Error_message' => 'Invalid Login']);

        }


        $stmt = $conn->prepare($sql);
        $stmt->execute(["name" => $name]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['password'] === $password) {
            echo json_encode(value: ['status'=>1,'user' => $user]);
        } else {
            echo json_encode(['status'=>0,'message' => 'something Error']);
        }

    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'invalid request']);
}


?>