<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Pillow_Zestimate
{
    /**
     * Zestimate Amount
     * @var string $amount
     */
    public $amount;

    /**
     * Last updated
     * @var string $lastUpdated
     */
    public $lastUpdated;

    /**
     * Value Change
     * @var string $valueChange
     */
    public $valueChange;

    /**
     * Valuation Range (low, high)
     * @var stdClass $valuationRange
     */
    public $valuationRange;

    /**
     * Percentile
     * @var string $percentile
     */
    public $percentile;
    
    /**
     * Array of links
     * @var array $links
     */
    public $links;

    public function __construct()
    {
        $this->valuationRange = new stdClass;
        $this->valuationRange->low = NULL;
        $this->valuationRange->high = NULL;
        $this->links = array();
    }
    
    public static function getByZpid($zpid) {
    $reader = Pillow_WebService::getXMLReader();
    
    if( !$reader->open(Pillow_WebService::getZestimateUrl($zpid)) ) {
      throw new Pillow_Exception('unable to open uri');
    }
    
    $zestimate = self::extractZestimate($reader);
    
    
    if(!$zestimate) {
      //do something when no result
    }
    
    return $zestimate;
  }
  
  private static function extractZestimate($XMLReader) {
    $ret_value = FALSE;
    
    while($XMLReader->read()) {
      if($XMLReader->nodeType !== XMLReader::ELEMENT) {
        continue;
      }

      if($XMLReader->name == 'response') {
        $xml = simplexml_load_string($XMLReader->readOuterXml());
        $ret_value = self::mapXmlToZestimate($xml);
        break;
      }
    }

    return $ret_value;
  }
  
  public static function mapXmlToZestimate($xml) {
    $p = new Pillow_Zestimate();
    
    $p->amount = (string) $xml->zestimate->amount;
    $p->lastUpdated = (string) $xml->zestimate->{'last-updated'};
    $p->percentile = (string) $xml->zestimate->percentile;
    $p->valueChange = (string) $xml->zestimate->valueChange;
    $p->valuationRange->low = (string) $xml->zestimate->valuationRange->low;
    $p->valuationRange->high = (string) $xml->zestimate->valuationRange->high;
    
    $p->links['homedetails'] = (string) $xml->links->homedetails;
    $p->links['graphsanddata'] = (string) $xml->links->graphsanddata;
    $p->links['mapthishome'] = (string) $xml->links->mapthishome;
    $p->links['myestimator'] = (string) $xml->links->myestimator;
    $p->links['comparables'] = (string) $xml->links->myestimator;

    return $p;
  }
}

