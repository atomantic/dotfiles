<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Cordova extends Repo
{
	protected $id         = 'cordova';
	protected $kind       = 'plugins';
	protected $url        = 'https://cordova.apache.org/plugins/?q=';
	protected $search_url = 'https://npmsearch.com/query?fields=name,keywords,license,description,author,modified,homepage,version,rating&q=keywords:%22ecosystem:cordova%22&sort=rating:desc&size=20&start=0&q=';
	//protected $has_db     = true;

	public function search($query)
	{
		if (!$this->hasMinQueryLength($query)) {
			return $this->xml(); 
		}

        $this->pkgs = $this->cache->get_query_json(
            $this->id,
            $query,
            "{$this->search_url}{$query}"
        );

		foreach($this->pkgs->results as $pkg) {

            $title = str_replace('gulp-', '', $pkg->name[0]); // remove gulp- from title

            // add version to title
            if (isset($pkg->version)) {
                $title .= " v{$pkg->version[0]}";
            }
            // add author to title
            if (isset($pkg->author)) {
                $title .= " by {$pkg->author[0]}";
            }

            // skip DEPRECATED repos
            // if (strpos($plugin->description, "DEPRECATED") !== false) {
            // 	continue;
            // }

            $this->cache->w->result(
                $pkg->name[0],
                $this->makeArg($pkg->name[0], $pkg->homepage[0]),
                $title,
                $pkg->description[0],
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
// $repo = new Cordova();
// echo $repo->search('min');
?>