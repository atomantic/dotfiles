<?php
/**
 * Examples for how to use CFPropertyList
 * Read a Binary PropertyList
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
 * since we know it's a binary file, we can skip format-determination
 */
$plist = new CFPropertyList( __DIR__.'/sample.binary.plist', CFPropertyList::FORMAT_BINARY );

/*
 * retrieve the array structure of sample.plist and dump to stdout
 */

echo '<pre>';
var_dump( $plist->toArray() );
echo '</pre>';

$plist->saveBinary( __DIR__.'/sample.binary.plist' );

?>