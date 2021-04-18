<?php
class Cart66MerchantWarrior extends Cart66GatewayAbstract {

  var $field_string;
  var $fields = array();
  var $response_string;
  var $response = array();
  var $gateway_url;
   
  public function __construct() {
    parent::__construct();
    
    // initialize error arrays
    $this->_errors = array();
    $this->_jqErrors = array();
    
    $mode = 'LIVE';
    if(Cart66Setting::getValue('mwarrior_test_mode')) {
      $mode = 'TEST';
    }

    $this->clearErrors();
    
    // Set end point and api credentials
    $apiPassphrase = Cart66Setting::getValue('mwarrior_api_passphrase');
    $merchantUUID = Cart66Setting::getValue('mwarrior_merchant_uuid');
    $apiKey = Cart66Setting::getValue('mwarrior_api_key');
    $apiEndPoint = Cart66Setting::getValue('auth_url');
    if("TEST" == $mode) {
      $apiEndPoint = 'https://base.merchantwarrior.com/post/';
      $apiPassphrase = Cart66Setting::getValue('mwarrior_test_api_passphrase');
      $merchantUUID = Cart66Setting::getValue('mwarrior_test_merchant_uuid');
      $apiKey = Cart66Setting::getValue('mwarrior_test_api_key');
    }
    $this->_apiEndPoint = $apiEndPoint;
    
    // Set api data
    $this->_apiData['APIPASSPHRASE'] = $apiPassphrase;
    $this->_apiData['APIKEY'] = $apiKey;
    $this->_apiData['MERCHANTUUID'] = $merchantUUID;

    if(!($this->_apiData['APIPASSPHRASE'] && $this->_apiData['APIKEY'] && $this->_apiData['MERCHANTUUID'])) {
      throw new Cart66Exception('Invalid Merchant Warrior Configuration', 66540); 
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
   
  public function addField($field, $value) {
    $this->fields["$field"] = $value;   
  }

  public function initCheckout($total) {
    $p = $this->getPayment();
    $b = $this->getBilling();
    Cart66Common::log("Payment info for checkout: " . print_r($p, true));

	  $expDate = $p['cardExpirationMonth'] . substr($p['cardExpirationYear'], 2);
	
	  $this->addField('Description', 'Your shopping cart');
	  $this->addField('method', 'processCard');
	  $this->addField('transactionAmount', number_format($total,2, '.', ''));
	  $this->addField('transactionCurrency', Cart66Setting::getValue('mwarrior_currency'));
	  $this->addField('transactionProduct', '12345');
	  $this->addField('merchantUUID', $this->_apiData['MERCHANTUUID']);
	  $this->addField('apiKey', $this->_apiData['APIKEY']);
	  $this->addField('customerName', $b['firstName'].' '.$b['lastName']);
	  $this->addField('customerCountry', $b['country']);
	  $this->addField('customerAddress', $b['address']);
	  $this->addField('customerCity', $b['city']);
	  $this->addField('customerState', $b['state']);
	  $this->addField('customerPostCode', $b['zip']);
	  $this->addField('customerPhone', $p['phone']);
	  $this->addField('customerEmail', $p['email']);
	  $this->addField('paymentCardNumber', $p['cardNumber']);
	  $this->addField('paymentCardExpiry', $expDate);
	  $this->addField('paymentCardCSC', $p['securityId']);
	  $this->addField('paymentCardName', $b['firstName'].' '.$b['lastName']);
	  $this->addField('customerIP', self::getRemoteIP());
	  $this->addField('hash', self::calculateHash($this->fields));

  }

  function doSale() {
    // This function actually processes the payment.  This function will 
    // load the $response array with all the returned information.  
    // The response code values are:
    // 1 - Approved
    // 2 - Declined
    // 3 - Error
      
    $sale = false;
    
    if($this->fields['transactionAmount'] > 0) {
      // Construct the fields string to pass to merchant warrior
      foreach( $this->fields as $key => $value ) {
        $this->field_string .= "$key=" . urlencode( $value ) . "&";
      }

      // Execute the HTTPS post via CURL
      $ch = curl_init($this->_apiEndPoint); 
      curl_setopt($ch, CURLOPT_HEADER, 0); 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
      curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $this->field_string, "& " )); 
        
      // Do not worry about checking for SSL certs
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		
      $this->response_string = urldecode(curl_exec($ch)); 

      if (curl_errno($ch)) {
        $this->response['Response Reason Text'] = curl_error($ch);
      }
      else {
        curl_close ($ch);
      }
		
		  // Run CURL
		  $response = $this->response_string;
		  if(isset($this->response['Response Reason Text'])) {
		    $error = $this->response['Response Reason Text'];
		  }
		
		  // Check for CURL errors
		  if (isset($error) && strlen($error)) {
			  $errorMessage = "Transaction Error: Could not successfully communicate with Payment Processor ({$error}).";
		  }
	
		  // Make sure the API returned something
		  if (!isset($response) || strlen($response) < 1) {
			  $errorMessage = "Transaction Error: Payment Processor did not return a valid response.";
		  }
		
		  // Parse the XML
		  $xml = simplexml_load_string($response);
		  // Convert the result from a SimpleXMLObject into an array
		  $xml = (array)$xml;
			
		  // Check for a valid response code
		  if (!isset($xml['responseCode']) || strlen($xml['responseCode']) < 1) {
			  $errorMessage = "Transaction Error: Payment Processor did not return a valid response code.";
			  $xml['responseCode'] = 0;
		  }
		  
		  // Validate the response - the only successful code is 0
		  $status = ((int)$xml['responseCode'] === 0) ? true : false;
		  
		  // Make the response a little more useable
		  $result = array('status' => $status, 'transactionID' => (isset($xml['transactionID']) ? $xml['transactionID'] : null), 'responseData' => $xml);
		  
		  // Set an error message if the transaction failed
		  if ($status === false) {
			  $errorMessage = "Transaction Declined: {$xml['responseCode']} {$xml['responseMessage']}.";
		  }
		
		  // Set an error and redirect if something went wrong
		  if (isset($errorMessage) && strlen($errorMessage)) {
			  //return $errorMessage;
			  $this->response['Response Reason Text'] = $errorMessage;
			  $this->response['Reason Response Code'] = $xml['responseCode'];
		  }
		  else {
			  // Successful transaction!
			  $this->response['Transaction ID'] = isset($xml['transactionID']) ? $xml['transactionID'] : null;
			  $this->response['Response Reason Text'] = "OK";
			  $sale = $this->response['Transaction ID'];
		  }
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
    $description['errorcode'] = $this->response['Response Reason Code'];
    $this->_logFields();
    $this->_logResponse();
    return $description;
  }
   
  protected function _logResponse() {
    $out = "Merchant Warrior Response Log\n";
    foreach ($this->response as $key => $value) {
      $out .= "\t$key = $value\n";
    }
    Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] $out");
  }
   
  protected function _logFields() {
    $out = "Merchant Warrior Field Log\n";
    foreach ($this->fields as $key => $value) {
      $out .= "\t$key = $value\n";
    }
    Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] $out");
  }

  function dumpFields() {
    // Used for debugging, this function will output all the field/value pairs
    // that are currently defined in the instance of the class using the
    // add_field() function.
      
    echo "<h3>mwarrior_class->dump_fields() Output:</h3>";
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
    // about merchant warrior's response.
      
    echo "<h3>mwarrior_class->dump_response() Output:</h3>";
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

 	/**
	 * Returns the (best guess) customer's IP
	 *
	 * @return string
	 */
	public function getRemoteIP() {
	  $remoteIP = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null);
		if (strstr($remoteIP, ',')) {
		  $chunks = explode(',', $remoteIP);
			$remoteIP = trim($chunks[0]);
		}
		return $remoteIP;
	}
	
	/**
	 * Generates and returns the request hash after being
	 * provided with the postData array.
	 *
	 * @param array $postData
	 */
	public function calculateHash(array $postData = array()) {
	  // Check the amount param
	  if (!isset($postData['transactionAmount']) || !strlen($postData['transactionAmount'])) {
			exit("Missing or blank amount field in postData array.");
		}
		
		// Check the currency param
		if (!isset($postData['transactionCurrency']) || !strlen($postData['transactionCurrency'])) {
			exit("Missing or blank currency field in postData array.");
		}
		
		// Generate & return the hash
		return md5(strtolower($this->_apiData['APIPASSPHRASE'] . $this->_apiData['MERCHANTUUID'] . $postData['transactionAmount'] . $postData['transactionCurrency']));
	}

}
