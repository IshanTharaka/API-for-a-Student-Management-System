<?php
require 'dbConnection.php';
header('Content-Type: application/json');

$response = array();

$requestMethod =$_SERVER['REQUEST_METHOD'];

if($requestMethod == 'PUT'){
    $studentID = $_GET['StudentID'] ?? null;

    if ($studentID !== null) {
        // Validate studentID (numeric and positive)
        if (!is_numeric($studentID) || $studentID <= 0) {
            $response = [
                'status' => 400,
                'message' => 'Invalid StudentID Format',
            ];
            header("HTTP/1.0 400 Bad Request"); 
            echo json_encode($response);
            exit();
        }
    
        $requestBody = file_get_contents("php://input");
        $data = json_decode($requestBody);
    
        if (isset($data->FirstName, $data->LastName, $data->DateofBirth, $data->Address, $data->Email)) {
            $firstName = $data->FirstName;
            $lastName = $data->LastName;
            $dateOfBirth = $data->DateofBirth;
            $address = $data->Address;
            $email = $data->Email;
            
            // Validate input data (example: check if required fields are not empty)
            if (empty($firstName) || empty($lastName) || empty($dateOfBirth) || empty($address) || empty($email)) {
                $response = [
                    'status' => 400,
                    'message' => 'Incomplete Data Provided',
                ];
                header("HTTP/1.0 400 Bad Request");              
                echo json_encode($response);
                exit();
            }
    
            try{
                $query = "UPDAE student SET FirstName = ?, LastName = ?, DateofBirth = ?, Address = ?, Email = ? WHERE StudentID = ?";
            
                $stmt = $connect->prepare($query);
                $stmt->bind_param("sssssi", $firstName, $lastName, $dateOfBirth, $address, $email, $studentID); // 's' stands for string, 'i' for integer
                
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $response = [
                            'status' => 200,
                            'message' => 'Student Record Updated Successfully',
                        ];
                        header("HTTP/1.0 200 OK");
                    } else {
                        $response = [
                            'status' => 404,
                            'message' => 'Student Record Not Found OR No Changes Made',
                          ];
                          header("HTTP/1.0 404 Not found");
                    }
                } else {
                    $response = [
                        'status' => 500,
                        'message' => 'Internal Server Error (Query error)',
                    ];
                    header("HTTP/1.0 500 Internal Server Error");
                }
                $stmt->close();
            }catch(Exception $e){
                $response = [
                    'status' => 500,
                    'message' => 'Internal Server Error (Query error)',
                ];
                header("HTTP/1.0 500 Internal Server Error");
            }
            
        } else {
            $response = [
                'status' => 400,
                'message' => 'Incomplete Data Provided',
            ];
            header("HTTP/1.0 400 Bad Request"); 
        }
    } else {
        $response = [
            'status' => 400,
            'message' => 'StudentID Parameter Missing',
          ];
          header("HTTP/1.0 400 Bad Request"); 
    }
}else{
    $response = [
        'status' => 405,
        'message' => $requestMethod.' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
}

echo json_encode($response);
mysqli_close($connect);
?>
