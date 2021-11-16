<?php

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
$apartments = $enteties["apartments"];

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

foreach ($apartments as $index => $apartment) {
    if ($apartment["id"] === $id) {
        $found = true;
        array_splice($apartments, $index, 1);
        $enteties["apartments"] = $apartments;
        
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