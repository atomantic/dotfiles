<?php

namespace CFPropertyList;

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors','on');

if(!defined('LIBDIR')) {
  define('LIBDIR',__DIR__.'/../classes/CFPropertyList');
}

if(!defined('TEST_BINARY_DATA_FILE')) {
  define('TEST_BINARY_DATA_FILE',__DIR__.'/binary-data.plist');
}

require_once(LIBDIR.'/CFPropertyList.php');

class BinaryParseTest extends \PHPUnit_Framework_TestCase {
  public function testParseBinary() {
    $plist = new CFPropertyList(TEST_BINARY_DATA_FILE);

    $vals = $plist->toArray();
    $this->assertEquals(count($vals),4);

    $this->assertEquals($vals['names']['given-name'],'John');
    $this->assertEquals($vals['names']['surname'],'Dow');

    $this->assertEquals($vals['pets'][0],'Jonny');
    $this->assertEquals($vals['pets'][1],'Bello');

    $this->assertEquals($vals['age'],28);
    $this->assertEquals($vals['birth-date'],412035803);
  }

  public function testParseBinaryString() {
    $content = file_get_contents(TEST_BINARY_DATA_FILE);

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
    if(($fd = fopen(TEST_BINARY_DATA_FILE,"rb")) == NULL) {
      throw new IOException("Error opening test data file for reading!");
    }

    $plist->readBinaryStream($fd);

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
   * @expectedException CFPropertyList\PListException
   */
  public function testEmptyString() {
    $plist = new CFPropertyList();
    $plist->parseBinary('');
  }

  public function testInvalidString() {
    $catched = false;

    try {
      $plist = new CFPropertyList();
      $plist->parseBinary('lalala');
    }
    catch(PListException $e) {
      $catched = true;
    }

    if($catched == false) {
      $this->fail('No exception thrown for invalid string!');
    }

    $catched = false;
    try {
      $plist = new CFPropertyList();
      $plist->parseBinary('bplist00dfwefwefwef');
    }
    catch(PListException $e) {
      return;
    }

    $this->fail('No exception thrown for invalid string!');
  }

}

# eof
