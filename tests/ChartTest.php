<?php
require_once(dirname(__FILE__) . '/../lib/pillow/WebService.php');
require_once(dirname(__FILE__) . '/../lib/pillow/Exceptions.php');
require_once(dirname(__FILE__) . '/../lib/pillow/XMLReader.php');
require_once(dirname(__FILE__) . '/../lib/pillow/Chart.php');

class ChartGetByZpidTest extends PHPUnit_Framework_TestCase
{
  public function setUp() {
    Pillow_WebService::setServiceId('test');
  }
  
  public function tearDown() {
    Pillow_Chart::$chartServiceUrl = NULL;
    Pillow_WebService::setServiceId(NULL);
  }
  
  /**
   * @test
   */
  public function itCreatesTheCorrectObjectFromXml()
  {
    $url = dirname(__FILE__) . '/test_data/chart_success.xml';
    Pillow_Chart::$chartServiceUrl = $url;
    $chart = Pillow_Chart::getByZpid('test', NULL, NULL, NULL, NULL);
    $this->assertSame('http://example.com/url', $chart->url);
    $this->assertSame('http://example.com/graphsanddata', $chart->graphsAndData);
  }
  
  /**
   * @test
   * @expectedException Pillow_InvalidXmlException
   */
  public function itThrowsExceptionWhenInvalidXml() {
    $url = dirname(__FILE__) . '/test_data/chart_invalid_file.xml';
    Pillow_Chart::$chartServiceUrl = $url;
    $chart = Pillow_Chart::getByZpid('test', NULL, NULL, NULL, NULL);
  }
}