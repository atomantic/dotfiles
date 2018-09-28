<?php
namespace WillFarrell\AlfredPkgMan;

ini_set('memory_limit', '-1');
error_reporting(0);

require_once('Workflows.php');

class Cache {
	public $cache_age = 14;
	public $dbs = array(
		"alcatraz" => "https://raw.githubusercontent.com/mneorr/alcatraz-packages/master/packages.json",
		"apple" => "http://cocoadocs.org/apple_documents.jsonp", // CocoaDocs
		"cocoa" => "http://cocoadocs.org/documents.jsonp",
		"grunt" => "http://gruntjs.com/plugin-list.json",
		"gulp" => "http://npmsearch.com/query?fields=name,keywords,rating,description,author,modified,homepage,version,license&q=keywords:gulpplugin,gulpfriendly&size=9999&sort=rating:desc&start=0",
		"raspbian" => "http://archive.raspbian.org/raspbian/dists/wheezy/main/binary-armhf/Packages",
		"yo" => "http://yeoman-generator-list.herokuapp.com/"
	);
	public $query_file = "queries";
	
	public function __construct() {
		$this->w = new Workflows();
		
		$q = $this->w->read($this->query_file.'.json');
		$this->queries = $q ? (array)$q : array();
	}
	
	public function __destruct() {
		$this->w->write($this->queries, $this->query_file.'.json');
	}
	
	public function get_query_data($id, $query, $url) {
		if (!$query) { return array(); }
		return $this->w->request($url);
	}
	
	public function get_db($id) {
		if (!array_key_exists($id, $this->dbs)) { return array(); }
		$name = $id;
		
		$pkgs = $this->w->read($name.'.json');
		$timestamp = $this->w->filetime($name.'.json');
		if (!$pkgs || $timestamp < (time() - $this->cache_age * 86400)) {
			$data = $this->w->request( $this->dbs[$id] );
			if (substr($this->dbs[$id], -5) == 'jsonp') { $data = preg_replace('/.+?([\[{].+[\]}]).+/','$1',$data); } // clean jsonp wrapper
			
			$this->w->write($data, $name.'.json');
			$pkgs = json_decode( $data );
		} else if (!$pkgs) {
			$pkgs = array();
		}
		return $pkgs;
	}
	
	public function get_query_json($id, $query, $url) {
		if (!$query) { return array(); }
		$name = $id.'.'.$query;
		
		$pkgs = $this->w->read($name.'.json');
		$timestamp = $this->w->filetime($name.'.json');
		if (!$pkgs || $timestamp < (time() - $this->cache_age * 86400)) {
			$data = $this->w->request($url);
			if (substr($url, -5) == 'jsonp') { $data = preg_replace('/.+?([\[{].+[\]}]).+/','$1',$data); } // clean jsonp wrapper
			$this->w->write($data, $name.'.json');
			$this->queries[$name] = time();
			$pkgs = json_decode( $data );
		} else if (!$pkgs) {
			$pkgs = array();
		}
		return $pkgs;
	}
	
	public function get_query_regex($id, $query, $url, $regex, $regex_pos = 1) {
		if (!$query) { return array(); }
		$name = $id.'.'.$query;
		
		$pkgs = $this->w->read($name.'.json');
		$timestamp = $this->w->filetime($name.'.json');
		if (!$pkgs || $timestamp < (time() - $this->cache_age * 86400) || 1) { // update - Add || 1 for debuggin
			$data = $this->w->request($url);
			preg_match_all($regex, $data, $matches);
			$data = $matches[$regex_pos];
			$this->w->write($data, $name.'.json');
			$pkgs = is_string($data) ? json_decode( $data ) : $data;
			$this->queries[$name] = time();
		} else if (!$pkgs) {
			$pkgs = array();
		}
		return $pkgs;
	}
	
	public function update_db($id) {
		$data = $this->w->request( $this->dbs[$id] );
	
		// clean jsonp wrapper
		$data = preg_replace('/.+?({.+}).+/','$1',$data);
		
		$this->w->write($data, $id.'.json');
		return $data;
	}
	
	public function clear() {
		// remove db json files
		foreach($this->dbs as $key => $url) {
			$this->w->delete($key.'.json');
			
		}
		
		// remove query json files
		foreach($this->queries as $key => $timestamp) {
			$this->w->delete($key.'.json');
		}
		$this->queries = array();
	}
}
