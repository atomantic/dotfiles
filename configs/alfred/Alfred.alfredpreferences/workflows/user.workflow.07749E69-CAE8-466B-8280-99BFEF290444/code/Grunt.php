<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Grunt extends Repo
{
	protected $id         = 'grunt';
	protected $kind       = 'plugins';
	protected $url        = 'http://gruntjs.com';
	protected $search_url = 'http://gruntjs.com/plugins/';
	protected $has_db     = true;

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}

		foreach($this->pkgs->aaData as $pkg) {
			
			// make params
			if ($this->check($pkg, $query, 'name', 'ds')) {
				// remove grunt- from title
				$title = str_replace('grunt-', '', $pkg->name);
			
				// add author to title
				if (isset($pkg->author)) {
					$title .= " by {$pkg->author}";
				}
				$url = "https://www.npmjs.org/package/{$pkg->name}";
				
				// Uncomment to skip deprecated plugins
				// if (strpos($plugin->description, "DEPRECATED") !== false) { 
				// 	continue; 
				// }

				$this->cache->w->result(
					$pkg->name,
					$this->makeArg($pkg->name, $url),
					$title,
					$pkg->ds,
					"icon-cache/{$this->id}.png"
				);
			}
			
			
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
// $repo = new Grunt();
// echo $repo->search('contrib');
