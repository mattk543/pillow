<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 * Provides cross cutting message detection when reading Zillow xml files
 */
class Pillow_XMLReader extends XMLReader
{
  private $lastZillowMessageText;
  
  private $lastZillowMessageCode;
  
  public function open($URI, $encoding = NULL, $options = 0) {
    $result = @parent::open($URI, $encoding, $options);
    if(!$result) {
      throw new Pillow_Exception('Unable to open uri ' . $URI);
    }
    
    return $result;
  }
  
  /**
   * Move to next node in document. In doing so, checks for the message element
   * which should exist in all Zillow xml docs. Set the lastErrorMessageCode and
   * Text with what was found. If an error code is found, throw exception.
   * 
   * @throws Pillow_Exception when message error code not zero
   * @return boolean 
   */
  public function read() {
    $ret_value = parent::read();
    
    if(!$ret_value) return $ret_value;
    
    if($this->nodeType === XMLReader::ELEMENT && $this->name == 'message') {
      $xml = simplexml_load_string($this->readOuterXml());
      $this->lastZillowMessageText = (string) $xml->text;
      $this->lastZillowMessageCode = (int) (string) $xml->code;
      
      //When error code is anything but 0, throw the exception to allow caller
      // to determine what to do with it
      if($this->lastZillowMessageCode !== 0) {
        throw new Pillow_Exception($this->lastZillowMessageText, $this->lastZillowMessageCode);
      }
    }
    
    return $ret_value;
  }
}
