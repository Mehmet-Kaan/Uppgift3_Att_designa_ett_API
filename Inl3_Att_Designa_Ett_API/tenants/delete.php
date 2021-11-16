<?php
/* Kunna radera en entitet baserat på ett ID. Ni ska kontrollera att ID:et dom specificerat faktiskt existerar. 
Skulle något gå fel ska ni svara med något relevant meddelande så att användaren av ert API förstår vad som gått fel. */

require_once "../functions.php";

$method = $_SERVER["REQUEST_METHOD"];

// Kontrollera att rätt metod används
if ($method !== "DELETE") {
    sendJson(
        [
            "message" => "This method is not allowed"
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

// Kontrollerar att "id" skickats med
if (!isset($requestData["id"])) {
    sendJson(
        [
            "message" => "You're missing an `id` of request body"
        ],
        400
    );
}

$id = $requestData["id"];
$found = false;

// Hittar "tenant" som har 'id' som skickades med, och raderar ur arrayen
foreach ($tenants as $index => $tenant) {
    if ($tenant["id"] === $id) {
        $found = true;
        $deletedTenant = $tenant;

        array_splice($tenants, $index, 1);
        $enteties["tenants"] = $tenants;
        
        // Avbryter loopen
        break;
    }
}

// Om "tenant" inte hittas så skickas ett felmeddelande
if ($found == false) {
    sendJson(
        [
            "message" => "Tenant not found"
        ],
        404
    );
}

// Sparar den uppdaterade databasen
$saved = saveJson("../database.json", $enteties);

// Om "sparandet" gick bra skickas den raderade användaren
if ($saved == true) {
    sendJson($deletedTenant);
}

?>