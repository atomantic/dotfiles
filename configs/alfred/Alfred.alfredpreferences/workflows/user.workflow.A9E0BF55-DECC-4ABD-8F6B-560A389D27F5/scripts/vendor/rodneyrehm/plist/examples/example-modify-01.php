<?php
/**
 * Examples for how to use CFPropertyList
 * Modify a PropertyList
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


// load an existing list
$plist = new CFPropertyList( __DIR__.'/sample.xml.plist' );


foreach( $plist->getValue(true) as $key => $value )
{
	if( $key == "City Of Birth" )
	{
		$value->setValue( 'Mars' );
	}
	
	if( $value instanceof \Iterator )
	{
		// The value is a CFDictionary or CFArray, you may continue down the tree
	}
}


// save data
$plist->save( __DIR__.'/modified.plist', CFPropertyList::FORMAT_XML );

?>