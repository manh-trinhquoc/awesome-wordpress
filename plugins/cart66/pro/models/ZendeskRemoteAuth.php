<?php
class ZendeskRemoteAuth {

  public static function login(Cart66Account $account) {
    $name = $account->firstName . ' ' . $account->lastName;
    $email = $account->email;
    $externalId = $account->id;
    $organization = Cart66Setting::getValue('zendesk_organization');
    $key = Cart66Setting::getValue('zendesk_token');
    $prefix = Cart66Setting::getValue('zendesk_prefix');
    
    if(Cart66Setting::getValue('zendesk_jwt')) {
      $now       = time();
      $token = array(
        "jti"   => md5($now . rand()),
        "iat"   => $now,
        "name"  => $name,
        "email" => $email
      );
      
      include_once(CART66_PATH . "/pro/models/JWT.php");
      $jwt = JWT::encode($token, $key);
      
      // Redirect
      header("Location: https://" . $prefix . ".zendesk.com/access/jwt?jwt=" . $jwt);
      exit;
    }
    else {
      /* Build the message */
      $ts = isset($_GET['timestamp']) ? $_GET['timestamp'] : time(); 
      $message = $name . '|' . $email . '|' . $externalId . '|' . $organization . '|||' . $key . '|' . $ts;
      $hash = MD5($message);
      $remoteAuthUrl = 'http://' . $prefix . '.zendesk.com/access/remoteauth/';
      $arguments = array(
        'name' => $name,
        'email' => $email,
        'external_id' => $externalId,
        'organization' => $organization,
        'timestamp' => $ts,
        'hash' => $hash
      );
      $url = add_query_arg($arguments, $remoteAuthUrl);
      header("Location: " . $url);
      exit;
    }
  } 
}