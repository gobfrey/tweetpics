<?php
$lib_files = array(
'TweetPics.class.php',
'f3_bootstrap.php',
'Image.class.php',
'Tweet.class.php'
);

foreach ($lib_files as $file)
{
	require_once $file;
}
?>
