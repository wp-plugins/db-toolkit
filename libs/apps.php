<?php
/* 
 * Apps Browser system
 * (C) copyright David Cramer 2011
 * DB-Toolkit
 */



/*
 * Fetch App Categories
 *
 */

function app_doCall($url, $post = false){

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
    curl_setopt($ch, CURLOPT_URL, $url);

    if(!empty($post)){
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }

    $output = curl_exec($ch);
    
    if($data = json_decode($output, true)){
        return $data;
    }else{
        return $output;
    }
    
}

function app_marketLogin($user, $pass){
    //setup your postvars
    $vars = array(
        "user"=>$user,
        "pass"=>$pass
    );

    // do the call
    $response = app_doCall('http://localhost/wordpress/categories/auth/json', $vars);
    
    if($response['result'] == 'success'){
        update_option('_app_marketid_'.get_current_user_id(), $response['token']);
    }
    return $response;
}

function app_fetchCategories($token){
    $response = app_doCall('http://localhost/wordpress/categories/'.$token.'/list/json');//, $vars);
    return $response['entries'];
}

function app_fetchApps($cat){
    $token = get_option('_app_marketid_'.get_current_user_id());
    $vars = array(
        //"appmarket_categoriesID"=>$cat
        "itemID"=>$cat
    );
    
    $response = app_doCall('http://localhost/wordpress/apps/'.$token.'/fetch/html', $vars);//, $vars);
    $out = trim(strip_tags($response));
    if(empty($out)){
        return 'This category is empty';
    }
    return $response;
}


?>
