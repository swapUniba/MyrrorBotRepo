<?php

namespace Google\Cloud\Samples\Dialogflow;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;

require __DIR__.'/vendor/autoload.php';

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
    $displayName = $intent->getDisplayName(); //Intent name
    $confidence = $queryResult->getIntentDetectionConfidence();
    $fulfilmentText = $queryResult->getFulfillmentText();

    // Output relevant info
    //print(str_repeat("=", 20) . PHP_EOL);
    //printf('Query text: %s' . PHP_EOL, $queryText);
    //printf('Detected intent: %s (confidence: %f)' . PHP_EOL, $displayName, $confidence);
    //print(PHP_EOL);
    //printf('Fulfilment text: %s' . PHP_EOL, $fulfilmentText);

    $arr = array('intentName' => $displayName, 'confidence' => $confidence);
    printf(json_encode($arr,JSON_UNESCAPED_UNICODE)); //JSON_UNESCAPED_UNICODE utilizzato per il formato UTF8

    $sessionsClient->close();
}

detect_intent_texts('myrrorbot-4f360',$testo,'123456');