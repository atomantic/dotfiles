<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Docker extends Repo
{
	protected $id         = 'docker';
	protected $kind       = 'images';
	protected $url        = 'https://hub.docker.com';
	protected $search_url = 'https://index.docker.io/v1/search?q=';

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}
		
		$this->pkgs = $this->cache->get_query_json(
			$this->id,
			$query,
			"{$this->search_url}{$query}"
        );
		
		foreach($this->pkgs->results as $pkg) {
			
			// make params
            $title = $pkg->name;
            $repository = ($pkg->is_official ) ? '_' : 'r';
			$url = $this->url . '/'. $repository . '/'. $pkg->name;
            $description = $pkg->description;
		
			$this->cache->w->result(
				$title,
				$this->makeArg($title, $url),
				$title.' ~ '.$pkg->star_count,
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
// $repo = new Docker();
// echo $repo->search('ng');
