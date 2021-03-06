<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Pillow_Factory
{
    /**
     * The zws id string to use throughout
     * @var string $_zws_id
     */
    private $_zws_id;

    /**
     * Stores a transporter object to inject into created Pillow objects
     * @var Pillow_Transporter $_transporter
     */
    private $_transporter;

    /**
     * Class Constructor
     * @param string $zws_id
     */
    public function __construct( $zws_id )
    {
        $this->_zws_id = $zws_id;
    }

    /**
     * Creates (if not already created) and returns a Pillow_Transporter object
     * @return Pillow_Transporter
     */
    public function createTransporter()
    {
        if( FALSE === isset($this->_transporter) )
            $this->_transporter = new Pillow_Transporter();

        return $this->_transporter;
    }

    /**
     * Finds and returns matching properties with basic elements set.
     * Will throw any number of exceptions based on Zillow error codes
     * @param string $address
     * @param string $city_state_zip
     * @return array of Pillow_Property objects
     * @link http://www.zillow.com/howto/api/GetSearchResults.htm
     * @throws Pillow_Exception
     */
    public function findProperties( $address, $city_state_zip )
    {
        $trans = $this->createTransporter();

        $uri = 'http://www.zillow.com/webservice/GetSearchResults.htm?'
              . 'zws-id='       . $this->_zws_id        . '&'
              . 'address='      . urlencode( $address ) . '&'
              . 'citystatezip=' . urlencode( $city_state_zip );

        $ret_array = array();

        try {
            $xml = $trans->get( $uri, 'search' );

            foreach ( $xml->response->results->children() as $result )
            {
                $args = array(
                    'zpid'          => (string) $result->zpid,
                    'links'         => $this->_createStdObject( $result->links ),
                    'city'          => (string) $result->address->city,
                    'latitude'      => (string) $result->address->latitude,
                    'longitude'     => (string) $result->address->longitude,
                    'state'         => (string) $result->address->state,
                    'street'        => (string) $result->address->street,
                    'zipcode'       => (string) $result->address->zipcode
                );

                $ret_array[] = $this->_createProperty( $args );
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Attempts to find an exact Property match for an address
     * @param string $address
     * @param string $city_state_zip
     * @param boolean $deep - get deep property (default FALSE)
     * @link http://www.zillow.com/howto/api/GetSearchResults.htm
     * @link http://www.zillow.com/howto/api/GetDeepSearchResults.htm
     * @return Pillow_Property
     */
    public function findExactProperty( $address, $city_state_zip, $deep = FALSE )
    {
        try {

            if( TRUE === $deep )
                $results = $this->findDeepProperties( $address, $city_state_zip );
            else
                $results = $this->findProperties( $address, $city_state_zip );

            if( count($results) > 0 )
            {
                return $results[0];
            }

        } catch ( Exception $e ) {
            //Do nothing with caught exception
        }
        return $this->_createProperty( array() );
    }

    /**
     * Finds and returns matching properties with deep elements set.
     * Will throw any number of exceptions based on Zillow error codes
     * @param string $address
     * @param string $city_state_zip
     * @return array of Pillow_Property objects
     * @link http://www.zillow.com/howto/api/GetDeepSearchResults.htm
     * @throws Pillow_Exception
     */
    public function findDeepProperties( $address, $city_state_zip )
    {
        $trans = $this->createTransporter();
        
        $uri = 'http://www.zillow.com/webservice/GetDeepSearchResults.htm?'
              . 'zws-id='       . $this->_zws_id        . '&'
              . 'address='      . urlencode( $address ) . '&'
              . 'citystatezip=' . urlencode( $city_state_zip );
        
        $ret_array = array();

        try {
            $xml = $trans->get( $uri, 'search' );

            foreach ( $xml->response->results->children() as $result )
            {
                $args = array(
                    'zpid'          => (string) $result->zpid,
                    'links'         => $this->_createStdObject( $result->links ),
                    'city'          => (string) $result->address->city,
                    'latitude'      => (string) $result->address->latitude,
                    'longitude'     => (string) $result->address->longitude,
                    'state'         => (string) $result->address->state,
                    'street'        => (string) $result->address->street,
                    'zipcode'       => (string) $result->address->zipcode,
                    'fipsCounty'    => (string) $result->FIPScounty,
                    'useCode'       => (string) $result->useCode,
                    'yearBuilt'     => (string) $result->yearBuilt,
                    'lotSizeSqFt'   => (string) $result->lotSizeSqFt,
                    'finishedSqFt'  => (string) $result->finishedSqFt,
                    'bathrooms'     => (string) $result->bathrooms,
                    'bedrooms'      => (string) $result->bedrooms,
                    'lastSoldDate'  => (string) $result->lastSoldDate,
                    'lastSoldPrice' => (string) $result->lastSoldPrice,
                    'zestimate'     => $this->_createZestimate($result->zestimate)
                );
                
                $ret_array[] = $this->_createProperty( $args );
            }
            return $ret_array;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Creates a Pillow_Property using assoc array to set class member values
     * @param array $vars
     * @return Pillow_Property
     */
    private function _createProperty( $vars )
    {
        $property = new Pillow_Property();
        $property->setFactory($this);
        foreach( $vars as $key => $value )
        {
            $property->$key = $value;
        }
        return $property;
    }

    /**
     * Creates a Pillow_Zestimate object based on supplied zpid
     * @param string $zpid
     * @return Pillow_Zestimate
     * @link http://www.zillow.com/howto/api/GetZestimate.htm
     * @throws Pillow_Exception
     */
    public function createZestimate( $zpid )
    {
        $trans = $this->createTransporter();
        
        $uri = 'http://www.zillow.com/webservice/GetZestimate.htm?'
             . 'zws-id=' . $this->_zws_id . '&'
             . 'zpid='   . $zpid;

         try {
            $xml = $trans->get( $uri, 'zestimate' );

            return $this->_createZestimate( $xml->response->zestimate );
            
         } catch (Exception $e) {
            throw $e;
         }
    }

    /**
     * Creates a zestimate object from supplied xml object
     * @param simpleXmlObject $xml
     * @return Pillow_Zestimate
     */
    private function _createZestimate( $xml )
    {
        $estimate = new Pillow_Zestimate;

        $estimate->amount                = (string) $xml->amount;
        $estimate->lastUpdated           = (string) $xml->{'last-updated'};
        $estimate->valueChange           = (string) $xml->valueChange;
        $estimate->valuationRange->low   = (string) $xml->valuationRange->low;
        $estimate->valuationRange->high  = (string) $xml->valuationRange->high;
        $estimate->percentile            = (string) $xml->percentile;

        return $estimate;
    }

    /**
     * Creates a stdClass object to represent a Zillow Chart record
     * @param string $zpid
     * @param string $unit_type
     * @param int $width - optional
     * @param int $height - optional
     * @param string $chartDuration - optional
     * @return Pillow_Chart
     * @throws Pillow_Exception
     * @link http://www.zillow.com/howto/api/GetChart.htm
     */
    public function createChart( $zpid, $unit_type, $width = NULL, $height = NULL, $chartDuration = NULL )
    {
        $trans = $this->createTransporter();

        $uri = 'http://www.zillow.com/webservice/GetChart.htm?'
             . 'zws-id='         . $this->_zws_id . '&'
             . 'zpid='           . $zpid . '&'
             . 'unit-type='      . $unit_type . '&'
             . 'width='          . $width . '&'
             . 'height='         . $height . '&'
             . 'chartDuration='  . $chartDuration
             ;

        try {
            $xml = $trans->get( $uri, 'chart' );

            $chart = new Pillow_Chart;
            $chart->url = $xml->response->url;
            $chart->graphsAndData = $xml->response->graphsanddata;

            return $chart;
            
         } catch (Exception $e) {
            throw $e;
         }
    }

    /**
     * Finds and returns array of Pillow_Property objects
     * @param string $zpid
     * @param int $count
     * @return array
     * @link http://www.zillow.com/howto/api/GetComps.htm
     */
    public function findComps( $zpid, $count )
    {
        $trans = $this->createTransporter();

        $ret_array = array();

        $uri = 'http://www.zillow.com/webservice/GetComps.htm?'
             . 'zws-id='    . $this->_zws_id . '&'
             . 'zpid='      . $zpid . '&'
             . 'count='     . $count
             ;

        try {
            $xml = $trans->get( $uri, 'comp' );

            foreach ( $xml->response->properties->comparables->comp as $result )
            {
                $args = array(
                    'zpid'      => (string) $result->zpid,
                    'links'     => $this->_createStdObject( $result->links ),
                    'city'      => (string) $result->address->city,
                    'latitude'  => (string) $result->address->latitude,
                    'longitude' => (string) $result->address->longitude,
                    'state'     => (string) $result->address->state,
                    'street'    => (string) $result->address->street,
                    'zipcode'   => (string) $result->address->zipcode,
                    'zestimate' => $this->_createZestimate($result->zestimate)
                );

                $ret_array[] = $this->_createProperty( $args );
            }
            return $ret_array;
         } catch (Exception $e) {
            throw $e;
         }
    }

    /**
     * Finds and returns array of Pillow_Property objects
     * @param string $zpid
     * @param int $count
     * @return array
     * @link http://www.zillow.com/howto/api/GetDeepComps.htm
     */
    public function findDeepComps( $zpid, $count )
    {
        $trans = $this->createTransporter();

        $ret_array = array();

        $uri = 'http://www.zillow.com/webservice/GetDeepComps.htm?'
             . 'zws-id='    . $this->_zws_id . '&'
             . 'zpid='      . $zpid . '&'
             . 'count='     . $count
             ;

        try {
            $xml = $trans->get( $uri, 'comp' );

            foreach ( $xml->response->properties->comparables->comp as $result )
            {
                $args = array(
                    'zpid'          => (string) $result->zpid,
                    'links'         => $this->_createStdObject( $result->links ),
                    'city'          => (string) $result->address->city,
                    'latitude'      => (string) $result->address->latitude,
                    'longitude'     => (string) $result->address->longitude,
                    'state'         => (string) $result->address->state,
                    'street'        => (string) $result->address->street,
                    'zipcode'       => (string) $result->address->zipcode,
                    'fipsCounty'    => (string) $result->FIPScounty,
                    'useCode'       => (string) $result->useCode,
                    'yearBuilt'     => (string) $result->yearBuilt,
                    'lotSizeSqFt'   => (string) $result->lotSizeSqFt,
                    'finishedSqFt'  => (string) $result->finishedSqFt,
                    'bathrooms'     => (string) $result->bathrooms,
                    'bedrooms'      => (string) $result->bedrooms,
                    'lastSoldDate'  => (string) $result->lastSoldDate,
                    'lastSoldPrice' => (string) $result->lastSoldPrice,
                    'zestimate'     => $this->_createZestimate($result->zestimate)
                );

                $ret_array[] = $this->_createProperty( $args );
            }
            return $ret_array;
         } catch (Exception $e) {
            throw $e;
         }
    }

    /**
     * Converts a simpleXMLObject to either a string or stdClass object
     * @param simpleXMLObject $xml_obj
     * @return mixed
     */
    protected function _createStdObject( $xml_obj )
    {
        $obj;
        if( count($xml_obj) == 0 )
        {
            $obj = (string) $xml_obj;
        }
        else
        {
            $obj = new stdClass;
            foreach( $xml_obj->children() as $child )
            {
                $name = $child->getName();
                $obj->$name = $this->_createStdObject($child);
            }
        }

        return $obj;
    }
}
