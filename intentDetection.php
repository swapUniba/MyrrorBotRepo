<?php



namespace Google\Cloud\Samples\Dialogflow;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;

require __DIR__.'/vendor/autoload.php';
include "myrrorlogin.php";

include 'Behaviors.php';
include "PhysicalState.php";
include "Affects.php";
include 'Demographics.php';
include 'Interests.php';
include 'SocialRelations.php';
include 'CognitiveAspects.php';


header('Content-type: text/plain; charset=utf-8');
//Check if variabile is null or not
if (isset($_POST{'testo'})) {
    $testo = $_POST{'testo'};
}

function detect_intent_texts($projectId, $text, $sessionId, $languageCode = 'it-IT')
{
    // new session
    $test = array('credentials' => 'myrrorbot-4f360-cbcab170b890.json');
    $sessionsClient = new SessionsClient($test);
    $session = $sessionsClient->sessionName($projectId, $sessionId ?: uniqid());
    //STAMPA SESSIONE -------- printf('Session path: %s' . PHP_EOL, $session);

    // create text input
    $textInput = new TextInput();
    $textInput->setText($text);
    $textInput->setLanguageCode($languageCode);

    // create query input
    $queryInput = new QueryInput();
    $queryInput->setText($textInput);

    // get response and relevant info
    $response = $sessionsClient->detectIntent($session, $queryInput);
    $queryResult = $response->getQueryResult();
    $queryText = $queryResult->getQueryText();
    $intent = $queryResult->getIntent();
    if(!is_null($intent)){
        $displayName = $intent->getDisplayName(); //Intent name
        $confidence = $queryResult->getIntentDetectionConfidence();
        selectIntent($displayName,$confidence,$text);
      
        
        
    }else{
        echo "intent non riconosciuto";
    }
    

    $fulfilmentText = $queryResult->getFulfillmentText();


    // Output relevant info
    //print(str_repeat("=", 20) . PHP_EOL);
    //printf('Query text: %s' . PHP_EOL, $queryText);
    //printf('Detected intent: %s (confidence: %f)' . PHP_EOL, $displayName, $confidence);
    //print(PHP_EOL);
    //printf('Fulfilment text: %s' . PHP_EOL, $fulfilmentText);

    
    $sessionsClient->close();
}

function selectIntent($intent,$confidence,$text){


if(($confidence > 0.86 ||  str_word_count($text) >= 2) && $confidence > 0.67){              

$answer = null;

switch ($intent) {
    case 'Attivita fisica':
    
    $values = attivitaFisica($text,$confidence);
    $activity = $values['nameActivity'];
    $timestamp = $values['timestamp'];
    $activityValue = null;
    //nel caso l'attività sia fairly i minuti di attività sono contenuti in minutesFairlyActive

         if($activity == "fairly"){
             $activityValue = $values['minutesFairlyActive'];        
         }else if($activity == "veryActive"){
             $activityValue = $values["minutesVeryActive"];
         }else if ($activity == "calories"){
             $activityValue = $values["activityCalories"];
         }else{
             $activityValue = $values[$activity];
         }

    $answer = "hai svolto ".$values["nameActivity"]. " per ".$activityValue." minuti"; 
       
    $arr = array('intentName' => $intent, 'confidence' => $confidence,'answer' => $answer);
    printf(json_encode($arr,JSON_UNESCAPED_UNICODE)); //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8
        # code...
        break;
    
    case 'Battito cardiaco':
        $values = getCardio();
        $timestamp = $values['timestamp'];  
        $heart = $values['restingHeartRate'];
       
        $answer = " il tuo battito cardiaco è ".$heart; 

        $arr = array('intentName' => $intent, 'confidence' => $confidence,'answer' => $answer);
        printf(json_encode($arr,JSON_UNESCAPED_UNICODE)); //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8

        break;
     

    case 'Calorie bruciate':
        
        $values = getCalories();
        $activity = $values['nameActivity'];
        $timestamp = $values['timestamp'];
        $activityValue = null;
        
        //if ($activity == "calories"){
        
         $activityValue = $values['activityCalories'];
         $answer = "hai bruciato ".$activityValue." calorie"; 
    
       // }

        $arr = array('intentName' => $intent, 'confidence' => $confidence,'answer' => $answer);
        printf(json_encode($arr,JSON_UNESCAPED_UNICODE)); //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8


        break;


    case 'Contapassi':
        $values = getSteps();
        $activity = $values['nameActivity'];
        $timestamp = $values['timestamp'];
        $activityValue = null;
        
         $activityValue = $values['steps'];
         $answer = "hai fatto un totale di ".$activityValue." passi"; 

        $arr = array('intentName' => $intent, 'confidence' => $confidence,'answer' => $answer);
        printf(json_encode($arr,JSON_UNESCAPED_UNICODE)); //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8

        break;


    case 'Contatti':
         $contatti = contatti($text,$confidence);
        $answer = "I tuoi contatti sono:";
        printf($answer ."\n");

        foreach ($contatti as $item){
            printf($item ."\n");
        }
        break;


    case 'Email':
        $email = email($text,$confidence);
        $answer = "La tua email è " .$email;
        printf($answer);
        break;


    case 'Emozioni':
        $values = getSentiment();
        
        $emotion = $values['emotion'];
         
        $answer = "stai provando ".$emotion; 

        $arr = array('intentName' => $intent, 'confidence' => $confidence,'answer' => $answer);
        printf(json_encode($arr,JSON_UNESCAPED_UNICODE)); //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8

        break;

        
    case 'Umore':
        $values = getSentiment();
        
        $mood = $values['sentiment'];
         
        if($mood == 1){
           $answer = "sei di buon umore";
        }else if($mood == -1){
            $answer = "sei di cattivo umore";
        }else{
            $answer = "il tuo umore è neutro";
        }
        

        $arr = array('intentName' => $intent, 'confidence' => $confidence,'answer' => $answer);
        printf(json_encode($arr,JSON_UNESCAPED_UNICODE)); //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8
        break;


    case 'Eta':
        # code...
        break;


    case 'Identita utente':
        $identitaUtente = identitaUtente($text,$confidence);
        $answer = "Il tuo nome è " .$identitaUtente;
        printf($answer);
        break;

    case 'Interessi':
        $interessi = interessi($text,$confidence);
        $answer = "I tuoi interessi sono:";
        printf($answer ."\n");

        foreach ($interessi as $item){
            printf($item ."\n");
        }
        break;

    case 'Lavoro':
        $lavoro = lavoro($text,$confidence);
        $answer = "Il tuo lavoro è " .$lavoro;
        printf($answer);
        break;

    case 'Luogo di nascita':
        # code...
        break;

    case 'Personalita':
        $personalita = personalita($text,$confidence);
        $answer = "Sei un tipo " .$personalita;
        printf($answer);
        break;

    case 'Qualita del sonno':

        $values = getSleep();
        $minutesAsleep = $values['minutesAsleep'];
        $timestamp = $values['timestamp'];

         $answer = "hai dormito ".$minutesAsleep." minuti"; 

        $arr = array('intentName' => $intent, 'confidence' => $confidence,'answer' => $answer);
        printf(json_encode($arr,JSON_UNESCAPED_UNICODE)); //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8
        break;

    case 'Sedentarieta':
         
        $values = getSedentary();
        $activity = $values['nameActivity'];
        $timestamp = $values['timestamp'];
        $activityValue = null;
               
        $activityValue = $values['minutesSedentary'];
        $answer = "sei stato sedentario per ".$activityValue." minuti"; 
    
        $arr = array('intentName' => $intent, 'confidence' => $confidence,'answer' => $answer);
        printf(json_encode($arr,JSON_UNESCAPED_UNICODE)); //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8

        break;

        



    default:
        # code...
    echo "intent non riconosciuto";
        break;
}

 }else{
            echo "intent non riconosciuto riprova";
  }



}

detect_intent_texts('myrrorbot-4f360',$testo,'123456');


