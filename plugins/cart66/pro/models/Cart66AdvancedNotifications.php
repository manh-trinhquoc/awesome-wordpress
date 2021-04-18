<?php
class Cart66AdvancedNotifications extends Cart66Notifications {
  
  public function __construct($id=null) {
    parent::__construct($id);
  }
  
  public static function buildEmailHeader($from_name, $from_email) {
    $mime_boundary = 'Multipart_Boundary_x'.md5(time()).'x';
    $headers = '';
    if(!CART66_WPMAIL || false) {
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\r\n";
      $headers .= "Content-Transfer-Encoding: 7bit\r\n";
    }
    $headers .= 'From: ' . $from_name . ' <' . $from_email . '>' . "\r\n";
    $headers .= "X-Sender-IP: $_SERVER[SERVER_ADDR]\r\n";
    $headers .= 'Date: ' . date('n/d/Y g:i A', Cart66Common::localTs()) . "\r\n";
    
    $data = array(
      'headers' => $headers,
      'mime' => $mime_boundary
    );
    
    return $data;
  }
  
  public static function buildEmailBody($plain_content, $html_content, $mime_boundary, $sendHtml) {
    $body = "\r\n";
    $no_plain = Cart66Setting::getValue('disable_plain_email');
    $include_mime_boundary = Cart66Setting::getValue('include_mime_boundary');
    
    if(!$no_plain || $sendHtml == false){
      if($include_mime_boundary){
        $body .= "--$mime_boundary\r\n";
        $body .= "Content-Type: text/plain; charset=\"charset=us-ascii\"\r\n";
        $body .= "Content-Transfer-Encoding: 7bit\r\n";
      }
      $body .= "$plain_content";
      $body .= "\n\n";
    }
    
     
    if($sendHtml == true) {
      // Add in HTML version    
      if($include_mime_boundary){
        $body .= "--$mime_boundary\r\n";
        $body .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
        $body .= "Content-Transfer-Encoding: 7bit\r\n";
      }
      $body .= $html_content;
      $body .= "\n\n";
    }    
    
    // Attachments would go here
    
  // End email
    if(!$no_plain){
      $body .= "--$mime_boundary--\r\n";
    }
    
    return $body;
  }
  
  public function sendEmail($email_data) {
    $isSent = false;
    
    $isSent = $this->mail($email_data['to_email'], $email_data['subject'], $email_data['msg'], $email_data['head']['headers']);
    $log = new Cart66EmailLog();
    if(!$isSent) {
      Cart66Common::log("Mail not sent to: " . $email_data['to_email']);
      if(Cart66Setting::getValue('log_' . $email_data['log'])) {
        $log->saveEmailLog($email_data, $email_data['email_type'], 'ORIGINAL', 'FAILED');
      }
    }
    else {
      if(Cart66Setting::getValue('log_' . $email_data['log'])) {
        $log->saveEmailLog($email_data, $email_data['email_type'], 'ORIGINAL', 'SUCCESSFUL');
      }
    }
    
    $others = $email_data['copy_to'];
    if($others) {
      $list = explode(',', $others);
      foreach($list as $e) {
        $e = trim($e);
        $isSent = $this->mail($e, $email_data['subject'], $email_data['msg_cc'], $email_data['head']['headers']);
        if(!$isSent) {
          Cart66Common::log("Mail not sent to: $e");
          if(Cart66Setting::getValue('log_' . $email_data['log']) && Cart66Setting::getValue('log_cc_emails')) {
            $log->saveEmailLog($email_data, $email_data['email_type'], 'COPY', 'FAILED');
          }
        }
        else {
          Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Also mailed to: $e");
          if(Cart66Setting::getValue('log_' . $email_data['log']) && Cart66Setting::getValue('log_cc_emails')) {
            $log->saveEmailLog($email_data, $email_data['email_type'], 'COPY', 'SUCCESSFUL');
          }
        }
      }
    }
    return $isSent;
  }
  
  public function sendStatusUpdateEmail($status) {
    $status = str_replace(' ', '_', $status);
    $isSent = false;
    $subject = $this->parseReceiptShortcodes(Cart66Setting::getValue($status . '_subject'), $this->_order->id, null, null);
    $from_name = Cart66Setting::getValue($status . '_from_name');
    $from_email = Cart66Setting::getValue($status . '_from_address');
    $head = $this->buildEmailHeader($from_name, $from_email);
    $email_data = array(
      'from_email' => $from_email,
      'from_name' => $from_name,
      'to_email' => $this->_order->email,
      'to_name' => $this->_order->bill_first_name . ' ' . $this->_order->bill_last_name,
      'copy_to' => Cart66Setting::getValue($status . '_copy'),
      'head' => $head,
      'subject' => $subject,
      'msg' => $this->getAdvancedEmailMessage($this->_order, $head['mime'], null, $status),
      'msg_cc' => $this->getAdvancedEmailMessage($this->_order, $head['mime'], 'cc', $status),
      'attachments' => null,
      'order_id' => $this->_order->id,
      'email_type' => 'STATUS',
      'log' => 'status_update_emails',
      'status' => $status
    );
    
    if($this->_order) {
      $isSent = $this->sendEmail($email_data);
    }
    return $isSent;
  }
  
  public function sendAdvancedEmailReceipts($firstTime=true) {
    $isSent = false;
    $subject = $this->parseReceiptShortcodes(Cart66Setting::getValue('receipt_subject'), $this->_order->id, null, 'receipt');
    $from_email = Cart66Setting::getValue('receipt_from_address');
    $from_name = Cart66Setting::getValue('receipt_from_name');
    $head = $this->buildEmailHeader($from_name, $from_email);
    $email_data = array(
      'from_email' => $from_email,
      'from_name' => $from_name,
      'to_email' => $this->_order->email,
      'to_name' => $this->_order->bill_first_name . ' ' . $this->_order->bill_last_name,
      'copy_to' => Cart66Setting::getValue('receipt_copy'),
      'head' => $head,
      'subject' => $subject,
      'msg' => $this->getAdvancedEmailMessage($this->_order, $head['mime']),
      'msg_cc' => $this->getAdvancedEmailMessage($this->_order, $head['mime'], 'cc'),
      'attachments' => null,
      'order_id' => $this->_order->id,
      'email_type' => 'RECEIPT',
      'log' => 'email_receipts',
      'status' => ''
    );
    
    if($this->_order) {
      $isSent = $this->sendEmail($email_data);
      if($firstTime == true) {
        $orderFulfillment = new Cart66OrderFulfillment();
        $orderFulfillment->checkFulfillmentSettings($this->_order->id);
      }
    }
    
    return $isSent;
  }
  
  public function sendOrderFulfillmentEmails($orderFulfillmentId, $status='fulfillment') {
    $isSent = false;
    
    $orderFulfillment = new Cart66OrderFulfillment($orderFulfillmentId);
    $subject = $this->parseReceiptShortcodes(Cart66Setting::getValue($status . '_subject'), $this->_order->id, null, $status);
    $from_email = Cart66Setting::getValue('fulfillment_from_address');
    $from_name = Cart66Setting::getValue('fulfillment_from_name');
    $head = $this->buildEmailHeader($from_name, $from_email);
    $email_data = array(
      'from_email' => $from_email,
      'from_name' => $from_name,
      'to_email' => $orderFulfillment->email,
      'to_name' => $orderFulfillment->name,
      'copy_to' => Cart66Setting::getValue($status . '_copy'),
      'head' => $head,
      'subject' => $subject,
      'msg' => $this->getAdvancedEmailMessage($this->_order, $head['mime'], null, $status, $orderFulfillmentId),
      'msg_cc' => $this->getAdvancedEmailMessage($this->_order, $head['mime'], 'cc', $status, $orderFulfillmentId),
      'attachments' => null,
      'order_id' => $this->_order->id,
      'email_type' => 'FULFILLMENT',
      'log' => 'fulfillment_emails',
      'status' => $status
    );
    
    if($this->_order) {
      $isSent = $this->sendEmail($email_data);
    }
    return $isSent;
  }
  
  public static function dailyFollowupEmailCheck() {
    if(Cart66Setting::getValue('enable_followup_emails') == '1') {
      Cart66Setting::setValue('daily_followup_last_checked', Cart66Common::localTs());
      // Function that fires daily to send out followup emails.  This will be triggered once a day at 3 AM.
      // If this function fires emails will be sent.
    
      $dayStart = date('Y-m-d 00:00:00', Cart66Common::localTs());
      $dayEnd = date('Y-m-d 00:00:00', strtotime('+ 1 day', Cart66Common::localTs()));
    
      $total = Cart66Setting::getValue('followup_email_number') . ' ' . Cart66Setting::getValue('followup_email_time');
    
      $start = date('Y-m-d H:i:s', strtotime('- ' . $total, strtotime($dayStart)));
      $end = date('Y-m-d H:i:s', strtotime('- ' . $total, strtotime($dayEnd)));
    
      $order = new Cart66Order();
      $orders = $order->getModels("where ordered_on >= '$start' AND ordered_on < '$end'");
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Start: $start :: End: $end");
      foreach($orders as $o) {
        Cart66AdvancedNotifications::sendFollowupEmail($o->id);
      }
    }
    
  }
  
  public static function sendFollowupEmail($orderId) {
    Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] sending followup for $orderId");
    $isSent = false;
    $subject = $this->parseReceiptShortcodes(Cart66Setting::getValue('followup_subject'), $this->_order->id, null, 'followup');
    $notify = new Cart66AdvancedNotifications($orderId);
    
    $from_email = Cart66Setting::getValue('followup_from_address');
    $from_name = Cart66Setting::getValue('followup_from_name');
    $head = $notify->buildEmailHeader($from_name, $from_email);
    $email_data = array(
      'from_email' => $from_email,
      'from_name' => $from_name,
      'to_email' => $notify->_order->email,
      'to_name' => $notify->_order->bill_first_name . ' ' . $notify->_order->bill_last_name,
      'copy_to' => Cart66Setting::getValue('followup_copy'),
      'head' => $head,
      'subject' => $subject,
      'msg' => $notify->getAdvancedEmailMessage($notify->_order, $head['mime'], null, 'followup'),
      'msg_cc' => $notify->getAdvancedEmailMessage($notify->_order, $head['mime'], 'cc', 'followup'),
      'attachments' => null,
      'order_id' => $notify->_order->id,
      'email_type' => 'FOLLOWUP',
      'log' => 'followup_emails',
      'status' => 'followup'
    );
        
    if($notify->_order) {
      $isSent = $notify->sendEmail($email_data);
    }
    return $isSent;
  }
  
  public static function sendTestEmail($sendTestTo, $status) {
    $isSent = false;
    
    $notify = new Cart66AdvancedNotifications();
    
    $from_email = Cart66Setting::getValue($status . '_from_address');
    $from_name = Cart66Setting::getValue($status . '_from_name');
    $head = $notify->buildEmailHeader($from_name, $from_email);
    $email_data = array(
      'from_email' => $from_email,
      'from_name' => $from_name,
      'to_email' => $sendTestTo,
      'to_name' => '',
      'copy_to' => '',
      'head' => $head,
      'subject' => __('TEST EMAIL', 'cart66') . ' --' . strtoupper($status) . '-- ' . Cart66Setting::getValue($status . '_subject'),
      'msg' => $notify->getAdvancedEmailMessage(null, $head['mime'], 'test', Cart66Common::postVal('status')),
      'msg_cc' => null,
      'attachments' => null,
      'order_id' => $notify->_order->id,
      'email_type' => 'TEST',
      'log' => 'test_emails',
      'status' => 'test'
    );
    
    $isSent = $notify->sendEmail($email_data);
    
    return $isSent;
  }
  
  public function getAdvancedEmailMessage($order, $mime_boundary, $type=null, $setting='receipt', $emailVariable=null) {
    $source_types = array(
      'receipt',
      'fulfillment',
      'followup',
      'reminder',
      'status'
    );
    if(!in_array($setting, $source_types)) {
      $source = null;
    }
    else {
      $source = $setting;
    }
    if($type == 'test') {
      if(Cart66Setting::getValue($setting . '_plain_email') && !Cart66Setting::getValue($setting . '_message_intro')) {
        $plain_content = Cart66Setting::getValue($setting . '_plain_email');
      }
      else {
        $plain_content = $this->getAdvancedEmailMessageContent($setting, null, false, true, $emailVariable);
      }
      if(Cart66Setting::getValue($setting . '_html_email') && !Cart66Setting::getValue($setting . '_message_intro')) {
        $html_content = Cart66Setting::getValue($setting . '_html_email');
      }
      else {
        $html_content = $this->getAdvancedEmailMessageContent($setting, null, true, true, $emailVariable);
      }
    }
    else {
      if(Cart66Setting::getValue($setting . '_plain_email') && !Cart66Setting::getValue($setting . '_message_intro')) {
        $plain_content = strip_tags(str_replace('<br />', "\n", $this->parseReceiptShortcodes(Cart66Setting::getValue($setting . '_plain_email'), $order->id, 'plain', $source, $emailVariable)));
      }
      else {
        $plain_content = strip_tags(str_replace('<br />', "\n", $this->parseReceiptShortcodes($this->getAdvancedEmailMessageContent($setting, $order, false, false, $emailVariable), $order->id, 'plain', $source, $emailVariable)));
      }
      if(Cart66Setting::getValue($setting . '_html_email') && !Cart66Setting::getValue($setting . '_message_intro')) {
        $html_content = $this->parseReceiptShortcodes(Cart66Setting::getValue($setting . '_html_email'), $order->id, 'html', $source, $emailVariable);
      }
      else {
        $html_content = $this->parseReceiptShortcodes($this->getAdvancedEmailMessageContent($setting, $order, true, false, $emailVariable), $order->id, 'html', $source, $emailVariable);
      }
    }
    
    if($type == 'cc') {
      $plain_content = "THIS IS A COPY OF THE EMAIL MESSAGE\n\n$plain_content";
      $html_content = "THIS IS A COPY OF THE EMAIL MESSAGE<br /><br />$html_content";
    }
    
    $sendHtml = false;
    if(Cart66Setting::getValue($setting . '_send_html_emails') == 1) {
      $sendHtml = true;
    }
    $body = $this->buildEmailBody($plain_content, $html_content, $mime_boundary, $sendHtml);
    if(CART66_WPMAIL) {
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] YES SENDING MAIL WITH THE CART66 WPMAIL OVERRIDE");
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
  
  public function getAdvancedEmailMessageContent($setting, $order, $html=null, $test=null, $emailVariable=null) {
    $setting_array = array(
      'receipt',
      'reminder',
      'fulfillment',
      'followup'
    );
    if(!in_array($setting, $setting_array)) {
      $emailVariable = $setting;
      $setting = 'status';
    }
    switch($setting) {
      case 'receipt':
        $msg = $this->getEmailReceiptMessage($order, $html, $test);
        break;
      default:
        $msg = Cart66Common::getView('pro/views/emails/default-email-' . $setting . '.php', array($order, $html, $test, $emailVariable));
        break;
    }
    return $msg;
  }
  
  public static function parseReceiptShortcodes($string, $id, $emailType=null, $source, $emailVariable=null){
    if($source == null) {
      $source = 'status';
    }
    return do_shortcode(preg_replace(array(
      '/{{/', '/}}/'
    ), array(
      '[email_shortcodes id="' . $id . '" att="', '" type="' . $emailType . '" source="' . $source . '" variable="' . $emailVariable . '"]'
    ), $string));
  }
  
  public static function removeTrackingNumber($order) {
    $tracking = explode(',', $order->tracking_number);
    if(in_array(Cart66Common::postVal('remove'), $tracking)) {
      $key = array_search(Cart66Common::postVal('remove'), $tracking);
      unset($tracking[$key]);
    }
    $tracking = implode(',', $tracking);
    if($tracking == '') {
      $order->updateTracking(null);
    }
    else {
      $order->updateTracking($tracking);
    }
  }
  
  public static function addTrackingNumbers($order) {
    $tracking = array();
    foreach($_POST as $key => $value) {
      $track = substr(strstr($key, '_'), 1, 8);
      if($track == 'tracking' && $value != '') {
        $tracking[] = $value;
      }
    }
    $carrier = array();
    foreach($_POST as $key => $value) {
      $carry = substr(strstr($key, '_'), 1, 7);
      if($carry == 'carrier' && $value != '') {
        $carrier[] = $value;
      }
    }
    foreach ($tracking as $track => $t) {
      if(isset($carrier[$track])) {
        $tracking_number[] = $carrier[$track] . '_' . $t;
      }
    }
    if(isset($tracking_number)) {
      $tracking_number = implode(",", $tracking_number);
      if($tracking_number != '_') {
        if($order->tracking_number == null) {
          $order->updateTracking($tracking_number);
        }
        else {
          $tracking_number = $order->tracking_number . ',' . $tracking_number;
          $order->updateTracking($tracking_number);
        }
        //$order->updateTracking(null);
      }
    }
  }
  
  public static function convertCarrierNames($carrier) {
    if($carrier == 'CaPost') {
      $carrier = 'Canada Post';
    }
    elseif($carrier == 'AuPost') {
      $carrier = 'Australia Post';
    }
    return $carrier;
  }
  
  public static function getCarrierLink($carrier, $number) {
    $link = '';
    switch($carrier) {
      case 'FedEx':
        $link = 'http://www.fedex.com/Tracking?action=track&tracknumbers=' . $number;
        break;
      case 'USPS':
        $link = 'http://tools.usps.com/go/TrackConfirmAction?CAMEFROM=OK&strOrigTrackNum=' . $number;
        break;
      case 'UPS':
        $link = 'http://wwwapps.ups.com/WebTracking/track?HTMLVersion=5.0&loc=en_US&Requester=UPSHome&WBPM_lid=homepage%2Fct1.html_pnl_trk&trackNums=' . $number . '&track.x=Track';
        break;
      case 'DHL':
        $link = 'http://www.dhl.com/content/g0/en/express/tracking.shtml?brand=DHL&AWB=' . $number . '%0D%0A';
        break;
      case 'Canada Post':
        $link = 'http://www.canadapost.ca/cpotools/apps/track/personal/findByTrackNumber?trackingNumber=' . $number;
        break;
      case 'Australia Post':
        $link = 'http://auspost.com.au/track/track.html?id=' . $number;
        break;
    }

    return $link;
  }
  
  public static function updateTracking($order, $attrs) {
    $output = '';
    if($order->tracking_number == null){
      $output = '';
    }
    else {
      $i = 0;
      $tracking = explode(",", $order->tracking_number);
      foreach($tracking as $track) {
        $content = strstr($attrs['att'], ':');
        $content = substr($content, 1);
        $i++;
        $content = str_replace('$i', $i, $content);
        $carrier = mb_strstr($track,'_', true);
        $number = substr(strstr($track, '_'), 1);
        $carrier = self::convertCarrierNames($carrier);
        $link = self::getCarrierLink($carrier, $number);
        if($attrs['type'] == 'html') {
          $output .= str_replace('$carrier', $carrier, $content) . ' <a target="_blank" href="' . $link . '">' . $number . '</a><br />';
        }
        else {
          $output .= str_replace('$carrier', $carrier, $content) . ' ' . $number . ' ' . $link . '<br />';
        }
      }
    }
    return $output;
  }
  
  public static function updateDate($attrs) {
    $date = strstr($attrs['att'], ':');
    $date = substr($date, 1);
    $output = date($date, Cart66Common::localTs());
    return $output;
  }
  
  public static function updateDateOrdered($order, $attrs) {
    $output = '';
    if($order->ordered_on == null){
      $output = '';
    }
    else {
      $date = strstr($attrs['att'], ':');
      $date = substr($date, 1);
      $output = date($date, strtotime($order->ordered_on));
    }
    return $output;
  }
  
}