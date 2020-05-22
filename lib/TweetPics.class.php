<?php

class TweetPics
{
	public $database;
	private $config;

	public function __construct($db_ini_file)
	{
		$this->connect_to_db($db_ini_file);
	}

	/******
	*
	* Returns the base directory of this installation (actually the parent directory of
	* the lib directory in which this file resides)
	*
	*******/
	public function base_dir ()
	{
		return __DIR__ . '/..';
	}

	/******
	*
	* Returns the web root directory 
	*
	*******/
	public function www_dir ()
	{
		return $this->base_dir() . '/www';
	}

	/******
	*
	* Check if a tweet exists in the database
	*
	* Arguments: a tweet ID as supplied by twitter
	* Returns: true if the tweet exists in our database, false otherwise
	*
	*******/
	public function tweet_id_exists($tweet_id)
	{
		$tweet = new Tweet($this);
		$n = $tweet->count("tweet_id='$tweet_id'");
		return ( ( $n > 0 ) ? true : false );
	}

	/******
	*
	* Check if a tweet exists in the database
	*
	* Arguments: a tweet ID as supplied by twitter
	* Returns: true if the tweet exists in our database, false otherwise
	*
	*******/
	public function image_id_exists($image_id)
	{
		$tweet = new Image($this);
		$n = $tweet->count("image_id='$image_id'");
		return ( ( $n > 0 ) ? true : false );
	}

	/******
	*
	* Count the number of tweets in the database
	*
	* Arguments: None
	* Returns: The number of tweets in the database
	*
	*******/
	public function number_of_tweets()
	{
		$tweet = new Tweet($this);
		return $tweet->count();
	}


	/******
	*
	* Get a tweet with a known ID
	*
	* Arguments: a tweet ID
	* Returns: a tweet object
	*
	*******/
	public function tweet ($tweet_id)
	{
		$tweet = new Tweet($this);
		$tweet->load("tweet_id=$tweet_id");
		return $tweet;
	}

	/******
	*
	* Returns a subset of all tweets in the database in reverse chronological order
	*
	* Arguments:
		* count (integer) : how many tweets to return
		* offset (integer) : where in the ordered collection of tweets to start returning 
	* Returns: An array of tweet objects
	*
	*******/
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

	/******
	*
	* Creates a new tweet from parsed json data from twitter
	*
	* Arguments: A data structure as parsed from twitter API JSON
	* Returns: nothing
	*
	*******/
	public function create_tweet ($twitter_data)
	{
		$tweet = new Tweet($this);
		$tweet->hydrate_with_twitter_data($twitter_data);
		$tweet->save();
	}

	/******
	*
	* Creates a new tweet from parsed json data from twitter
	*
	* Arguments: A data structure as parsed from twitter API JSON
	* Returns: nothing
	*
	*******/
	public function update_tweet ($twitter_data)
	{
		$tweet_id = $twitter_data->id;
		if ($this->tweet_id_exists($tweet_id))
		{
			$tweet = $this->tweet($tweet_id);
			$tweet->hydrate_with_twitter_data($twitter_data);
			$tweet->update();
		}
		else
		{
			$this->create_tweet($twitter_data);
		}
	}

	/******
	*
	* Connects to the database and stored the connection in the $database object property.
	*
	* Arguments: The path to the database .ini file
	* Returns: Nothing
	*
	*******/
	private function connect_to_db($db_ini_file)
	{
		$params = parse_ini_file($db_ini_file);

		$connect_string = $this->generate_db_connect_string($params);
		$username = $params['username'];
		$password = $params['password'];
		$this->database =new \DB\SQL($connect_string,$username,$password);
	}

	/******
	*
	* Constructs the database connection string
	*
	* Arguments: Database parameters as parsed from the ini file 
	* Returns: a database connection string
	*
	*******/
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
