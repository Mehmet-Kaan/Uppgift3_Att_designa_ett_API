<?php
    $method = $_SERVER["REQUEST_METHOD"];

    //Inkluderar funktioner 
    require_once "../functions.php";

    //Om metoden är inte GET, skickar error och http-koden 405
    if($method !== "GET"){
        sendJson(
            [
                "code" => 1,
                "message" => "Not allowed method!"
            ],
            405
        );
    } 

    //hämtar alla entiteter
    $entiteter = loadJson("../database.json");

    //Alla hyresgäster
    $hyresgaster = $entiteter["hyresgaster"];

    //Alla lägenheter
    $lagenheter = $entiteter["lagenheter"];

    //Alla ägare
    $owners = $entiteter["owners"];

    //Kontrollerar om förfrågan är en hyresgäst?
    if(isset($_GET["id"])){
        $id = $_GET["id"];
        $found = false;
        $hyresgast;

        //Loopar genom arrayen av hyresgäster och hittar den som samma id som har förfrågat
        foreach($hyresgaster as $gast){
            if($gast["id"] == $id){
                $found = true;
                $hyresgast = $gast;
            }
        }

        //Om id:n inte inte finns, skickar error och http-koden 400
        if($found == false){
            sendJson(
                [
                    "code" => 2,
                    "error" => "Tenant with given id could not found",
                    "message" => "Bad request"
                ],
                400
            );
        }

        ////Kontrollerar om include finns i förfrågan
        if(isset($_GET["include"])){
            $includeId = $_GET["include"];

            //Loopar genom ägaren för att lägga till apartment namn 
            foreach($aparments as $aparment){
                if($aparment["owner"] == $hyresgast["aparment"]){
                    $hyresgast["aparment"] = $aparment["owner"];
                    sendJson($hyresgast);
                }
            }
        }

        sendJson($hyresgast);
    }

    //Kontollerar om förfrågan innehåller flera ids
    if(isset($_GET["ids"])){
        $ids = explode(",",$_GET["ids"]);
        $hyresgasterByIds = [];

        $found = false;

        //Loopar genom alla hyresgäster för att hitta de som har samma id som givna
        foreach($hyresgaster as $gast){
            if(in_array($gast["id"], $ids)){
                $hyresgasterByIds[] = $gast;
                $found = true;
            }
        }

        //Om ingen av ids finns, skickas en error med http-koden 400
        if($found == false){
            sendJson(
                [
                    "code" => 3,
                    "error" => "Tenants with given ids could not found",
                    "message" => "Bad request"
                ],
                400
            );
        }

        //kontrollerar om förfrågan innehåller "limit"
        if(isset($_GET["limit"])){ 
            $limit = $_GET["limit"];
            $hyresgasterByIds = array_slice($hyresgasterByIds, 0, $limit);
        }

        //Växlar alla ids till owners för valda hyresgäster by given ids
        if(isset($_GET["include"])){
            $includeId = $_GET["include"];

            $hyresgasterByIdsWithOwnersName = [];

            foreach($hyresgasterByIds as $hyresgastById){
                foreach($owners as $owner){
                    if($hyresgastById["owner"] == $owner["id"]){
                        $hyresgastById["owner"] = $owner["name"];
                        $hyresgasterByIdsWithOwnersName[] = $hyresgastById;
                    }
                }
            }
        }

        if(!empty($hyresgasterByIdsWithOwnersName)){
            sendJson($hyresgasterByIdsWithOwnersName);
        }

        sendJson($hyresgasterByIds);
    }

    //Kontrollerar om hyresgäster som har samma hyresvärde är förfrågat
    if(isset($_GET["owner"])){
        $ownerId = $_GET["owner"];
        $found = false;

        $sameOwner = [];

        //Loopar genom hyresgäster och lägger till arrayen som har samma hyresvärde 
        foreach ($hyresgaster as $gast) {
            if($gast["owner"] == $ownerId){
                $found = true;
                array_push($sameOwner, $gast);
            }
        }

        //Om hyresgäst inte finns, skickas error med http-koden 400
        if($found == false){
            sendJson(
                [
                    "code" => 5,
                    "error" => "There is no tenants with given id of owner",
                    "message" => "Bad request"
                ],
                400
            );
        }

        //Kontrollerar om en limit är beviljad
        if(isset($_GET["limit"])){ 
            $limit = $_GET["limit"];

            $limitedOwners = array_slice($sameOwner, 0, $limit);
            sendJson($limitedOwners);
        }
        
        sendJson($sameOwner);
    }

    //Kontrollerar om en specifick antal av hyresgäster är förfrågat
    if(isset($_GET["limit"])){ 
        $limit = $_GET["limit"];

        $slicedHyresgaster = array_slice($hyresgaster, 0, $limit);
        sendJson($slicedHyresgaster);
    }

    //Om det inte finns någon paramter, så anropas funktionen sendJson för att skicka
    //hela entiteter
    sendJson($entiteter);
    
    ?>