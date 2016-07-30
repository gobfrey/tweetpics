<?php

class TweetPics
{
	public $database;
	private $config;

	public function __construct($db_ini_file)
	{
		$this->connect_to_db($db_ini_file);
	}

	public function base_dir ()
	{
		return __DIR__ . '/..';
	}

	public function www_dir ()
	{
		return $this->base_dir() . '/www';
	}

	public function tweet_id_exists($tweet_id)
	{
		$tweet = new Tweet($this);
		$n = $tweet->count("tweet_id='$tweet_id'");
		return ( ( $n > 0 ) ? true : false );
	}

	public function number_of_tweets()
	{
		$tweet = new Tweet($this);
		return $tweet->count();
	}


	public function tweet ($tweet_id)
	{
		$tweet = new Tweet($this);
		$tweet->load("tweet_id=$tweet_id");
		return $tweet;
	}

	public function image ($image_id)
	{
		$image = new Image($this);
		$image->load($image_id);
		return $image;
	}

	//return collection of tweets in ID (chronological) order
	public function tweets ($count = 10, $offset = 0)
	{
		$tweet_mapper = new Tweet($this);
		$tweets = $tweet_mapper->find(
			'',
			array(
				'order' => 'tweet_id DESC',
				'limit' => $count,
				'offset' => $offset
			)
		);
		return $tweets;
	}

	public function create_tweet ($twitter_data)
	{
		$tweet = new Tweet($this);
		$tweet->hydrate_with_twitter_data($twitter_data);
		$tweet->save();
	}





	private function connect_to_db($db_ini_file)
	{
		$params = parse_ini_file($db_ini_file);

		$connect_string = $this->generate_db_connect_string($params);
		$username = $params['username'];
		$password = $params['password'];
		$this->database =new \DB\SQL($connect_string,$username,$password);
	}

	private function generate_db_connect_string($db_params)
	{
		$host = 'localhost';
		if (array_key_exists('host',$db_params))	
		{
			$host = $db_params['host'];
		}

		$port = 3306;
		if (array_key_exists('port',$db_params))	
		{
			$port = $db_params['port'];
		}
		
		$dbname = $db_params['dbname'];

		$db_connect_string = "mysql:host=$host;port=$port;dbname=$dbname";


		return $db_connect_string;
	}

}
