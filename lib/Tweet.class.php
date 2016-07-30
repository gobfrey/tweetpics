<?php

class Tweet extends \DB\SQL\Mapper {
	private $tweetpics;

	public function __construct($tweetpics) {
		$this->tweetpics = $tweetpics;
		parent::__construct($tweetpics->database, 'tweet' );
	}


	public function hydrate_with_twitter_data ($twitter_tweet_object)
	{
		$this->tweet_id = $twitter_tweet_object->id;
		$this->text = $twitter_tweet_object->text;

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

	public function images()
	{
		$image = new Image($this->tweetpics);
		$list = $image->find('tweet_id=' . $this->tweet_id);
		return $list;
	}

	public function attach_image ($twitter_media_object)
	{
		$image = new Image($this->tweetpics);
		$image->tweet_id = $this->tweet_id;
		$image->hydrate_with_twitter_data($twitter_media_object);
		$image->save();
	}
}

?>
