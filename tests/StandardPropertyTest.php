<?php
require_once(dirname(__FILE__) . '/../lib/pillow/WebService.php');
require_once(dirname(__FILE__) . '/../lib/pillow/Exceptions.php');
require_once(dirname(__FILE__) . '/../lib/pillow/XMLReader.php');
require_once(dirname(__FILE__) . '/../lib/pillow/Property.php');
require_once(dirname(__FILE__) . '/../lib/pillow/StandardProperty.php');

class StandardPropertyGetByAddressTest extends PHPUnit_Framework_TestCase
{
  public function setUp() {
    Pillow_WebService::setServiceId('test');
  }
  
  public function tearDown() {
    Pillow_WebService::setServiceId(NULL);
    Pillow_StandardProperty::$propertyServiceUrl = NULL;
  }
  
  /**
   * @test
   */
  public function itCreatesTheCorrectObjectFromXml() {
    $url = dirname(__FILE__) . '/test_data/standard_property_success.xml';
    Pillow_StandardProperty::$propertyServiceUrl =  $url;
    $prop = Pillow_StandardProperty::getByAddress('test', 'test');
    
    $this->assertSame('3333333', $prop->zpid);
    $this->assertSame('555 Nowhere Way', $prop->street);
    $this->assertSame('77777', $prop->zipcode);
    $this->assertSame('NOWHERE', $prop->city);
    $this->assertSame('TX', $prop->state);
    $this->assertSame('35.0', $prop->latitude);
    $this->assertSame('-94.0', $prop->longitude);
    $this->assertSame('125500', $prop->zestimate->amount);
    $this->assertSame('01/03/2011', $prop->zestimate->lastUpdated);
    $this->assertSame('0', $prop->zestimate->percentile);
    $this->assertSame('95380', $prop->zestimate->valuationRange->low);
    $this->assertSame('136795', $prop->zestimate->valuationRange->high);
  }
  
  /**
   * @test
   * @expectedException Pillow_InvalidXmlException
   */
  public function itThrowsExceptionWhenInvalidXml() {
    $url = dirname(__FILE__) . '/test_data/chart_invalid_file.xml';
    Pillow_StandardProperty::$propertyServiceUrl = $url;
    $prop = Pillow_StandardProperty::getByAddress('test', 'test');
  }
}