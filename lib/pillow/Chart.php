<?php

class Pillow_Chart
{
  /**
   *
   * @var url $url
   */
  public $url;

  /**
   *
   * @var url $graphsAndData
   */
  public $graphsAndData;
  
  /**
   * Here for testing purposes, do not set in production
   * 
   * @var url $chartServiceUrl
   */
  public static $chartServiceUrl;
  
  /**
   * Gets a Pillow_Chart object using specified parameters
   * 
   * @param string $zpid
   * @param Pillow_UnitType::DOLLAR|Pillow_UnitType::PERCENT $unitType
   * @param numeric $width
   * @param numeric $height
   * @param numeric $chartDuration
   * @throws Pillow_InvalidXmlException when the xml file cannot be properly parsed
   * @return Pillow_Chart 
   */
  public static function getByZpid($zpid, $unitType, $width, $height, $chartDuration) {
    $url = self::getChartServiceUrl($zpid, $unitType, $width, $height, $chartDuration);
    $chart = new Pillow_Chart();
    $reader = new Pillow_XMLReader();
    
    $reader->open($url);

    if( ! self::extractIntoChart($chart, $reader) ) {
      throw new Pillow_InvalidXmlException($url);
    }

    return $chart;
  }
  
  /**
   * Extracts the XMLReader data into the $chart. Returns TRUE if successful,
   * FALSE otherwise
   * 
   * @param Pillow_Chart $chart
   * @param XMLReader $XMLReader
   * @return boolean 
   */
  private static function extractIntoChart(Pillow_Chart $chart, XMLReader $XMLReader) {
    $ret_value = FALSE;
    
    while($XMLReader->read()) {
      if($XMLReader->nodeType !== XMLReader::ELEMENT) {
        continue;
      }

      if($XMLReader->name == 'response') {
        $xml = simplexml_load_string($XMLReader->readOuterXml());
        self::mapXmlToChart($xml, $chart);
        $ret_value = TRUE;
        break;
      }
    }

    return $ret_value;
  }
  
  /**
   * Maps the SimpleXMLElement to the $chart
   * 
   * @param SimpleXMLElement $xml
   * @param Pillow_Chart $chart
   * @return type 
   */
  private static function mapXmlToChart(SimpleXMLElement $xml, Pillow_Chart $chart) {
    $chart->url = (string) $xml->url;
    $chart->graphsAndData = (string) $xml->graphsanddata;
  }
  
  /**
   * Gets the Chart Service URL using supplied parameters. 
   * If Pillow_Chart::$chartServiceUrl is set, it's value will be returned instead.
   * 
   * @param string $zpid
   * @param Pillow_UnitType::DOLLAR|Pillow_UnitType::PERCENT $unitType
   * @param numeric $width
   * @param numeric $height
   * @param numeric $chartDuration
   * @return string 
   */
  private static function getChartServiceUrl($zpid, $unitType, $width, $height, $chartDuration) {
    $zwsId = Pillow_WebService::getServiceId();
    
    if(self::$chartServiceUrl) {
      return self::$chartServiceUrl;
    }
    
    $url = 'http://www.zillow.com/webservice/GetChart.htm?'
         . 'zws-id='        . urlencode($zwsId)  . '&'
         . 'zpid='          . urlencode($zpid) . '&'
         . 'unit-type='     . urlencode($unitType) . '&'
         . 'width='         . urlencode($width) . '&'
         . 'height='        . urlencode($height) . '&'
         . 'chartDuration=' . urlencode($chartDuration)
         ;
    
    return $url;
  }
}

