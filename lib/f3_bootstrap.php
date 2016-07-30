<?php

global $f3;
$f3 = require(__DIR__ . '/fatfree-master/lib/base.php');
$f3->config(__DIR__ . '/../config.ini');

$url = $f3->get("SCHEME")."://".$f3->get("HOST").$f3->get("BASE");
$f3->set("BASEURL", $url);

#$f3->set('ONERROR', function($f3) { echo \Template::instance()->render('pages/error.htm'); });

$f3->set('tweetpics', new TweetPics(__DIR__ . '/../db.ini'));

?>
