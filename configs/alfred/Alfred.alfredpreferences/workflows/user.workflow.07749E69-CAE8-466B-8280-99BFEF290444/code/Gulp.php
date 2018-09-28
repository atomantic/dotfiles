<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Gulp extends Repo
{
	protected $id         = 'gulp';
	protected $kind       = 'plugins';
	protected $url        = 'http://gulpjs.com';
	protected $search_url = 'http://npmsearch.com/query?fields=name,keywords,rating,description,author,modified,homepage,version&q=keywords:gulpfriendly&q=keywords:gulpplugin&size=20&sort=rating:desc&start=0&q=';
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
// $repo = new Gulp();
// echo $repo->search('min');
?>