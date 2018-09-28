<?php

require './vendor/autoload.php';
require './src/functions.php';
require_once './src/workflows.php';
$w = new Workflows('com.vdesabou.spotify.mini.player');

//
// Read settings from JSON
//

$settings = getSettings($w);

$oauth_client_id = $settings->oauth_client_id;
$oauth_client_secret = $settings->oauth_client_secret;
$oauth_redirect_uri = $settings->oauth_redirect_uri;

try {
	$session = new SpotifyWebAPI\Session($oauth_client_id, $oauth_client_secret, $oauth_redirect_uri);

	if (!empty($_GET['code'])) {

		// Request a access token using the code from Spotify
		$ret = $session->requestAccessToken($_GET['code']);

		if ($ret == true) {
			$api = new SpotifyWebAPI\SpotifyWebAPI();
			// Set the code on the API wrapper
			$api->setAccessToken($session->getAccessToken());
			$user = $api->me();

		    $ret = updateSetting($w,'oauth_access_token',$session->getAccessToken());
		    if($ret == false) {
			 	echo "There was an error when updating settings";
			 	exec("kill -9 $(ps -efx | grep \"php -S localhost:15298\"  | grep -v grep | awk '{print $2}')");
			 	return;
		    }

		    $ret = updateSetting($w,'oauth_expires',time());
		    if($ret == false) {
			 	echo "There was an error when updating settings";
			 	exec("kill -9 $(ps -efx | grep \"php -S localhost:15298\"  | grep -v grep | awk '{print $2}')");
			 	return;
		    }

		    $ret = updateSetting($w,'oauth_refresh_token',$session->getRefreshToken());
		    if($ret == false) {
			 	echo "There was an error when updating settings";
			 	exec("kill -9 $(ps -efx | grep \"php -S localhost:15298\"  | grep -v grep | awk '{print $2}')");
			 	return;
		    }

		    $ret = updateSetting($w,'country_code',$user->country);
		    if($ret == false) {
			 	echo "There was an error when updating settings";
			 	exec("kill -9 $(ps -efx | grep \"php -S localhost:15298\"  | grep -v grep | awk '{print $2}')");
			 	return;
		    }

		    $ret = updateSetting($w,'display_name',$user->display_name);
		    if($ret == false) {
			 	echo "There was an error when updating settings";
			 	exec("kill -9 $(ps -efx | grep \"php -S localhost:15298\"  | grep -v grep | awk '{print $2}')");
			 	return;
		    }

		    $ret = updateSetting($w,'userid',$user->id);
		    if($ret == false) {
			 	echo "There was an error when updating settings";
			 	exec("kill -9 $(ps -efx | grep \"php -S localhost:15298\"  | grep -v grep | awk '{print $2}')");
			 	return;
		    }

			echo "Hello $user->display_name ! You are now successfully logged and you can close this window.";

		} else {
			echo "There was an error during the authentication (could not get token)";
		}
	} else {
		echo "There was an error during the authentication (could not get code)";
	}

}
catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
	echo "There was an error during the authentication (exception " . $e . ")";
}

exec("kill -9 $(ps -efx | grep \"php -S localhost:15298\"  | grep -v grep | awk '{print $2}')");
?>
