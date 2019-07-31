<?php


if (isset($_POST['answer']) && isset($_POST['rating']) && isset($_POST['quest']) && isset($_POST['email']) 
  && isset($_POST['timestampStart']) && isset($_POST['timestampEnd'])) {
	
	  $answer = $_POST['answer'];
    $rate = $_POST['rating'];
    $quest = $_POST['quest'];
    $email = $_POST['email'];
    $timestamp = time();

    $timestampStart = $_POST['timestampStart'];
    $timestampEnd = $_POST['timestampEnd'];

    $file = fopen("logging.txt", "a") or die("Unable to open file!");

    fwrite($file, "time: ".$timestamp."\r\n");
    fwrite($file, "user: ".$email."\r\n");

    fwrite($file, "timestampStart: ".$timestampStart."\r\n");
    fwrite($file, "timestampEnd: ".$timestampEnd."\r\n");

    $string = "domanda: ".$quest."\r\n";
    fwrite($file, $string);
    fwrite($file, serialize($answer));
    fwrite($file, "\r\n");
    $current = "rate:".$rate."\r\n\r\n";

    //Write the contents back to the file
    //file_put_contents($file, $current);
    fwrite($file, $current);
    fclose($file);
}




?>