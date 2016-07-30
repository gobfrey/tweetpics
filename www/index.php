<?php

error_reporting(E_ALL);

ini_set('display_errors', 1);


require('../lib/includes.php');


global $f3;
$f3->run();


function frontpage ($f3)
{
	$tweetpics = $f3->get('tweetpics');

	//move to cfg;
	$page_size = 20;

	$page = $f3->get('PARAMS.page');
	if (!$page)
	{
		$page = 1;
	}

	$f3->set('page',$page);

	$f3->set('next_page', false);
	if ($tweetpics->number_of_tweets() > $page * $page_size)
	{
		$f3->set('next_page', true);
	}

	$count = $page_size;
	$offset = ($page-1) * $page_size;

	$f3->set('tweets', $tweetpics->tweets($count, $offset));

	echo \Template::instance()->render('pages/index.htm');
}

function tweet ($f3)
{
	$tweetpics = $f3->get('tweetpics');
	$tweet = $tweetpics->tweet($f3->get('PARAMS.tweet_id'));

	$f3->set('tweet', $tweet);
	echo \Template::instance()->render('pages/tweet.htm');
}

