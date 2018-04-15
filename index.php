<?php
session_start();
require 'autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

define('CONSUMER_KEY', 'r51KkaJeeAZvoieKg27Og25RB'); // add your app consumer key between single quotes
define('CONSUMER_SECRET', 'mwhSayNtEXVr2g64IrWcQ03PsDc74bcEjs6aSNaXVP7wLcvp4b'); // add your app consumer secret key between single quotes
define('OAUTH_CALLBACK', 'http://www.devprueba.com.mialias.net/twitter/callback.php'); // your app callback URL

if (!isset($_SESSION['access_token'])) {
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
	$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));
	$_SESSION['oauth_token'] = $request_token['oauth_token'];
	$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
	$url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
	//echo $url;
} else {
	$access_token = $_SESSION['access_token'];
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

	// getting basic user info
	$user = $connection->get("account/verify_credentials");

	// printing username on screen
	//echo "Welcome " . $user->screen_name . '<br>';
	// getting recent tweeets by user 'snowden' on twitter
	$tweets = $connection->get('statuses/user_timeline', ['count' => 8, 'exclude_replies' => true, 'screen_name' => 'ThePracticalDev', 'include_rts' => false]);
	$totalTweets[] = $tweets;
	$page = 0;
	for ($count = 0; $count < 10; $count += 10) {
		$max = count($totalTweets[$page]) - 1;
		$tweets = $connection->get('statuses/user_timeline', ['count' => 8, 'exclude_replies' => true, 'max_id' => $totalTweets[$page][$max]->id_str, 'screen_name' => 'ThePracticalDev', 'include_rts' => false]);
		$totalTweets[] = $tweets;
		$page += 1;
	}
	// printing recent tweets on screen
	$start = 1;
	$response = array();
	foreach ($totalTweets as $page) {
		foreach ($page as $key) {
			//echo $start . ':' . $key->text . '<br>';
			//echo "<pre>";
			$array['fecha'] = $key->created_at;
			$array['text'] = $key->text;
			$array['url'] = $key->user->profile_image_url;
			//echo print_r($key);

			array_push($response,$array);
			//echo "</pre>";

			$start++;
		}
	}
	echo json_encode($response);
}
