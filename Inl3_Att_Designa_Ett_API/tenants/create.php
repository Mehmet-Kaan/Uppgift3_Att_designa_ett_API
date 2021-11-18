<?php

$method = $_SERVER["REQUEST_METHOD"];
$contentType = $_SERVER["CONTENT_TYPE"];

require_once "../functions.php";

// Kontrollera att rätt content-type skickades med
if ($contentType !== "application/json") {
    sendJson(
        [
            "code" => 7,
            "error" => "The API only accepts JSON!",
            "message" => "Bad request!"
        ],
        400
    );
}

// Kontrollera att rätt metod skickades med
if ($method !== "POST") {
    sendJson(
        [
            "code" => 8,
            "message" => "This method is not allowed!"
        ],
        405
    );
}

// Hämtar databas
$enteties = loadJson("../database.json");
$tenants = $enteties["tenants"];

// Hämtar data som skickats med requesten
$data = file_get_contents("php://input");
$requestData = json_decode($data, true);

// Kontrollerar att "first_name", "last_name", "email", "gender", "apartmentId" skickats med
if (!isset($requestData["first_name"], $requestData["last_name"], $requestData["email"], $requestData["gender"], $requestData["apartment"])) {
    sendJson(
        [
            "code" => 9,
            "error" => "You're missing `first name` or `last name` or `email` or `gender` or `apartment number` of request body!",
            "message" => "Bad request!"
        ],
        400
    );
}

$firstName = $requestData["first_name"]; 
$lastName = $requestData["last_name"]; 
$email =  $requestData["email"];
$gender = $requestData["gender"];
$apartmentId = $requestData["apartment"];

$highestID = 0;

// Loopar genom "tenants" för att hitta den högsta 'id'
foreach ($tenants as $tenant) {
    if ($tenant["id"] > $highestID) {
        $highestID = $tenant["id"];
    }
}

$highestID += 1;

$newTenant = [
    "id" => $highestID,
    "first_name" => $firstName,
    "last_name" => $lastName,
    "email" => $email,
    "gender" => $gender,
    "apartment" => $apartmentId
];

//Pushar in den nya hyresägsten till tenants
array_push($enteties["tenants"],$newTenant);

// Sparar den uppdaterade databasen
saveJson("../database.json", $enteties);

sendJson($newTenant);

?>