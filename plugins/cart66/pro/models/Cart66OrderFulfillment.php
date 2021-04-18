<?php
class Cart66OrderFulfillment extends Cart66ModelAbstract {
  
  public function __construct($id=null) {
    $this->_tableName = Cart66Common::getTableName('order_fulfillment');
    parent::__construct($id);
  }
  
  public function save() {
    $errors = $this->validate();
    
    if(count($errors) == 0) {
      $fulfillmentSave = parent::save();
    }
    if(count($errors)) {
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] " . get_class($this) . " save errors: " . print_r($errors, true));
      $this->setErrors($errors);
      $errors = print_r($errors, true);
      throw new Cart66Exception('Order fulfillment save failed: ' . $errors, 66303);
    }
    return $fulfillmentSave;
  }
  
  public function validate() {
    $errors = array();
    
    if(empty($this->name)) {
      $errors['name'] = __('A name is required for order fulfillment', 'cart66');
    }
    
    if(!Cart66Common::isValidEmail($this->email)) {
      $errors['email'] = __('Please enter a valid email address', 'cart66');
    }
    
    if(empty($this->email)) {
      $errors['email'] = __('Email is required for order fulfillment', 'cart66');
    }
    
    return $errors;
  }
  
  public function productNames() {
    $product = new Cart66Product();
    $ids = explode(',', $this->products);
    $selected = array();
    foreach($ids as $id) {
      $product->load($id);
      $selected[] = array('id' => $id, 'name' => $product->name);
    }
    return $selected;
  }
  
  public function checkFulfillmentSettings($orderId) {
    $order = new Cart66Order($orderId);
    $data = array();
    foreach($order->getItems() as $item) {
      $data[] = $item->product_id;
    }
    
    $orderFulfillment = new Cart66OrderFulfillment();
    $orderF = $orderFulfillment->getModels();
    $notify = new Cart66AdvancedNotifications($orderId);
    foreach($orderF as $of) {
      $products = array_filter(explode(',', $of->products));
      if(array_intersect($data, $products) || empty($products)) {
        Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] THEY INTERSECT!");
        $notify->sendOrderFulfillmentEmails($of->id);
      }
    }
    
  }
}