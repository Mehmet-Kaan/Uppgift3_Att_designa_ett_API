<?php

    include('../functions.php');

    // vars: apt, tnt, own
    function loadData($var = 'db'){
        $data = loadJson('../database.json');

        if ( $var == 'apt' ){
            return $data['apartments'];
        }

        if ($var == 'tnt'){
            return $data['tenants'];
        }

        if ( $var == 'own'){
            return $data['owners'];
        }
        
        return $data;
    }

    // om inga paramentrar, skicka alla
    if (count( $_GET ) == 0){
        sendJson( ['apartments' => loadData('apt')] );
    } 

    if( isset($_GET['id']) ){       

        function findEntry($entry){
            if( $entry['id'] == $_GET['id']){
                return true;
            }
        }

        // find entry in array 
        $entry = array_pop( array_filter( loadData('apt'), 'findEntry' ) );

        if( isset($entry) ){
            sendJson( $entry );
        } else {
            sendJson(['message' => 'Not Found'], 404);
        }        
    }

    if ( isset($_GET['ids']) ){

        $ids = explode( ',', $_GET['ids'] );

        foreach( loadData('apt') as $apartment ){
            if( in_array( $apartment['id'], $ids ) ){
                $entries[] = $apartment;
            }
        }

        // difference between arrays will be unfound ids
        // if true then there are unmatched ids
        $diff = (bool) array_diff( $ids, array_column( $entries, 'id' ) );

        if($diff){
            sendJson(['message' => 'Not Found', 'apartments' => $entries], 404);
        } else {
            sendJson(['apartments' => $entries]);
        }

    }

    if( isset($_GET['color']) ){
        //find all apartments with color

        // if trying to imput more than one color
        if( strPos( $_GET['color'], ',' ) ){
            sendJson(['message' => 'Bad Request'], 400);
        } 

        foreach( loadData('apt') as $apartment ){
            if(strtolower($apartment['apartment_color']) == strtolower($_GET['color'])){
                $entries[] = $apartment;
            }
        }

        // if trying to find color that does not exist
        if( $entries == NULL ){
            sendJson(['message' => 'Bad Request'], 400);
        } else { // else show results
            sendJson(['apartments' => $entries]);
        }
    }





    // if()
?>