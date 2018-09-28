<?php
/**
 * Examples for how to use CFPropertyList
 * Create the PropertyList sample.xml.plist by using {@link CFTypeDetector}.
 * This example shows how to get around the limitation of guess() regarding {@link CFDate} and {@link CFData}.
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
 * import the array structure to create the sample.xml.plist
 * We make use of CFTypeDetector, which truly is not almighty!
 */

$structure = array(
  'Year Of Birth' => 1965,
  // Note: dates cannot be guessed, it thus has to be specified explicitly
  'Date Of Graduation' => new CFDate( gmmktime( 19, 23, 43, 06, 22, 2004 ) ),
  'Pets Names' => array(),
  // Note: data cannot be guessed, it thus has to be specified explicitly
  'Picture' => new CFData( 'PEKBpYGlmYFCPA==', true ),
  'City Of Birth' => 'Springfield',
  'Name' => 'John Doe',
  'Kids Names' => array( 'John', 'Kyra' ),
);

$td = new CFTypeDetector();  
$guessedStructure = $td->toCFType( $structure );
$plist->add( $guessedStructure );


/*
 * Save PList as XML
 */
$plist->saveXML( __DIR__.'/example-create-03.xml.plist' );

/*
 * Save PList as Binary
 */
$plist->saveBinary( __DIR__.'/example-create-03.binary.plist' );

?>