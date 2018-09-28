<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Composer extends Repo
{
	protected $id         = 'composer';
	protected $url        = 'https://packagist.org';
	protected $search_url = 'https://packagist.org/search/?q=';

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}
		
		$this->pkgs = $this->cache->get_query_regex(
			$this->id,
			$query,
			"{$this->search_url}{$query}",
			'/<li data-url="(.*?)">([\s\S]*?)<\/li>/i',
			2
		);
		
		foreach($this->pkgs as $pkg) {
			
			// make params
			preg_match('/<a(.*?)<\/a>/i', $pkg, $matches);
			$title = strip_tags($matches[0]);
			
			preg_match('/<p class="package-description">([\s\S]*?)<\/p>/i', $pkg, $matches);
			$details = strip_tags($matches[1]);
	
			$this->cache->w->result(
				$title,
				$this->makeArg($title, "{$this->url}/packages/{$title}"),
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
// $repo = new Composer();
// echo $repo->search('c');
