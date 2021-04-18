<?php
class Cart66LiveRate extends Cart66BaseModelAbstract {
  
  protected $_data = array();
  
  public function __construct($carrier, $service, $rate, $isSelected=false) {
    $this->_data['carrier'] = $carrier;
    $this->_data['service'] = $service;
    $this->_data['rate'] = $rate;
    $this->_data['isSelected'] = $isSelected;
  }
  
  public function getCarrier() {
    return $this->_data['carrier'];
  }
  
  public function getService() {
    return $this->_data['service'];
  }
  
  public function setService($value) {
    $this->_data['service'] = $value;
  }
  
  public function getRate() {
    return $this->_data['rate'];
  }
  
  public function setRate($value) {
    $this->_data['rate'] = $value;
  }
  
  public function isSelected() {
    return $this->_data['isSelected'];
  }
  
  public function setSelected($value) {
    if($value) {
      $this->_data['isSelected'] = true;
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Setting this live rate to selected: " . $this->_data['service']);
    }
    else {
      $this->_data['isSelected'] = false;
    }
  }

}