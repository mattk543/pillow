<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2011, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Pillow_WebService
{
  /**
   * The zws id string to use throughout
   * @var string $zwsId
   */
  private static $zwsId;
  
  public static function setServiceId( $zwsid ) {
    self::$zwsId = $zwsid;
  }
  
  public static function getServiceId() {
    if(strlen(self::$zwsId) < 1) {
      throw new Pillow_ZwsIdNotSetException();
    }
    
    return self::$zwsId;
  }
  
  public static function getXMLReader() {
    return new XMLReader();
  }
  
  public static function getDeepSearchUrl($address, $cityStateZip) {
    $zws_id = self::getServiceId();
    
    if(!$zws_id) {
      throw new Pillow_ZwsIdNotSetException();
    }
    
    $url = 'http://www.zillow.com/webservice/GetDeepSearchResults.htm?'
         . 'zws-id='       . urlencode(self::$zwsId) . '&'
         . 'address='      . urlencode( $address ) . '&'
         . 'citystatezip=' . urlencode( $cityStateZip )
         ;
    
    $url = '../tests/test_data/deep_property_query_success.xml';
    
    return $url;
  }
  
  public static function getSearchUrl($address, $cityStateZip) {
    $zws_id = self::getServiceId();
    
    if(!$zws_id) {
      throw new Pillow_ZwsIdNotSetException();
    }
    
    $url = 'http://www.zillow.com/webservice/GetSearchResults.htm?'
         . 'zws-id='       . urlencode(self::$zwsId) . '&'
         . 'address='      . urlencode( $address ) . '&'
         . 'citystatezip=' . urlencode( $cityStateZip )
         ;
    //echo $url . "\n";
    $url = '../tests/test_data/property_query_success.xml';
    
    return $url;
  }
  
  public static function getRegionPostingsUrl($cityStateZip, $rentalsEnabled) {
    $zws_id = self::getServiceId();
    
    if(!$zws_id) {
      throw new Pillow_ZwsIdNotSetException();
    }
    
    $url  = 'http://www.zillow.com/webservice/GetRegionPostings.htm?';
    $url .= 'zws-id='         . urlencode(self::$zwsId);
    $url .= '&citystatezip='  . urlencode($cityStateZip);
    $url .= '&rental=';
    $url .= ($rentalsEnabled === TRUE) ? 'true' : 'false';
    //echo $url;
    $url = 'test_data2.xml';

    return $url;
  }
  
  public static function getZestimateUrl($zpid) {
    $url = 'http://www.zillow.com/webservice/GetZestimate.htm?'
         . 'zws-id='  . urlencode(self::$zwsId)  . '&'
         . 'zpid='    . urlencode($zpid)
         ;
    
    $url = '../tests/test_data/zestimate_success.xml';
    
    return $url;
  }
  
}