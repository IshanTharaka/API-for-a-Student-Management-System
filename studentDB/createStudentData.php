<?php
require 'dbConnection.php';
header('Content-Type: application/json');

$response = array();

$requestMethod =$_SERVER['REQUEST_METHOD'];

if($requestMethod == 'POST'){
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
            $query = "INSERT INTO student (FirstName, LastName, DateofBirth, Address, Email) VALUES (?, ?, ?, ?, ?)";
        
            $stmt = $connect->prepare($query);
            $stmt->bind_param("sssss", $firstName, $lastName, $dateOfBirth, $address, $email); // 's' stands for string
        
            if ($stmt->execute()) {
                $response = [
                    'status' => 201,
                    'message' => 'Student Record Created Successfully',
                ];
                header("HTTP/1.0 201 Created");
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