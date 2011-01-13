<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Pillow_Property
{
  /**
   * zpid
   * @var string $zpid
   */
  public $zpid;
  
  /**
   * Street
   * @var string $street
   */
  public $street;
  
  /**
   * City
   * @var string $city
   */
  public $city;
  
  /**
   * State
   * @var string $state
   */
  public $state;
  
  /**
   * Zip
   * @var string $zipcode
   */
  public $zipcode;
  
  /**
   * latitude
   * @var string $latitude
   */
  public $latitude;
  
  /**
   * longitude
   * @var string $longitude
   */
  public $longitude;
  
  /**
   * Group of zillow links for the property
   * @var array $links
   */
  public $links;

  public function __construct() {
    $this->links = array();
  }

  public function getExpandedZestimate() {
    $zestimate = Pillow_Zestimate::getByZpid($this->zpid);
    
    return $zestimate;
  }
  
  public function getChart( $unit_type, $width = NULL, $height = NULL, $chartDuration = NULL ) {
    $chart = Pillow_Chart::getByZpid($this->zpid, $unit_type, $width, $height, $chartDuration);
    
    return $chart;
  }
  
  public function getComps( $count ) {
    
  }
  
  public function getDeepComps( $count ) {
    
  }
}