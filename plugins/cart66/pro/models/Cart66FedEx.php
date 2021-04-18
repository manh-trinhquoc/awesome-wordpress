<?php 
class Cart66FedEx {
  protected $developerKey;  
  protected $password;  
  protected $accountNumber;
  protected $meterNumber;
  protected $dimensionsUnits = "IN";
  protected $weightUnits = "LB";
  protected $fromZip;
  protected $dropOffType;
  protected $credentials;

  public function __construct() {
    $setting = new Cart66Setting();
    $this->developerKey = Cart66Setting::getValue('fedex_developer_key');
    $this->password = Cart66Setting::getValue('fedex_password');
    $this->accountNumber = Cart66Setting::getValue('fedex_account_number');
    $this->meterNumber = Cart66Setting::getValue('fedex_meter_number');
    $this->fromZip = Cart66Setting::getValue('fedex_ship_from_zip');
    $this->dropOffType = Cart66Setting::getValue('fedex_pickup_code');
    $this->credentials = 1;
  }
  
  public function setDimensionsUnits($unit){
    $this->dimensionsUnits = $unit;
  }
  
  public function setWeightUnits($unit){
    $this->weightUnits = $unit;
  }
  
  public function getRate($PostalCode, $dest_zip, $dest_country_code, $service, $weight, $length=0, $width=0, $height=0) {
    $setting= new Cart66Setting();
    $home_country = explode('~', Cart66Setting::getValue('home_country'));
    $countryCode = array_shift($home_country);
    $pickupCode = (Cart66Setting::getValue('fedex_pickup_code')) ? Cart66Setting::getValue('fedex_pickup_code') : "REGULAR_PICKUP";
    $residential = (Cart66Setting::getValue('fedex_only_ship_commercial')) ? "0" : "1";
    $locationType = (Cart66Setting::getValue('fedex_location_type') == 'commercial') ? "0" : "1";
    
    if ($this->credentials != 1) {
      print 'Please set your credentials with the setCredentials function';
      die();
    }
    
    $path_to_wsdl = CART66_PATH . "/pro/models/RateService_v14.wsdl";
    
    ini_set("soap.wsdl_cache_enabled", "0");
    
    $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

    $request['WebAuthenticationDetail'] = array(
      'UserCredential' => array(
        'Key' => $this->developerKey, 
        'Password' => $this->password
      )
    ); 
    
    $request['ClientDetail'] = array(
      'AccountNumber' => $this->accountNumber, 
      'MeterNumber' => $this->meterNumber
    );
    
    $request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Available Services Request v14 using PHP ***');
    $request['Version'] = array(
      'ServiceId' => 'crs', 
      'Major' => '14', 
      'Intermediate' => '0', 
      'Minor' => '0'
    );
    $request['ReturnTransitAndCommit'] = true;
    $request['RequestedShipment']['DropoffType'] = $pickupCode; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
    
    $request['RequestedShipment']['ShipTimestamp'] = date('c');
    
    // Service Type and Packaging Type are not passed in the request
    $request['RequestedShipment']['Shipper'] = array(
      'Address' => array(
        'PostalCode' => $this->fromZip,
        'CountryCode' => $countryCode,
        'Residential' => $locationType
      )
    );
    
    $request['RequestedShipment']['Recipient'] = array(
      'Address' => array(
        'PostalCode' => $dest_zip,
        'CountryCode' => $dest_country_code,
        'Residential' => $residential
      )
    );
    
    $request['RequestedShipment']['ShippingChargesPayment'] = array(
      'PaymentType' => 'SENDER',
         'Payor' => array(
        'ResponsibleParty' => array(
          'AccountNumber' => $this->accountNumber,
          'Contact' => null,
          'Address' => array(
            'CountryCode' => $countryCode
          )
        )
      )
    );
    
    $request['RequestedShipment']['RateRequestTypes'] = 'ACCOUNT'; 
    $request['RequestedShipment']['RateRequestTypes'] = 'LIST'; 
    $request['RequestedShipment']['PackageCount'] = $this->getPackageCount();
    
    $request['RequestedShipment']['RequestedPackageLineItems'] = $this->getRequestedPackageLineItems($weight);
    
    try {
      $client->__setLocation('https://gateway.fedex.com:443/web-services');
      
      $response = $client->getRates($request);
        
      if($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
        $rate = array();
        if(is_array($response->RateReplyDetails)){
          foreach($response->RateReplyDetails as $rateReply){
            $serviceType = $rateReply->ServiceType;
            $amount = $rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount;
            $rate[] = array('name' => $serviceType, 'rate' => $amount);
          }
        }
        else{
          $serviceType = $response->RateReplyDetails->ServiceType;
          $amount = $response->RateReplyDetails->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount;
          $rate[] = array('name' => $serviceType, 'rate' => $amount);
        }
      }
      else {
        $rate = false;
        Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Error: " . print_r($response->Notifications, true));
      }
    }
    catch (SoapFault $exception) {
      $this->printFault($exception, $client);
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] FedEx Error|| Code: " . $exception->faultcode . " Message: " . $exception->faultstring);
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Client Details: " . print_r($client, true));
      $rate = false;
    }
    return $rate;
    
  }
  
  /**
   * Return an array where the keys are the service names and the values are the prices
   */
  public function getAllRates($toZip, $toCountryCode, $weight) {
    
    $rates = array();
    $method = new Cart66ShippingMethod();
    if($toCountryCode == 'US' || $toCountryCode == 'CA') {
      $fedexServices = $method->getServicesForCarrier('fedex');
      $rate = $this->getRate($this->fromZip, $toZip, $toCountryCode, null, $weight);
      if($rate !== false) {
        foreach($fedexServices as $service => $code) {
          foreach($rate as $r) {
            if($r["name"] == $code) {
              $rates[$service] = number_format((float) $r["rate"], 2, '.', '');
            }
          }
        }
        Cart66Common::log("LIVE RATE REMOTE RESULT ==> ZIP: $toZip Service: $service $code) Rate: " . print_r($rates, true));
      }
      $fedexServices = $method->getServicesForCarrier('fedex_intl');
      $rate = $this->getRate($this->fromZip, $toZip, $toCountryCode, null, $weight);
      if($rate !== false) {
        foreach($fedexServices as $service => $code) {
          foreach($rate as $r) {
            if($r["name"] == $code) {
              $rates[$service] = number_format((float) $r["rate"], 2, '.', '');
            }
          }
        }
        Cart66Common::log("LIVE RATE REMOTE RESULT ==> ZIP: $toZip Service: $service $code) Rate: " . print_r($rates, true));
      }
    } else {
      $fedexServices = $method->getServicesForCarrier('fedex_intl');
      $rate = $this->getRate($this->fromZip, $toZip, $toCountryCode, null, $weight);
      if($rate !== false) {
        foreach($fedexServices as $service => $code) {
          foreach($rate as $r) {
            if($r["name"] == $code) {
              $rates[$service] = number_format((float) $r["rate"], 2, '.', '');
            }
          }
        }
        Cart66Common::log("LIVE RATE REMOTE RESULT ==> ZIP: $toZip Service: $service $code) Rate: " . print_r($rates, true));
      }
    }
    return $rates;
  } 
  
  public function getPackageCount() {
    $items = Cart66Session::get('Cart66Cart')->getItems();
    $count = 0;
    if(Cart66Setting::getValue('fedex_ship_individually')) {
      foreach($items as $item) {
        for($i=1; $i <= $item->getQuantity(); $i++){
          $count++;
        }
      }
    }
    else {
      $count = 1;
    }
    return $count;
  }

  public function getRequestedPackageLineItems($weight) {
    $items = Cart66Session::get('Cart66Cart')->getItems();
    $length = 0;
    $width = 0;
    $height = 0;
    $data = array();
    if(Cart66Setting::getValue('fedex_ship_individually')) {
      foreach($items as $item) {
        for($i=1; $i <= $item->getQuantity(); $i++){
          $data[] = array(
            'SequenceNumber' => $i,
            'GroupPackageCount' => 1,
            'Weight' => array(
              'Value' => $item->getWeight(),
              'Units' => $this->weightUnits
            ),
            'Dimensions' => array(
              'Length' => $length,
              'Width' => $width,
              'Height' => $height,
              'Units' => $this->dimensionsUnits
            )
          );
        }
      }
    }
    else {
      $data[] = array(
        'SequenceNumber' => 1,
        'GroupPackageCount' => 1,
        'Weight' => array(
          'Value' => $weight,
          'Units' => $this->weightUnits
        ),
        'Dimensions' => array(
          'Length' => $length,
          'Width' => $width,
          'Height' => $height,
          'Units' => $this->dimensionsUnits
        )
      );
    }
    return $data;
  }
  
}