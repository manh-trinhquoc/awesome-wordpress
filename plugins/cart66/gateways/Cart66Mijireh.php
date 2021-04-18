<?php 
require_once(CART66_PATH . "/models/Pest.php");
require_once(CART66_PATH . "/models/PestJSON.php");

class Cart66Mijireh extends Cart66GatewayAbstract {
  
  var $response = array();
  
  public function __construct() {
    parent::__construct();
    if(!Cart66Setting::getValue('mijireh_access_key')) {
      throw new Cart66Exception('Invalid Mijireh Configuration', 66512);
    }
  }
  
  public function getCreditCardTypes() {
    return array();
  }
  
  public function initCheckout($amount) {
    $cart = Cart66Session::get('Cart66Cart');
    $tax = $this->getTaxAmount();
    
    $order = array(
      'return_url' => Cart66Common::appendWurlQueryString('task=mijireh_notification'),
      'tax' => $tax,
      'shipping' => $cart->getShippingCost(),
      'discount' => $cart->getDiscountAmount(),
      'subtotal' => $cart->getSubTotal(),
      'total' => number_format($cart->getGrandTotal() + $tax, 2, '.', ''),
      'items' => array()
    );
    
    // Prepare the shipping address if it is available
    if(strlen($this->_shipping['address']) > 3) {
      $order['shipping_address'] = array(
        'first_name'     => $this->_shipping['firstName'],
        'last_name'      => $this->_shipping['lastName'],
        'street'         => $this->_shipping['address'],
        'apt_suite'      => $this->_shipping['address2'],
        'city'           => $this->_shipping['city'],
        'state_province' => $this->_shipping['state'],
        'zip_code'       => $this->_shipping['zip'],
        'country'        => $this->_shipping['country'],
        'phone'          => $this->_payment['phone']
      );
    }
    
    // Add shipping method and promotion code as meta_data
    $order['meta_data'] = array(
      'shipping_method' => Cart66Session::get('Cart66Cart')->getShippingMethodName(),
      'coupon' => Cart66Common::getPromoMessage(),
      'custom-field' => $this->_payment['custom-field'],
    );
    
    // Add logged in users id to the meta_data for membership product upgrades/extensions
    $account_id = Cart66Common::isLoggedIn();
    if($account_id) {
      $order['meta_data']['account_id'] = $account_id;
    }
    
    // Add coupon code as meta_data
    foreach($cart->getItems() as $key => $item) {
      $sku = $item->getItemNumber();
      $order_item_data = array(
        'sku' => $sku,
        'name' => $item->getFullDisplayName(),
        'price' => $item->getProductPrice(),
        'quantity' => $item->getQuantity()
      );
      
      if($custom_desc = $item->getCustomFieldDesc()) {
        $order_item_data['name'] .= "\n" . $custom_desc;
      }
      
      if($custom_info = $item->getCustomFieldInfo()) {
        $order_item_data['name'] .= "\n" . $custom_info;
      }
      
      $order['items'][$key] = $order_item_data;
      
      $option_info = trim($item->getOptionInfo());
      if(!empty($option_info)) {
        $order['meta_data']['options_' . $sku] = $option_info;
      }
      
      if($item->hasAttachedForms()) {
        $form_ids = $item->getFormEntryIds();
        if(is_array($form_ids) && count($form_ids)) {
          $form_ids = implode(',', $form_ids);
          $order['meta_data'][$key]['gforms_' . $sku] = $form_ids;
        }
      }
    }
    
    // DBG
    /*
    echo "<pre>";
    print_r($order);
    echo "</pre>";
    die();
    */
    
    try {
      //Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Sending Order To Mijireh" . print_r($order, true));
      $access_key = Cart66Setting::getValue('mijireh_access_key');
      $rest = new PestJSON(MIJIREH_CHECKOUT);
      $rest->setupAuth($access_key, '');
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Sending Order To Mijireh: " . print_r($order, true));
      $result = $rest->post('/api/1/orders', $order);
      wp_redirect($result['checkout_url']);
      //wp_redirect(MIJIREH_CHECKOUT .  '/checkout/' . $result['order_number']);
      exit;
    }
    catch(Pest_Unauthorized $e) {
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] REST Request Failed because it was unauthorized: " . $e->getMessage());
      $this->response['error_message'] = __("Your Mijireh Access key is invalid, please check your access settings and try again","cart66");
      $this->response['error_code'] = 1;
      if(strlen($this->_shipping['address']) < 3) {
        $gatewayResponse = $this->getTransactionResponseDescription();
        $exception = Cart66Exception::exceptionMessages(66500, __('Your order could not be processed for the following reasons:', 'cart66'), array('error_code' => 'Error: ' . $gatewayResponse['errorcode'], strtolower($gatewayResponse['errormessage'])));
        echo Cart66Common::getView('views/error-messages.php', $exception);
      }
    }
    catch(Exception $e) {
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] REST Request Failed: " . $e->getMessage());
    }
    
  }
  
  public function doSale() {
    return false;
  }
  
  public function getTransactionResponseDescription() {
     $description['errormessage'] = $this->response['error_message'];
     $description['errorcode'] = $this->response['error_code'];
     return $description;
   }
  
  public function setPayment($p) {
    $this->_payment['phone'] = isset($p['phone']) ? $p['phone'] : '';
    $this->_payment['email'] = isset($p['email']) ? $p['email'] : '';
    $this->_payment['custom-field'] = isset($p['custom-field']) ? $p['custom-field'] : '';
    
    // For subscription accounts
    if(isset($p['password'])) {
      if($p['password'] != $p['password2']) {
        $this->_errors['Password'] = __('Passwords do not match', 'cart66');
        $this->_jqErrors[] = 'payment-password';
      }
    }
  }
  
  public function saveMijirehOrder($order_number) {
    global $wpdb;
    
    // Make sure the order is not already in the database
    $orders_table = Cart66Common::getTableName('orders');
    $sql = "select id from $orders_table where trans_id = %s";
    $sql = $wpdb->prepare($sql, $order_number);
    $order_id = $wpdb->get_var($sql);
    
    if(!$order_id) {
      // Save the order
      $order = new Cart66Order();
      $cloud_order = $this->pullOrder($order_number);
      $order_data = $this->buildOrderDataArray($cloud_order);
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Order data: " . print_r($order_data, true));
      $order_id = $order->rawSave($order_data);

      // Save the order items
      $order_items_table = Cart66Common::getTableName('order_items');
      foreach($cloud_order['items'] as $key => $item) {
        $product = new Cart66Product();
        $product->loadByItemNumber($item['sku']);
        $data = array(
          'order_id' => $order_id,
          'product_id' => $product->id,
          'product_price' => $item['price'],
          'item_number' => $item['sku'],
          'description' => $item['name'],
          'quantity' => $item['quantity'],
          'duid' => md5($order_id . $item['sku'])
        );
        
        // Look for gravity forms data
        if(isset($cloud_order['meta_data'][$key]['gforms_' . $item['sku']])){
          $data['form_entry_ids'] = $cloud_order['meta_data'][$key]['gforms_' . $item['sku']];
        }
        $fIds = array();
        if(isset($data['form_entry_ids'])) {
          $fIds = explode(',', $data['form_entry_ids']);
          if(is_array($fIds) && count($fIds)) {
            foreach($fIds as $entryId) {
              if(class_exists('RGFormsModel')) {
                if($lead = RGFormsModel::get_lead($entryId)) {
                  $lead['status'] = 'active';
                  RGFormsModel::update_lead($lead);
                }
              }
            }
          }
        }
        
        Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Trying to save this order item:" . print_r($data, true));
        $wpdb->insert($order_items_table, $data);
        $order_item_id = $wpdb->insert_id;
        
        // Decrement inventory after sale
        if(Cart66Setting::getValue('track_inventory') == 1) {
          $option_info = '';
          if(isset($cloud_order['meta_data']['options_' . $item['sku']])) {
            $option_info = $cloud_order['meta_data']['options_' . $item['sku']];
          }
          Cart66Product::decrementInventory($data['product_id'], $option_info, $data['quantity']);
        }
        
        // Look for membership product upgrades/extensions
        if(isset($cloud_order['meta_data']['account_id']) && is_numeric($cloud_order['meta_data']['account_id'])) {
          $order->load($order_id);
          $account_id = $cloud_order['meta_data']['account_id'];
          if($mp = $order->getMembershipProduct()) {
            $account = new Cart66Account();
            $account->load($account_id);
            $account->attachMembershipProduct($mp, $account->firstName, $account->lastName);
            $order->account_id = $account->id;
            $order->save();
          }
        }
        
      }
      
      //update the number of redemptions for the promotion code.
      if(Cart66Session::get('Cart66Promotion')) {
        Cart66Session::get('Cart66Promotion')->updateRedemptions();
      }
      
      // Send email receipts
      if(CART66_PRO && CART66_EMAILS && Cart66Setting::getValue('enable_advanced_notifications') == 1) {
        $notify = new Cart66AdvancedNotifications($order_id);
        $notify->sendAdvancedEmailReceipts();
      }
      elseif(CART66_EMAILS) {
        $notify = new Cart66Notifications($order_id);
        $notify->sendEmailReceipts();
      }
      //Cart66Common::sendEmailReceipts($order_id);
    }
    
    // Redirect to receipt page
    $this->goToReceipt($order_id);
  }
  
  /**
   * Redirect buyer to receipt page for the given order id
   * 
   * @param int The id in the orders table
   */
  public function goToReceipt($order_id) {
    $order = new Cart66Order($order_id);
    $receipt = Cart66Common::getPageLink('store/receipt');
    $vars = strpos($receipt, '?') ? '&' : '?';
    $vars .= "ouid=" . $order->ouid;
    
    // Look for newsletter options
    if(Cart66Setting::getValue('constantcontact_list_ids') || Cart66Setting::getValue('mailchimp_list_ids')) {
      $vars .= '&newsletter=1';
    }
    
    $receipt .= $vars;
    Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Redirecting to: $receipt");
    wp_redirect($receipt);
    exit;
  }
  
  /**
   * Return an array of data matching the rows in the Cart66 orders table for the given order.
   * 
   * @param array The order as retrieved from mijireh
   * @return array
   */
  public function buildOrderDataArray($cloud_order) {
    $statusOptions = Cart66Common::getOrderStatusOptions();
    $status = $statusOptions[0];
    
    $order_token = $cloud_order['order_number'] . $cloud_order['email'];
    $ouid = md5($order_token);
    
    $order_info = array(
      'trans_id' => $cloud_order['order_number'],
      'authorization' => $cloud_order['authorization'],
      'shipping' => $cloud_order['shipping'],
      'shipping_method' => $cloud_order['meta_data']['shipping_method'],
      'subtotal' => $cloud_order['subtotal'],
      'discount_amount' => $cloud_order['discount'],
      'tax' => $cloud_order['tax'],
      'total' => $cloud_order['total'],
      'status' => $status,
      'email' => $cloud_order['email'],
      'bill_first_name' => $cloud_order['first_name'],
      'bill_last_name' => $cloud_order['last_name'],
      'ordered_on' => $cloud_order['order_date'],
      'ouid' => $ouid,
      'coupon' => $cloud_order['meta_data']['coupon'],
      'custom_field' => $cloud_order['meta_data']['custom-field'],
    );
    
    if(isset($cloud_order['shipping_address']) && is_array($cloud_order['shipping_address'])) {
      $address = $cloud_order['shipping_address'];
      $order_info['ship_first_name'] = $address['first_name'];
      $order_info['ship_last_name'] = $address['last_name'];
      $order_info['ship_address'] = $address['street'];
      $order_info['ship_address2'] = $address['apt_suite'];
      $order_info['ship_city'] = $address['city'];
      $order_info['ship_state'] = $address['state_province'];
      $order_info['ship_zip'] = $address['zip_code'];
      $order_info['ship_country'] = Cart66Common::getCountryName($address['country']);
      $order_info['phone'] = $address['phone'];
    }
    
    return $order_info;
  }
  
  public function pullOrder($order_number) {
    $access_key = Cart66Setting::getValue('mijireh_access_key');
    $rest = new PestJSON(MIJIREH_CHECKOUT);
    $rest->setupAuth($access_key, '');
    $order_data = $rest->get('/api/1/orders/' . $order_number);
    Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] GETTING MIJIREH ORDER: " . print_r($order_data, true));
    return $order_data;
  }
  
}