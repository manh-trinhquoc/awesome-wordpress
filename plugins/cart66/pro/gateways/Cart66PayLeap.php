<?php
class Cart66PayLeap extends Cart66GatewayAbstract {
  
  protected $_apiData;
  protected $_apiEndPoint;
  
  public function __construct() {
    parent::__construct();
    
    // initialize error arrays
    $this->_errors = array();
    $this->_jqErrors = array();
    
    $mode = 'LIVE';
    if(Cart66Setting::getValue('payleap_test_mode')) {
      $mode = 'TEST';
    }

    $this->clearErrors();
    
    // Set end point and api credentials
    $apiUsername = Cart66Setting::getValue('payleap_api_username');
    $apiTransactionKey = Cart66Setting::getValue('payleap_transaction_key');
    $apiEndPoint = Cart66Setting::getValue('auth_url');
    if("TEST" == $mode) {
      $apiEndPoint = 'https://uat.payleap.com/TransactServices.svc/ProcessCreditCard';
      $apiUsername = Cart66Setting::getValue('payleap_test_api_username');
      $apiTransactionKey = Cart66Setting::getValue('payleap_test_transaction_key');
    }
    $this->_apiEndPoint = $apiEndPoint;
    
    // Set api data
    $this->_apiData['APIUSERNAME'] = $apiUsername;
    $this->_apiData['TRANSACTIONKEY'] = $apiTransactionKey;

    if(!($this->_apiData['APIUSERNAME'] && $this->_apiData['TRANSACTIONKEY'])) {
      throw new Cart66Exception('Invalid PayLeap Configuration', 66520); 
    }
    
  }
  
  /**
   * Return an array of accepted credit card types where the keys are the diplay values and the values are the gateway values
   * 
   * @return array
   */
  public function getCreditCardTypes() {
    $cardTypes = array();
    $setting = new Cart66Setting();
    $cards = Cart66Setting::getValue('auth_card_types');
    if($cards) {
      $cards = explode('~', $cards);
      if(in_array('mastercard', $cards)) {
        $cardTypes['MasterCard'] = 'mastercard';
      }
      if(in_array('visa', $cards)) {
        $cardTypes['Visa'] = 'visa';
      }
      if(in_array('amex', $cards)) {
        $cardTypes['American Express'] = 'amex';
      }
      if(in_array('discover', $cards)) {
        $cardTypes['Discover'] = 'discover';
      }
    }
    return $cardTypes;
  }

  public function initCheckout($total) {
    $p = $this->getPayment();
    $b = $this->getBilling();
    Cart66Common::log("Payment info for checkout: " . print_r($p, true));
    
    $extData = $this->generateExtendedData();
    
    $expMonth = $p['cardExpirationMonth'];
    $expYear = substr($p['cardExpirationYear'], -2);
    $this->addField('Username', $this->_apiData['APIUSERNAME']);
    $this->addField('Password', $this->_apiData['TRANSACTIONKEY']);
    $this->addField('TransType', 'Sale');
    $this->addField('NameOnCard', $b['firstName'] . ' ' . $b['lastName']);
    $this->addField('CardNum', $p['cardNumber']);
    $this->addField('ExpDate', $expMonth . $expYear);
	  $this->addField('CVNum', $p['securityId']);
    $this->addField('Amount', $total);
    $this->addField('ExtData', $extData);
	  $this->addField('PNRef', '');
    $this->addField('MagData', '');

  }
  
  private function generateExtendedData() {
    $b = $this->getBilling();
    $p = $this->getPayment();
    
    $billTo = array(
      'Name' => $b['firstName'] . ' ' . $b['lastName'],
      'Address' => array(
        'Street' => $b['address'],
        'City' => $b['city'],
        'State' => $b['state'],
        'Zip' => $b['zip'],
        'Country' => $b['country']
      ),
      'Email' => $p['email'],
      'Phone' => preg_replace('/\D/', '', $p['phone']  )
    );
    
    $invoice = array(
      'InvNum' => '',
      'BillTo' => $billTo
    );
    
    $data = array(
      'TrainingMode' => 'F',
      'Invoice' => $invoice
    );
    
    $xml = trim($this->arrayToXml($data));
    $xml = preg_replace('/>\s+</', '><', $xml);
    return $xml;
  }
  
  public static function arrayToXml($array, $name=false, $space='', $standalone=false, $beginning=true, $nested=0) {
    $output = '';
    if ($beginning) {
      if($standalone) header("content-type:text/xml;charset=utf-8");
      if(!isset($output)) { $output = ''; }
      if($standalone) $output .= '<'.'?'.'xml version="1.0" encoding="UTF-8"'.'?'.'>' . "\n";
      if(!empty($space)) {
        $output .= '<' . $name . ' xmlns="' . $space . '">' . "\n";
      }
      elseif($name) {
        $output .= '<' . $name . '>' . "\n";
      }
      $nested = 0;
    }

    // This is required because XML standards do not allow a tag to start with a number or symbol, you can change this value to whatever you like:
    $ArrayNumberPrefix = 'ARRAY_NUMBER_';

     foreach ($array as $root=>$child) {
      if (is_array($child)) {
        $output .= str_repeat(" ", (2 * $nested)) . '  <' . (is_string($root) ? $root : $ArrayNumberPrefix . $root) . '>' . "\n";
        $nested++;
        $output .= self::arrayToXml($child,NULL,NULL,NULL,FALSE, $nested);
        $nested--;
        $tag = is_string($root) ? $root : $ArrayNumberPrefix . $root;
        $ex = explode(' ', $tag);
        $tag = array_shift($ex);
        $output .= str_repeat(" ", (2 * $nested)) . '  </' . $tag . '>' . "\n";
      }
      else {
        if(!isset($output)) { $output = ''; }
        $tag = is_string($root) ? $root : $ArrayNumberPrefix . $root;
        $ex = explode(' ', $tag);
        $tag = array_shift($ex);
        $output .= str_repeat(" ", (2 * $nested)) . '  <' . (is_string($root) ? $root : $ArrayNumberPrefix . $root) . '>' . $child . '</' . $tag . '>' . "\n";
      }
    }
    
    $ex = explode(' ', $name);
    $name = array_shift($ex);
    if ($beginning && $name) $output .= '</' . $name . '>';

    return $output;
  }
  
  public function addField($field, $value) {
    $this->fields["$field"] = $value;   
  }
    
  public function doSale() {
    $sale = false;
    
    if($this->fields['Amount'] > 0) {
      foreach( $this->fields as $key => $value ) {
        $this->field_string .= "$key=" . urlencode( $value ) . "&";
      }
    
      $header = array("MIME-Version: 1.0","Content-type: application/x-www-form-urlencoded","Contenttransfer-encoding: text"); 
      $ch = curl_init();

      // set URL and other appropriate options 
      curl_setopt($ch, CURLOPT_URL, $this->_apiEndPoint); 
      curl_setopt($ch, CURLOPT_VERBOSE, 1); 
      curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); 
      // uncomment for host with proxy server
      // curl_setopt ($ch, CURLOPT_PROXY, "http://proxyaddress:port"); 
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
      curl_setopt($ch, CURLOPT_POST, true); 
      curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $this->field_string, "& " )); 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
      curl_setopt ($ch, CURLOPT_TIMEOUT, 10);
    
      // send packet and receive response
      // close the curl resource, and free system resources
      $this->response_string = urldecode(curl_exec($ch)); 

      if (curl_errno($ch)) {
      $this->response['Response Reason Text'] = curl_error($ch);
      }
      else {
        curl_close ($ch);
      }
      $xml = new SimpleXMLElement($this->response_string);
      $this->response['Response Reason Text'] = $xml->RespMSG;
      $this->response['Transaction ID'] = $xml->PNRef;
      $this->response['Response Code'] = $xml->Result;
      // $this->dump_response();
      
      // Prepare to return the transaction id for this sale.
      if($this->response['Response Code'] == 0) {
        $sale = $this->response['Transaction ID'];
      }
    }
    else {
      // Process free orders without sending to the Auth.net gateway
      $this->response['Transaction ID'] = 'MT-' . Cart66Common::getRandString();
      $sale = $this->response['Transaction ID'];
    }
    return $sale;
  }
  
  function getResponseReasonText() {
      return $this->response['Response Reason Text'];
   }
   
   function getTransactionId() {
     return $this->response['Transaction ID'];
   }
  
  public function getTransactionResponseDescription() {
    $description['errormessage'] = $this->getResponseReasonText();
    $description['errorcode'] = $this->response['Response Code'];
    $this->_logFields();
    $this->_logResponse();
    return $description;
  }
   
  protected function _logResponse() {
     $out = "PayLeap Response Log\n";
     foreach ($this->response as $key => $value) {
       $out .= "\t$key = $value\n";
     }
     Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] $out");
   }
   
   protected function _logFields() {
     $out = "PayLeap Field Log\n";
     foreach ($this->fields as $key => $value) {
        $out .= "\t$key = $value\n";
     }
     Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] $out");
   }

   function dumpFields() {
 
      // Used for debugging, this function will output all the field/value pairs
      // that are currently defined in the instance of the class using the
      // add_field() function.
      
      echo "<h3>payleap_class->dump_fields() Output:</h3>";
      echo "<table width=\"95%\" border=\"1\" cellpadding=\"2\" cellspacing=\"0\">
            <tr>
               <td bgcolor=\"black\"><b><font color=\"white\">Field Name</font></b></td>
               <td bgcolor=\"black\"><b><font color=\"white\">Value</font></b></td>
            </tr>"; 
            
      foreach ($this->fields as $key => $value) {
         echo "<tr><td>$key</td><td>".urldecode($value)."&nbsp;</td></tr>";
      }
 
      echo "</table><br>"; 
   }

   function dumpResponse() {
 
      // Used for debugging, this function will output all the response field
      // names and the values returned for the payment submission.  This should
      // be called AFTER the process() function has been called to view details
      // about payleap's response.
      
      echo "<h3>payleap_class->dump_response() Output:</h3>";
      echo "<table width=\"95%\" border=\"1\" cellpadding=\"2\" cellspacing=\"0\">
            <tr>
               <td bgcolor=\"black\"><b><font color=\"white\">Index&nbsp;</font></b></td>
               <td bgcolor=\"black\"><b><font color=\"white\">Field Name</font></b></td>
               <td bgcolor=\"black\"><b><font color=\"white\">Value</font></b></td>
            </tr>";
            
      $i = 0;
      foreach ($this->response as $key => $value) {
         echo "<tr>
                  <td valign=\"top\" align=\"center\">$i</td>
                  <td valign=\"top\">$key</td>
                  <td valign=\"top\">$value&nbsp;</td>
               </tr>";
         $i++;
      } 
      echo "</table><br>";
   }
  
}