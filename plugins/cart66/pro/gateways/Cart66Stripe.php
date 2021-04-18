<?php
class Cart66Stripe extends Cart66GatewayAbstract {
  
  var $response = array();
  var $gateway_url;
  var $params;
  var $response_string;
  protected $_apiKey;
   
  public function __construct() {
    parent::__construct();
    
    // initialize error arrays
    $this->_errors = array();
    $this->_jqErrors = array();
    
    $this->_apiKey = Cart66Setting::getValue('stripe_api_key');
    
    
    $mode = 'LIVE';
    if(Cart66Setting::getValue('stripe_test')) {
      $mode = 'TEST';
    }
    if($mode == 'TEST') {
      $this->_apiKey = Cart66Setting::getValue('stripe_test_api_key');
    }
    
    if($this->_apiKey == null) {
      throw new Cart66Exception('Invalid Stripe Configuration', 66505);
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
  
  public function initCheckout($total) {
    $p = $this->getPayment();
    $b = $this->getBilling();
    Cart66Common::log("Payment info for checkout: " . print_r($p, true));
    
    // Load gateway url from Cart66 settings
    $gatewayUrl = Cart66Setting::getValue('auth_url');
    $this->gateway_url = $gatewayUrl;
    
    $b['address2'] = ($b['address2'] == "") ? null : $b['address2'];
    
    $cardData = array(
      'number' => $p['cardNumber'], 
      'exp_month' => $p['cardExpirationMonth'], 
      'exp_year' => $p['cardExpirationYear'], 
      'cvc' => $p['securityId'], 
      'name' => $b['firstName'] . ' ' . $b['lastName'], 
      'address_line1' => $b['address'],
      'address_line2' => $b['address2'], 
      'address_zip' => $b['zip'], 
      'address_state' => $b['state'], 
      'address_country' => $b['country']
    );

    $this->params = array(
      'card' => $cardData, 
      'amount' => number_format($total, 2, '', ''),
      'currency' => (Cart66Setting::getValue('stripe_currency_code')) ? Cart66Setting::getValue('stripe_currency_code') : strtolower(Cart66Setting::getValue('currency_code')),
      'description' => ''
    );
    
  }
  
  function doSale() {
    
    $sale = false;
    
    if($this->params['amount'] > 0) {
      // Execute the HTTPS post via CURL
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->gateway_url);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
      curl_setopt($ch, CURLOPT_TIMEOUT, 80);
      curl_setopt($ch, CURLOPT_USERPWD, $this->_apiKey);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, self::encodeParams($this->params));

      // Do not worry about checking for SSL certs
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

      $this->response_string = json_decode(curl_exec($ch));
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] response: " . print_r($this->response_string, true));
      //$errno = curl_errno($ch);


      curl_close ($ch);
      
      if(isset($this->response_string->error)) {
        $this->response['Response Reason Text'] = $this->response_string->error->message;
        $this->response['Response Reason Code'] = $this->response_string->error->type;
      }
      else {
        if(isset($this->response_string->paid) && $this->response_string->paid == 1) {
          $sale = $this->response_string->id;
        }
        else {
          $this->response['Response Reason Text'] = 'No Transaction ID Provided';
        }
      }
      
    }
    else {
      // Process free orders without sending to the Stripe gateway
      $this->response_string->id = 'MT-' . Cart66Common::getRandString();
      $sale = $this->response_string->id;
    }
    
    return $sale;
  }
  
  function getResponseReasonText() {
    return $this->response['Response Reason Text'];
  }
  
  public function getTransactionResponseDescription() {
    $description['errormessage'] = $this->getResponseReasonText();
    $description['errorcode'] = $this->response['Response Reason Code'];
    return $description;
  }
  
  public static function encodeParams($params) {
    return http_build_query($params, null, '&');
  }
  
}