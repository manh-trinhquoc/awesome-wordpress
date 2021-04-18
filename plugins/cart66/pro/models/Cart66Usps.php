<?php
class Cart66Usps {
  
  protected $_uspsUsername;
  protected $_rates;
  
  public function __construct() {
    $this->_uspsUsername = Cart66Setting::getValue('usps_username');
  }
  
  public function clearRates() {
    $this->_rates = array();
  }
  
  public function getRates($zipOrigin, $zipDestination, $pounds=0, $ounces=0, $container='VARIABLE', $size='REGULAR', $service='ALL', $machinable='true', $width=10, $length=15, $height=10) {
    $weight = $this->_convertToPoundOunces($pounds);
    $pounds = $weight->pounds;
    $ounces += $weight->ounces;
    $rateReq = 'RateV4Request USERID="' . $this->_uspsUsername . '"';
    $data = array(
      'Revision' => '4',
      'Package ID="1"' => array(
        'Service' => 'ALL',
        'ZipOrigination' => $zipOrigin,
        'ZipDestination' => $zipDestination,
        'Pounds' => $pounds,
        'Ounces' => $ounces,
        'Container' => $container,
        'Size' => $size,
        'Width' => $width,
        'Length' => $length,
        'Height' => $height,
        'Machinable' => $machinable
      )
    );
    $xml = Cart66Common::arrayToXml($data, $rateReq);
    Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] USPS Domestic Rate Request XML:\n$xml");
    $url = 'http://production.shippingapis.com/ShippingAPI.dll?API=RateV4&Xml=' . urlencode($xml);
    $result = Cart66Common::curl($url);
    Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] USPS Result:\n" . $result);
    $this->_parseResult($result);
    return $this->_rates;
  }
  
  public function getIntlRates($zipOrigin, $countryCode, $value, $pounds=0, $ounces=0, $commercial='N', $mailType='Package', $container='VARIABLE', $size="REGULAR", $machinable='true', $width=10, $length=15, $height=10, $girth=0) {
    $rateReq = 'IntlRateV2Request USERID="' . $this->_uspsUsername . '"';
    $countryName = Cart66Common::getCountryName($countryCode);
    
    $weight = $this->_convertToPoundOunces($pounds);
    $pounds = $weight->pounds;
    $ounces += $weight->ounces;
    
    $data = array(
      'Revision' => '4',
      'Package ID="1"' => array(
        'Pounds' => $pounds,
        'Ounces' => $ounces,
        'Machinable' => $machinable,
        'MailType' => $mailType,
        'ValueOfContents' => $value,
        'Country' => $countryName,
        'Container' => $container,
        'Size' => $size,
        'Width' => $width,
        'Length' => $length,
        'Height' => $height,
        'Girth' => $girth,
        'OriginZip' => $zipOrigin
      )
    );
    $xml = Cart66Common::arrayToXml($data, $rateReq);
    Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] USPS Intl rate request xml:\n$xml");
    $url = 'http://production.shippingapis.com/ShippingAPI.dll?API=IntlRateV2&Xml=' . urlencode($xml);
    $result = Cart66Common::curl($url);
    // Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] USPS Result:\n" . $result);
    $this->_parseIntlResult($result);
    return $this->_rates;
  }
  
  public function addRate($name, $price) {
    $this->_rates[$name] = $price;
  }
  
  /**
   * Given an XML string return an array where the keys are the services and the values are the rates.
   * 
   * @param string $xml An xml string
   * @return array
   */
  public function _parseResult($xml) {
    if($xml = simplexml_load_string($xml)) {
      $this->clearRates();
      if($xml->Package->Postage){
        foreach($xml->Package->Postage as $service) {
          $name = (string)$service->MailService;
          $rate = (float)$service->Rate;
          $name = str_replace('&lt;sup&gt;&#174;&lt;/sup&gt;', '', $name);
          $name = str_replace('&lt;sup&gt;&#8482;&lt;/sup&gt;', '', $name);
          $this->addRate($name, $rate);
        }
      }
      else{
        Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Unknown Failure: ".print_r($xml,true));
      }      
    }
    else {
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] USPS domestic xml parsing failure. Unable to load XML string: $xml");
    }
  }
  
  /**
   * Given an XML string return an array where the keys are the services and the values are the rates.
   * 
   * @param string $xml An xml string
   * @return array
   */
  public function _parseIntlResult($xml) {
    if($xml = simplexml_load_string($xml)) {
      $this->clearRates();
      if($xml->Package->Service){
        foreach($xml->Package->Service as $service) {
          $name = (string)$service->SvcDescription;
          $rate = (float)$service->Postage;
          $name = str_replace('&lt;sup&gt;&#174;&lt;/sup&gt;', '', $name);
          $name = str_replace('&lt;sup&gt;&#8482;&lt;/sup&gt;', '', $name);
          $name = str_replace('*', '', $name);
          $this->addRate($name, $rate);
          Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] USPS: Adding international rate ===> $name -- $rate");
        }
      }
      else{
        Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Unknown Failure: ".print_r($xml,true));
      }
    }
    else {
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] USPS Intl xml parsing failure. Unable to load XML string: $xml");
    }
  }
  
  protected function _convertToPoundOunces($pounds) {
    $weight = new stdClass();
    $weight->pounds = floor($pounds);
    $weight->ounces = round(($pounds - $weight->pounds)*16);
    return $weight;
  }
}