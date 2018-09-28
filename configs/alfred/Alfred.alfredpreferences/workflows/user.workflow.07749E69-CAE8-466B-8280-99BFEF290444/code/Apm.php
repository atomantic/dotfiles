<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Apm extends Repo
{
	protected $id         = 'apm';
	protected $url        = 'https://atom.io';
	protected $search_url = 'https://atom.io/packages/search?q=';

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml();
		}

		$data = $this->cache->get_query_regex(
			$this->id,
			$query,
			"{$this->search_url}{$query}",
			'/<div class="package-list">([\s\S]*?)<div class="footer-pad">/i'
		);

		$this->pkgs = explode('<div class="grid-cell">', $data[0]);
		array_shift($this->pkgs);

		foreach($this->pkgs as $pkg) {
			// make params
			preg_match('/<h4 class="card-name">([\s\S]*?)<\/h4>/i', $pkg, $matches);
			$title = trim(strip_tags($matches[1]));

			preg_match(
				'/<span class="css-truncate-target card-description">([\s\S]*?)<\/span>/i',
				$pkg,
				$matches
			);

			$description = html_entity_decode(trim(strip_tags($matches[1])));

			preg_match('/<a href="[\s\S]*?" class="author">([\s\S]*?)<\/a>/i', $pkg, $matches);
			$author = trim(strip_tags($matches[1]));
			// $version = trim(strip_tags($matches[1]));

			$this->cache->w->result(
				$title,
				$this->makeArg($title, "{$this->url}/packages/{$title}"),
				"{$title} ~ by {$author}", // "v{$version}"
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
