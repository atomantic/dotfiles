<?php
namespace WillFarrell\AlfredPkgMan;

require_once('Cache.php');
require_once('Repo.php');

class Crates extends Repo
{
  protected $id         = 'crates';
  protected $kind       = 'libraries';
  protected $url        = 'http://crates.io';
  protected $search_url = 'https://crates.io/api/v1/crates?page=1&per_page=100&q=';

  public function search($query)
  {
    if (!$this->hasMinQueryLength($query)) {
      return $this->xml();
    }

    $this->pkgs = $this->cache->get_query_json(
      'crates',
      $query,
      "{$this->search_url}{$query}"
    )->crates;

    foreach($this->pkgs as $pkg) {
      $url = str_replace("git://", "https://", $pkg->repository);
      $this->cache->w->result(
        $pkg->id,
        $this->makeArg($pkg->name, $url, "\"{$pkg->name}\"=\"{$pkg->max_version}\""),
        $pkg->name,
        count($pkg->description) ? trim($pkg->description) : $url,
        "icon-cache/{$this->id}.png"
      );

      // Only show results up to the $max_return
      if ( count ( $this->cache->w->results() ) == $this->max_return ) {
        break;
      }
    }

    $this->noResults($query, "{$this->url}/search?q={$query}");

    return $this->xml();
  }
}

// Test code, uncomment to debug this script from the command-line
// $repo = new Crates();
// echo $repo->search('glob');
