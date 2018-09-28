<?php
// ****************
//error_reporting(0);
require_once('Cache.php');

$cache = new Cache();

foreach($cache->dbs as $key => $url) {
	$cache->update_db($key);
}
// ****************
?>