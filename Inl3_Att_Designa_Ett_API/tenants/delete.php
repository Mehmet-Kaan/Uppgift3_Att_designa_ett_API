<?php
/* Kunna radera en entitet baserat på ett ID. Ni ska kontrollera att ID:et dom specificerat faktiskt existerar. 
Skulle något gå fel ska ni svara med något relevant meddelande så att användaren av ert API förstår vad som gått fel. */

require_once "../functions.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method !== "DELETE") {
    sendJson(
        [
            "message" => "This method is not allowed"
        ],
        405
    );
}

$enteties = loadJson("../database.json");
$tenants = $enteties["tenants"];

$data = file_get_contents("php://input");
$requestData = json_decode($data, true);

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

foreach ($tenants as $index => $tenant) {
    if ($tenant["id"] === $id) {
        $found = true;
        array_splice($tenants, $index, 1);
        $enteties["tenants"] = $tenants;
        
        break;
    }
}

if ($found == false) {
    sendJson(
        [
            "code" => 2,
            "message" => "Not found"
        ],
        404
    );
}

$saved = saveJson("../database.json", $enteties);

if ($saved == true) {
    sendJson(["id" => $id]);
}

?>