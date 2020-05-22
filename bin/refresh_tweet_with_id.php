<?php

include_once(__DIR__ . '/../lib/twitter-api-php/TwitterAPIExchange.php');
include_once(__DIR__ . '/../lib/includes.php');

$tweetpics = new TweetPics(__DIR__ . '/../db.ini');

$settings = parse_ini_file(__DIR__ . '/../twitter_oath.ini');


$twitterid = $argv[1];

if (
	!$twitterid
	or !is_numeric($twitterid)
	or !is_int(1 + $twitterid)
)
{

	die ("refresh_tweet_with_id.php *tweetid*\n");
}

$url = 'https://api.twitter.com/1.1/statuses/show/' . $twitterid . '.json';
$params = array
(
	'id' => $twitterid,
	'include_entities' => true,
	'tweet_mode' => 'extended'
);

$getfield = '?' . http_build_query($params);
$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);
$response = $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();

$tweet = json_decode($response);

if (tweet_is_interesting($tweet))
{
	$tweetpics->update_tweet($tweet);
}

function tweet_is_interesting ($tweet)
{
	global $tweetpics;

	if (
		$tweet->user->screen_name != 'gobfrey' //is by me
		|| !property_exists($tweet->entities,'media') //has media attached
		|| property_exists($tweet, 'retweeted_status') // is not a retweet
	)
	{
		return false;
	}

	foreach ($tweet->entities->media as $media)  // is there at least on photo?
	{
		if ($media->type == 'photo')
		{
			return true;
		}
	}

	return false;
}


