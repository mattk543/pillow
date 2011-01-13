<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Pillow_RegionPostingsProperty extends Pillow_Property
{
  public $imageCount;

  public $price;

  public $lastRefreshedDate;
  
  /**
   * The use code
   * @var string $useCode
   */
  public $useCode;
  
  /**
   * lot size
   * @var string $lotSizeSqFt
   */
  public $lotSizeSqFt;
  
  /**
   * finished size
   * @var string $finishedSqFt
   */
  public $finishedSqFt;
  
  /**
   * number of bathrooms
   * @var string $bathrooms
   */
  public $bathrooms;
  
  /**
   * number of bedrooms
   * @var string $bedrooms
   */
  public $bedrooms;
}
