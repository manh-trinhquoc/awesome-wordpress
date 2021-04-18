<?php 
class Cart66CaPost {
  protected $merchantId;  
  protected $fromZip;
  protected $credentials;
  protected $language = 'en';

  public function __construct() {
    $setting = new Cart66Setting();
    $this->username = Cart66Setting::getValue('capost_username');
    $this->password = Cart66Setting::getValue('capost_password');
    $this->customer_number = Cart66Setting::getValue('capost_customer_number');
    $this->fromZip = Cart66Setting::getValue('capost_ship_from_zip');
    $this->credentials = 1;
  }
  
  public function getRate($PostalCode, $dest_zip, $dest_country_code, $service, $weight, $length=0, $width=0, $height=0) {
    
    if($this->credentials != 1) {
      print 'Please set your credentials with the setCredentials function';
      die();
    }
    $rate = array();
    $dest_zip = strtoupper(str_replace(' ', '', $dest_zip));
    $PostalCode = strtoupper(str_replace(' ', '', $PostalCode));
    
    $username = $this->username;
    $password = $this->password;
    $mailedBy = $this->customer_number;
    
    $weight = number_format($weight / 2.2, 2); // Convert to Kilograms for accurate pricing
    
    if($dest_country_code == 'CA') {
      $destination = "<destination>
        <domestic>
          <postal-code>{$dest_zip}</postal-code>
        </domestic>
      </destination>";
    }
    elseif($dest_country_code == 'US') {
      $destination = "<destination>
        <united-states>
          <zip-code>{$dest_zip}</zip-code>
        </united-states>
      </destination>";
    }
    else {
      $destination = "<destination>
        <international>
          <country-code>{$dest_country_code}</country-code>
        </international>
      </destination>";
    }

    $xmlRequest = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
    <mailing-scenario xmlns=\"http://www.canadapost.ca/ws/ship/rate\">
      <customer-number>{$mailedBy}</customer-number>
      <parcel-characteristics>
        <weight>{$weight}</weight>
      </parcel-characteristics>
      <origin-postal-code>{$PostalCode}</origin-postal-code>
      $destination
    </mailing-scenario>";

    $curl = curl_init('https://soa-gw.canadapost.ca/rs/ship/price'); // Create REST Request
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    //curl_setopt($curl, CURLOPT_CAINFO, realpath(dirname($argv[0])) . '/../../../third-party/cert/cacert.pem'); // Signer Certificate in PEM format
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlRequest);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, $username . ':' . $password);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/vnd.cpc.ship.rate+xml', 'Accept: application/vnd.cpc.ship.rate+xml'));
    $curl_response = curl_exec($curl); // Execute REST Request
    if(curl_errno($curl)){
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Curl error: " . curl_error($curl) . "\n");
    }
    //Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] request: " . $xmlRequest);
    //Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] response: " . print_r($curl_response, true));
    curl_close($curl);
    
    // Example of using SimpleXML to parse xml response
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string('<root>' . preg_replace('/<\?xml.*\?>/','',$curl_response) . '</root>');
    if(!$xml) {
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Failed loading XML: " . $curl_response);
      foreach(libxml_get_errors() as $error) {
        Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] message: " . $error->message);
      }
    }
    else {
      if($xml->{'price-quotes'} ) {
        $priceQuotes = $xml->{'price-quotes'}->children('http://www.canadapost.ca/ws/ship/rate');
        if($priceQuotes->{'price-quote'}) {
          foreach($priceQuotes as $priceQuote) {
            $rate[] = array('name' => (string) $priceQuote->{'service-name'}[0], 'rate' => (string) $priceQuote->{'price-details'}->{'due'}[0]);
            //echo 'Service Name: ' . var_dump((string) $priceQuote->{'service-name'}[0]) . "\n";
            //echo 'Price: ' . var_dump((string) $priceQuote->{'price-details'}->{'due'}[0]) . "\n\n";	
          }
        }
      }
      if($xml->{'messages'}) {
        $messages = $xml->{'messages'}->children('http://www.canadapost.ca/ws/messages');		
        foreach($messages as $message) {
          Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] error code: " . $message->code);
          Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] error message: " . $message->description);
        }
      }

    }
    
    return $rate;
  }
  
  /**
   * Return an array where the keys are the service names and the values are the prices
   */
  public function getAllRates($toZip, $toCountryCode, $weight) {
    
    $rates = array();
    $method = new Cart66ShippingMethod();
    if($toCountryCode == 'CA') {
      $capostServices = $method->getServicesForCarrier('capost');
      $rate = $this->getRate($this->fromZip, $toZip, $toCountryCode, null, $weight);
      if($rate !== false) {
        foreach($capostServices as $service => $code) {
          if(is_array($rate)) {
            foreach($rate as $r) {
              if($rate !== FALSE && $r["name"] == $code) {
                $rates[$service] = number_format((float) $r["rate"], 2, '.', '');
              }
              //Cart66Common::log("LIVE RATE REMOTE RESULT ==> ZIP: $toZip Service: $service $code) Rate: " . print_r($rate, true));
            }
          }
        }
      }
    } else {
      $capostServices = $method->getServicesForCarrier('capost_intl');
      $rate = $this->getRate($this->fromZip, $toZip, $toCountryCode, null, $weight);
      if($rate !== false) {
        foreach($capostServices as $service => $code) {
          if(is_array($rate)) {
            foreach($rate as $r) {
              $code = str_replace('INTL', 'INT\'L', $code);
              if($rate !== FALSE && $r["name"] == $code) {
                $rates[$service] = number_format((float) $r["rate"], 2, '.', '');
              }
              //Cart66Common::log("LIVE RATE REMOTE RESULT ==> ZIP: $toZip Service: $service $code) Rate: " . print_r($rate, true));
            }
          }
        }
      }
    }
    return $rates;
  }  
  
}