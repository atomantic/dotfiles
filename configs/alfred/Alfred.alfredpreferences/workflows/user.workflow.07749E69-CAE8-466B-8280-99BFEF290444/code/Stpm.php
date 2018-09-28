<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Stpm extends Repo
{
	protected $id         = 'stpm';
	protected $url        = 'https://packagecontrol.io';
	protected $search_url = 'https://packagecontrol.io/search/';

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml();
		}

		$data = $this->cache->get_query_regex(
			$this->id,
			$query,
			"{$this->search_url}{$query}",
			'/<ul class="packages results">([\s\S]*?)<\/ul>/i'
		);

		$this->pkgs = explode('<li class="package">', $data[0]);
		array_shift($this->pkgs);

		foreach($this->pkgs as $pkg) {
			// make params
			preg_match('/<h3>([\s\S]*?)<\/h3>/i', $pkg, $matches);
			$title = trim(strip_tags($matches[1]));

			preg_match(
				'/<div class="description">([\s\S]*?)<\/div>/i',
				$pkg,
				$matches
			);

			$description = html_entity_decode(trim(strip_tags($matches[1])));

			preg_match('/<span class="author">([\s\S]*?)<\/span>/i', $pkg, $matches);
			$author = trim(strip_tags($matches[1]));
			// $version = trim(strip_tags($matches[1]));

			$this->cache->w->result(
				$title,
				$this->makeArg($title, "{$this->url}/packages/{$title}"),
				"{$title} ~ {$author}", // "v{$version}"
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
// $repo = new Atom();
// echo $repo->search('q');
