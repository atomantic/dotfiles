<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Brew extends Repo
{
	protected $id         = 'brew';
	protected $kind       = 'plugins';
	protected $url        = 'http://braumeister.org';
	protected $search_url = 'http://braumeister.org/search/';

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}

		// special case - exact match
		$redirect_check = $this->cache->get_query_data(
			$this->id,
			$query,
			"{$this->search_url}{$query}"
		);

		$exact_match = (
			$redirect_check === '<html><body>You are being <a href="'.$this->url.'/formula/'.$query.'">redirected</a>.</body></html>'
				? true
				: false
		);
		
		if ($exact_match) {
			$this->pkgs = $this->cache->get_query_regex(
				$this->id,
				$query,
				"{$this->url}/formula/{$query}",
				'/<div id="content">([\s\S]*?)<div id="deps">/i',
				1
			);
		} else {
			$this->pkgs = $this->cache->get_query_regex(
				$this->id,
				$query,
				"{$this->search_url}{$query}",
				'/<div class="formula (odd|even)">([\s\S]*?)<\/div>/i',
				2
			);
		}
		
		foreach($this->pkgs as $pkg) {
			if ($exact_match) {
				// name
				$title = $query;

				// version
				preg_match('/<strong class="version spec-stable">([\s\S]*?)<\/strong>/i', $pkg, $matches);
				$version = trim(strip_tags($matches[0]));

				// details
				preg_match('/Homepage: <em><a href="(.*?)">(.*?)<\/a>/i', $pkg, $matches);
				$details = strip_tags($matches[1]);
			} else {
				// name
				preg_match('/<a class="formula" href="(.*?)">(.*?)<\/a>/i', $pkg, $matches);
				$title = strip_tags($matches[0]);

				// version
				preg_match('/<strong class="version spec-stable">([\s\S]*?)<\/strong>/i', $pkg, $matches);
				$version = trim(strip_tags($matches[0]));

				// url
				preg_match('/Homepage: <a href="(.*?)">(.*?)<\/a>/i', $pkg, $matches);
				$details = strip_tags($matches[1]);
			}
			
			$this->cache->w->result(
				$title,
				"{$this->url}/formula/{$title}",
				"{$title} v{$version}",
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
// $repo = new Brew();
// echo $repo->search('pk');
