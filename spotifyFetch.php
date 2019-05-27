<?php
require 'vendor/autoload.php';

// Fetch the saved access token from somewhere. A cookie for example.
if(!isset($_COOKIE["Spotifytoken"])) {
    echo "access Token is not set!";
} else {
    $accessToken = $_COOKIE["Spotifytoken"];
}


$api = new SpotifyWebAPI\SpotifyWebAPI();
$api->setAccessToken($accessToken);

echo $accessToken;

/*It's now possible to request data from the Spotify catalog
print_r(
    $api->search('category','pop' ));
*/
/*
$results = $api->search('rap', 'category');
$playlist = $api->getUserPlaylist('username', '5zgeLghhm80q2zMcStonVL');
print_r($playlist);

foreach ($playlist->track  => $value) {
	echo $value;
}
*/
/*
foreach ($results->artists->items as $artist) {
    echo $artist->name, '<br>';
}
*/
$playlists = $api->getCategoryPlaylists('dinner', [
    'country' => 'se',
]);

print_r($playlists);

