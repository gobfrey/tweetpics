<?php

class Tweet extends \DB\SQL\Mapper {
	private $tweetpics;

	public function __construct($tweetpics) {
		$this->tweetpics = $tweetpics;
		parent::__construct($tweetpics->database, 'tweet' );
	}

	/******
	*
	* Process data from twitter to set fields in this object
	*
	* Arguments: Data strcture as parsed from JSON returned by twitter API for one tweet
	* Returns: Nothing
	*
	*******/
	public function hydrate_with_twitter_data ($twitter_tweet_object)
	{
		$this->tweet_id = $twitter_tweet_object->id;
		$this->text = $twitter_tweet_object->full_text;
		if (!$this->text)
		{
			$this->text = $twitter_tweet_object->text;
		}

		$datetime = new DateTime($twitter_tweet_object->created_at);
		$datetime->setTimezone(new DateTimeZone('Europe/London'));
		$this->time = $datetime->format('Y-m-d H:i:s');

		$this->save();

		if (
			property_exists($twitter_tweet_object, 'extended_entities')
			&& property_exists($twitter_tweet_object->extended_entities, 'media')
		)
		{
			foreach ($twitter_tweet_object->extended_entities->media as $media)
			{
				if ($media->type == 'photo')
				{
					$this->attach_image($media);
				}
			}
		}
	}

	/******
	*
	* Gets the images attached to this tweet
	*
	* Arguments: None
	* Returns: An array of Image objects
	*
	*******/
	public function images()
	{
		$image = new Image($this->tweetpics);
		$list = $image->find('tweet_id=' . $this->tweet_id);
		return $list;
	}

	/******
	*
	* As part of the hydration process, attach create and associate image objects
	*
	* Arguments: Data as parsed from JSON returned from twitter API (an elemrnt of the extended_entities array)
	* Returns: nothing
	*
	*******/
	public function attach_image ($twitter_media_object)
	{
		$image = new Image($this->tweetpics);
		$image->tweet_id = $this->tweet_id;
		$image->hydrate_with_twitter_data($twitter_media_object);
		$image->save();
	}
}

?>
