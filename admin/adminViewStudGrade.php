<?php

include "../conn.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$response = ['status' => 0, 'message' => 'An unknown error occurred'];

$input = json_decode(file_get_contents('php://input'));

$studentId = isset($input->studentId) ? intval($input->studentId) : null;

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        if (isset($input->action) && $input->action === 'grade') {
            if (isset($input->grade, $input->payment)) {
                $grade = intval($input->grade);
                $payment = intval($input->payment);
                $admin = "admin";

                $sql = "INSERT INTO grade (student_id, admin_id, grade, payment) VALUES (:studentId, :admin_id, :grade, :payment)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":studentId", $studentId, PDO::PARAM_INT);
                $stmt->bindParam(":admin_id", $admin, PDO::PARAM_STR);
                $stmt->bindParam(":grade", $grade, PDO::PARAM_INT);
                $stmt->bindParam(":payment", $payment, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $response = [
                        'status' => 1,
                        'data' => [
                            'date' => date('Y-m-d'),
                            'grade' => $grade,
                            'payment' => $payment
                        ]
                    ];
                } else {
                    $response['message'] = 'Failed to insert data.';
                }
            }
        }else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input->action) && $input->action === "GetGrades") {
            $gradeId = $input->gradeId;
            $sql = "SELECT * FROM grade WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":id", $gradeId, PDO::PARAM_INT);
        
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $response=["status" => 1, "grade" => $result];
            } else {
                $response=["status" => 0, "message" => "Failed to fetched"];
            }
        
        } 
        
        else if (isset($input->action) && $input->action === 'updateMark') {
            $gradeId = $input->gradeId;
            $date=$input->date;
            $grade=$input->grade;
            $payment=$input->payment;
            $mark = $input->mark;
            $gradeResult = $input->gradeResult;

            $sql = "UPDATE grade SET updatedDate=CURDATE(), date=:date,grade=:grade,payment=:payment, mark=:mark, gradeResult=:gradeResult WHERE id=:id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':date',$date,PDO::PARAM_STR);
            $stmt->bindParam(':grade',$grade,PDO::PARAM_INT);
            $stmt->bindParam(':payment',$payment,PDO::PARAM_INT);
            $stmt->bindParam(':mark', $mark, PDO::PARAM_INT);
            $stmt->bindParam(':gradeResult', $gradeResult, PDO::PARAM_STR);
            $stmt->bindParam(':id', $gradeId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                
                $response = [
                    'status' => 1,
                    'markGrade' => [
                        'id' => $gradeId,
                        'date' => $date,
                        'grade' => $grade,
                        'payment' => $payment,
                        'mark' => $mark,
                        'gradeResult' => $gradeResult
                        
                    ]
                ];
            } else {
                $response['message'] = 'Failed to update grade.';
            }
        } else if (isset($input->action) && $input->action === 'fetchGrades') {
            if ($studentId) {
                $sql = "SELECT * FROM grade WHERE student_id = :studentId";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":studentId", $studentId, PDO::PARAM_INT);
                $stmt->execute();
                $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $response = [
                    'status' => 1,
                    'grades' => $grades
                ];
            } else {
                $response['message'] = 'Missing studentId.';
            }
        } else {
            $response['message'] = 'Invalid action specified.';
        }
    } else {
        $response['message'] = 'Invalid request method.';
    }
} catch (Exception $e) {
    $response = ['status' => 0, 'error' => $e->getMessage()];
}

echo json_encode($response);
exit();

?>
