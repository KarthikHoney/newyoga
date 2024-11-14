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
    if (isset($_POST['action']) && $_POST['action'] === "generateCertificate" && isset($_POST['studentId'])) {

        $sql = "SELECT 
            individual_student.name,
            individual_student.roll,
            individual_student.enroll,
            grade.grade,
            grade.mark,
            grade.gradeResult,
            grade.updatedDate
        FROM 
            individual_student
        JOIN 
            grade ON individual_student.id = grade.student_id
        WHERE 
            individual_student.id = :studentId AND grade.id = :gradeId ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':studentId', $studentId, PDO::PARAM_INT);
        $stmt->bindParam(':gradeId', $gradeId, PDO::PARAM_INT);

        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            $studentName = ucwords($student['name']);
            $studentRollNo = $student['roll'];
            $studentEnrollNo = $student['enroll'];
            $studentGrade = $student['grade'];
            $studentMark = $student['mark'];
            $studentGradeResult = $student['gradeResult'];
            $currentDate = isset($student['updatedDate'])? $student['updatedDate']:null;
            
          
            $certificateImage = imagecreatefromjpeg(__DIR__ . '/certificate.jpeg');
            if (!$certificateImage) {
                $error = error_get_last();
                $response = ["status" => 0, "message" => "Error loading image: " . $error['message']];
                exit;
            }

            $textColorBlack = imagecolorallocate($certificateImage, 0, 0, 0);

            $fontSize = 80;
            $fontSizer = 13;
            $fontSizeMG = 20;
            $fontSizeGN = 33;
            $mark =[500,674];
            $gradeResult =[1140,674];
            $gradeNumber =[900,410];
            $namePosition = [450, 580];
            $RollPosition = [700, 76];
            $EnrollPosition = [880, 76];
            $datePosition = [1185,120];


            $fontPath = __DIR__ . '/fonts/Roboto/Roboto-Bold.ttf';
            
            if (!file_exists($fontPath)) {
                $response = ["status" => 0, "message" => "Font file not found"];
                exit;
            }

            imagettftext($certificateImage, $fontSize, 0, $namePosition[0], $namePosition[1], $textColorBlack, $fontPath, $studentName);
            imagettftext($certificateImage, $fontSizer, 0, $RollPosition[0], $RollPosition[1], $textColorBlack, $fontPath, $studentRollNo);
            imagettftext($certificateImage, $fontSizer, 0, $EnrollPosition[0], $EnrollPosition[1], $textColorBlack, $fontPath, $studentEnrollNo);
            imagettftext($certificateImage, $fontSizeMG, 0, $mark[0], $mark[1], $textColorBlack, $fontPath, $studentMark);
            imagettftext($certificateImage, $fontSizeMG, 0, $gradeResult[0], $gradeResult[1], $textColorBlack, $fontPath, $studentGradeResult);
            imagettftext($certificateImage, $fontSizeGN, 0, $gradeNumber[0], $gradeNumber[1], $textColorBlack, $fontPath, $studentGrade);

            if ($currentDate) {
                imagettftext($certificateImage, $fontSizer, 0, $datePosition[0], $datePosition[1], $textColorBlack, $fontPath, $currentDate);
            } else {
                imagettftext($certificateImage, $fontSizer, 0, $datePosition[0], $datePosition[1], $textColorBlack, $fontPath, "Date not available");
            }
            ob_start();
            imagejpeg($certificateImage);
            $imageData = ob_get_clean();

            imagedestroy($certificateImage);

            $base64Image = base64_encode($imageData);
            $response = ["status" => 1, "certificate" => "data:image/jpeg;base64," . $base64Image];

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