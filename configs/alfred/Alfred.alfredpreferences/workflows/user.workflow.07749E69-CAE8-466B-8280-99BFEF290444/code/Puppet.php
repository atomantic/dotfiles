<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Puppet extends Repo
{
	protected $id         = 'puppet';
	protected $url        = 'https://forge.puppetlabs.com';
	protected $search_url = 'https://forgeapi.puppetlabs.com/v3/modules?query=';
	protected $kind       = 'modules';

	public function search($query) {
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}
		
		$this->pkgs = $this->cache->get_query_json(
			$this->id,
			$query, 
			"{$this->search_url}{$query}&limit={$this->max_return}"
		)->results;

		foreach($this->pkgs as $pkg) {
			$name        = "{$pkg->owner->username}/{$pkg->name}";
			$version     = $pkg->current_release->version;
			$description = $pkg->current_release->metadata->summary;

			$this->cache->w->result(
				$name,
				$this->makeArg($name, "{$this->url}/{$name}"),
				"{$name} ~ v{$version}",
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
// $repo = new Puppet();
// echo $repo->search('nginx');
