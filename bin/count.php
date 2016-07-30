<?php

include_once(__DIR__ . '/../lib/includes.php');

$tweetpics = new TweetPics(__DIR__ . '/../db.ini');

echo $tweetpics->number_of_tweets() . "\n";
