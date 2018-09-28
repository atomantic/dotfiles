<?php
/**
 * Examples for how to use CFPropertyList
 * Create the PropertyList sample.xml.plist by using {@link CFTypeDetector}.
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

class DemoDetector extends CFTypeDetector {
  
  public function toCFType($value) {
    if( $value instanceof PListException ) {
      return new CFString( $value->getMessage() );
    }

    return parent::toCFType($value);
  }
  
}

/*
 * import the array structure to create the sample.xml.plist
 * We make use of CFTypeDetector, which truly is not almighty!
 */

$stack = new \SplStack();
$stack[] = 1;
$stack[] = 2;
$stack[] = 3;

$structure = array(
  'NullValueTest' => null,
  'IteratorTest' => $stack,
  'ObjectTest' => new PListException('Just a test...'),
);

/*
 * Try default detection
 */
try {
  $plist = new CFPropertyList();
  $td = new CFTypeDetector();  
  $guessedStructure = $td->toCFType( $structure );
  $plist->add( $guessedStructure );
  $plist->saveXML( __DIR__.'/example-create-04.xml.plist' );
  $plist->saveBinary( __DIR__.'/example-create-04.binary.plist' );
}
catch( PListException $e ) {
  echo 'Normal detection: ', $e->getMessage(), "\n";
}

/*
 * Try detection by omitting exceptions
 */
try {
  $plist = new CFPropertyList();
  $td = new CFTypeDetector( array('suppressExceptions' => true) );
  $guessedStructure = $td->toCFType( $structure );
  $plist->add( $guessedStructure );
  $plist->saveXML( __DIR__.'/example-create-04.xml.plist' );
  $plist->saveBinary( __DIR__.'/example-create-04.binary.plist' );
}
catch( PListException $e ) {
  echo 'Silent detection: ', $e->getMessage(), "\n";
}

/*
 * Try detection with an extended version of CFTypeDetector
 */
try {
  $plist = new CFPropertyList();
  $td = new DemoDetector();  
  $guessedStructure = $td->toCFType( $structure );
  $plist->add( $guessedStructure );
  $plist->saveXML( __DIR__.'/example-create-04.xml.plist' );
  $plist->saveBinary( __DIR__.'/example-create-04.binary.plist' );
}
catch( PListException $e ) {
  echo 'User defined detection: ', $e->getMessage(), "\n";
}

?>