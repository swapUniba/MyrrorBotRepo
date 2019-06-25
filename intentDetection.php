<?php

namespace Google\Cloud\Samples\Dialogflow;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;

require __DIR__.'/vendor/autoload.php';

include "readLocaljson.php";
include 'Behaviors.php';
include "PhysicalState.php";
include "Affects.php";
include 'Demographics.php';
include 'Interests.php';
include 'SocialRelations.php';
include 'CognitiveAspects.php';
include 'SpotifyIntent.php';
include 'Video.php';
include 'News.php';


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
 
    //entities
    $parameters=json_decode($queryResult->getParameters()->serializeToJsonString(), true);
    
    $queryText = $queryResult->getQueryText();
    $intent = $queryResult->getIntent();
    
    //risposta intent
    $fulfilmentText = $queryResult->getFulfillmentText();

    if(!is_null($intent)){
        $displayName = $intent->getDisplayName(); //Nome dell'intent
        $confidence = $queryResult->getIntentDetectionConfidence(); //Livello di confidence
        selectIntent($displayName,$confidence,$text,$fulfilmentText,$parameters);
        
    }else{

        $answer = "Purtroppo non ho capito la domanda. Prova a rifarla con altre parole! Devo ancora imparare molte cose &#x1F605;";

        //Stampo la risposta relativa all'intent non identificato
        $arr = array('intentName' => "Non identificato", 'confidence' => "0",'answer' => $answer);
        printf(json_encode($arr,JSON_UNESCAPED_UNICODE));  //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8

    }
    
    $sessionsClient->close();
}



function selectIntent($intent, $confidence,$text,$resp,$parameters){

    if(($confidence > 0.86 ||  str_word_count($text) >= 2) && $confidence >= 0.60){              

        $answer = null;

        switch ($intent) {

            case 'Attivita fisica':
                $answer = attivitaFisica($resp,$parameters,$text);
                break;

            case 'Attivita fisica binario':
                $answer = attivitaFisicaBinary($resp,$parameters,$text);
                break;
    
            case 'Battito cardiaco':
                $answer= getCardio($resp,$parameters,$text);
                break;

            case 'Battito cardiaco binario':
                $answer= getCardioBinary($resp,$parameters,$text);
                break;
     
            case 'Calorie bruciate':
                $answer = getCalories($resp,$parameters,$text);
                break;

            case 'Calorie bruciate binario':
                $answer = getCaloriesBinary($resp,$parameters,$text);
                break;

            case 'Contapassi':
                $answer = getSteps($resp,$parameters,$text);      
                break;

            case 'Contapassi binario':
                $answer = getStepsBinary($resp,$parameters,$text);      
                break;    

            case 'Contatti':
                $answer = contatti($resp,$parameters,$text);
                break;

            case 'Email':
                $answer = email($resp,$parameters,$text);
                break;
                
            case 'Peso':
                $answer = getWeight($resp,$parameters,$text);
                break;

            case 'Altezza':
                $answer = getHeight($resp,$parameters,$text);
                break;

            case 'Emozioni':
                $answer = getSentiment(1,$resp,$parameters);
                break;

            case 'Emozioni binario':
                $answer = getSentimentBinario(1,$resp,$parameters);
                break;

            case 'Umore':
                $answer = getSentiment(0,$resp,$parameters);
                break;

            case 'Umore binario':
                $answer = getSentimentBinario(0,$resp,$parameters);
                break;

            case 'Eta':
                $answer = getEta($resp,$parameters,$text);
                break;

            case 'Identita utente':
                $answer = identitaUtente($resp,$parameters,$text);
                break;

            case 'Interessi':
                $answer = interessi($resp,$parameters);
                break;

            case 'Lavoro':
                $answer = lavoro($resp,$parameters,$text);
                break;

            case 'Luogo di nascita':
                $answer = getCountry($resp,$parameters,$text);
                break;

            case 'Personalita':
                $answer = personalita($resp,$parameters);
                break;

            case 'Personalita binario':
                $answer = personalitaBinario($resp,$parameters);
                break;

            case 'Qualita del sonno':
                $answer = getSleep($resp,$parameters,$text);
                break;

            case 'Qualita del sonno binario':
                $answer = getSleepBinary($resp,$parameters,$text);
                break;

            case 'Sedentarieta':
                $answer = getSedentary($resp,$parameters,$text);
                break;

            case 'Sedentarieta binario':
                $answer = getSedentaryBinary($resp,$parameters,$text);
                break;

            default:
                if ($resp != "") { //Small Talk
                    $answer = $resp;
                }else{
                    $answer = "Purtroppo non ho capito la domanda. Prova a rifarla con altre parole! Devo ancora imparare molte cose &#x1F605;";

                }
                break;
        }

    }else {
        $answer = "Purtroppo non ho capito la domanda. Prova a rifarla con altre parole! Devo ancora imparare molte cose &#x1F605;";
    }


    //SPOTIFY --> Valori soglia diversi
    if(($confidence > 0.86 ||  str_word_count($text) >= 2) && $confidence >= 0.50 && ($intent == 'Canzone per nome' || $intent == 'Canzone per artista' || $intent == 'Canzoni in base al genere' || $intent == 'Playlist di canzoni in base alle emozioni' || $intent == 'Canzoni in base alle emozioni' || $intent == 'Canzoni personalizzate')){
        switch ($intent) {
            case 'Canzone per nome':
                $answer = getMusicByTrack($resp,$parameters,$text);
                break;

            case 'Canzone per artista':
                $answer = getMusicByArtist($resp,$parameters,$text);
                break;
            
            case 'Canzoni in base al genere':
                $answer = getMusicByGenre($resp,$parameters,$text);
                break;

            case 'Playlist di canzoni in base alle emozioni':
                $answer = getPlaylistByEmotion($resp,$parameters,$text);
                break;

            case 'Canzoni in base alle emozioni':
                $answer = getMusicByEmotion($resp,$parameters,$text);
                break;

            case 'Canzoni personalizzate':
                $answer = getMusicCustom($resp,$parameters,$text);
                break;

            default:
                $answer = "Purtroppo non ho capito la domanda. Prova a rifarla con altre parole! Devo ancora imparare molte cose &#x1F605;";
                break;;
        }
    }


    //YOUTUBE --> Valori soglia diversi
    if(($confidence > 0.86 ||  str_word_count($text) >= 2) && $confidence >= 0.50 && ($intent == 'Video in base alle emozioni' || $intent == 'Ricerca Video')){
        switch ($intent) {
            case 'Video in base alle emozioni':
                $answer = getVideoByEmotion($resp,$parameters,$text);
                break;
            case 'Ricerca Video':
                $answer = getVideoBySearch($resp,$parameters,$text);
                break;

            default:
                $answer = "Purtroppo non ho capito la domanda. Prova a rifarla con altre parole! Devo ancora imparare molte cose &#x1F605;";
                break;;
        }
    }
    
        //GOOGLE-NEWS--> Valori soglia diversi
    if($confidence >= 0.50  && ($intent == 'Notizie in base ad un argomento' || $intent == 'Notizie in base agli interessi' || $intent == 'Notizie odierne' || $intent == 'Ricerca articolo' )){

        switch ($intent) {
             case 'Notizie in base ad un argomento':
                $answer = getNewsTopic($parameters);
                break;

            case 'Notizie in base agli interessi':
                $answer = getInterestsNews();
                break;

            case 'Notizie odierne':
                $answer = getTodayNews();   
                break;

            case 'Ricerca articolo':
                $answer = cercaNews($parameters);   
                break;  

            default:
                $answer = "Purtroppo non ho capito la domanda. Prova a rifarla con altre parole! Devo ancora imparare molte cose &#x1F605;";
                break;
        }

    }

    //Stampo la risposta
    $arr = array('intentName' => $intent, 'confidence' => $confidence,'answer' => $answer);

    if ($arr['intentName'] == 'Canzone per nome') {
        printf(json_encode($arr)); //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8
    }else{
        printf(json_encode($arr,JSON_UNESCAPED_UNICODE)); //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8
    }
}

date_default_timezone_set('Europe/Madrid'); //Imposto la stessa timezone di Dialogflow (per gli orari)

detect_intent_texts('myrrorbot-4f360',$testo,'123456');


