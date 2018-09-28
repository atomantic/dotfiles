<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class DefinitelyTyped extends Repo
{
    protected $id         = 'dt';
    protected $url        = 'http://definitelytyped.org';
    protected $search_url = 'https://api.npms.io/v2/search?q=scope:types+';

    public function search($query)
    {
        if (!$this->hasMinQueryLength($query)) {
            return $this->xml();
        }

        $this->pkgs = $this->cache->get_query_json(
            $this->id,
            $query,
            "{$this->search_url}{$query}&size={$this->max_return}"
        );

        foreach($this->pkgs->results as $pkg) {
            $p = $pkg->package;
            $name = $p->name;

            $this->cache->w->result(
                $this->id,
                $this->makeArg($name, $p->links->npm, "{$p->name}: {$p->version}"),
                $name,
                $p->description,
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
// $repo = new DefinitelyTyped();
// echo $repo->search('react');
