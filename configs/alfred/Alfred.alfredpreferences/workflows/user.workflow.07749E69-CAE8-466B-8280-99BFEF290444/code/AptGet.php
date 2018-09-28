<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class AptGet extends Repo
{
	protected $id         = 'apt-get';
	protected $url        = 'https://apps.ubuntu.com';
	protected $search_url = 'https://apps.ubuntu.com/cat/search/?q=';

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}
		
		$this->pkgs = $this->cache->get_query_regex(
			$this->id,
			$query,
			"{$this->search_url}{$query}",
			'/<tr>([\s\S]*?)<\/tr>/i'
		);
		
		foreach($this->pkgs as $item) {
			preg_match('/<p>(.*?)<\/p>/i', $item, $matches);
			$name = trim(strip_tags($matches[1]));
			
			preg_match('/<h3>([\s\S]*?)<\/h3>/i', $item, $matches);
			$description = trim(strip_tags($matches[1]));
		
			$this->cache->w->result(
				$name,
				$this->makeArg($name, "{$this->url}/cat/applications/{$name}"),
				$name,
				$description,
				"icon-cache/{$this->id}.png"
			);

			// only search till max return reached
			if ( count ( $this->cache->w->results() ) == $this->max_return ) {
				break;
			}
		}
		
		$this->noResults($query, "{$this->search_url}{$query}");

		return $this->xml();
	}
}

// Test code, uncomment to debug this script from the command-line
// $repo = new AptGet();
// echo $repo->search('leaflet');
