<?php
require 'dbConnection.php';
header('Content-Type: application/json');

$response = array();

$requestMethod =$_SERVER['REQUEST_METHOD'];

if($requestMethod == 'GET'){
  if (isset($_GET['StudentID'])) {
    $studentID = $_GET['StudentID'];
    
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
  
    try{
      $query = "SELECT * FROM student WHERE StudentID = ?";
   
      $stmt = $connect->prepare($query);
      $stmt->bind_param("i", $studentID); // 'i' stands for integer
  
      if ($stmt->execute()) {
        $result = $stmt->get_result();
      
          if ($result->num_rows > 0) {
            $row = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $response = [
              'status' => 200,
              'message' => 'Student Record Feteched Successfully',
              'data' => $row
            ];
            header("HTTP/1.0 200 OK");
          }else {
            $response = [
              'status' => 404,
              'message' => 'Student Record Not Found',
            ];
            header("HTTP/1.0 404 Not found");
          }
      }else {
        $response = [
          'status' => 500,
          'message' => 'Internal Server Error (Query error)',
        ];
        header("HTTP/1.0 500 Internal Server Error");
      }
      $stmt->close();
    }catch (Exception $e){
      $response = [
        'status' => 500,
        'message' => 'Internal Server Error (Query error)',
      ];
      header("HTTP/1.0 500 Internal Server Error");
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