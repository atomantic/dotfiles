<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Pypi extends Repo
{
	protected $id               = 'pypi';
	protected $url              = 'https://pypi.python.org';
	protected $search_url       = 'https://pypi.python.org/pypi?%3Aaction=search&submit=search&term=';
	protected $min_query_length = 3;

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}

		$this->pkgs = $this->cache->get_query_regex(
			$this->id,
			$query,
			"{$this->search_url}{$query}",
			'/<tr class="(.*?)">([\s\S]*?)<\/tr>/i',
			2
		);
		
		foreach($this->pkgs as $pkg) {
			
			// make params
			// name
			preg_match('/<a href="(.*?)">(.*?)<\/a>/i', $pkg, $matches);
			$title = str_replace("&nbsp;", " ", strip_tags($matches[0]));
			$url = strip_tags($matches[1]);
			
			preg_match_all('/<td>([\s\S]*?)<\/td>/i', $pkg, $matches);
			$downloads = strip_tags($matches[1][1]);
			$details = strip_tags($matches[1][2]);
		
			$this->cache->w->result(
				$title,
				$this->makeArg($title, "{$this->url}{$url}"),
				"{$title}    {$downloads}",
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

// $repo = new Pypi();
// echo $repo->search('lib');
