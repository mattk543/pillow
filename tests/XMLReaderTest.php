<?php

require_once(dirname(__FILE__) . '/../lib/pillow/Exceptions.php');
require_once(dirname(__FILE__) . '/../lib/pillow/XMLReader.php');

class XMLReaderOpenTest extends PHPUnit_Framework_TestCase
{
  /**
   * @test
   * @expectedException Pillow_Exception
   */
  public function itThrowsExceptionWhenUriIsNotFound() {
    $uri = dirname(__FILE__) . '/test_data/file_does_not_exist.xml';
    $reader = new Pillow_XMLReader();
    $reader->open($uri);
  }
  
  /**
   * @test
   */
  public function itDoesNotThrowExceptionWhenUriIsFound() {
    $uri = dirname(__FILE__) . '/test_data/message_without_error.xml';
    $reader = new Pillow_XMLReader();
    
    $thrown = FALSE;
    try {
      $reader->open($uri);
      $reader->close();
    } catch (Pillow_Exception $e) {
      $thrown = TRUE;
    }
    
    $this->assertFalse($thrown);
  }
}

class XMLReaderReadTest extends PHPUnit_Framework_TestCase
{
  /**
   * @test
   * @expectedException Pillow_Exception
   */
  public function itThrowsExceptionWhenMessageCodeIsNotZero() {
    $uri = dirname(__FILE__) . '/test_data/message_with_error.xml';
    $reader = new Pillow_XMLReader();
    $reader->open($uri);
    while($reader->read()) {}
  }
  
  /**
   * @test
   */
  public function itDoesNotThrowExceptionWhenMessageCodeIsZero() {
    $uri = dirname(__FILE__) . '/test_data/message_without_error.xml';
    $reader = new Pillow_XMLReader();
    $reader->open($uri);
    
    $thrown = FALSE;
    try {
      while($reader->read()) {}
    } catch (Pillow_Exception $e) {
      $thrown = TRUE;
    }
    
    $this->assertFalse($thrown);
  }
}
