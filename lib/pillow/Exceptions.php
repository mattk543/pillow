<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Pillow_Exception extends Exception {}

class Pillow_ZwsIdNotSetException extends Pillow_Exception
{
  public function __construct() {
    $message = 'zws id not set. Did you forget Pillow_WebService::setServiceId()?';
    parent::__construct($message);
  }
}

class Pillow_InvalidXmlException extends Pillow_Exception {}