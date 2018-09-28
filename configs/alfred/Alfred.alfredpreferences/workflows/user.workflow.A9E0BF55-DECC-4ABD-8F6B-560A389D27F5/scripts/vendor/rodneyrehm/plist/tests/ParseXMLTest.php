<?php

namespace CFPropertyList;

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors','on');

if(!defined('LIBDIR')) {
  define('LIBDIR',__DIR__.'/../classes/CFPropertyList');
}

if(!defined('TEST_XML_DATA_FILE')) {
  define('TEST_XML_DATA_FILE',__DIR__.'/xml-data.plist');
}

require_once(LIBDIR.'/CFPropertyList.php');

class ParseXMLTest extends \PHPUnit_Framework_TestCase {
  public function testParse() {
    $plist = new CFPropertyList(TEST_XML_DATA_FILE);

    $vals = $plist->toArray();
    $this->assertEquals(count($vals),4);

    $this->assertEquals($vals['names']['given-name'],'John');
    $this->assertEquals($vals['names']['surname'],'Dow');

    $this->assertEquals($vals['pets'][0],'Jonny');
    $this->assertEquals($vals['pets'][1],'Bello');

    $this->assertEquals($vals['age'],28);
    $this->assertEquals($vals['birth-date'],412035803);
  }

  public function testParseString() {
    $content = file_get_contents(TEST_XML_DATA_FILE);

    $plist = new CFPropertyList();
    $plist->parse($content);

    $vals = $plist->toArray();
    $this->assertEquals(count($vals),4);

    $this->assertEquals($vals['names']['given-name'],'John');
    $this->assertEquals($vals['names']['surname'],'Dow');

    $this->assertEquals($vals['pets'][0],'Jonny');
    $this->assertEquals($vals['pets'][1],'Bello');

    $this->assertEquals($vals['age'],28);
    $this->assertEquals($vals['birth-date'],412035803);
  }

  public function testParseStream() {
    $plist = new CFPropertyList();
    if(($fd = fopen(TEST_XML_DATA_FILE,"r")) == NULL) {
      throw new IOException("Error opening test data file for reading!");
    }

    $plist->loadXMLStream($fd);

    $vals = $plist->toArray();
    $this->assertEquals(count($vals),4);

    $this->assertEquals($vals['names']['given-name'],'John');
    $this->assertEquals($vals['names']['surname'],'Dow');

    $this->assertEquals($vals['pets'][0],'Jonny');
    $this->assertEquals($vals['pets'][1],'Bello');

    $this->assertEquals($vals['age'],28);
    $this->assertEquals($vals['birth-date'],412035803);
  }

  /**
   * @expectedException CFPropertyList\IOException
   */
  public function testEmptyString() {
    $plist = new CFPropertyList();
    $plist->parse('');
  }

  public function testInvalidString() {
    $catched = false;

    try {
      $plist = new CFPropertyList();
      $plist->parse('lalala');
    }
    catch(\DOMException $e) {
      $catched = true;
    }
    catch(\PHPUnit_Framework_Error $e) {
      $catched = true;
    }

    if($catched == false) {
      $this->fail('No exception thrown for invalid string!');
    }

    $catched = false;
    try {
      $plist = new CFPropertyList();
      $plist->parse('<plist>');
    }
    catch(PListException $e) {
      return;
    }
    catch(\PHPUnit_Framework_Error $e) {
      return;
    }

    $this->fail('No exception thrown for invalid string!');
  }

}

# eof
