<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Pear extends Repo
{
	protected $id         = 'pear';
	protected $url        = 'http://pear.php.net';
	protected $search_url = 'http://pear.php.net/search.php?q=';

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}
		
		$this->pkgs = $this->cache->get_query_regex(
			$this->id,
			$query,
			"{$this->search_url}{$query}",
			'/<li>([\s\S]*?)<\/li>/i'
		);
		
		array_shift($this->pkgs); // remove register link
		
		foreach($this->pkgs as $pkg) {
			
			// make params
			// name
			preg_match('/<a(.*?)>(.*?)<\/a>/i', $pkg, $matches);
			$title = strip_tags($matches[0]);
			
			// url
			$details = strip_tags(substr($pkg, strpos($pkg, ":")+2));
	
			$this->cache->w->result(
				$title,
				$this->makeArg($title, "{$this->url}/package/{$title}"),
				$title,
				$details,
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
// $repo = new Pear();
// echo $repo->search('test');
