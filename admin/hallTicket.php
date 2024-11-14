<?php
include "../conn.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type,Authorization');

$studentId = isset($_POST['studentId']) ? intval($_POST['studentId']) : null;
$gradeId = isset($_POST['gradeId']) ? intval($_POST['gradeId']) : null;

$response = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action']) && $_POST['action'] === "generateHallTicket" && $studentId && $gradeId) {

        if (empty($studentId) || empty($gradeId)) {
            $response = ["status" => 0, "message" => "Student ID or Grade ID is missing"];
            echo json_encode($response);
            exit;
        }

        $sql = "SELECT 
                    individual_student.name,
                    individual_student.roll,
                    individual_student.enroll,
                    individual_student.gmail,
                    individual_student.parentname,
                    individual_student.image,
                    grade.grade
                FROM 
                    individual_student
                JOIN 
                    grade ON individual_student.id = grade.student_id
                WHERE 
                    individual_student.id = :studentId AND grade.id = :gradeId";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':studentId', $studentId, PDO::PARAM_INT);
        $stmt->bindParam(':gradeId', $gradeId, PDO::PARAM_INT);

        $stmt->execute();
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            $response = ["status" => 0, "message" => "SQL error: " . $errorInfo[2]];
            echo json_encode($response);
            exit;
        }
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            $studentImagePath = __DIR__ . '/../uploads/' . basename($student['image']);
            // $studentImagePath = str_replace(' ','_',$studentImagePath);
            // $studentImagePath = str_replace('\\','/',$studentImagePath);

           
            if (!file_exists($studentImagePath)) {
                $response = ["status" => 0, "message" => "Image not found at path: " . $studentImagePath];
                echo json_encode($response);
                exit;
            }

            // Load hall ticket background
            $hallTicketImage = imagecreatefromjpeg(__DIR__ . DIRECTORY_SEPARATOR . 'hallTicket.jpeg');
            if (!$hallTicketImage) {
                $error = error_get_last();
                $response = ["status" => 0, "message" => "Error loading hall ticket image: " . $error['message']];
                echo json_encode($response);
                exit;
            }

            $textColorBlack = imagecolorallocate($hallTicketImage, 0, 0, 0);

            // Define font and text positions
            $fontSize = 26;
            $fontPath = __DIR__ . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . 'Roboto' . DIRECTORY_SEPARATOR . 'Roboto-Regular.ttf';
            if (!file_exists($fontPath)) {
                $response = ["status" => 0, "message" => "Font file not found"];
                echo json_encode($response);
                exit;
            }

            // Add text to the hall ticket
            imagettftext($hallTicketImage, $fontSize, 0, 600, 983, $textColorBlack, $fontPath, ucwords($student['name']));
            imagettftext($hallTicketImage, $fontSize, 0, 600, 789, $textColorBlack, $fontPath, $student['roll']);
            imagettftext($hallTicketImage, $fontSize, 0, 600, 885, $textColorBlack, $fontPath, $student['enroll']);
            imagettftext($hallTicketImage, $fontSize, 0, 600, 1160, $textColorBlack, $fontPath, $student['gmail']);
            imagettftext($hallTicketImage, $fontSize, 0, 600, 1070, $textColorBlack, $fontPath, ucwords($student['parentname']));
            imagettftext($hallTicketImage, $fontSize, 0, 600, 1260, $textColorBlack, $fontPath, $student['grade']);

            // Handle student image
            $mimeType = mime_content_type($studentImagePath);
            switch ($mimeType) {
                case 'image/jpeg':
                case 'image/jpg':
                    $studentImage = imagecreatefromjpeg($studentImagePath);
                    break;
                case 'image/png':
                    $studentImage = imagecreatefrompng($studentImagePath);
                    break;
                default:
                    $response = ["status" => 0, "message" => "Unsupported image format"];
                    echo json_encode($response);
                    exit;
            }

            // Copy student image onto hall ticket
            if ($studentImage) {
                imagecopyresampled($hallTicketImage, $studentImage, 860, 250, 0, 0, 200, 300, imagesx($studentImage), imagesy($studentImage));
                imagedestroy($studentImage);
            } else {
                $response = ["status" => 0, "message" => "Failed to load student image"];
                echo json_encode($response);
                exit;
            }

            // Generate base64 hall ticket
            ob_start();
            imagejpeg($hallTicketImage);
            $imageData = ob_get_clean();
            imagedestroy($hallTicketImage);
            $base64Image = base64_encode($imageData);
            $response = ["status" => 1, "hallTicket" => "data:image/jpeg;base64," . $base64Image];

        } else {
            $response = ["status" => 0, "message" => "Student not found"];
        }
    } else {
        $response = ["error" => "Invalid action"];
    }
} else {
    $response = ["error" => "Invalid request"];
}
echo json_encode($response);
?>
