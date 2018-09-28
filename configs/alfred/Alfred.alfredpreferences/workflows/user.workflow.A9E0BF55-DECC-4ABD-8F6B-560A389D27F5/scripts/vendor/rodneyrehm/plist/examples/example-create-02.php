<?php
/**
 * Examples for how to use CFPropertyList
 * Create the PropertyList sample.xml.plist by using {@link CFTypeDetector}.
 * @package plist
 * @subpackage plist.examples
 */
namespace CFPropertyList;
use \DateTime, \DateTimeZone;

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
  // Note: dates cannot be guessed, so this will become a CFNumber and be treated as an integer
  // See example-04.php for a possible workaround
  'Date Of Graduation' => gmmktime( 19, 23, 43, 06, 22, 2004 ),
  'Date Of Birth' => new DateTime( '1984-09-07 08:15:23', new DateTimeZone( 'Europe/Berlin' ) ),
  'Pets Names' => array(),
  // Note: data cannot be guessed, so this will become a CFString
  // See example-03.php for a possible workaround
  'Picture' => 'PEKBpYGlmYFCPA==',
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
$plist->saveXML( __DIR__.'/example-create-02.xml.plist' );

/*
 * Save PList as Binary
 */
$plist->saveBinary( __DIR__.'/example-create-02.binary.plist' );

?>