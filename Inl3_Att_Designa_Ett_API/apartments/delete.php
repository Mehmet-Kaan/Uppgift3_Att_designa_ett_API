<?php

require_once "../functions.php";

$method = $_SERVER["REQUEST_METHOD"];

// Kontrollerar att rätt metod används
if ($method !== "DELETE") {
    sendJson(
        [
            "message" => "This method is not allowed"
        ],
        405
    );
}

// Hämtar databas och omvandlar till php
$enteties = loadJson("../database.json");
$apartments = $enteties["apartments"];

// Hämtar data som skickats med requesten
$data = file_get_contents("php://input");
$requestData = json_decode($data, true);

// Kontrollerar att ett 'id' skickats med i requesten
if (!isset($requestData["id"])) {
    sendJson(
        [
            "message" => "You're missing an `id` of request body"
        ],
        400
    );
}

// Sparar undan id samt skapar en variabel för att kontrollera om lägenheten hittats
$id = $requestData["id"];
$found = false;

// Letar efter lägenheten med samma 'id' som skickats med i requesten
foreach ($apartments as $index => $apartment) {
    if ($apartment["id"] === $id) {
        // Uppdaterar $found om lägenheten hittats och 
        // sparar undan lägenheten i en variabel för senare användning
        $found = true;
        $deletedApartment = $apartment;

        // Raderar lägenheten ur arrayen och uppdaterar $enteties
        array_splice($apartments, $index, 1);
        $enteties["apartments"] = $apartments;
        
        break;
    }
}

// Om lägenheten inte hittas skickas ett felmeddelande
if ($found == false) {
    sendJson(
        [
            "message" => "Requested 'id' was not found"
        ],
        404
    );
}

// Sparar ner den uppdaterade databasen
$saved = saveJson("../database.json", $enteties);

// Om "sparandet" gick bra skickas den raderade lägenheten till användaren
if ($saved == true) {
    sendJson($deletedApartment);
}

?>