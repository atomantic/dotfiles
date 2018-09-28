<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Rpm extends Repo
{
	protected $id         = 'rpm';
	protected $url        = 'http://rpmfind.net';
	protected $search_url = 'http://rpmfind.net/linux/rpm2html/search.php?query=';

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}
		
		$this->pkgs = $this->cache->get_query_regex(
			$this->id,
			$query,
			"{$this->search_url}{$query}",
			'/<tr bgcolor=\'\'>([\s\S]*?)<\/tr>/i'
		);
		
		foreach($this->pkgs as $pkg) {
			// make params
			preg_match('/<a href=[\'"](.*?)[\'"]>(.*?)<\/a>/i', $pkg, $matches);
			$title = strip_tags($matches[2]);
			$url = strip_tags($matches[1]);
			
			preg_match_all('/<td>([\s\S]*?)<\/td>/i', $pkg, $matches);
			$dist = trim(strip_tags($matches[1][2]));
			$details = trim(strip_tags($matches[1][1]));

			$this->cache->w->result(
				$title,
				$this->makeArg($title, $url),
				$title,
				"{$dist} - {$details}",
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
// $repo = new Rpm();
// echo $repo->search('r');
