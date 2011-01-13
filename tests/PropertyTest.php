<?php
require_once(dirname(__FILE__) . '/../lib/pillow/Property.php');
require_once(dirname(__FILE__) . '/../lib/pillow/Chart.php');
require_once(dirname(__FILE__) . '/../lib/pillow/UnitType.php');


class PropertyGetChartTest extends PHPUnit_Framework_TestCase
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
  public function itGetsAChart() {
    $url = dirname(__FILE__) . '/test_data/chart_success.xml';
    Pillow_Chart::$chartServiceUrl = $url;
    
    $property = new Pillow_Property();
    $chart = $property->getChart(Pillow_UnitType::DOLLAR);
    $this->assertInstanceOf('Pillow_Chart', $chart);
  }
}