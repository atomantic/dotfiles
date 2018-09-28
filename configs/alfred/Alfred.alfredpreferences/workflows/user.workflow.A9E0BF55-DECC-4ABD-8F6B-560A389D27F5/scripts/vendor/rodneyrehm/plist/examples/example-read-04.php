<?php
/**
 * Examples for how to use CFPropertyList with strings
 * Read a binary from a string PropertyList
 * @package plist
 * @subpackage plist.examples
 */
namespace CFPropertyList;

// just in case...
error_reporting( E_ALL );
ini_set( 'display_errors', 'on' );

/**
 * Require CFPropertyList
 */
require_once(__DIR__.'/../classes/CFPropertyList/CFPropertyList.php');


/*
 * create a new CFPropertyList instance which loads the sample.plist on construct.
 * since we know it's an binary file, we can skip format-determination
 */
$content = file_get_contents(__DIR__.'/sample.binary.plist');
$plist = new CFPropertyList();
$plist->parseBinary($content);

/*
 * retrieve the array structure of sample.plist and dump to stdout
 */

echo '<pre>';
var_dump( $plist->toArray() );
echo '</pre>';

?>