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

//Controllo se la variabile 'testo' ricevuta è nulla
if (isset($_POST{'testo'})) {
    $testo = $_POST{'testo'};
}

function detect_intent_texts($projectId, $text, $sessionId, $languageCode = 'it-IT')
{
    // new session
    $test = array('credentials' => 'myrrorbot-4f360-cbcab170b890.json');
    $sessionsClient = new SessionsClient($test);
    $session = $sessionsClient->sessionName($projectId, $sessionId ?: uniqid());

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
        $displayName = $intent->getDisplayName(); //Nome dell'intent
        $confidence = $queryResult->getIntentDetectionConfidence(); //Livello di confidence
        selectIntent($displayName,$confidence,$text);
        
    }else{

        $answer = "Intent non riconosciuto. Riprova con altre parole!";

        //Stampo la risposta relativa all'intent non identificato
        $arr = array('intentName' => "Non identificato", 'confidence' => "0",'answer' => $answer);
        printf(json_encode($arr,JSON_UNESCAPED_UNICODE)); //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8
    }
    
    $fulfilmentText = $queryResult->getFulfillmentText();
    $sessionsClient->close();
}


function selectIntent($intent, $confidence, $text){

    if(($confidence > 0.86 ||  str_word_count($text) >= 2) && $confidence > 0.67){              

        $answer = null;

        switch ($intent) {

            case 'Attivita fisica':
                $answer = attivitaFisica($text,$confidence);
                break;
    
            case 'Battito cardiaco':
                $answer= getCardio();
                break;
     
            case 'Calorie bruciate':
                $answer = getCalories();
                break;

            case 'Contapassi':
                $answer = getSteps();      
                break;

            case 'Contatti':
                $answer = contatti($text,$confidence);
                break;

            case 'Email':
                $answer = email($text,$confidence);
                break;

            case 'Emozioni':
                $answer = getSentiment(1);
                break;

            case 'Umore':
                $answer = getSentiment(0);
                break;

            case 'Eta':
                $answer = getEta();
                break;

            case 'Identita utente':
                $answer = identitaUtente($text,$confidence);
                break;

            case 'Interessi':
                $answer = interessi($text,$confidence);
                break;

            case 'Lavoro':
                $answer = lavoro($text,$confidence);
                break;

            case 'Luogo di nascita':
                $answer = getCountry();
                break;

            case 'Personalita':
                $answer = personalita($text,$confidence);
                break;

            case 'Qualita del sonno':
                $answer = getSleep();
                break;

            case 'Sedentarieta':
                $answer = getSedentary();
                break;

            default:
                $answer = "Intent non riconosciuto";
                break;
        }

    }else {
        $answer = "Intent non riconosciuto. Riprova con altre parole!";
    }

    //Stampo la risposta
    $arr = array('intentName' => $intent, 'confidence' => $confidence,'answer' => $answer);
    printf(json_encode($arr,JSON_UNESCAPED_UNICODE)); //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8
}

detect_intent_texts('myrrorbot-4f360',$testo,'123456');


