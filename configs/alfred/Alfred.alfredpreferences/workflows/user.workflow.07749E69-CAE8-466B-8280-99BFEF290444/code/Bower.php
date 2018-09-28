<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Bower extends Repo
{
	protected $id         = 'bower';
	protected $kind       = 'components';
	protected $url        = 'http://bower.io';
	protected $search_url = 'https://bower.herokuapp.com/packages/search/';

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}
		
		$this->pkgs = $this->cache->get_query_json(
			'bower',
			$query,
			"{$this->search_url}{$query}"
		);
		
		foreach($this->pkgs as $pkg) {
			$url = str_replace("git://", "https://", $pkg->url);
			$version = "*";
			$this->cache->w->result(
				$pkg->url,
				$this->makeArg($pkg->name, $url, "\"{$pkg->name}\": \"{$version}\""),
				$pkg->name,
				$url,
				"icon-cache/{$this->id}.png"
			);
			
			// Only show results up to the $max_return
			if ( count ( $this->cache->w->results() ) == $this->max_return ) {
				break;
			}
		}

		$this->noResults($query, "{$this->url}/search/?q={$query}");

		return $this->xml();
	}
}

// Test code, uncomment to debug this script from the command-line
// $repo = new Bower();
// echo $repo->search('leaflet');
