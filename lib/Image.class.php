<?php

class Image extends \DB\SQL\Mapper {
	private $tweetpics;

	public function __construct($tweetpics) {
		$this->tweetpics = $tweetpics;
		
		parent::__construct($tweetpics->database, 'image' );
	}

	/******
	*
	* Process data from twitter to set fields in this object
	*
	* Arguments: Data strcture as parsed from JSON returned by twitter API for one media object
	* Returns: Nothing
	*
	*******/
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

	/******
	*
	* As part of the hydration process, download and store an image file
	*
	* Arguments: The public URL of the image
	* Returns: Nothing
	*
	*******/
	private function download_image($image_url)
	{
		$image = file_get_contents($image_url);

		file_put_contents($this->file_path(), $image);
	}

	/******
	*
	* Returns the location of the image file
	*
	* Arguments: None
	* Returns: The path (a relative one) to the image file (or where it hsould be saved)
	*
	*******/
	public function file_path()
	{
		$path = $this->tweetpics->www_dir() . '/images/' . $this->filename;
		if (file_exists($path))
		{
			return $path;
		}
		return null;
	}

	/******
	*
	* Returns the URL of the image relative to the base of the site
	*
	* Arguments: None
	* Returns: The URL relative to the base URL of the site (e.g. /images/foo.jpg
	*
	*******/
	public function relative_url()
	{
		return '/images/' . $this->filename;
	}

	/******
	*
	* Used when laying out the images -- is it a wide picture
	*
	* Arguments: None
	* Returns: Returns true if the image is wider than it is tall, false otherwise
	*
	*******/
	public function is_wide()
	{
		$path = $this->file_path();

		if (!$path)
		{
			return false;
		}

		$info = getimagesize($this->file_path());
		if ($info[0] > $info[1])
		{
			return true;
		}
		return false;
	}

}




?>
