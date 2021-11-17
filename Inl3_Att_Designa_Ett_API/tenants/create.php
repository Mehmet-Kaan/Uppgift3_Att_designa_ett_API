<?php
/* Kunna radera en entitet baserat på ett ID. Ni ska kontrollera att ID:et dom specificerat faktiskt existerar. 
Skulle något gå fel ska ni svara med något relevant meddelande så att användaren av ert API förstår vad som gått fel. */

require_once "../functions.php";

$method = $_SERVER["REQUEST_METHOD"];
$contentType = $_SERVER["CONTENT-TYPE"];

// Kontrollera att rätt metod används
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

// Kontrollera att rätt metod används
if ($method !== "POST") {
    sendJson(
        [
            "code" => 8,
            "message" => "This method is not allowed!"
        ],
        405
    );
}

// Hämtar databas och gör om till php
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
$found = false;

$highestID = 0;

// Loopar genom "tenants" för att hitta den högsta 'id'
foreach ($tenants as $index => $tenant) {
    if ($tenant["id"] > $highestID) {
        $highestID = $tenant["id"];
    }
}

$highestID = $highestID + 1;

$newTenant = [
    "id" => $highestID,
    "first_name" => $firstName,
    "last_name" => $lastName,
    "email" => $email,
    "gender" => $gender,
    "apartment" => $apartmentId
];

//Pushar den nya hyresägsten till tenants
array_push($tenants, $newTenant); 

// Sparar den uppdaterade databasen
$saved = saveJson("../database.json", $enteties);

// Om "sparandet" gick bra skickas
if ($saved == true) {
    sendJson($newTenant);
} else {
    sendJson(
        [
            "code" => 10,
            "error" => "New tenants could not added succesfully!",
            "message" => "Bad request!"
        ],
        400
    );
}

?>