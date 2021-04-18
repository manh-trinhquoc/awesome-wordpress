<?php
class Cart66Eway extends Cart66GatewayAbstract {

  protected $_apiEndPoint;
  protected $_customerId;
  
  var $field_string;
  var $fields = array();
  var $response_string;
  var $response = array();
   
  public function __construct() {
    parent::__construct();
    
    // initialize error arrays
    $this->_errors = array();
    $this->_jqErrors = array();
    
    $mode = 'LIVE';
    if(Cart66Setting::getValue('eway_sandbox')) {
      $mode = 'TEST';
    }

    $this->clearErrors();
    
    // Set end point
    $apiEndPoint = Cart66Setting::getValue('auth_url');
    $customerId = Cart66Setting::getValue('eway_customer_id');
    if("TEST" == $mode) {
      $apiEndPoint = 'https://www.eway.com.au/gateway_cvn/xmltest/testpage.asp';
      $customerId = Cart66Setting::getValue('eway_sandbox_customer_id');
    }
    $this->_apiEndPoint = $apiEndPoint;
    $this->_customerId = $customerId;
    
    if(!($this->_customerId)) {
      throw new Cart66Exception('Invalid eWay Configuration', 66530);
    }
    
  }
 
  /**
   * Return an array of accepted credit card types where the keys are the display values and the values are the gateway values
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
        
    $this->gateway_url = $this->_apiEndPoint;
	  
	  $this->addField('ewayTotalAmount', number_format($total,2, '', ''));
  	$this->addField('ewayCustomerFirstName', $b['firstName']);
  	$this->addField('ewayCustomerLastName', $b['lastName']);
  	$this->addField('ewayCustomerEmail', $p['email']);
  	$this->addField('ewayCustomerAddress', $b['address'] . ', ' . $b['city'] . ' ' . $b['state'] . ', ' . $b['country']);
  	$this->addField('ewayCustomerPostcode', $b['zip']);
  	$this->addField('ewayCustomerInvoiceDescription', 'Your Order Details');
  	$this->addField('ewayCustomerInvoiceRef', 'cart66');
  	$this->addField('ewayCardHoldersName', $b['firstName'] . ' ' . $b['lastName']);
  	$this->addField('ewayCardNumber', $p['cardNumber']);
  	$this->addField('ewayCardExpiryMonth', $p['cardExpirationMonth']);
  	$this->addField('ewayCardExpiryYear', substr($p['cardExpirationYear'], 2));
  	$this->addField('ewayTrxnNumber', "4230");
  	$this->addField('ewayOption1', "ewayOption1");
  	$this->addField('ewayOption2', "ewayOption2");
  	$this->addField('ewayOption3', "ewayOption3");
    $this->addField('ewayCVN', $p['securityId']);
	  if(Cart66Setting::getValue('eway_geo_ip_anti_fraud')) {
	    $this->addField('ewayCustomerIPAddress', self::getRemoteIP());
	    $this->addField('ewayCustomerBillingCountry', $b['country']);	  
    }
  }

  //Payment Function
	function doSale() {
	  $sale = false;
		if($this->fields['ewayTotalAmount'] > 0) {
		  $xmlRequest = "<ewaygateway><ewayCustomerID>" . $this->_customerId . "</ewayCustomerID>";
		  foreach($this->fields as $key=>$value) {
			  $xmlRequest .= "<$key>$value</$key>";
		  }
      $xmlRequest .= "</ewaygateway>";
      
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] XML Data sent to EWAY:\n" . $xmlRequest);

		  $xmlResponse = $this->sendTransactionToEway($xmlRequest);
      
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] XML Response from EWAY:\n" . $xmlResponse);
      
		  if($xmlResponse!=""){
			  $ewayResponseFields = $this->parseResponse($xmlResponse);
			  if(strtolower($ewayResponseFields["EWAYTRXNSTATUS"])=="false") {
          $this->response['Response Reason Text'] = $ewayResponseFields["EWAYTRXNERROR"];
        }
        elseif(strtolower($ewayResponseFields["EWAYTRXNSTATUS"])=="true") {
          $this->response['Transaction ID'] = isset($ewayResponseFields['EWAYTRXNNUMBER']) ? $ewayResponseFields['EWAYTRXNNUMBER'] : null;
  			  $this->response['Response Reason Text'] = $ewayResponseFields["EWAYTRXNERROR"];
  			  $this->response['Reason Response Code'] = $ewayResponseFields['EWAYTRXNNUMBER'];
  			  $sale = $this->response['Transaction ID'];
        }
        else {
          $this->response['Response Reason Text'] = "Error: An invalid response was received from the payment gateway.";
        }
		  }
      else {
        $this->response['Response Reason Text'] = "Error in XML response from eWay: " + $xmlResponse;
      }
    }
    else {
      // Process free orders without sending to the Eway gateway
      $this->response['Transaction ID'] = 'MT-' . Cart66Common::getRandString();
      $sale = $this->response['Transaction ID'];
    }
    return $sale;
	}

	//Send XML Transaction Data and receive XML response
	function sendTransactionToEway($xmlRequest) {
	  $ch = curl_init($this->gateway_url); 
    curl_setopt($ch, CURLOPT_HEADER, 0); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest); 
    
    // Do not worry about checking for SSL certs
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        
    $xmlResponse = curl_exec($ch);

    if(curl_errno( $ch ) == CURLE_OK) {
    	return $xmlResponse;
    }
    else {
      curl_close ($ch);
    }
	}
	
	//Parse XML response from eway and place them into an array
	function parseResponse($xmlResponse){
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser,  $xmlResponse, $xmlData, $index);
    $responseFields = array();
    foreach($xmlData as $data) {
      if (empty($data["value"])) {
          $data["value"] = 'No Response From Eway';
      }
	    if($data["level"] == 2) {
      	$responseFields[$data["tag"]] = $data["value"];
      }
    }
    return $responseFields;
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
    $out = "eWay Response Log\n";
    foreach ($this->response as $key => $value) {
      $out .= "\t$key = $value\n";
    }
    Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] $out");
   }
   
   protected function _logFields() {
     $out = "eWay Field Log\n";
     foreach ($this->fields as $key => $value) {
        $out .= "\t$key = $value\n";
     }
     Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] $out");
   }

   function dumpFields() {
 
      // Used for debugging, this function will output all the field/value pairs
      // that are currently defined in the instance of the class using the
      // add_field() function.
      
      echo "<h3>eway_class->dump_fields() Output:</h3>";
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
      // about eway's response.
      
      echo "<h3>eway_class->dump_response() Output:</h3>";
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

}
