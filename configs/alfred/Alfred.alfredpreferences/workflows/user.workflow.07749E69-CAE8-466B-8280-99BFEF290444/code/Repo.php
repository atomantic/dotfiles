<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');

class Repo
{
	/**
	 * The ID of the Repo we will search
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The text to display when returning a "nothing found" message
	 *
	 * @var string
	 */
	protected $kind = 'packages';

	/**
	 * The URL to the repo itself
	 * 
	 * @var string
	 */
	protected $url;

	/**
	 * The minimum number of characters to require before attempting a search
	 *
	 * Increase this for slow repositories
	 *
	 * @var integer
	 */
	protected $min_query_length = 1;

	/**
	 * The maximum number of results to return to Alfred
	 *
	 * @var integer
	 */
	protected $max_return = 25;

	/**
	 * Whether or not the Repo in question has a cached DB
	 * 
	 * @var boolean
	 */
	protected $has_db = false;

	/**
	 * The Cache object, used to share search code amongst Repos
	 *
	 * @var \WillFarrell\AlfredPkgMan\Cache
	 */
	protected $cache;

	/**
	 * An object or array of each package/item found from searching the Repo
	 *
	 * @var StdObject|Array
	 */
	protected $pkgs;

	/**
	 * Factory method to create a particular type of Repo
	 *
	 * @param  string  $id      The ID of the Repo subclass
	 * @param  boolean $has_db  Determine whether we use the cached DB to search
	 *                          or search the repo live via an HTTP request
	 * @param  array   $options An associative array, containing only keys that
	 *                          match the properties existing in this class
	 * @return Class            One of the many Repo subclasses
	 */
	public function __construct() {
		$this->cache = new Cache();

		if ($this->has_db) {
			// get DB here if not dynamic search
			$data = $this->cache->get_db($this->id);
			$this->pkgs = $data;
		}
	}

	/**
	 * Generate a pipe-delimited string for use in key-modified actions
	 *
	 * @param  string $id      The unique ID of a package/repo item
	 * @param  string $url     The URL to the package/item in question
	 * @param  string $pkgstr  The package string  used in a config file ie `"$pkgname": "$version"`
	 * @return string          A pipe-delimited string of these values
	 *                           Corresponds to `id | url | pkgstr` in Alfred
	 */
	protected function makeArg($id, $url, $pkgstr = null)
	{
		if ($pkgstr == null) { $pkgstr = $id; }
		return "{$id}|{$url}|{$pkgstr}";
	}

	/**
	 * Check that the query meets the minimum length defined
	 *
	 * Prevents searching a repo with very short strings, and provides a 
	 * "minimum length not met" result to Alfred
	 * 
	 * @param  string  $query The query being searched
	 * @return boolean        True if the query meets the minimum, false otherwise
	 */
	protected function hasMinQueryLength($query)
	{
		$has_min_length = true;

		if ( strlen($query) === 0 ) { 
			$has_min_length = false;
		} elseif (strlen($query) < $this->min_query_length) {
			$this->cache->w->result(
				"{$this->id}-min",
				$query,
				"Minimum query length of {$this->min_query_length} not met.",
				'',
				"icon-cache/{$this->id}.png"
			);

			$has_min_length = false;
		}

		return $has_min_length;
	}

	/**
	 * Check that a specified package's name or description matches, 
	 * case-insensitively, the specified query
	 *
	 * @param  StdObject $pkg   Package object/array
	 * @param  string    $query The query to filter for
	 * @return boolean          False if the query does not match name or
	 *                               description, true otherwise
	 */
	protected function check(
		$pkg,
		$query,
		$name_key = 'name',
		$desc_key = 'description'
	) {
		if (stripos($pkg->$name_key, $query) !== false
			|| (isset($pkg->$desc_key) && stripos($pkg->$desc_key, $query) !== false)
		) {
			return true;
		}

		return false;
	}

	/**
	 * Provide a "no results" message to Alfred
	 * 
	 * @param  string $query The query being searched
	 * @return void
	 */
	protected function noResults($query, $search_url)
	{
		if (count($this->cache->w->results()) == 0) {
			$this->cache->w->result(
				"{$this->id}-search",
				$search_url,
				"No {$this->kind} were found that matched \"{$query}\"",
				"Click to see the results for yourself",
				"icon-cache/{$this->id}.png"
			);
		}
	}

	/**
	 * Process the results into an XML string for Alfred to consume, adding
	 * a final "visit the website" item
	 *
	 * @return string XML  string representation of the array
	 */
	public function xml()
	{
		$this->cache->w->result(
			"{$this->id}-www-".time(),
			"{$this->url}/",
			'Go to the website',
			"{$this->url}",
			"icon-cache/{$this->id}.png"
		);

		return $this->cache->w->toxml();
	}
}
