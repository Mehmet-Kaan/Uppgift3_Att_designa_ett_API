<?php
    error_reporting(-1);

    include("../functions.php");

    checkMethod("POST");
    checkConentType();

    $data = loadJson("../database.json");
    $apartments = $data['apartments'];

    $modelApt = array_keys( reset( $apartments ) );
    $request = json_decode( file_get_contents("php://input"), true );

    // which keys are supposed to exist but dont
    $missingKeys = count( array_diff( $modelApt, array_keys( $request )) );

    $extraKeys= count( array_diff( array_keys( $request ), $modelApt) );

    if( in_array("id", array_keys( $request )) ){
        sendJson(['message'=> 'Bad Request: cannot submit id'], 400);
    }

    // all but id should exist
    if( $missingKeys !== 1 ){
        sendJson(['message'=> 'Bad Request: one or more keys missing'], 400);
    }

    if( $extraKeys > 0 ){
        sendJson(['message'=> 'Bad Request: incorrect keys'], 400);
    }

    foreach( $apartments as $apt ){
        if( $apt['city'] == $request['city'] 
        and $apt['street_adress'] == $request['street_adress'] ){
            sendJson( ['message' => "Unprocessable Entity: apartment with same address already exists in database"], 422 );
        }
    }

    //Create new apartments
    $id = max( array_column($apartments, "id") ) + 1;
    $newApt = [
        "id" => $id,
        "owner" => $request['owner'],
        "city" => $request['city'],
        "street_adress" => $request['street_adress'],
        "apartment_color" => $request['apartment_color'],
    ];

    array_push($data['apartments'], $newApt);

    saveJson("../database.json", $data);

    sendJson($data['apartments']);
?>