<?php
    $method = $_SERVER["REQUEST_METHOD"];

    //Inkluderar funktioner 
    require_once "../functions.php";

    //Om metoden är inte GET, skickar error och http-koden 405
    if($method !== "GET"){
        sendJson(
            [
                "message" => "Not allowed method!"
            ],
            405
        );
    } 

    //hämtar alla entiteter
    $enteties = loadJson("../database.json");

    //Alla hyresgäster
    $tenants = $enteties["tenants"];

    //Alla lägenheter
    $apartments = $enteties["apartments"];

    //Alla ägare
    $owners = $enteties["owners"];

    //Kontrollerar om förfrågan är en hyresgäst?
    if(isset($_GET["id"])){
        $id = $_GET["id"];
        $found = false;
        $tenantByid;

        //Loopar genom arrayen av hyresgäster och hittar den som samma id som har förfrågat
        foreach($tenants as $tenant){
            if($tenant["id"] == $id){
                $found = true;
                $tenantByid = $tenant;
            }
        }

        //Om id:n inte finns i tenants, skickar error och http-koden 400
        if($found == false){
            sendJson(
                [
                    "message" => "Tenant with given id could not found"
                ],
                400
            );
        }

        ////Kontrollerar om include finns i förfrågan
        if(!empty($_GET["include"]) && $_GET["include"] !== "false"){
            $includeId = $_GET["include"];
            //Loopar genom ägaren för att lägga till apartment namn 
            foreach($apartments as $apartment){
                if($apartment["id"] == $tenantByid["apartment"]){
                    foreach($owners as $owner){
                        if($apartment["id"] == $owner["id"]){
                            $tenantByid["apartment"] = "{ownsBy : ".$owner["name"]."}";
                        }
                    }
                }
            }
            sendJson($tenantByid);
        }

        sendJson($tenantByid);
    }

    //Kontollerar om förfrågan innehåller flera ids
    if(isset($_GET["ids"])){
        $ids = explode(",",$_GET["ids"]);
        $tenantsByIds = [];

        $found = false;

        //Loopar genom alla hyresgäster för att hitta de som har samma id som givna
        foreach($tenants as $tenant){
            if(in_array($tenant["id"], $ids)){
                $tenantsByIds[] = $tenant;
                $found = true;
            }
        }

        //Om ingen av ids finns i tenants, skickas en error med http-koden 400
        if($found == false){
            sendJson(
                [
                    "message" => "Tenants with given ids could not found"
                ],
                400
            );
        }

        //kontrollerar om förfrågan innehåller "limit"
        if(isset($_GET["limit"])){ 
            $limit = $_GET["limit"];
            $tenantsByIds = array_slice($tenantsByIds, 0, $limit);
        }

        //Byter alla ids med ägarens namn som äger lägenheten för valda hyresgäster by given ids
        if(!empty($_GET["include"]) && $_GET["include"] !== "false"){
            $includeId = $_GET["include"];
            $tenantsByIdsWithOwnersnameOfApartments = [];
        
            //Loppar genom tenants för att 
            foreach($tenantsByIds as $tenantById){
                foreach($apartments as $apartment){
                    if($tenantById["apartment"] == $apartment["id"]){
                        $tenantById["apartment"] = "{ownsBy : ".$apartment["owner"]."}";
                        $tenantsByIdsWithOwnersnameOfApartments[] = $tenantById;                                }
                }
            }
                        
        }

        if(!empty($tenantsByIdsWithOwnersnameOfApartments)){
            sendJson($tenantsByIdsWithOwnersnameOfApartments);
        }

        sendJson($tenantsByIds);
    }

    //Kontrollerar om hyresgäster som bor i samma lägenhet är förfrågat
    if(isset($_GET["apartment"])){
        $apartmentId = $_GET["apartment"];
        $found = false;

        $sameApartment = [];

        //Loopar genom hyresgäster och lägger till arrayen som bor i samma lägenhet 
        foreach ($tenants as $tenant) {
            if($tenant["apartment"] == $apartmentId){
                $found = true;
                array_push($sameApartment, $tenant);
            }
        }

        //Om apartmen med given id har ingen tenant, skickas error med http-koden 400
        if($found == false){
            sendJson(
                [
                    "message" => "There is no tenant who lives in apartment with given id"
                ],
                400
            );
        }

        //Kontrollerar om en limit är beviljad
        if(isset($_GET["limit"])){ 
            $limit = $_GET["limit"];

            $limitedApartments = array_slice($sameApartment, 0, $limit);
            sendJson($limitedApartments);
        }
        
        sendJson($sameApartment);
    }

    //Kontrollerar om en specifikt antal av hyresgäster är förfrågat
    if(isset($_GET["limit"])){ 
        $limit = $_GET["limit"];

        $slicedTenants = array_slice($tenants, 0, $limit);
        $slicedTenantsWithOwnerOfApartmentsName = [];
        
        if(!empty($_GET["include"]) && $_GET["include"] !== "false"){
            foreach($slicedTenants as $tenant){
                foreach($apartments as $apartment){
                    if($apartment["id"] == $tenant["apartment"]){
                        foreach($owners as $owner){
                            if($owner["id"] == $apartment["id"]){
                                $tenant["apartment"] = "{ownsBy : ".$owner["name"]."}";
                                $slicedTenantsWithOwnerOfApartmentsName[] = $tenant;                                }
                        }
                    }
                }
            }
            
        }

        //Kontrollerar om slicedTenantsWithOwnerOfApartmentsName är inte tom
        if(!empty($slicedTenantsWithOwnerOfApartmentsName)){
            sendJson($slicedTenantsWithOwnerOfApartmentsName);
        }

        sendJson($slicedTenants);
    }

    //Om det inte finns någon paramater, då skickas hela entiteter
    sendJson($enteties);
    
    ?>