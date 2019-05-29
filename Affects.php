<?php

function getSentiment($flag){

	//flag 0 per emozioni 1 per umore


$param = "?f=Affects";
$json_data = queryMyrror($param);
$result = null;
$max = "";
$emotion = "";

foreach ($json_data['affects'] as $key1 => $value1) {

     	   
		$date = substr($value1['date'], 10);
	    if($date > $max){
          
           $result = $value1;
           $max = $date;

   	    }
		
	}
if($flag == 1){


    if(isset($result['emotion'] )){



    	if (strpos($result['emotion'], 'joy') !== false) {
    		$emotion = "gioia";
    	}else if (strpos($result['emotion'], 'fear') !== false) {
    		 $emotion = "paura";
    	}else if (strpos($result['emotion'], 'anger') !== false) {
    		$emotion = "rabbia";
    	}else if (strpos($result['emotion'], 'disgust') !== false) {
    		$emotion = "disgusto";
    	}else if (strpos($result['emotion'], 'sad') !== false) {
    		$emotion = "tristezza";
    	}else if (strpos($result['emotion'], 'surprise') !== false) {
    		$emotion = "sorpresa";
    	}else{
           return "non stai provando emozioni";
    	}
    

       switch (rand(1,3)) {
       	case 1:
       		$answer = "Stai provando: ".$emotion ;
       		break;

       	case 2:
       		$answer = "Oggi ti senti ".$emotion ;
       		break;
       	
       	default:
       		$answer = "Sei ".$emotion ;
       		break;
       }

    }else{
    	
	$answer = "informazione non trovata";
    }

}else{

     if($result['sentiment'] != null){

       $mood = $result['sentiment'];

     if($mood == 1){
           $answer = "sei di buon umore";
     }else if($mood == -1){
            $answer = "sei di cattivo umore";
     }else{
            $answer = "il tuo umore è neutro";
     }

    }else{
    	
	$answer = "informazione non trovata";
    }

}


	return $answer;

}






   
