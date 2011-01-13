<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2011, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Pillow_DeepPropertyQuery extends Pillow_PropertyQuery
{
  public function __construct($address, $city_state_zip) {
    parent::__construct($address, $city_state_zip);
    $this->searchUrl = Pillow_WebService::getDeepSearchUrl($this->address, $this->cityStateZip);
  }
  
  protected function mapXmlToProperty($outer_xml) {
    $xml = simplexml_load_string($outer_xml);

    $p = new Pillow_DeepProperty();
    $p->zpid = (string) $xml->zpid;
    $p->city = (string) $xml->address->city;
    $p->latitude = (string) $xml->address->latitude;
    $p->longitude = (string) $xml->address->longitude;
    $p->state = (string) $xml->address->state;
    $p->street = (string) $xml->address->street;
    $p->zipcode = (string) $xml->address->zipcode;
    
    $p->FIPScounty = (string) $xml->FIPScounty;
    $p->useCode = (string) $xml->useCode;
    $p->taxAssessmentYear = (string) $xml->taxAssessmentYear;
    $p->taxAssessment = (string) $xml->taxAssessment;
    $p->yearBuilt = (string) $xml->yearBuilt;
    $p->finishedSqFt = (string) $xml->finishedSqFt;
    
    $p->zestimate = Pillow_Zestimate::mapXmlToZestimate($xml);
    
    $p->links['homedetails'] = (string) $xml->links->homedetails;
    $p->links['graphsanddata'] = (string) $xml->links->graphsanddata;
    $p->links['mapthishome'] = (string) $xml->links->mapthishome;
    $p->links['myestimator'] = (string) $xml->links->myestimator;
    $p->links['comparables'] = (string) $xml->links->comparables;

    return $p;
  }
}
