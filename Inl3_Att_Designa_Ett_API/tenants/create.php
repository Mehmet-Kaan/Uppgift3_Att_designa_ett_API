<?php
//Inkluderar funktioner 
require_once "../functions.php";

// Kontrollera att rätt content-type skickades med
checkConentType();

// Kontrollera att rätt metod skickades med
checkMethod("POST");

// Hämtar databas
$enteties = loadJson("../database.json");
$tenants = $enteties["tenants"];
$apartments = $enteties["apartments"];

// Hämtar data som skickats med requesten
$data = file_get_contents("php://input");
$requestData = json_decode($data, true);

// Kontrollerar att "first_name", "last_name", "email", "gender", "apartmentId" skickats med
if (!isset($requestData["first_name"], $requestData["last_name"], $requestData["email"], $requestData["gender"], $requestData["apartment"])) {
    sendJson(
        [
            "message" => "You're missing `first name` or `last name` or `email` or `gender` or `apartment number` of request body!"
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

$apartmentFound = false;

//Loopar genom lägenheter för att kontrollera om lägenheten med given id finns
foreach($apartments as $apartment){
    if ($apartment["id"] == $apartmentId){
        $apartmentFound = true;
    };
}

//Kontrollerar om lägenheten hittades
if($apartmentFound == false){
    sendJson(
        [
            "message" => "Apartment with that id doesn`t exist"
        ],
        400
    );
}

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