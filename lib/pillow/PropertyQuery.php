<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2011, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Pillow_PropertyQuery
{
  protected $searchUrl;
  
  public function __construct($address, $city_state_zip) {
    $this->address = $address;
    $this->cityStateZip = $city_state_zip;
    $this->searchUrl = Pillow_WebService::getSearchUrl($this->address, $this->cityStateZip);
  }
  
  public function execute() {
    $reader = Pillow_WebService::getXMLReader();
    
    if( !$reader->open($this->searchUrl) ) {
      throw new Pillow_Exception('unable to open uri');
    }
    
    $property = $this->extractProperty($reader);
    
    
    if(!$property) {
      //do something when no result
    }
    
    return $property;
  }
  
  private function extractProperty($XMLReader) {
    $ret_value = FALSE;
    
    while($XMLReader->read()) {
      if($XMLReader->nodeType !== XMLReader::ELEMENT) {
        continue;
      }

      if($XMLReader->name == 'result') {
        $ret_value = $this->mapXmlToProperty($XMLReader->readOuterXml());
        break;
      }
    }

    return $ret_value;
  }
  
  protected function mapXmlToProperty($outer_xml) {
    $xml = simplexml_load_string($outer_xml);

    $p = new Pillow_StandardProperty();
    $p->zpid = (string) $xml->zpid;
    $p->city = (string) $xml->address->city;
    $p->latitude = (string) $xml->address->latitude;
    $p->longitude = (string) $xml->address->longitude;
    $p->state = (string) $xml->address->state;
    $p->street = (string) $xml->address->street;
    $p->zipcode = (string) $xml->address->zipcode;
    
    $p->zestimate->amount = (string) $xml->zestimate->amount;
    $p->zestimate->lastUpdated = (string) $xml->zestimate->{'last-updated'};
    $p->zestimate->percentile = (string) $xml->zestimate->percentile;
    $p->zestimate->valueChange = (string) $xml->zestimate->valueChange;
    $p->zestimate->valuationRange->low = (string) $xml->zestimate->valuationRange->low;
    $p->zestimate->valuationRange->high = (string) $xml->zestimate->valuationRange->high;
    

    $p->links['homedetails'] = (string) $xml->links->homedetails;
    $p->links['graphsanddata'] = (string) $xml->links->graphsanddata;
    $p->links['mapthishome'] = (string) $xml->links->mapthishome;
    $p->links['myestimator'] = (string) $xml->links->myestimator;

    return $p;
  }
}
