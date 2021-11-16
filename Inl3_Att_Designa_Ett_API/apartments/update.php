<?php
/* Kunna redigera en entitet baserat på ett ID. 
Ni ska kontrollera att dom fält som dom skickar med inte är tomma samt att ID:et dom specificerat faktiskt existerar. 
Skulle något gå fel ska ni svara med något relevant meddelande så att användaren av ert API förstår vad som gått fel. */

require_once "../functions.php";

$method = $_SERVER["REQUEST_METHOD"];

// Kontrollerar att rätt metod används
if ($method !== "PATCH") {
    sendJson(
        [
            "message" => "This method is not allowed"
        ],
        405
    );
}

// Hämtar datan som skickas med requesten
$data = file_get_contents("php://input");
$requestData = json_decode($data, true);

// Kontrollerar att ett id skickas med
if (!isset($requestData["id"])) {
    sendJson(
        [
            "code" => 1,
            "message" => "Missing `id` of request body"
        ],
        400
    );
}

// Kontrollerar att någon typ av data som ska ändras skickas med
if (!isset($requestData["owner"]) && !isset($requestData["city"]) && !isset($requestData["street_name"]) 
    && !isset($requestData["street_adress"]) && !isset($requestData["apartment_color"])) {   
    sendJson(
        [
            "code" => 2,
            "message" => "Bad request"
        ],
        400
    );
}

$id = $requestData["id"];
$enteties = loadJson("../database.json");
$apartments = $enteties["apartments"];

$found = false;
$foundApartment = null;

// Går igenom lägenheterna och hittar lägenheten med rätt id
foreach ($apartments as $index => $apartment) {
    if ($apartment["id"] == $id) {
        $found = true;  
        
        if (isset($requestData["owner"])) {
            $apartment["owner"] = $requestData["owner"];
        }

        if (isset($requestData["city"])) {
            $apartment["city"] = $requestData["city"];
        }
        
        if (isset($requestData["street_adress"])) {
            $apartment["street_adress"] = $requestData["street_adress"];
        }

        if (isset($requestData["street_name"])) {
            $apartment["street_name"] = $requestData["street_name"];
        }

        if (isset($requestData["apartment_color"])) {
            $apartment["apartment_color"] = $requestData["apartment_color"];
        }

        $apartments[$index] = $apartment;
        $foundApartment = $apartment;
        $enteties["apartments"] = $apartments;
        // Avbryter loopen
        break;
    }
}

if ($found == false) {
    sendJson(
        [
            "code" => 2,
            "message" => "The users by `id` does not exist"
        ],
        404
    );
}

saveJson("../database.json", $enteties);

sendJson($foundApartment);
?>