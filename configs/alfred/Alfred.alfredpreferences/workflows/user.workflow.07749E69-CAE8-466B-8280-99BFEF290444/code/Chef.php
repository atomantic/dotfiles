<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Chef extends Repo
{
	protected $id     = 'chef';
	protected $kind   = 'cookbooks';
	protected $url    = 'https://supermarket.chef.io';

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}

		$this->pkgs = $this->cache->get_query_json(
			$this->id, 
			$query, 
			"{$this->url}/api/v1/search?q={$query}"
		)->items;
		
		foreach ($this->pkgs as $pkg) {
			if ($this->check($pkg, $query, 'cookbook_name', 'cookbook_description')) {
				$title = $pkg->cookbook_name;
		
				// add author to title
				if (isset($pkg->cookbook_maintainer)) {
					$title .= " by {$pkg->cookbook_maintainer}";
				}
		
				$this->cache->w->result(
					$pkg->cookbook_name,
					$this->makeArg(
						$pkg->cookbook_name, 
						"{$this->url}/cookbooks/{$pkg->cookbook_name}"
					),
					$title,
					$pkg->cookbook_description,
					"icon-cache/{$this->id}.png"
				);
			}
			// only search till max return reached
			if ( count ( $this->cache->w->results() ) == $this->max_return ) {
				break;
			}
		}

		$this->noResults($query, "{$this->url}/cookbooks/{$query}");

		return $this->xml();
	}
}

// Test code, uncomment to debug this script from the command-line
// $repo = new Chef();
// echo $repo->search('apt');
