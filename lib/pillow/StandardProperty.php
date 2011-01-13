<?php

class Pillow_StandardProperty extends Pillow_Property
{
  public static $propertyServiceUrl;
  
  /**
   * The zestimate which accompanies the property
   * @var Pillow_Zestimate $zestimate
   */
  public $zestimate;
  
  public function __construct() {
    parent::__construct();
    
    $this->zestimate = new Pillow_Zestimate();
  }
  
  /**
   * Gets a new standard property by address and cityStateZip
   * 
   * @param string $address
   * @param string $cityStateZip
   * @return Pillow_StandardProperty 
   */
  public static function getByAddress($address, $cityStateZip) {
    $url = self::getPropertyServiceUrl($address, $cityStateZip);
    $property = new Pillow_StandardProperty();
    $reader = new Pillow_XMLReader();
    
    $reader->open($url);

    if( ! self::extractIntoProperty($property, $reader) ) {
      throw new Pillow_InvalidXmlException($url);
    }

    return $property;
  }
  
  /**
   * Gets the Standard Property Service URL using supplied parameters. 
   * If Pillow_StandardProperty::$propertyServiceUrl is set, it's value will be returned instead.
   * 
   * @param string $address
   * @param string $cityStateZip
   * @return string 
   */
  private static function getPropertyServiceUrl($address, $cityStateZip) {
    $zwsId = Pillow_WebService::getServiceId();
    
    if(self::$propertyServiceUrl) {
      return self::$propertyServiceUrl;
    }
    
    $url = 'http://www.zillow.com/webservice/GetSearchResults.htm?'
         . 'zws-id='       . urlencode($zwsId) . '&'
         . 'address='      . urlencode( $address ) . '&'
         . 'citystatezip=' . urlencode( $cityStateZip )
         ;
    
    return $url;
  }
  
  /**
   * Extracts the XMLReader data into the $property. Returns TRUE if successful,
   * FALSE otherwise
   * 
   * @param Pillow_StandardProperty $property
   * @param XMLReader $XMLReader
   * @return boolean 
   */
  private static function extractIntoProperty(Pillow_StandardProperty $property, XMLReader $XMLReader) {
    $ret_value = FALSE;
    
    while($XMLReader->read()) {
      if($XMLReader->nodeType !== XMLReader::ELEMENT) {
        continue;
      }

      if($XMLReader->name == 'result') {
        $xml = simplexml_load_string($XMLReader->readOuterXml());
        self::mapXmlToProperty($xml, $property);
        $ret_value = TRUE;
        break;
      }
    }

    return $ret_value;
  }
  
  /**
   * Maps the SimpleXMLElement to the $property
   * 
   * @param SimpleXMLElement $xml
   * @param Pillow_StandardProperty $property
   */
  private static function mapXmlToProperty(SimpleXMLElement $xml, Pillow_Property $property) {
    $property->zpid = (string) $xml->zpid;
    $property->city = (string) $xml->address->city;
    $property->latitude = (string) $xml->address->latitude;
    $property->longitude = (string) $xml->address->longitude;
    $property->state = (string) $xml->address->state;
    $property->street = (string) $xml->address->street;
    $property->zipcode = (string) $xml->address->zipcode;
    
    $property->zestimate->amount = (string) $xml->zestimate->amount;
    $property->zestimate->lastUpdated = (string) $xml->zestimate->{'last-updated'};
    $property->zestimate->percentile = (string) $xml->zestimate->percentile;
    $property->zestimate->valueChange = (string) $xml->zestimate->valueChange;
    $property->zestimate->valuationRange->low = (string) $xml->zestimate->valuationRange->low;
    $property->zestimate->valuationRange->high = (string) $xml->zestimate->valuationRange->high;
    

    $property->links['homedetails'] = (string) $xml->links->homedetails;
    $property->links['graphsanddata'] = (string) $xml->links->graphsanddata;
    $property->links['mapthishome'] = (string) $xml->links->mapthishome;
    $property->links['myestimator'] = (string) $xml->links->myestimator;
  }
}