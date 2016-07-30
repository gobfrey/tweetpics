<?php

class Image extends \DB\SQL\Mapper {
	private $tweetpics;

	public function __construct($tweetpics) {
		$this->tweetpics = $tweetpics;
		
		parent::__construct($tweetpics->database, 'image' );
	}

	public function hydrate_with_twitter_data($twitter_extended_media_object)
	{
		$this->image_id = $twitter_extended_media_object->id;
		$url = $twitter_extended_media_object->media_url_https;
		$parts = explode('/',$url);
		$filename = array_pop($parts);

		$this->filename = $filename;
		$this->save();


		$this->download_image($url);

	}

	private function download_image($image_url)
	{
		$image = file_get_contents($image_url);

		file_put_contents($this->file_path(), $image);
	}

	public function file_path()
	{
		return $this->tweetpics->www_dir() . '/images/' . $this->filename;
	}

	public function relative_url()
	{
		return '/images/' . $this->filename;
	}

}




?>
