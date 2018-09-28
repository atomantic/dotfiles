<?php
/**
 * Name:        Workflows
 * Description:    This PHP class object provides several useful functions for retrieving, parsing,
 *                and formatting data to be used with Alfred 3 Workflows.
 * Author:        David Ferguson (@jdfwarrior)
 * Revised:        2/9/2013
 * Version:        0.3.
 */
class Workflows

{
	private $cache;
	private $data;
	private $bundle;
	private $path;
	private $home;
	private $results;
	/**
	 * Description:
	 * Class constructor function. Intializes all class variables. Accepts one optional parameter
	 * of the workflow bundle id in the case that you want to specify a different bundle id. This
	 * would adjust the output directories for storing data.
	 *
	 * @param $bundleid - optional bundle id if not found automatically
	 *
	 * @return none
	 */
	public

	function __construct($bundleid = null)
	{
		$this->home = getenv('HOME');
		if (!is_null($bundleid)):
			$this->bundle = $bundleid;
		else:
			if (file_exists('info.plist')):
				$this->path();
				$this->bundle = $this->get('bundleid', 'info.plist');
			endif;
		endif;
		$this->cache = $this->home . '/Library/Caches/com.runningwithcrayons.Alfred-3/Workflow Data/' . $this->bundle;
		$this->data = $this->home . '/Library/Application Support/Alfred 3/Workflow Data/' . $this->bundle;
		if (!file_exists($this->cache)):
			exec("mkdir -p '" . $this->cache . "'");
		endif;
		if (!file_exists($this->data)):
			exec("mkdir -p '" . $this->data . "'");
		endif;
		$this->results = array();
	}

	/**
	 * Description:
	 * Accepts no parameter and returns the value of the bundle id for the current workflow.
	 * If no value is available, then false is returned.
	 *
	 * @param none
	 *
	 * @return false if not available, bundle id value if available
	 */
	public

	function bundle()
	{
		if (is_null($this->bundle)):
			return false;
		else:
			return $this->bundle;
		endif;
	}

	/**
	 * Description:
	 * Accepts no parameter and returns the value of the path to the cache directory for your
	 * workflow if it is available. Returns false if the value isn't available.
	 *
	 * @param none
	 *
	 * @return false if not available, path to the cache directory for your workflow if available
	 */
	public

	function cache()
	{
		if (is_null($this->bundle)):
			return false;
		else:
			if (is_null($this->cache)):
				return false;
			else:
				return $this->cache;
			endif;
		endif;
	}

	/**
	 * Description:
	 * Accepts no parameter and returns the value of the path to the storage directory for your
	 * workflow if it is available. Returns false if the value isn't available.
	 *
	 * @param none
	 *
	 * @return false if not available, path to the storage directory for your workflow if available
	 */
	public

	function data()
	{
		if (is_null($this->bundle)):
			return false;
		else:
			if (is_null($this->data)):
				return false;
			else:
				return $this->data;
			endif;
		endif;
	}

	/**
	 * Description:
	 * Accepts no parameter and returns the value of the path to the current directory for your
	 * workflow if it is available. Returns false if the value isn't available.
	 *
	 * @param none
	 *
	 * @return false if not available, path to the current directory for your workflow if available
	 */
	public

	function path()
	{
		if (is_null($this->path)):
			$this->path = getenv('PWD');
		endif;
		return $this->path;
	}

	/**
	 * Description:
	 * Accepts no parameter and returns the value of the home path for the current user
	 * Returns false if the value isn't available.
	 *
	 * @param none
	 *
	 * @return false if not available, home path for the current user if available
	 */
	public

	function home()
	{
		if (is_null($this->home)):
			return false;
		else:
			return $this->home;
		endif;
	}

	/**
	 * Description:
	 * Returns an array of available result items.
	 *
	 * @param none
	 *
	 * @return array - list of result items
	 */
	public

	function results()
	{
		return $this->results;
	}

	public

	function tojson($a = null)
	{
		if (is_null($a) && !empty($this->results)):
			$a = $this->results;
		elseif (is_null($a) && empty($this->results)):
			return false;
		endif;
		$items = array();

		// cat << EOB
		// {"items": [
		//	{
		//		"uid": "desktop",
		//		"type": "file",
		//		"title": "Desktop",
		//		"subtitle": "~/Desktop",
		//		"arg": "~/Desktop",
		//		"autocomplete": "Desktop",
		//		"icon": {
		//			"type": "fileicon",
		//			"path": "~/Desktop"
		//		}
		//	},
		//	{
		//		"valid": false,
		//		"uid": "flickr",
		//		"title": "Flickr",
		//		"icon": {
		//			"path": "flickr.png"
		//		}
		//	},
		//	{
		//		"uid": "image",
		//		"type": "file",
		//		"title": "My holiday photo",
		//		"subtitle": "~/Pictures/My holiday photo.jpg",
		//		"autocomplete": "My holiday photo",
		//		"icon": {
		//			"type": "filetype",
		//			"path": "public.jpeg"
		//		}
		//	},
		//	{
		//		"valid": false,
		//		"uid": "alfredapp",
		//		"title": "Alfred Website",
		//		"subtitle": "https://www.alfredapp.com/",
		//		"arg": "alfredapp.com",
		//		"autocomplete": "Alfred Website",
		//		"quicklookurl": "https://www.alfredapp.com/",
		//		"mods": {
		//			"alt": {
		//				"valid": true,
		//				"arg": "alfredapp.com/powerpack",
		//				"subtitle": "https://www.alfredapp.com/powerpack/"
		//			},
		//			"cmd": {
		//				"valid": true,
		//				"arg": "alfredapp.com/powerpack/buy/",
		//				"subtitle": "https://www.alfredapp.com/powerpack/buy/"
		//			},
		//		},
		//		"text": {
		//			"copy": "https://www.alfredapp.com/ (text here to copy)",
		//			"largetype": "https://www.alfredapp.com/ (text here for large type)"
		//		}
		//	}
		// ]}
		// EOB

		foreach($a as $b):
			$c = new StdClass(); // Loop through each object in the array
			$c_keys = array_keys($b); // Grab all the keys for that item
			foreach($c_keys as $key): // For each of those keys
				if ($key == 'uid'):
					if ($b[$key] === null || $b[$key] === ''):
						continue;
					else:
						$c->uid = $b[$key];
					endif;
				elseif ($key == 'arg'):
					$c->arg = $b[$key];
				elseif ($key == 'type'):
					$c->type = $b[$key];
				elseif ($key == 'quicklookurl'):
					$c->quicklookurl = $b[$key];
				elseif ($key == 'valid'):
					if ($b[$key] == 'yes' || $b[$key] == 'no'):
						$c->valid = $b[$key];
					endif;
				elseif ($key == 'autocomplete'):
					if ($b[$key] === null || $b[$key] === ''):
						continue;
					else:
						$c->autocomplete = $b[$key];
					endif;
				elseif ($key == 'icon'):
					$icon = new StdClass();
					if (substr($b[$key], 0, 9) == 'fileicon:'):
						$val = substr($b[$key], 9);
						$icon->path = $val;
						$icon->type = 'fileicon';
					elseif (substr($b[$key], 0, 9) == 'filetype:'):
						$val = substr($b[$key], 9);
						$icon->path = $val;
						$icon->type = 'filetype';
					else:
						$icon->path = $b[$key];
					endif;
					$c->icon = $icon;
				elseif ($key == 'subtitle'):
					if (gettype($b[$key]) == 'array'):
						$mods = new StdClass();
						$subtitle_types = array(
							'shift',
							'fn',
							'ctrl',
							'alt',
							'cmd'
						);
						$subtitles = $b[$key];
						$subtitle_keys = array_keys($subtitles);
						$c->subtitle = $subtitles[0];
						foreach($subtitle_keys as $subtitle_key):
							if (in_array($subtitle_key, $subtitle_types, true)):
								$m = new StdClass();
								$m->valid = 'true';
								$m->arg = $c->arg;
								$m->subtitle = $subtitles[$subtitle_key];
								$mods->$subtitle_key = $m;
							endif;
						endforeach;
						$c->mods = $mods;
					else:
						$c->$key = $b[$key];
					endif;
				elseif ($key == 'text' && gettype($b[$key]) == 'array'):
					$text = new StdClass();
					$text_types = array(
						'copy',
						'largetype'
					);
					$texts = $b[$key];
					$text_keys = array_keys($texts);
					foreach($text_keys as $text_key):
						if (in_array($text_key, $text_types)):
							$text->$text_key = $texts[$text_key];
						endif;
					endforeach;
					$c->text = $text;
				else:
					$c->$key = $b[$key];
				endif;
			endforeach;
			array_push($items, $c);
		endforeach;
		$results = new StdClass();
		$results->items = $items;
		return json_encode($results);
	}

	/**
	 * Description:
	 * Convert an associative array into XML format.
	 *
	 * @param $a - An associative array to convert
	 * @param $format - format of data being passed (json or array), defaults to array
	 *
	 * @return - XML string representation of the array
	 */
	public

	function toxml($a = null, $format = 'array')
	{
		if ($format == 'json'):
			$a = json_decode($a, true);
		endif;
		if (is_null($a) && !empty($this->results)):
			$a = $this->results;
		elseif (is_null($a) && empty($this->results)):
			return false;
		endif;
		$items = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><items></items>'); // Create new XML element
		foreach($a as $b): // Loop through each object in the array
			$c = $items->addChild('item'); // Add a new 'item' element for each object
			$c_keys = array_keys($b); // Grab all the keys for that item
			foreach($c_keys as $key): // For each of those keys
				if ($key == 'uid'):
					if ($b[$key] === null || $b[$key] === ''):
						continue;
					else:
						$c->addAttribute('uid', $b[$key]);
					endif;
				elseif ($key == 'arg'):
					$c->addAttribute('arg', $b[$key]);
					$c->$key = $b[$key];
				elseif ($key == 'type'):
					$c->addAttribute('type', $b[$key]);
				elseif ($key == 'valid'):
					if ($b[$key] == 'yes' || $b[$key] == 'no'):
						$c->addAttribute('valid', $b[$key]);
					endif;
				elseif ($key == 'autocomplete'):
					if ($b[$key] === null || $b[$key] === ''):
						continue;
					else:
						$c->addAttribute('autocomplete', $b[$key]);
					endif;
				elseif ($key == 'icon'):
					if (substr($b[$key], 0, 9) == 'fileicon:'):
						$val = substr($b[$key], 9);
						$c->$key = $val;
						$c->$key->addAttribute('type', 'fileicon');
					elseif (substr($b[$key], 0, 9) == 'filetype:'):
						$val = substr($b[$key], 9);
						$c->$key = $val;
						$c->$key->addAttribute('type', 'filetype');
					else:
						$c->$key = $b[$key];
					endif;
				elseif ($key == 'subtitle'):
					if (gettype($b[$key]) == 'array'):
						$subtitle_types = array(
							'shift',
							'fn',
							'ctrl',
							'alt',
							'cmd'
						);
						$subtitles = $b[$key];
						$subtitle_keys = array_keys($subtitles);
						foreach($subtitle_keys as $subtitle_key):
							$subtitle_element = $c->addChild('subtitle', $subtitles[$subtitle_key]);
							if (in_array($subtitle_key, $subtitle_types, true)):
								$subtitle_element->addAttribute('mod', $subtitle_key);
							endif;
						endforeach;
					else:
						$c->$key = $b[$key];
					endif;
				elseif ($key == 'text' && gettype($b[$key]) == 'array'):
					$text_types = array(
						'copy',
						'largetype'
					);
					$texts = $b[$key];
					$text_keys = array_keys($texts);
					foreach($text_keys as $text_key):
						if (in_array($text_key, $text_types)):
							$c->addChild('text', $texts[$text_key])->addAttribute('type', $text_key);
						endif;
					endforeach;
				else:
					$c->$key = $b[$key];
				endif;
			endforeach;
		endforeach;
		return $items->asXML(); // Return XML string representation of the array
	}

	/**
	 * Description:
	 * Remove all items from an associative array that do not have a value.
	 *
	 * @param $a - Associative array
	 *
	 * @return bool
	 */
	private
	function empty_filter($a)
	{
		if ($a == '' || $a == null): // if $a is empty or null
			return false; // return false, else, return true
			else:
				return true;
			endif;
		}

		/**
		 * Description:
		 * Save values to a specified plist. If the first parameter is an associative
		 * array, then the second parameter becomes the plist file to save to. If the
		 * first parameter is string, then it is assumed that the first parameter is
		 * the label, the second parameter is the value, and the third parameter is
		 * the plist file to save the data to.
		 *
		 * @param $a - associative array of values to save
		 * @param $b - the value of the setting
		 * @param $c - the plist to save the values into
		 *
		 * @return string - execution output
		 */
		public

		function set($a = null, $b = null, $c = null)
		{
			if (is_array($a)):
				if (file_exists($b)):
					if (file_exists($this->path . '/' . $b)):
						$b = $this->path . '/' . $b;
					endif;
				elseif (file_exists($this->data . '/' . $b)):
					$b = $this->data . '/' . $b;
				elseif (file_exists($this->cache . '/' . $b)):
					$b = $this->cache . '/' . $b;
				else:
					$b = $this->data . '/' . $b;
				endif;
			else:
				if (file_exists($c)):
					if (file_exists($this->path . '/' . $c)):
						$c = $this->path . '/' . $c;
					endif;
				elseif (file_exists($this->data . '/' . $c)):
					$c = $this->data . '/' . $c;
				elseif (file_exists($this->cache . '/' . $c)):
					$c = $this->cache . '/' . $c;
				else:
					$c = $this->data . '/' . $c;
				endif;
			endif;
			if (is_array($a)):
				foreach($a as $k => $v):
					exec('defaults write "' . $b . '" ' . $k . ' "' . $v . '"');
				endforeach;
			else:
				exec('defaults write "' . $c . '" ' . $a . ' "' . $b . '"');
			endif;
		}

		/**
		 * Description:
		 * Read a value from the specified plist.
		 *
		 * @param $a - the value to read
		 * @param $b - plist to read the values from
		 *
		 * @return bool false if not found, string if found
		 */
		public

		function get($a, $b)
		{
			if (file_exists($b)):
				if (file_exists($this->path . '/' . $b)):
					$b = $this->path . '/' . $b;
				endif;
			elseif (file_exists($this->data . '/' . $b)):
				$b = $this->data . '/' . $b;
			elseif (file_exists($this->cache . '/' . $b)):
				$b = $this->cache . '/' . $b;
			else:
				return false;
			endif;
			exec('defaults read "' . $b . '" ' . $a, $out); // Execute system call to read plist value
			if ($out == ''):
				return false;
			endif;
			$out = $out[0];
			return $out; // Return item value
		}

		/**
		 * Description:
		 * Read data from a remote file/url, essentially a shortcut for curl.
		 *
		 * @param $url - URL to request
		 * @param $options - Array of curl options
		 *
		 * @return result from curl_exec
		 */
		public

		function request($url = null, $options = null)
		{
			if (is_null($url)):
				return false;
			endif;
			$defaults = array( // Create a list of default curl options
				CURLOPT_RETURNTRANSFER => true, // Returns the result as a string
				CURLOPT_URL => $url, // Sets the url to request
				CURLOPT_FRESH_CONNECT => true,
			);
			if ($options):
				foreach($options as $k => $v):
					$defaults[$k] = $v;
				endforeach;
			endif;
			array_filter($defaults, // Filter out empty options from the array
			array(
				$this,
				'empty_filter'
			));
			$ch = curl_init(); // Init new curl object
			curl_setopt_array($ch, $defaults); // Set curl options
			$out = curl_exec($ch); // Request remote data
			$err = curl_error($ch);
			curl_close($ch); // End curl request
			if ($err):
				return $err;
			else:
				return $out;
			endif;
		}

		/**
		 * Description:
		 * Allows searching the local hard drive using mdfind.
		 *
		 * @param $query - search string
		 *
		 * @return array - array of search results
		 */
		public

		function mdfind($query)
		{
			exec('mdfind "' . $query . '"', $results);
			return $results;
		}

		/**
		 * Description:
		 * Accepts data and a string file name to store data to local file as cache.
		 *
		 * @param array - data to save to file
		 * @param file - filename to write the cache data to
		 *
		 * @return none
		 */
		public

		function write($a, $b)
		{
			if (file_exists($b)):
				if (file_exists($this->path . '/' . $b)):
					$b = $this->path . '/' . $b;
				endif;
			elseif (file_exists($this->data . '/' . $b)):
				$b = $this->data . '/' . $b;
			elseif (file_exists($this->cache . '/' . $b)):
				$b = $this->cache . '/' . $b;
			else:
				$b = $this->data . '/' . $b;
			endif;
			if (is_array($a)):
				$a = json_encode($a);
				file_put_contents($b, $a);
				return true;
			elseif (is_string($a)):
				file_put_contents($b, $a);
				return true;
			else:
				return false;
			endif;
		}

		/**
		 * Description:
		 * Returns data from a local cache file.
		 *
		 * @param file - filename to read the cache data from
		 *
		 * @return false if the file cannot be found, the file data if found. If the file
		 *               format is json encoded, then a json object is returned
		 */
		public

		function read($a, $array = false)
		{
			if (file_exists($a)):
				if (file_exists($this->path . '/' . $a)):
					$a = $this->path . '/' . $a;
				endif;
			elseif (file_exists($this->data . '/' . $a)):
				$a = $this->data . '/' . $a;
			elseif (file_exists($this->cache . '/' . $a)):
				$a = $this->cache . '/' . $a;
			else:
				return false;
			endif;
			$out = file_get_contents($a);
			if (!is_null(json_decode($out)) && !$array):
				$out = json_decode($out);
			elseif (!is_null(json_decode($out)) && !$array):
				$out = json_decode($out, true);
			endif;
			return $out;
		}

		/**
		 * Description:
		 * Helper function that just makes it easier to pass values into a function
		 * and create an array result to be passed back to Alfred.
		 *
		 * @param $uid - the uid of the result, should be unique
		 * @param $arg - the argument that will be passed on
		 * @param $title - The title of the result item
		 * @param $sub - The subtitle text for the result item; can be an array of mod values or a string
		 * @param $icon - the icon to use for the result item
		 * @param $valid - sets whether the result item can be actioned
		 * @param $text - array with keys 'copy' and/or 'largetype' and their respective string values
		 * @param $auto - the autocomplete value for the result item
		 *
		 * @return array - array item to be passed back to Alfred
		 */
		public

		function result($uid, $arg, $title, $sub, $icon, $valid = 'yes', $text = null, $auto = null, $type = null, $quicklookurl = null)
		{
			$temp = array(
				'uid' => $uid,
				'arg' => $arg,
				'title' => $title,
				'subtitle' => $sub,
				'icon' => $icon,
				'valid' => $valid,
				'text' => $text,
				'autocomplete' => $auto,
				'type' => $type,
				'quicklookurl' => $quicklookurl,
			);
			if (is_null($type)):
				unset($temp['type']);
			endif;
			if (is_null($quicklookurl)):
				unset($temp['quicklookurl']);
			endif;
			array_push($this->results, $temp);
			return $temp;
		}

		public

		function internet()
		{
			$internet = @fsockopen('api.spotify.com', 80);
			if ($internet):
				fclose($internet);
				return true;
			else:
				return false;
			endif;
		}
	}