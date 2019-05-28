<?php

//PERSONALITA': viene ricavata in base al valore più grande tra le “personalities” in relazione al più recente timestamp.

function personalita($text,$confidence){

	$param = "?f=CognitiveAspects";
	$json_data = queryMyrror($param);
	$result = null;

	$openness = "";
	$conscientiousness = "";
	$extroversion = "";
	$agreeableness = "";
	$neuroticism = "";
	$confidence = "";

	foreach ($json_data as $key1 => $value1) {

		if(isset($value1['personalities'])){

			$max = 0;

			foreach ($value1['personalities'] as $key2 => $value2) {

				$timestamp = $value2['timestamp'];

				$openness = $value2['openness'];
				$conscientiousness = $value2['conscientiousness'];
				$extroversion = $value2['extroversion'];
				$agreeableness = $value2['agreeableness'];
				$neuroticism = $value2['neuroticism'];
				$confidence = $value2['confidence'];

				//print_r($timestamp + "<br>");
		 
         		if($timestamp > $max ){
         
           			$max = $timestamp;
           			$openness = $value2['openness'];
					$conscientiousness = $value2['conscientiousness'];
					$extroversion = $value2['extroversion'];
					$agreeableness = $value2['agreeableness'];
					$neuroticism = $value2['neuroticism'];
					$confidence = $value2['confidence'];
         		}	
        	}	
		}
	}

	if ($openness > $conscientiousness && $openness > $extroversion && $openness > $agreeableness && $openness > $neuroticism && $openness > $confidence) {
		return "aperto";
	}elseif ($conscientiousness > $openness && $conscientiousness > $extroversion && $conscientiousness > $agreeableness && $conscientiousness > $neuroticism	 && $conscientiousness > $confidence) {
		return "coscienzioso";
	}elseif ($extroversion > $openness && $extroversion > $conscientiousness && $extroversion > $agreeableness && $extroversion > $neuroticism	 && $extroversion > $confidence) {
		return "estroverso";
	}elseif ($agreeableness > $openness && $agreeableness > $conscientiousness && $agreeableness > $extroversion && $agreeableness > $neuroticism	 && $agreeableness > $confidence) {
		return "piacevole";
	}elseif ($neuroticism > $openness && $neuroticism > $conscientiousness && $neuroticism > $extroversion && $neuroticism > $agreeableness	 && $neuroticism > $confidence) {
		return "nevrotico";
	}elseif ($confidence > $openness && $confidence > $conscientiousness && $confidence > $extroversion && $confidence > $agreeableness	 && $confidence > $neuroticism) {
		return "fiducioso";
	}

}
