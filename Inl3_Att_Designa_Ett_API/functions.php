<?php
// Kontrollerar metoden
function checkMethod($method) {
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    if ($requestMethod !== $method) {
        sendJson(
            [
                "message" => "This method is not allowed!"
            ],
            405
        );
    }
}

// Kontrolerar Content Typen
function checkConentType() {
    $contentType = $_SERVER["CONTENT_TYPE"];

    if ($contentType !== "application/json") {
        sendJson(
            [
                "error" => "The API only accepts JSON!",
                "message" => "Bad request!"
            ],
            400
        );
    }
}

// Skickar ut JSON till användaren
function sendJson($data, $statuscode = 200){
    header("Content-Type: application/json");
    http_response_code($statuscode);
    $json = json_encode($data);
    echo $json;
    die();
}

// Hämtar data från $filename 
function loadJson($filename) {
    $json = file_get_contents($filename);
    return json_decode($json, true); 
}

// Sparar in data till $filename
function saveJson($filename, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($filename, $json);
    
    return true;
}

// Skriver ut $var som skickas som argument
// För att inspektera
function inspect($var){
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}

// Limiterar arrayen som funktionen anropas med
function limitTheArray($array, $limit){
    return array_slice($array, 0, $limit);
}
?>