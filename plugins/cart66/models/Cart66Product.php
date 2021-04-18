<?php
class Cart66Product extends Cart66ModelAbstract {
  
  protected $_creditAmount;
  
  public function __construct($id=null) {
    $this->_tableName = Cart66Common::getTableName('products');
    parent::__construct($id);
    $this->_creditAmount = 0;
  }
  
  public function getOptions() {
    $opt1 = $this->_buildOptionList(1);
    $opt2 = $this->_buildOptionList(2);
    return $opt1 . $opt2;
  }
  
  public function loadByDuid($duid) {
    $itemsTable = Cart66Common::getTableName('order_items');
    $sql = "SELECT product_id from $itemsTable where duid = '$duid'";
    $id = $this->_db->get_var($sql);
    $this->load($id);
    return $this->id;
  }
  
  public function loadItemIdByDuid($duid) {
    $itemsTable = Cart66Common::getTableName('order_items');
    $sql = "SELECT id from $itemsTable where duid = '$duid'";
    $id = $this->_db->get_var($sql);
    return $id;
  }
  
  public function loadByItemNumber($itemNumber) {
    $itemNumber = esc_sql($itemNumber);
    $sql = "SELECT id from $this->_tableName where item_number = '$itemNumber'";
    $id = $this->_db->get_var($sql);
    $this->load($id);
    return $this->id;
  }

  public function loadFromShortcode($attrs) {
    if(is_array($attrs)) {
      if(isset($attrs['item'])) {
        $this->loadByItemNumber($attrs['item']);
      }
      else {
        $id = $attrs['id'];
        $this->load($id);
      }
    }
    return $this->id;
  }

  public function countDownloadsForDuid($duid, $order_item_id) {
    $downloadsTable = Cart66Common::getTableName('downloads');
    $sql = "SELECT count(*) from $downloadsTable where duid='$duid' AND order_item_id='$order_item_id'";
    return $this->_db->get_var($sql);
  }
  
  public function resetDownloadsForDuid($duid, $order_item_id) {
    $downloadsTable = Cart66Common::getTableName('downloads');
    $sql = "DELETE from $downloadsTable where duid='$duid' AND order_item_id='$order_item_id'";
    $this->_db->query($sql);
  }
  
  /**
   * Return the quantity of inventory in stock for the product with the given id and variation description.
   * 
   * The variation descriptins is a ~ separated string of options. The price info may be in the variation string but
   * will be stripped out before calculating the iKey.
   * 
   * @param int $id
   * @param string $variation
   * @return int Quantity of inventory in stock
   */
  public static function checkInventoryLevelForProduct($id, $variation='') {
    // Build varation ikey string component
    if(!empty($variation)) {
      $variation = self::scrubVaritationsForIkey($variation);
    }
    
    $p = new Cart66Product($id);
    $ikey = $p->getInventoryKey($variation);
    $count = $p->getInventoryCount($ikey);
    //Cart66Common::log("Check Inventory Level For Product: $ikey = $count");
    return $count;
  }
  
  public static function decrementInventory($id, $variation='', $qty=1) {
    Cart66Common::log("Decrementing Inventory: line " . __LINE__);
    // Build varation ikey string component
    if(!empty($variation)) {
      $variation = self::scrubVaritationsForIkey(str_replace(', ', '~', $variation));
    }
    
    $p = new Cart66Product($id);
    $is_gravity_form = false;
    $valid_options = array();
    if($p->isGravityProduct()) {
      $valid_options = Cart66GravityReader::getFormValuesArray($p->gravity_form_id);
      $is_gravity_form = true;
    }
    else {
      if(strlen($p->options_1) > 1) {
        $valid_options[] = explode(',', str_replace(' ', '', $p->options_1));
      }
      if(strlen($p->options_2) > 1) {
        $valid_options[] = explode(',', str_replace(' ', '', $p->options_2));
      }
    }
    $newVariation = '';
    $options = explode(',', $variation);
    foreach($options as $option) {
      if($p->validate_option($valid_options, $option, $is_gravity_form)) {
        $newVariation .= $option;
      }
    }
    $ikey = $p->getInventoryKey($newVariation);
    $count = $p->getInventoryCount($ikey);
    $newCount = $count - $qty;
    if($newCount < 0) {
      $newCount = 0;
    }
    $p->setInventoryLevel($ikey, $newCount);
  }
  
  public static function increaseInventory($id, $variation='', $qty=1) {
    Cart66Common::log("Increasing Inventory: line " . __LINE__);
    // Build varation ikey string component
    if(!empty($variation)) {
      $variation = self::scrubVaritationsForIkey(str_replace(', ', '~', $variation));
    }
    
    $p = new Cart66Product($id);
    $is_gravity_form = false;
    $valid_options = array();
    if($p->isGravityProduct()) {
      $valid_options = Cart66GravityReader::getFormValuesArray($p->gravity_form_id);
      $is_gravity_form = true;
    }
    else {
      if(strlen($p->options_1) > 1) {
        $valid_options[] = explode(',', str_replace(' ', '', $p->options_1));
      }
      if(strlen($p->options_2) > 1) {
        $valid_options[] = explode(',', str_replace(' ', '', $p->options_2));
      }
    }
    $newVariation = '';
    $options = explode(',', $variation);
    foreach($options as $option) {
      if($p->validate_option($valid_options, $option, $is_gravity_form)) {
        $newVariation .= $option;
      }
    }
    $ikey = $p->getInventoryKey($newVariation);
    $count = $p->getInventoryCount($ikey);
    $newCount = $count + $qty;
    $p->setInventoryLevel($ikey, $newCount);
  }
  
  protected function validate_option(&$valid_options, $choice, $is_gravity_form=false) {
    $found = false;
    
    foreach($valid_options as $key => $option_group) {
      foreach($option_group as $option) {
        $choice = preg_replace('[\W]', '', $choice);
        $option = preg_replace('[\W]', '', self::scrubVaritationsForIkey($option));
        
        Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Validating option :: $choice == $option");
        if($choice == $option) {
          $found = true;
          Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Removing option group: $key");

          // Gravity forms have checkbox options which allow multiple options from the same group
          if(!$is_gravity_form) {
            unset($valid_options[$key]);
          }
          
          return $found;
        }
      }
    }
    
    return $found;
  }
  
  public static function scrubVaritationsForIkey($variation='') {
    if(!empty($variation)) {
      $variations = explode('~', $variation);
      $options = array();
      foreach($variations as $opt) {
        if(strpos($opt, '$')) {
          $options[] = trim(preg_replace('/\s*([+-])[^$]*\$.*$/', '', $opt));
        }
        else {
          $options[] = trim(preg_replace('/\s*([+-])[^$]*\\'. CART66_CURRENCY_SYMBOL_TEXT . '.*$/', '', $opt));
        }
      }
      $variation = strtolower(str_replace('~', ',', str_replace(' ', '', implode(',', $options))));
    }
    Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] variation: $variation");
    return $variation;
  }
  
  public static function confirmInventory($id, $variation='', $desiredQty=1) {
    Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Confirming Inventory:\n$id | $variation | $desiredQty");
    $ok = true;
    $setting = new Cart66Setting();
    $trackInventory = Cart66Setting::getValue('track_inventory');
    if($trackInventory == 1) {
      $p = new Cart66Product($id);
      $variation = self::scrubVaritationsForIkey($variation);
      $ikey = $p->getInventoryKey($variation);
      if($p->isInventoryTracked($ikey)) {
        $qty = self::checkInventoryLevelForProduct($id, $variation);
        if($qty < $desiredQty) {
          $ok = false;
        }
      }
      else {
        Cart66Common::log("Inventory not tracked: $ikey");
      }
    }
    return $ok;
  }
  
  /**
   * Return an array of option names having stripped off any price variations
   * 
   * @param int $optNumber The option group number
   * @return array
   */
  public function getOptionNames($optNumber=1) {
    $names = array();
    $optionName = "options_$optNumber";
    $opts = explode(',', $this->$optionName);
    foreach($opts as $opt) {
      $name = $opt;
      if(strpos($opt, '$')) {
        $name = trim(preg_replace('/\s*([+-])[^$]*\$.*$/', '', $opt));
      }
      else {
        $name = trim(preg_replace('/\s*([+-])[^$]*\\'. CART66_CURRENCY_SYMBOL_TEXT . '.*$/', '', $opt));
      }
      
      if(!empty($name)) {
        $names[] = $name;
      }
    }
    return $names;
  }
  
  public function getAllOptionCombinations() {
    $combos = array();
    $opt1 = $this->getOptionNames(1);
    $opt2 = $this->getOptionNames(2);
    if(count($opt1)) {
      foreach($opt1 as $first) {
        if(count($opt2)) {
          foreach($opt2 as $second) {
            $combos[] = "$first, $second";
          }
        }
        else {
          $combos[] = "$first";
        }
      }
    }
    return $combos;
  }
  
  /**
   * Return the primary key used in the ikey table. 
   * This is the product name + variation name without price difference information in all lowercase with no spaces.
   * Only letters and numbers are used.
   * 
   * @param string The variation name without the price difference
   * @return string
   */
  public function getInventoryKey($variationName='') {
    $key = strtolower($this->id . $this->name . $variationName);
    $key = str_replace(' ', '', $key);
    $key = preg_replace('/\W/', '', $key);
    return $key;
  }
  
  public function insertInventoryData() {
    $keys = array();
    $combos = $this->getAllOptionCombinations();
    if(count($combos)) {
      foreach($combos as $c) {
        $key = $this->getInventoryKey($c);
        $keys[] = $key;
      }
    }
    else {
      // There are no product variations
      $key = $this->getInventoryKey();
      $keys[] = $key;
    }
    
    foreach($keys as $key) {
      $inventory = Cart66Common::getTableName('inventory');
      
      // Only insert new rows
      $sql = "SELECT ikey from $inventory where ikey = %s";
      $stmt = $this->_db->prepare($sql, $key);
      $foundKey = $this->_db->get_var($stmt);
      if(!$foundKey) {
        $sql = "INSERT into $inventory (ikey, track, product_id, quantity) VALUES (%s,%d,%d,%d)";
        $stmt = $this->_db->prepare($sql, $key, 0, $this->id, 0);
        $this->_db->query($stmt);
      }
      
    }
    
    // Delete obsolete inventory rows
    $keyList = implode("','", $keys);
    $sql = "DELETE from $inventory where product_id=$this->id and ikey not in ('$keyList')";
    $this->_db->query($sql);
  }
  
  public function updateInventoryFromPost($request) {
    global $wpdb;
    $inventory = Cart66Common::getTableName('inventory');
    foreach($request as $key => $value) {
      if (substr($key, 0, 4) == "qty_") {
        $ikey = substr($key, 4);
        $wpdb->query("UPDATE $inventory SET quantity='$value' WHERE ikey='$ikey'");
      }
      if (substr($key, 0, 6) == "track_") {
        $ikey = substr($key, 6);
        $wpdb->query("UPDATE $inventory SET track='$value' WHERE ikey='$ikey'");
      }
    }
  }
  
  public function updateInventoryFromPost2($ikey) {
    $inventory = Cart66Common::getTableName('inventory');
    $track = Cart66Common::postVal("track_$ikey");
    $qty = Cart66Common::postVal("qty_$ikey");
    $sql = "UPDATE $inventory set track=%d, quantity=%d where ikey=%s";
    $sql = $this->_db->prepare($sql, $track, $qty, $ikey);
    $this->_db->query($sql);
  }
  
  public function setInventoryLevel($ikey, $qty) {
    $inventory = Cart66Common::getTableName('inventory');
    $sql = "UPDATE $inventory set quantity=%d where ikey=%s";
    $sql = $this->_db->prepare($sql, $qty, $ikey);
    $this->_db->query($sql);
  }
  
  public function getInventoryCount($ikey) {
    $inventory = Cart66Common::getTableName('inventory');
    $sql = "SELECT quantity from $inventory where ikey=%s";
    $sql = $this->_db->prepare($sql, $ikey);
    $count = $this->_db->get_var($sql);
    return $count;
  }
  
  public function getInventoryNamesAndCounts() {
    $counts = array();
    $ikeyList = $this->getInventoryKeyList();
    foreach($ikeyList as $comboName => $ikey) {
      if($this->isInventoryTracked($ikey)) {
        $counts[$comboName] = $this->getInventoryCount($ikey);
      }
      else {
        $counts[$comboName] = 'in stock';
      }
    }
    return $counts;
  }
  
  /**
   * Return an array of all inventory keys for this product
   */
  public function getInventoryKeyList() {
    $ikeyList = array();
    $combos = $this->getAllOptionCombinations();
    if(count($combos)) {
      foreach($combos as $c) {
        $k = $this->getInventoryKey($c);
        $n = $this->name . ': ' . $c;
        $ikeyList[$n] = $k;
      }
    }
    else {
      $ikeyList[$this->name] = $this->getInventoryKey();
    }
    
    return $ikeyList;
  }
  
  /**
   * Return true if this product is available in any variation for purchase.
   * 
   * If inventory is not tracked or if any variations of the product are in stock, true is returned.
   * Otherwise, false is returned.
   * 
   * @return boolean
   */
  public function isAvailable() {
    $isAvailable = false;
    $inventory = Cart66Common::getTableName('inventory');
    $sql = "SELECT count(*) from $inventory where product_id=$this->id";
    $found = $this->_db->get_var($sql);
    if($found) {
      $sql = "SELECT sum(quantity) from $inventory where track=1 and product_id=$this->id";
      $qty = $this->_db->get_var($sql);
      if(is_numeric($qty) && $qty > 0) {
        $isAvailable = true;
      }
      else {
        $sql = "SELECT count(*) as c from $inventory where track=0 and product_id=$this->id";
        $notTracked = $this->_db->get_var($sql);
        if($notTracked > 0) {
          $isAvailable = true;
        }
      }
    }
    else {
      // Inventory table hasn't been refreshed so ignore inventory tracking for this product
      $isAvailable = true;
    }
    return $isAvailable;
  }
  
  public function isInventoryTracked($ikey) {
    $inventory = Cart66Common::getTableName('inventory');
    $sql = "SELECT track from $inventory where ikey=%s";
    $sql = $this->_db->prepare($sql, $ikey);
    $track = $this->_db->get_var($sql);
    //Cart66Common::log("Is inventory tracked query: $sql");
    $isTracked = ($track == 1) ? true : false;
    return $isTracked;
  }
  
  public function pruneInventory(array $ikeyList) {
    $inventory = Cart66Common::getTableName('inventory');
    $list = "'" . implode("','", $ikeyList) . "'";
    $sql = "DELETE from $inventory where ikey not in ($list)";
    $this->_db->query($sql);
    //Cart66Common::log("Prune Inventory: $sql");
  }
  
  private function _buildOptionList($optNumber) {
    $select = '';
    $optionName = "options_$optNumber";
    if(strlen($this->$optionName) > 1) {
      $select = "\n<select name=\"options_$optNumber\" id=\"options_$optNumber\" class=\"cart66Options options_$optNumber\">";
      $opts = explode(',', $this->$optionName);
      foreach($opts as $opt) {
        $opt = str_replace('+$', '+ $', $opt);
        $opt = trim($opt);
        $optDisplay = str_replace('$', CART66_CURRENCY_SYMBOL, $opt);
        $select .= "\n\t<option value=\"" . htmlentities($opt, ENT_COMPAT, 'UTF-8') . "\">$optDisplay</option>";
      }
      $select .= "\n</select>";
    }
    return $select;
  }

  public function isDigital() {
    $isDigital = false;
    if(strlen($this->downloadPath) > 2 || strlen($this->s3File) > 2) {
      $isDigital = true;
    }
    return $isDigital;
  }
  
  public function isShipped() {
    $isShipped = false;
    if($this->shipped > 0) {
      $isShipped = true;
    }
    return $isShipped;
  }

  /**
   * Return the shipping rate for this product for the given shipping method
   */
  public function getShippingPrice($methodId) {
    $methodId = (isset($methodId) && is_numeric($methodId)) ? $methodId : 0;
    // Look to see if there is a specific setting for this product and the given shipping method
    $ratesTable = Cart66Common::getTableName('shipping_rates');
    $sql = "SELECT shipping_rate from $ratesTable where product_id = " . $this->id . " and shipping_method_id = $methodId";
    $rate = $this->_db->get_var($sql);
    if($rate === NULL) {
      // If no specific rate is set, return the default rate for the given shipping method
      $shippingMethods = Cart66Common::getTableName('shipping_methods');
      $sql = "SELECT default_rate from $shippingMethods where id=$methodId";
      $rate = $this->_db->get_var($sql);
    }
    return $rate;
  }
  
  public function getBundleShippingPrice($methodId) {
    $methodId = (isset($methodId) && is_numeric($methodId)) ? $methodId : 0;
    $ratesTable = Cart66Common::getTableName('shipping_rates');
    $shippingMethods = Cart66Common::getTableName('shipping_methods');
    
    // Look to see if there is a specific bundle rate for this product and the given shipping method
    $sql = "SELECT shipping_bundle_rate from $ratesTable where product_id = " . $this->id . " and shipping_method_id = $methodId";
    $rate = $this->_db->get_var($sql);
    if($rate === NULL) {
      // If no specific rate is set, return the default bundle rate for the given shipping method
      $sql = "SELECT default_bundle_rate from $shippingMethods where id=$methodId";
      $rate = $this->_db->get_var($sql);
      return $rate;
    }
    return $rate;
  }
  
  public function isMembershipProduct() {
    $isMembershipProduct = false;
    if($this->isMembershipProduct == 1) {
      $isMembershipProduct = true;
    }
    return $isMembershipProduct;
  }
  
  public function isSubscription() {
    $isSub = false;
    if(CART66_PRO) {
      if($this->isSpreedlySubscription() || $this->isPayPalSubscription()) {
        $isSub = true;
      }
    }
    return $isSub;
  }
  
  public function isSpreedlySubscription() {
    $isSub = false;
    if(CART66_PRO && (is_numeric($this->spreedlySubscriptionId) && $this->spreedlySubscriptionId > 0)) {
      $isSub = true;
    }
    return $isSub;
  }
  
  public function isPayPalSubscription() {
    $isPayPalSubscription = false;
    if(CART66_PRO && $this->isPaypalSubscription == 1) {
      $isPayPalSubscription = true;
    }
    return $isPayPalSubscription;
  }
  
  public static function getProductIdByGravityFormId($id) {
    global $wpdb;
    $products = Cart66Common::getTableName('products');
    $sql = "SELECT id from $products where gravity_form_id = %d";
    $query = $wpdb->prepare($sql, $id);
    $productId = $wpdb->get_var($query);
    return $productId;
  }
  
  public static function getNonSubscriptionProducts($where=null, $order=null, $limit=null) {
    global $wpdb;
    $subscriptions = array();
    $product = new Cart66Product();
    $products = $product->getModels($where, $order, $limit);
    foreach($products as $p) {
      if(!$p->isSubscription()) {
        $subscriptions[] = $p;
      }
    }
    return $subscriptions;
  }
  
  public static function getSubscriptionProducts($where=null, $order=null, $limit=null) {
    global $wpdb;
    $subscriptions = array();
    $product = new Cart66Product();
    $products = $product->getModels($where, $order, $limit);
    foreach($products as $p) {
      if($p->isSubscription()) {
        $subscriptions[] = $p;
      }
    }
    return $subscriptions;
  }
  
  public static function getSpreedlyProducts($where=null, $order=null, $limit=null) {
    global $wpdb;
    $subscriptions = array();
    $product = new Cart66Product();
    $where = $where == null ? "where spreedly_subscription_id > 0" : $where . " AND spreedly_subscription_id > 0";
    $products = $product->getModels($where, $order, $limit);
    foreach($products as $p) {
      if($p->isSpreedlySubscription()) {
        $subscriptions[] = $p;
      }
    }
    return $subscriptions;
  }
  
  public static function getMembershipProducts() {
    global $wpdb;
    $memberships = array();
    $product = new Cart66Product();
    $products = $product->getModels('where is_membership_product=1');
    foreach($products as $p) {
      if($p->isMembershipProduct()) {
        $memberships[] = $p;
      }
    }
    return $memberships;
  }
  
  /**
   * Return the pricing for PayPal or Spreedly subscription plan.
   * The PayPal pricing takes precedence over the Spreedly pricing, 
   * but they should both be the same. If the $showAll paramter is 
   * true then a detailed price summary of all attached subscriptions
   * is returned.
   * 
   * @return string
   */
  public function getRecurringPriceSummary() {
    $priceSummary = "No recurring pricing";
    $paypalPriceSummary = false;
    $spreedlyPriceSummary = false;
    
    if($this->isPayPalSubscription()) {
      if(class_exists('Cart66PayPalSubscription')) {
        $subscription = new Cart66PayPalSubscription($this->id);
        $priceSummary = $subscription->getPriceDescription();
      }
    }
    elseif($this->isSpreedlySubscription()) {
      if(class_exists('SpreedlySubscription')) {
        if($this->isSubscription()) {
          $subscription = new SpreedlySubscription();
          $subscription->load($this->spreedlySubscriptionId);
          $priceSummary = $subscription->getPriceDescription();
        }
      }
    }
    
    return $priceSummary;
  }
  
  /**
   * Return true if only one subscription is attached or if both attached subscriptions are 
   * for the same amount.
   * 
   * @return boolean
   */
  public function subscriptionMismatch() {
    $ok = false;
    if($this->isSpreedlySubscription() && $this->isPayPalSubscription()) {
      if(class_exists(SpreedlySubscription) && class_exists(Cart66PayPalSubscription)) {
        $spreedly = new SpreedlySubscription();
        $spreedly->load($this->spreedlySubscriptionId);
        $paypal = new Cart66PayPalSubscription($this->id);
        $paypalPrice = number_format($paypal->price, 2, '.', '');
        $paypalInterval = $paypal->billingInterval; 
        $paypalUnits = $paypal->billingIntervalUnit;
        $pp = $paypalPrice . '|' . $paypalInterval . '|' . $paypalUnits;
        
        $spreedlyPrice = number_format($spreedly->price, 2, '.', '');
        $spreedlyInterval = $spreedly->durationQuantity;
        $spreedlyUnits = $spreedly->durationUnits;
        $sp = $spreedlyPrice . '|' . $spreedlyInterval . '|' . $spreedlyUnits;
        
        
        $this->chargeLaterDurationQuantity . '&nbsp;' . $this->chargeLaterDurationUnits;
        Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Comparing: $pp <--> $sp" );
        if($pp != $sp) {
          $ok = true;
        }
      }
    }
    return $ok;
  }
  
  public function hasFreeTrial() {
    $hasFreeTrial = false;
    if($this->isSubscription()) {
      $subscription = new SpreedlySubscription();
      $subscription->load($this->spreedlySubscriptionId);
      $hasFreeTrial = $subscription->hasFreeTrial();
    }
    return $hasFreeTrial;
  }
  
  /**
   * Return the number of sales for the given month
   * 
   * @param int $month An integer between 1 and 12 inclusive
   * @param int $year The four digit year
   * @return int
   */
  public function getSalesForMonth($month, $year) {
    $orders = Cart66Common::getTableName('orders');
    $orderItems = Cart66Common::getTableName('order_items');
    $start = date('Y-m-d 00:00:00', strtotime($month . '/1/' . $year));
    $end = date('Y-m-d 00:00:00', strtotime($month . '/1/' . $year . ' +1 month'));
    $sql = "SELECT sum(oi.quantity) as num 
      from 
        $orders as o, 
        $orderItems as oi 
      where
        oi.product_id = %s and
        oi.order_id = o.id and
        o.ordered_on >= '$start' and 
        o.ordered_on < '$end' and
        o.status != 'checkout_pending'
      ";
    $query = $this->_db->prepare($sql, $this->id);
    $num = $this->_db->get_var($query);
    return $num;
  }
  
  public function getSalesTotal() {
    $orders = Cart66Common::getTableName('orders');
    $orderItems = Cart66Common::getTableName('order_items');
    $sql = "SELECT sum(oi.quantity) as num 
      from 
        $orders as o, 
        $orderItems as oi 
      where
        oi.product_id = %s and
        oi.order_id = o.id and
        o.status != 'checkout_pending'
      ";
    $query = $this->_db->prepare($sql, $this->id);
    $num = $this->_db->get_var($query);
    return $num;
  }
  
  public function getIncomeTotal() {
    $orders = Cart66Common::getTableName('orders');
    $orderItems = Cart66Common::getTableName('order_items');
    $sql = "SELECT sum(oi.product_price * oi.quantity) as num 
      from 
        $orders as o, 
        $orderItems as oi 
      where
        oi.product_id = %s and
        oi.order_id = o.id and
        o.status != 'checkout_pending'
      ";
    $query = $this->_db->prepare($sql, $this->id);
    $num = $this->_db->get_var($query);
    return $num;
  }
  
  public function getIncomeForMonth($month, $year) {
    $orders = Cart66Common::getTableName('orders');
    $orderItems = Cart66Common::getTableName('order_items');
    $start = date('Y-m-d 00:00:00', strtotime($month . '/1/' . $year));
    $end = date('Y-m-d 00:00:00', strtotime($month . '/1/' . $year . ' +1 month'));
    
    $sql = "SELECT sum(oi.product_price * oi.quantity) as total
      FROM
        $orders as o,
        $orderItems as oi
      WHERE
        oi.product_id = %s and
        oi.order_id = o.id and
        o.ordered_on >= '$start' and 
        o.ordered_on < '$end' and
        o.status != 'checkout_pending'
      ";
       
    $query = $this->_db->prepare($sql, $this->id);
    $total = $this->_db->get_var($query);
    return $total;
  }
  
  public function validate($override_nonce=false) {
    $errors = array();
    
    if(!$override_nonce && !wp_verify_nonce($_POST['cart66_product_nonce'], 'cart66_product_nonce')) {
      $errors['nonce'] = __("An unknown error occurred, please try again later","cart66");
    }
    else {
      // Verify that the item number is present
      if(empty($this->item_number)) {
        $errors['item_number'] = __("Item number is required","cart66");
      }
    
      if(empty($this->spreedlySubscriptionId))  {
        $this->spreedlySubscriptionId = 0;
      }
    
      // Verify that no other products have the same item number
      if(empty($errors)) {
        $sql = "SELECT count(*) from $this->_tableName where item_number = %s and id != %d";
        $sql = $this->_db->prepare($sql, $this->item_number, $this->id);
        $count = $this->_db->get_var($sql);
        if($count > 0) {
          $errors['item_number'] = __("The item number must be unique","cart66");
        }
      }
    
      // Verify that if the product has been saved and there is a download path that there is a file located at the path
      if(!empty($this->download_path)) {
        $dir = Cart66Setting::getValue('product_folder');
        if(!file_exists($dir . DIRECTORY_SEPARATOR . $this->download_path)) {
          $errors['download_file'] = __("There is no file available at the download path:","cart66") . " " . $this->download_path;
        }
      }
    }

    return $errors;
  }
  
  /**
   * Check the gravity form entry for the quantity field.
   * Return the quanity in the field, or 1 if no quantity can be found.
   * 
   * @return int
   * @access public
   */
  public function gravityCheckForEntryQuantity($gfEntry) {
    $qty = 1;
    $qtyId = $this->gravity_form_qty_id;
    if($qtyId > 0) {
      if(isset($gfEntry[$qtyId]) && is_numeric($gfEntry[$qtyId])) {
        $qty = $gfEntry[$qtyId];
        unset($gfEntry[$qtyId]);
      }
    }
    return $qty;
  }
  
  public function gravityGetVariationPrices($gfEntry) {
    $options = array();
    //Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Gravity Forms Entry:  " . print_r($gfEntry, true));
    foreach($gfEntry as $id => $value) {
      if($id != 'source_url') {
        $exp = '/[+-]\s*\\' . CART66_CURRENCY_SYMBOL_TEXT . '\d/';
        if(preg_match($exp, $value)) {
          $options[] = $value;
        }
        else {
          $exp = '/[+-]\s*\$\d/';
          if(preg_match($exp, $value)) {
            $options[] = $value;
          }
        }
      }
    }
    $options = implode('~', $options);
    return $options;
  }
  
  public function isGravityProduct() {
    $isGravity = false;
    if($this->gravity_form_id > 0) {
      $isGravity = true;
    }
    return $isGravity;
  }
  
  public function handleFileUpload() {
    // Check for file upload
    if(strlen($_FILES['product']['tmp_name']['upload']) > 2) {
      $dir = Cart66Setting::getValue('product_folder');
      if($dir) {
        $filename = preg_replace('/\s/', '_', $_FILES['product']['name']['upload']);
        $path = $dir . DIRECTORY_SEPARATOR . $filename;
        $src = $_FILES['product']['tmp_name']['upload'];
        if(move_uploaded_file($src, $path)) {
          $_POST['product']['download_path'] = $filename;
        }
        else {
          $this->addError('File Upload', __("Unable to upload file","cart66"));
          $msg = "Could not upload file from $src to $path\n". print_r($_FILES, true);
          throw new Cart66Exception($msg, 66101);
        }
      }
    }
  }
  
  /**
   * Return the price to charge at checkout.
   * For subscriptions this may also include the first recurring payment if the recurring start number is 0. 
   * This function will return the exact product price if:
   *  - The product is not a subscription product
   *  - The product is a subscription with a free trial period
   */
  public function getCheckoutPrice() {
    $price = $this->price;
    if($this->isSpreedlySubscription()) {
      if(!$this->hasFreeTrial()) {
        $subscription = new SpreedlySubscription();
        $subscription->load($this->spreedlySubscriptionId);
        $price += $subscription->price;
      }
      
      if(Cart66Common::isLoggedIn()) {
        $proRateAmount = Cart66Session::get('Cart66ProRateAmount');
        if(!$proRateAmount) {
          $proRateData = $this->getProRateInfo();
          $proRateAmount = $proRateData->amount;
        }
        $price = ($proRateAmount > $price) ? 0 : $price - $proRateAmount;
        Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Calculated ProRate price: " . $price);
      }
      
    }
    elseif($this->isPayPalSubscription()) {
      $price = $this->setupFee;
      $plan = $this->getPayPalSubscription();
      if($plan->startRecurringNumber == 0) {
        if($plan->offerTrial) {
          $price += $plan->trialPrice;
        }
        else {
          $price += $plan->price;
        }
      }
    }
    
    return $price;
  }
  
  /**
   * Return a description of the subscription rate such as $10 / 1 month
   * 
   * @return string
   */
  public function getSubscriptionPriceSummary() {
    $desc = '';
    if($this->isSpreedlySubscription()) {
      $subscription = new SpreedlySubscription();
      $subscription->load($this->spreedlySubscriptionId);
      $desc = $subscription->getPriceDescription();
    }
    return $desc;
  }
  
  public function getPriceDescription($priceDifference=0) {
    if($this->id > 0) {
      if($this->isSpreedlySubscription()) {
        $price = $this->price + $priceDifference;
        $priceDescription = "";
        if($price > 0) {
          $priceDescription = $price;
        }

        if($this->hasFreeTrial()) {
          if(empty($this->priceDescription)){
            $priceDescription = "Free Trial";
          }
          else{
            $priceDescription = $this->priceDescription;	
          }
        }
        else {
          $priceDescription = Cart66Common::currency($priceDescription);
          if($price > 0) {
            $priceDescription .= ' (one time) +<br/> ';
          }
          else {
            $priceDescription = '';
          }
          $priceDescription .= $this->getSubscriptionPriceSummary();
        }
        
        $proRated = $this->getProRateInfo();
        if(is_object($proRated) && $proRated->amount > 0) {
          $proRatedInfo = $proRated->description . ':&nbsp;' . $proRated->money;
          $priceDescription .= '<br/>' . $proRatedInfo;
        }
        
      }
      elseif($this->isPayPalSubscription()) {
        $plan = new Cart66PayPalSubscription($this->id);
        $priceDescription = '';
        if($plan->offerTrial) {
          $priceDescription .= $plan->getTrialPriceDescription();
        }
        else {
          $priceDescription .= $plan->getPriceDescription();
        }
      }
      else {
        // Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Product custom price description: $this->priceDes");
        if(!empty($this->priceDescription)) {
          $priceDescription = $this->priceDescription;
        }
        else {
          $priceDescription = $this->price + $priceDifference;
        }
      }
    }
    return $priceDescription;
  }
  
  /**
   * Return information about pro-rated credit or false if there is none.
   * 
   * Returns a standard object:
   *   $data->description = The description of the credit
   *   $data->amount = The monetary amount of the credit
   *   $data->money = The formated monetary amount of the credit
   * 
   * return object or false
   */
  public function getProRateInfo() {
    $data = false;
    $proRateAmount = 0;
    if($this->isSpreedlySubscription()) {
      if(Cart66Common::isLoggedIn() && Cart66Session::get('Cart66Cart')) {
        if($subscriptionId = Cart66Session::get('Cart66Cart')->getSpreedlySubscriptionId()) {
          try {
            $invoiceData = array(
              'subscription-plan-id' => $subscriptionId,
              'subscriber' => array(
                'customer-id' => Cart66Session::get('Cart66AccountId')
              )
            );
            $invoice = new SpreedlyInvoice();
            $invoice->createFromArray($invoiceData);
            $this->_creditAmount = abs((float)$invoice->invoiceData->{'line-items'}->{'line-item'}[1]->amount);

            $data = new stdClass();
            $data->description = $invoice->invoiceData->{'line-items'}->{'line-item'}[1]->description;
            $data->amount = $this->_creditAmount;
            $data->money = Cart66Common::currency($this->_creditAmount);
            
            if($data->amount > 0) {
              $proRateAmount = $data->amount;
            }

            Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Spreedly Invoice: " . print_r($invoice->invoiceData, true));
          }
          catch(SpreedlyException $e) {
            Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Unable to locate spreedly customer: " . Cart66Session::get('Cart66AccountId'));
          }
        }
      }
    }
    Cart66Session::set('Cart66ProRateAmount', $proRateAmount, true);
    return $data;
  }
  
  /**
   * Return the Cart66PayPalSubscription associated with this products paypal subscription id.
   * If no paypal subscription is attached to this product, return false.
   * 
   * @return Cart66PayPalSubscription
   */
  public function getPayPalSubscription() {
    $sub = false;
    if($this->isPayPalSubscription()) {
      if(class_exists('Cart66PayPalSubscription')) {
        $sub = new Cart66PayPalSubscription($this->id);
      }
    }
    return $sub;
  }
  
  /**
   * Override base class save method by validating the data before and after saving.
   * Return the product id of the saved product.
   * Throw Cart66Exception if the save fails.
   * 
   * @return int The product id
   * @throws Cart66Exception on save failure
   */
  public function save($override_nonce=false) {
    $errors = $this->validate($override_nonce);
    if(count($errors) == 0) {
      $productId = parent::save();
      $errors = $this->validate($override_nonce);
    }
    if(count($errors)) {
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] " . get_class($this) . " save errors: " . print_r($errors, true));
      $this->setErrors($errors);
      $errors = print_r($errors, true);
      throw new Cart66Exception('Product save failed: ' . $errors, 66102);
    }
    return $productId;
  }
  
  public static function loadProductsOutsideOfClass($select='*', $where='id > 0', $orderBy='name') {
    $tableName = Cart66Common::getTableName('products');
    $sql = "SELECT $select
      from 
        $tableName 
      where
        $where
      order by
        $orderBy
    ";
    global $wpdb;
    $products = $wpdb->get_results($sql);
    return $products;
  }
  
}
