  <?php
    require_once "Affects.php";
    require_once "readLocaljson.php";

    // Call set_include_path() as needed to point to your client library.
    require_once ('google-api-php-client/src/Google_Client.php');
    require_once ('google-api-php-client/src/contrib/Google_YouTubeService.php');

    include "url.php";


function explainVideo($email){

  $emotion = getLastEmotion($email);
  $answer = "Ti ho consigliato questo video perchè ";
    switch ($emotion) {
      case 'gioia':
        $answer .= "sei felice  &#x1f601";
        break;

      case 'paura':
       $answer .= "sei spaventato &#x1f628";
        break;

      case 'rabbia':
        $answer .= "sei arrabbiato &#x1f621";
        break;

      case 'disgusto':
        $answer .= "sei disgustato &#x1f629";
        break;

      case 'tristezza':
       $answer .= "sei triste &#x1f625";
        break;

      case 'sorpresa':
        $answer .= "sei sorpreso &#x1f631";
        break;
      
      default:
       $answer .= "il tuo sato d'animo è neutro  &#x1f636";
        break;
    }

    return $answer;
}

  function getVideoByEmotion($resp,$parameters,$text,$email){
    $DEVELOPER_KEY = "AIzaSyADh47AR3xdQPMT0oXTPJatZQ_Cbhw9YhM";

    $videos = array();
    $emotion = getLastEmotion($email);
    $q = "";
    $searchResponse = "";

    $client = new Google_Client();
    $client->setDeveloperKey($DEVELOPER_KEY);
    $htmlBody = '';
    $youtube = new Google_YoutubeService($client);

    try {
    switch ($emotion) {
      case 'gioia':
        $q = "video divertenti";
        break;

      case 'paura':
        $q = "video motivazionali";
        break;

      case 'rabbia':
        $q = "video rilassanti";
        break;

      case 'disgusto':
        $q = "video simpatici";
        break;

      case 'tristezza':
        $q = "video divertenti";
        break;

      case 'sorpresa':
        $q = "video notizie";
        break;
      
      default:
        $q = "video notizie";
        break;
    }


      if( $searchResponse == ""){
          $searchResponse = $youtube->search->listSearch('id,snippet', array(
          'q' => $q,
          'type' => 'video',
          'maxResults' => 10
        ));
      }
      

         foreach ($searchResponse['items'] as $searchResult) {
          switch ($searchResult['id']['kind']) {
            case 'youtube#video':
             array_push($videos, "http://www.youtube.com/embed/".$searchResult['id']['videoId']);
              break;
          
           }
        }

       $ind = rand(0,sizeof($videos) -1);
       $spiegazione = explainVideo($email);
  
      return array("ind" => $videos[$ind], "explain" => $spiegazione);
      
        
      } catch (Google_ServiceException $e) {
        $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
          htmlspecialchars($e->getMessage()));
      } catch (Google_Exception $e) {
        $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
          htmlspecialchars($e->getMessage()));
      }
  
       return $htmlBody;

  }




  function getVideoBySearch($resp,$parameters,$text,$email){
    $DEVELOPER_KEY = "AIzaSyADh47AR3xdQPMT0oXTPJatZQ_Cbhw9YhM";

    $videos = array();
    $emotion = getLastEmotion($email);
    $q = "";
    $searchResponse = "";

    $client = new Google_Client();
    $client->setDeveloperKey($DEVELOPER_KEY);
    $htmlBody = '';
    $youtube = new Google_YoutubeService($client);

   
    //Verifico se è presente il genere del brano
    if ($parameters['any'] != "") {
      $q = $parameters['any']; //Genere del brano
    }else{
      $q = "";
      return "Frase non riconosciuto. riprova!";
    }

      if( $searchResponse == ""){
          $searchResponse = $youtube->search->listSearch('id,snippet', array(
          'q' => $q,
          'type' => 'video',
          'maxResults' => 10
        ));
      }
      

         foreach ($searchResponse['items'] as $searchResult) {
          switch ($searchResult['id']['kind']) {
            case 'youtube#video':
             array_push($videos, "http://www.youtube.com/embed/".$searchResult['id']['videoId']);
              break;
          
           }
        }

       $ind = rand(0,sizeof($videos) -1);
       $spiegazione = explainVideo($email);
  
      return array("ind" => $videos[$ind], "explain" => "");


  }



  //Inserisci la preferenza dell'utente relativo ai VIDEO
function insertPreferenceVideo($parameters,$text,$email){

  if (isset($_COOKIE['x-access-token'])) {
    $token =  $_COOKIE['x-access-token']; 


    if ($parameters['preferencepositive'] != "") { //Preference POSITIVE

      if ($parameters['any'] != ""){

        
        $videoPreference = [
              'username'=> $email,
              'title'=> $parameters['any'],
              'like'=> 1,
              'timestamp'=> time()
        ];

      }


    } elseif ($parameters['preferencenegative'] != "") {//Preference NEGATIVE
        
        if ($parameters['any'] != ""){

          $videoPreference = [
                'username'=> $email,
                'title'=> $parameters['any'],
                'like'=> 0,
                'timestamp'=> time()
          ];

      }
    }


      $ch = curl_init();
        $headers =[
            "x-access-token:".$token
        ];

        curl_setopt($ch, CURLOPT_URL, "http://".$GLOBALS['url'].
          ":5000/api/video/");

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($videoPreference));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);       
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);   

        curl_exec($ch);

        //Decode JSON
        //$json_data = json_decode($result2,true);

        curl_close ($ch);

        return "Preferenza sui video inserita";

  }

}

?>