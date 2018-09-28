<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Cocoa extends Repo
{
	protected $id         = 'cocoa';
	protected $kind       = 'libraries';
	protected $url        = 'http://cocoadocs.org';
	protected $search_url = 'http://cocoadocs.org/?q=';
	protected $has_db     = true;
	
	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}
		
		// For debugging, uncommenting this will bypass the cached copy
		// of the repo
		// $this->pkgs = $this->cache->get_query_json(
		// 	$this->id, 
		// 	$query, 
		// 	"http://cocoadocs.org/?q=you/{$query}"
		// );
		
		foreach($this->pkgs as $pkg) {
			
			// make params
			if ($this->check($pkg, $query, 'name', 'summary')) {
				$title = $pkg->name;
				if (isset($pkg->main_version)) { $title .= " (v{$pkg->main_version})"; }
				if (isset($pkg->user)) { $title .= " ~ {$pkg->user}"; }
				
				$url = (isset($pkg->url)) ? $pkg->url : $pkg->doc_url;
				$details = (isset($pkg->summary)) ? $pkg->summary : $pkg->framework;
				
				$icon = (isset($pkg->url)) ? 'xcode' : "{$this->id}";
				
				$this->cache->w->result(
					$pkg->name,
					$this->makeArg($pkg->name, $url),
					$title,
					$details,
					"icon-cache/{$icon}.png"
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
// $repo = new Cocoa();
// echo $repo->search('test');
