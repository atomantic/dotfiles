<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Yo extends Repo
{
	protected $id         = 'yo';
	protected $kind       = 'generators';
	protected $url        = 'http://yeoman.io';
	protected $search_url = 'http://yeoman.io/generators/';
	protected $has_db     = true;

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}

		foreach($this->pkgs as $pkg) {
			// make params
			if ($this->check($pkg, $query)) {
				$title = $pkg->name;
				
				// add author to title
				if (isset($pkg->owner)) {
					$title .= " by {$pkg->owner}";
				}
				
				$this->cache->w->result(
					$pkg->name,
					$this->makeArg($pkg->name, $pkg->website),
					$title,
					$pkg->description,
					"icon-cache/{$this->id}.png"
				);
			}

			// only search till max return reached
			if ( count ( $this->cache->w->results() ) == $this->max_return ) {
				break;
			}
		}

		$this->noResults($query, $this->search_url);

		return $this->xml();
	}
}

// Test code, uncomment to debug this script from the command-line
// $repo = new Yo();
// echo $repo->search('ang');
