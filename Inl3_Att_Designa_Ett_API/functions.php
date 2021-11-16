<?php

function sendJson($data, $statuscode = 200){
    header("Content-Type: application/json");
    http_response_code($statuscode);
    $json = json_encode($data);
    echo $json;
    die();
}

function loadJson($filename) {
    $json = file_get_contents($filename);
    return json_decode($json, true); 
}

function saveJson($filename, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($filename, $json);
    
    return true;
}
?>