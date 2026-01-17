<?php 
session_start();
include 'api.php';

$data = $_POST;
$response = userLogin($data);
// print_r($response);

if($response['status'] == 'success' || $response['success'] == true){
    $_SESSION['user'] = $response['user'];
    // json_encode($response);
}

echo json_encode($response);

