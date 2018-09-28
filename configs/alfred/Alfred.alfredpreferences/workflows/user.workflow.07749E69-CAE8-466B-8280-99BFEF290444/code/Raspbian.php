<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Repo.php');

class Raspbian extends Repo
{
	protected $id         = 'raspbian';
	protected $url        = 'http://www.raspbian.org';
	protected $search_url = 'https://packages.debian.org/wheezy/';
	protected $has_db     = true;

	public function __construct()
	{
		parent::__construct();

		// Clean up & update cached DB
		$pkgs = explode("\n\n", $this->pkgs);
		array_pop($pkgs); // remove file end
		
		$this->pkgs = [];
		for ($i = 0, $l = count($pkgs); $i < $l; $i++) {
			$pkg = explode("\n", $pkgs[$i]);
			// TODO: Figure out why `new stdClass()` doesn't work
			$new_pkg = (object) array();
			foreach ($pkg as $datum) {
				$data = explode(': ', $datum);
				$new_pkg->$data[0] = $data[1];
			}
			$this->pkgs[] = $new_pkg;
		}
	}

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}

		foreach($this->pkgs as $pkg) {
			if ($this->check($pkg, $query, 'Package', 'Description')) {
				$title = $pkg->Package;
			
				// add version to title
				if (isset($pkg->Version)) {
					$title .= " ~ v{$pkg->Version}";
				}
				// add author to title
				if (isset($pkg->Maintainer)) {
					preg_match("/([^\\n]+?) <[^\\n]+?>/i", $pkg->Maintainer, $matches);
					$title .= " ~ by {$matches[1]}";
				}
				$url = "{$this->search_url}{$pkg->Package}";

				$this->cache->w->result(
					$pkg->Package,
					$this->makeArg($pkg->Package, $url),
					$title,
					$pkg->description,
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
// $repo = new Raspbian();
// echo $repo->search('lib');
