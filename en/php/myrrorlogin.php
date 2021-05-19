<?php

require_once("url.php");

function queryMyrror($param, $credenziali){

  $ch = curl_init();
    $json_data = null;

  curl_setopt($ch, CURLOPT_URL,"http://".$GLOBALS['url'].
    ":5000/auth/login");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $credenziali);

  // Receive server response ...
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $server_output = curl_exec($ch);
  $result = json_decode($server_output,true);
  $username = $result['username'];
  setcookie('username',$username, time() + (86400 * 30), "/");
  curl_close ($ch);

  // Further processing ...
  if ($server_output) {
     $token = $result['token'];
     $ch = curl_init();

    $headers =[
      "x-access-token:".$token
    ];

    curl_setopt($ch, CURLOPT_URL, "http://".$GLOBALS['url'].":5000/api/profile/".$result['username'].$param);

    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);          

    $result2 = curl_exec($ch);
    //Decode JSON
    $json_data = json_decode($result2,true);

    curl_close ($ch);

    return $json_data;
  }

}


function queryMyrrorT($param, $token){
  $ch = curl_init();

    $headers =[
      "x-access-token:".$token
    ];

    if(isset($_COOKIE['username'])){
    curl_setopt($ch, CURLOPT_URL, "http://".$GLOBALS['url'].":5000/api/profile/".$_COOKIE['username'].$param);

    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);          

    $result2 = curl_exec($ch);
    //Decode JSON
    $json_data = json_decode($result2,true);

    curl_close ($ch);

    return $json_data;
    }
    
}



?>