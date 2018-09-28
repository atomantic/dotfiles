<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Gradle extends Repo
{
	protected $id         = 'gradle';
	protected $kind       = 'libraries';
	protected $url        = 'https://bintray.com/bintray/jcenter';
	protected $search_url = 'https://bintray.com/search?query=';

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}
		
		$this->pkgs = $this->cache->get_query_json(
			$this->id,
			$query,
			"https://api.bintray.com/search/packages/maven?q=*{$query}*"
		)->response->docs;
		
		foreach($this->pkgs as $pkg) {
			
			// make params
			$title = "{$pkg->a} (v{$pkg->latestVersion})";
			$url = "{$this->url}/{$pkg->id}/view";
			$details = "GroupId: {$pkg->id}";
	
			$this->cache->w->result(
				$pkg->a,
				$this->makeArg($pkg->id, $url, "{$pkg->id}:{$pkg->latestVersion}"),
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
// $repo = new Gradle();
// echo $repo->search('leaflet');
