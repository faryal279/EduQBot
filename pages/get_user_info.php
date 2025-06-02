<?php
session_start();
header('Content-Type: application/json');

$response = array(
    'loggedin' => false,
    'fullname' => '',
    'error' => ''
);

if (isset($_SESSION['user_id'])) {
    $response['loggedin'] = true;
    $response['fullname'] = $_SESSION['firstname'] . ' ' . $_SESSION['lastname'];
}

echo json_encode($response);
?> 