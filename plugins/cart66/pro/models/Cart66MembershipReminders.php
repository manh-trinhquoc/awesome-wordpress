<?php
class Cart66MembershipReminders extends Cart66ModelAbstract {
  
  public function __construct($id=null) {
    $this->_tableName = Cart66Common::getTableName('membership_reminders');
    parent::__construct($id);
  }
  
  public function save() {
    $errors = $this->validate();
    if(count($errors) == 0) {
      $reminderId = parent::save();
    }
    if(count($errors)) {
      $this->setErrors($errors);
      $errors = print_r($errors, true);
      throw new Cart66Exception('Reminder save failed: ' . $errors, 66302);
    }
    return $reminderId;
  }
  
  public function validate() {
    $errors = array();
    
    if($this->subscription_plan_id == null) {
      $errors['subscription_plan_id'] = __('Subscription is required', 'cart66');
    }
    
    if(empty($this->interval)) {
      $errors['interval'] = __('Reminder Interval is required', 'cart66');
    }
    
    if(empty($this->from_name)) {
      $errors['from_name'] = __('A name for the email is required', 'cart66');
    }
    
    if(empty($this->from_email) || !Cart66Common::isValidEmail($this->from_email)) {
      $errors['from_email'] = __('A valid from email address is required', 'cart66');
    }
    
    if(empty($this->subject)) {
      $errors['subject'] = __('A subject for the email is required', 'cart66');
    }
    
    return $errors;
  }
  
  public static function dailySubscriptionEmailReminderCheck() {
    Cart66Setting::setValue('daily_subscription_reminders_last_checked', Cart66Common::localTs());
    // Function that fires daily to send out subscription reminder emails.  This will be triggered once a day at 3 AM.
    // If this function fires emails will be sent.
    $dayStart = date('Y-m-d 00:00:00', Cart66Common::localTs());
    $dayEnd = date('Y-m-d 00:00:00', strtotime('+ 1 day', Cart66Common::localTs()));
    
    $reminder = new Cart66MembershipReminders();
    $reminders = $reminder->getModels();
    
    foreach($reminders as $r) {
      if($r->enable == 1) {
        $interval = explode(',', $r->interval);
        foreach($interval as $i) {
          $new_interval = trim($i) . ' ' . $r->interval_unit;

          $product = new Cart66Product($r->subscription_plan_id);

          $start = date('Y-m-d H:i:s', strtotime('+ ' . $new_interval, strtotime($dayStart)));
          $end = date('Y-m-d H:i:s', strtotime('+ ' . $new_interval, strtotime($dayEnd)));
          $sub = new Cart66AccountSubscription();
          $subs = $sub->getModels("where active_until >= '$start' AND active_until < '$end' AND lifetime != '1' AND product_id = '$product->id'");
          $log = array();
          foreach($subs as $s) {
            if($r->validateReminderEmails($s->id)) {
              $r->sendReminderEmails($s->id);
              $log[] = $s->id . ' :: ' . $s->billing_first_name . ' ' . $s->billing_last_name;
            }
          }
          Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Start: $start :: End: $end");
          Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] subscription ids that meet the criteria: " . print_r($log, true));
        }
      }
    }
    
  }
  
  public function validateReminderEmails($subscriptionId) {
    // This function checks and validates all rules to ensure only the correct emails get sent
    $validate = false;
    $sub = new Cart66AccountSubscription($subscriptionId);
    $account = new Cart66Account($sub->account_id);
    
    // Check to see if the customer has opted out already
    if(!$account->opt_out) {
      $validate = true;
    }
    
    return $validate;
  }
  
  public function sendReminderEmails($subscriptionId) {
    $isSent = false;
    $sub = new Cart66AccountSubscription($subscriptionId);
    $notify = new Cart66AdvancedNotifications();
    $account = new Cart66Account($sub->account_id);
    
    $from_email = $this->from_email;
    $from_name = $this->from_name;
    $head = $notify->buildEmailHeader($from_name, $from_email);
    $email_data = array(
      'from_email' => $from_email,
      'from_name' => $from_name,
      'to_email' => $account->email,
      'to_name' => $sub->billing_first_name . ' ' . $sub->billing_last_name,
      'copy_to' => $this->copy_to,
      'head' => $head,
      'subject' => $this->subject,
      'msg' => $this->getReminderEmailMessage($sub, $head['mime']),
      'msg_cc' => $this->getReminderEmailMessage($sub, $head['mime'], 'cc'),
      'attachments' => null,
      'order_id' => '',
      'email_type' => 'REMINDER',
      'log' => 'reminder_emails',
      'status' => 'reminder'
    );
    if($this) {
      $isSent = $notify->sendEmail($email_data);
    }
    return $isSent;
  }
  
  public static function sendTestReminderEmails($to_email, $reminderId) {
    $isSent = false;
    $notify = new Cart66AdvancedNotifications();
    $reminder = new Cart66MembershipReminders($reminderId);
    
    $from_email = $reminder->from_email;
    $from_name = $reminder->from_name;
    $head = $notify->buildEmailHeader($from_name, $from_email);
    $email_data = array(
      'from_email' => $from_email,
      'from_name' => $from_name,
      'to_email' => $to_email,
      'to_name' => '',
      'copy_to' => '',
      'head' => $head,
      'subject' => $reminder->subject,
      'msg' => $reminder->getReminderEmailMessage(null, $head['mime'], 'test'),
      'attachments' => null,
      'order_id' => '',
      'email_type' => 'TEST',
      'log' => 'test_emails',
      'status' => 'test'
    );
        
    if($reminder) {
      $isSent = $notify->sendEmail($email_data);
    }
    return $isSent;
  }
  
  public function getReminderEmailMessage($sub, $mime_boundary, $type=null) {
    $notify = new Cart66AdvancedNotifications();
    $subId = isset($sub->id) ? $sub->id : null;
    if($type == 'test') {
      if($this->reminder_plain_email) {
        $plain_content = $this->reminder_plain_email;
      }
      else {
        $plain_content = $notify->getAdvancedEmailMessageContent('reminder', null, false, true, $this->id);
      }
      if($this->reminder_html_email) {
        $html_content = $this->reminder_html_email;
      }
      else {
        $html_content = $notify->getAdvancedEmailMessageContent('reminder', null, true, true, $this->id);
      }
    }
    else {
      if($this->reminder_plain_email) {
        $plain_content = strip_tags(str_replace('<br />', "\n", $notify->parseReceiptShortcodes($this->reminder_plain_email, $subId, 'plain', 'reminder')));
      }
      else {
        $plain_content = $notify->getAdvancedEmailMessageContent('reminder', $subId, false, false, $this->id);
      }
      if($this->reminder_html_email) {
        $html_content = $notify->parseReceiptShortcodes($this->reminder_html_email, $subId, 'html', 'reminder');
      }
      else {
        $html_content = $notify->getAdvancedEmailMessageContent('reminder', $subId, true, false, $this->id);
      }
    }
    
    if($type == 'cc') {
      $plain_content = "THIS IS A COPY OF THE EMAIL MESSAGE\n\n$plain_content";
      $html_content = "THIS IS A COPY OF THE EMAIL MESSAGE<br /><br />$html_content";
    }
    
    $sendHtml = false;
    if($this->reminder_send_html_emails == 1) {
      $sendHtml = true;
    }
    $body = $notify->buildEmailBody($plain_content, $html_content, $mime_boundary, $sendHtml);
    if(CART66_WPMAIL) {
      if($sendHtml) {
        $body = array(
          'text/plain' => $plain_content,
          'text/html' => $html_content
        );
      }
      else {
        $body = $plain_content;
      }
    }
    
    return $body;
  }
  
  public function updateMembershipProductIds(){
    global $wpdb;
    $output = false;
    // Check for subscriptions lacking a product id
    $sql = 'SELECT id from ' . Cart66Common::getTableName('account_subscriptions') . " WHERE product_id='0' OR product_id IS NULL";
    $needyAccountSubscriptions = $wpdb->get_results($sql);
    Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] " . count($needyAccountSubscriptions) . " accounts found that need to have a product id updated.");
    
    if(count($needyAccountSubscriptions) > 0){
      // accounts needing product id have been found
      foreach($needyAccountSubscriptions as $accountId){
        $account = new Cart66AccountSubscription($accountId->id);
        $accountProductId = $account->getProductId();
        if($accountProductId && !is_array($accountProductId)){
          $account->updateProductId($accountProductId);
          Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Adding Product id: $accountProductId to account id: $accountId->id ");
        }
        elseif(is_array($accountProductId)){
          $latestProductId = $account->findLatestProductId($accountProductId);
          Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Multiple products found for account $accountId->id");  
          if($latestProductId){
            Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Latest membership product id found, id: $latestProductId");
            $account->updateProductId($latestProductId);
          }
                  
        }
        else{
          $output[] = "The subscription id:$accountId->id belonging to $account->billing_first_name $account->billing_last_name does not have a product ID associated with it. This will prevent notifications from being sent out. Please <a href='" . Cart66Common::replaceQueryString('page=cart66-accounts&accountId=' . $account->account_id) . "'>edit the account</a> and select a product ID.";
          Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] No products were found matching the feature level and subscription plan name of the account id: $accountId->id");
        }

      }
    }
    
    return $output;
  }
  
}