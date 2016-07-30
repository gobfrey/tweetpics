<?php

include_once(__DIR__ . '/../lib/twitter-api-php/TwitterAPIExchange.php');
include_once(__DIR__ . '/../lib/includes.php');

$tweetpics = new TweetPics(__DIR__ . '/../db.ini');

$settings = parse_ini_file(__DIR__ . '/../twitter_oath.ini');


$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
$getfield = '?screen_name=gobfrey&count=200&exclude_replies=true&include_rts=false';
$requestMethod = 'GET';



$twitter = new TwitterAPIExchange($settings);
$response = $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();

$tweets = json_decode($response);

foreach ($tweets as $tweet)
{
	if (tweet_is_interesting($tweet))
	{
		$tweetpics->create_tweet($tweet);
	}

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

	if ($tweetpics->tweet_id_exists($tweet->id)) //is it already in the database?
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


