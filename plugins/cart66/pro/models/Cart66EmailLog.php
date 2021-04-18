<?php
class Cart66EmailLog extends Cart66ModelAbstract {
  
  public function __construct($id=null) {
    $this->_tableName = Cart66Common::getTableName('email_log');
    parent::__construct($id);
  }
  
  public function saveEmailLog($email_data, $email_type, $copy, $status) {
    
    if(Cart66Setting::getValue('enable_email_log') == 1) {
      global $wpdb;
      $date = date("Y-m-d H:i:s", Cart66Common::localTs());
      if(is_array($email_data['msg'])) {
        $email_data['msg'] = $email_data['msg']['text/plain'] . '\n\n' . $email_data['msg']['text/html'];
      }
      $data = array(
        'send_date' => $date,
        'from_email' => $email_data['from_email'],
        'from_name' => $email_data['from_name'],
        'to_email' => $email_data['to_email'],
        'to_name' => $email_data['to_name'],
        'headers' => $email_data['head']['headers'],
        'subject' => $email_data['subject'],
        'body' => $email_data['msg'],
        'attachments' => $email_data['attachments'],
        'order_id' => $email_data['order_id'],
        'email_type' => $email_type,
        'copy' => $copy,
        'status' => $status
      );
      $logTable = Cart66Common::getTableName('email_log');
      $wpdb->insert($logTable, $data);
      $emailLogId = $wpdb->insert_id;
      Cart66Common::log("Saved email log ($emailLogId): " . $data['status'] . "\nSQL: " . $wpdb->last_query . ' ' . Cart66Common::localTs());
    }
  }
  
  public static function resendEmailFromLog($id) {
    $resendEmail = false;
    global $wpdb;
    $tableName = Cart66Common::getTableName('email_log');
    $sql = "SELECT * from $tableName where id = $id";
    $results = $wpdb->get_results($sql);
    if($results) {
      foreach($results as $r) {
        $resendEmail = Cart66Notifications::mail($r->to_email, $r->subject, $r->body, $r->headers);
        $email = new Cart66EmailLog();
        $email_data = array(
          'from_email' => $r->from_email,
          'from_name' => $r->from_name,
          'to_email' => $r->to_email,
          'to_name' => $r->to_name,
          'head' => array('headers' => $r->headers),
          'subject' => $r->subject,
          'msg' => $r->body,
          'attachments' => $r->attachments,
          'order_id' => $r->order_id,
        );
        if(!$resendEmail) {
          if(Cart66Setting::getValue('log_resent_emails')) {
            $email->saveEmailLog($email_data, $r->email_type, $r->copy, 'RESEND FAILED');
          }
        }
        else {
          if(Cart66Setting::getValue('log_resent_emails')) {
            $email->saveEmailLog($email_data, $r->email_type, $r->copy, 'RESEND SUCCESSFUL');
          }
        }
      }
    }
    return $resendEmail;
  }
  
}