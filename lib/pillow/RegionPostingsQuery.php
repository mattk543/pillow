<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2011, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Pillow_RegionPostingsQuery
{
  private $cityStateZip;

  private $rentalsEnabled;

  private $queryFilter;

  private $limit;

  private $offset;

  private $sortBy;

  public function __construct($cityStateZip) {
    $this->queryFilter = array();
    $this->cityStateZip = $cityStateZip;
  }

  public function enableRentals($enable) {
    $this->rentalsEnabled = $enable;
    return $this;
  }

  public function limit($limit) {
    $this->limit = $limit;
    return $this;
  }

  public function offset($offset) {
    $this->offset = $offset;
    return $this;
  }

  public function where($field, $operator, $value) {
    $this->queryFilter[] = array('field' => $field, 'operator' => $operator, 'value' => $value);
    return $this;
  }

  public function sortBy($field, $sort_direction = 'asc') {
    $this->sortBy = array('field' => $field, 'direction' => $sort_direction);
    return $this;
  }

  public function execute() {
    $reader = Pillow_WebService::getXMLReader();
    
    $reader->open(Pillow_WebService::getRegionPostingsUrl($this->cityStateZip, $this->rentalsEnabled));
    $rs = $this->extractProperties($reader);

    if(count($this->sortBy) > 0) {
      $rs = $this->applySort($rs);
    }

    if(count($this->queryFilter) > 0) {
      $rs = $this->applyFilter($rs);
    }

    if($this->limit || $this->offset) {
      $rs = $this->applyLimitOffset($rs);
    }
    
    return $rs;
  }

  private function extractProperties($XMLReader) {
    $ret_array = array();
    while($XMLReader->read()) {
      if($XMLReader->nodeType !== XMLReader::ELEMENT) {
        continue;
      }
      
      switch($XMLReader->name) {
        case 'makeMeMove':
          //
        break;
        case 'forSaleByOwner':
          //
        break;
      }

      if($XMLReader->name == 'result') {
        $property = $this->mapXmlToProperties($XMLReader->readOuterXml());
        $ret_array[] = $property;
      }
    }

    return $ret_array;
  }

  private function mapXmlToProperties($outer_xml) {
    $xml = simplexml_load_string($outer_xml);

    $p = new Pillow_RegionPostingsProperty();
    $p->bathrooms = (string) $xml->property->bathrooms;
    $p->bedrooms = (string) $xml->property->bedrooms;
    $p->city = (string) $xml->property->address->city;
    $p->finishedSqFt = (string) $xml->property->finishedSqFt;
    $p->imageCount = (string) $xml->property->images->count;
    $p->latitude = (string) $xml->property->address->latitude;
    $p->longitude = (string) $xml->property->address->longitude;
    $p->lotSizeSqFt = (string) $xml->property->lotSizeSqFt;
    $p->price = (string) $xml->price;
    $p->state = (string) $xml->property->address->state;
    $p->street = (string) $xml->property->address->street;
    $p->useCode = (string) $xml->property->useCode;
    $p->zipcode = (string) $xml->property->address->zipcode;
    $p->zpid = (string) $xml->property->zpid;

    $p->lastRefreshedDate = (string) $xml->lastRefreshedDate;

    $p->links['homedetails'] = (string) $xml->property->links->homedetails;

    return $p;
  }

  private function applyLimitOffset($rs) {
    $ret_array = array();

    $offset = 0;
    if($this->offset) {
      $offset = $this->offset;
    }

    $limit = 1000;
    if($this->limit) {
      $limit = $this->limit + $offset;
    }

    foreach($rs as $i => $property) {
      if($i < $offset) {
        continue;
      }

      if($i > $limit) {
        break;
      }

      $ret_array[] = $property;
    }

    return $ret_array;
  }

  private function applySort($rs) {

    $index = array();
    $properties = array();
    foreach($rs as $k => $property) {
      $field = $this->sortBy['field'];
      $key = 'prop_' . $k;
      $index[$key] = $property->$field;
      $properties[$key] = $property;
    }

    if($this->sortBy['direction'] == 'asc') {
      asort($index);
    } else {
      arsort($index);
    }

    $ret_array = array();
    foreach($index as $k => $v) {
      $ret_array[] = $properties[$k];
    }

    return $ret_array;
  }

  private function applyFilter($rs) {
    $ret_array = array();

    foreach($rs as $property) {
      foreach($this->queryFilter as $filter) {
        switch($filter['operator']) {
          case '=':
            if($property->{$filter['field']} == $filter['value']) {
              $ret_array[] = $property;
            }
          break;
          case '>':
            if($property->{$filter['field']} > $filter['value']) {
              $ret_array[] = $property;
            }
          break;
          case '<':
            if($property->{$filter['field']} < $filter['value']) {
              $ret_array[] = $property;
            }
          break;
          case '>=':
            if($property->{$filter['field']} >= $filter['value']) {
              $ret_array[] = $property;
            }
          break;
          case '<=':
            if($property->{$filter['field']} <= $filter['value']) {
              $ret_array[] = $property;
            }
          break;
          case 'between':
            if($property->{$filter['field']} >= $filter['value'][0] &&
             $property->{$filter['field']} <= $filter['value'][1]
             ) {
               $ret_array[] = $property;
            }
          break;
        }

      }
    }

    return $ret_array;
  }
}