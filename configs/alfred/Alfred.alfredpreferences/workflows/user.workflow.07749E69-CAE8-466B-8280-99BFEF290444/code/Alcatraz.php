<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Alcatraz extends Repo
{
	protected $id     = 'alcatraz';
	protected $url    = 'http://alcatraz.io';
	protected $has_db = true;

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}
		
		foreach($this->pkgs as $categories) {
			// plugins, color_scheme, project_templates, file_templates
			foreach($categories as $category) {
				foreach($category as $pkg) {
					if ($this->check($pkg, $query)) {
						$this->cache->w->result(
							$pkg->url,
							$this->makeArg($pkg->name, $pkg->url),
							$pkg->name,
							$pkg->description,
							"icon-cache/{$this->id}.png"
						);
					}

					// only search till max return reached
					if ( count ( $this->cache->w->results() ) == $this->max_return ) {
						break;
					}
				}
			}
		}
		
		// The search URL here isn’t quiet right, since Alcatraz doesn't have a
		// a web UI for its "repo"
		$this->noResults($query, $this->cache->dbs[$this->id]);

		return $this->xml();
	}
}

// Test code, uncomment to debug this script from the command-line
// $repo = new Alcatraz();
// echo $repo->search('avia');
