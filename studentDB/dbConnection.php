<?php

$host = 'localhost:3307';
$root = 'root';
$password = 'Ishan@1999';
$database = 'studentrecords';

try{
    $connect = mysqli_connect($host, $root, $password, $database);
}catch(Exception $e){
    $response = [
      'status' => 500,
      'message' => 'Internal Server Error - Database Connection Issue.'. mysqli_connect_error(),
    ];
    header("HTTP/1.0 500 Internal Server Error");
    echo json_encode($response);
    exit();
}

?>