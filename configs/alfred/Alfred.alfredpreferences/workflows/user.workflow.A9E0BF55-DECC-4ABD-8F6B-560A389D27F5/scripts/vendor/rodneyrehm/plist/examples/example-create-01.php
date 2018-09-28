<?php
/**
 * Examples for how to use CFPropertyList
 * Create the PropertyList sample.xml.plist by using the CFPropertyList API.
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
 * create a new CFPropertyList instance without loading any content
 */
$plist = new CFPropertyList();

/*
 * Manuall Create the sample.xml.plist 
 */
// the Root element of the PList is a Dictionary
$plist->add( $dict = new CFDictionary() );

// <key>Year Of Birth</key><integer>1965</integer>
$dict->add( 'Year Of Birth', new CFNumber( 1965 ) );

// <key>Date Of Graduation</key><date>2004-06-22T19:23:43Z</date>
$dict->add( 'Date Of Graduation', new CFDate( gmmktime( 19, 23, 43, 06, 22, 2004 ) ) );

// <key>Pets Names</key><array/>
$dict->add( 'Pets Names', new CFArray() );

// <key>Picture</key><data>PEKBpYGlmYFCPA==</data>
// to keep it simple we insert an already base64-encoded string
$dict->add( 'Picture', new CFData( 'PEKBpYGlmYFCPA==', true ) );

// <key>City Of Birth</key><string>Springfield</string>
$dict->add( 'City Of Birth', new CFString( 'Springfield' ) );

// <key>Name</key><string>John Doe</string>
$dict->add( 'Name', new CFString( 'John Doe' ) );

// <key>Kids Names</key><array><string>John</string><string>Kyra</string></array>
$dict->add( 'Kids Names', $array = new CFArray() );
$array->add( new CFString( 'John' ) );
$array->add( new CFString( 'Kyra' ) );


/*
 * Save PList as XML
 */
$plist->saveXML( __DIR__.'/example-create-01.xml.plist' );


/*
 * Save PList as Binary
 */
$plist->saveBinary( __DIR__.'/example-create-01.binary.plist' );

?>