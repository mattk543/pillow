<?php
require_once(dirname(__FILE__) . '/../lib/pillow/WebService.php');

class WebServiceSetServiceId extends PHPUnit_Framework_TestCase
{
  public function tearDown() {
    Pillow_WebService::setServiceId(NULL);
  }
  
  /**
   * @test
   */
  public function itSetsTheServiceId() {
    $id = 'test';
    Pillow_WebService::setServiceId($id);
    $this->assertSame($id, Pillow_WebService::getServiceId());
  }
}
