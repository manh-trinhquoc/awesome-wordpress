<?php
// Look for constant contact opt-in
$mcIds = Cart66Common::postVal('mailchimp_subscribe_ids');

if(isset($mcIds) && is_array($mcIds)) {
  
  Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Trying to register for Mail Chimp newsletter");
  $mc = new Cart66MailChimp();
  $api_key = Cart66Setting::getValue('mailchimp_apikey');
  $mc->MCAPI($api_key);
  
  
  if(isset($_POST['payment']) && isset($_POST['billing'])) {
    // Process from on-site checkout forms
    $email = $_POST['payment']['email'];
    $extraFields = array(
  		'FirstName' => $_POST['billing']['firstName'],
  		'LastName'  => $_POST['billing']['lastName']
  	);
  }
  elseif( isset($_POST['mailchimp_email']) && isset($_POST['mailchimp_first_name']) && isset($_POST['mailchimp_last_name']) ) {
    // Process from PayPal Express Checkout
    $email = Cart66Common::postVal('mailchimp_email');
    $extraFields = array(
  		'FirstName' => $_POST['mailchimp_first_name'],
  		'LastName'  => $_POST['mailchimp_last_name']
  	);
  }
  
  $mcDoubleOptin = (Cart66Setting::getValue('mailchimp_doubleoptin')=="no-optin") ? "false" : "true";
  
  if(isset($email) && !empty($email)) {
    $merge_vars = array('FNAME'=>$extraFields['FirstName'], 'LNAME'=>$extraFields['LastName'],"double_optin"=>$mcDoubleOptin);

    // By default this sends a confirmation email - you will not see new members
    // until the link contained in it is clicked!
    foreach($mcIds as $list_id) {
      $retval = $mc->listSubscribe( $list_id, $email, $merge_vars, "html", $mcDoubleOptin);
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] listSubscribe():\n List ID: $list_id\n Email: $email\n Merge Vars: ".print_r($merge_vars,true));
    }
  }
  
  if ($mc->errorCode) {
  	$logmsg = "Unable to load listSubscribe()!\n";
  	$logmsg .= "\tCode=".$mc->errorCode."\n";
  	$logmsg .= "\tMsg=".$mc->errorMessage."\n";
  } 
  else {
    
    //Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] MailChimp Subscribe output: ".print_r($retval,true));
    
    $list_ids = array();
    foreach($mcIds as $key=>$mcid){
      $list_ids[] = $mcid;
    }
    $subscribed_list_ids = implode(',', $list_ids);
    $listn = $mc->lists(array("list_id"=>$subscribed_list_ids),0,100);

    $list_names = array();
    foreach($listn['data'] as $list){
      $list_names[] = $list['name'];
    }
    $subscribed_list_names = implode(', ', $list_names);
    
    $logmsg = "Subscribed: " . $extraFields['FirstName'] . " " . $extraFields['LastName'] . " $email to ".print_r($subscribed_list_names,true);
    
  }
  
  Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] $logmsg");
  
  
}
