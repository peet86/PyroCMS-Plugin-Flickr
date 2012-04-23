<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Flickr Plugin
 * @author Philsquare LLC, philsquare.com
 *
 */
class Plugin_Flickr extends Plugin
{
	/**
	 * Flickr
	 *
	 * Usage:
	 * {{ flickr:images limit="5" }}
	 *
	 * @param	array
	 * @return	array
	 */
	function images()
	{
		$limit		= $this->attribute('limit', 20);
		$offset     = $this->attribute('offset', 0);
		$api_key    = $this->attribute('api_key');
		$photoset_id = $this->attribute('photoset_id');
		
		$params = array(
			'api_key'	=> $api_key,
			'method'	=> 'flickr.photosets.getPhotos',
			'photoset_id'	=> $photoset_id,
			'extras'	=> 'url_sq',
			'format'	=> 'php_serial',
			'per_page'  => $limit,
			'page'      => $offset
		);
		
		$encoded_params = array();
		foreach ($params as $k => $v){ $encoded_params[] = urlencode($k).'='.urlencode($v); }
		
		
		$ch = curl_init();
		$timeout = 5; // set to zero for no timeout
		curl_setopt ($ch, CURLOPT_URL, 'http://api.flickr.com/services/rest/?'.implode('&', $encoded_params));
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);

		$rsp_obj = unserialize($file_contents);
		$images = array();
		
		// build
		if ($rsp_obj['stat'] == 'ok')
		{
			$photos = $rsp_obj["photoset"]["photo"];

			foreach($photos as $photo)
			{

				$farm              = $photo['farm'];
				$server            = $photo['server'];
				$photo_id          = $photo['id'];
				$secret            = $photo['secret'];
				$photo_title       = $photo['title'];

				$images[]->img = '<img src="http://farm'.$photo['farm'].'.static.flickr.com/'.$photo['server'].'/'.$photo['id'].'_'.$photo['secret'].'_t.jpg" alt="'.$photo['title'].'" />';
			}
		}
		else
		{
			return NULL;
		}
		
		return $images;
		
	}
}

/* End of file flickr.php */