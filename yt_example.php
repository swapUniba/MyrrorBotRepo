<?php
//if ($_GET['q'] && $_GET['maxResults']) {
  // Call set_include_path() as needed to point to your client library.
  require_once ('google-api-php-client/src/Google_Client.php');
  require_once ('google-api-php-client/src/contrib/Google_YouTubeService.php');

  /* Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the
  Google APIs Console <http://code.google.com/apis/console#access>
  Please ensure that you have enabled the YouTube Data API for your project. */
  //$DEVELOPER_KEY = 'AIzaSyDOkg-u9jnhP-WnzX5WPJyV1sc5QQrtuyc';
  $DEVELOPER_KEY = "AIzaSyADh47AR3xdQPMT0oXTPJatZQ_Cbhw9YhM";

  $client = new Google_Client();
  $client->setDeveloperKey($DEVELOPER_KEY);
  $htmlBody = '';
  $youtube = new Google_YoutubeService($client);

  try {
    $searchResponse = $youtube->search->listSearch('id,snippet', array(
      'q' => 'valentino rossi 2008',
      'maxResults' => 10
    ));

    $videos = '';
    $channels = '';

//print_r($searchResponse);

    foreach ($searchResponse['items'] as $searchResult) {
      switch ($searchResult['id']['kind']) {
        case 'youtube#video':
          $videos .= sprintf('<li>%s (%s)</li>', $searchResult['snippet']['title'],
            $searchResult['id']['videoId']."<a href=http://www.youtube.com/watch?v=".$searchResult['id']['videoId']." target=_blank>   Watch This Video</a>");
          break;
        case 'youtube#channel':
          $channels .= sprintf('<li>%s (%s)</li>', $searchResult['snippet']['title'],
            $searchResult['id']['channelId']);
          break;
       }
    }

    echo $videos;

   } catch (Google_ServiceException $e) {
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  }
  echo $htmlBody;
//}
?>