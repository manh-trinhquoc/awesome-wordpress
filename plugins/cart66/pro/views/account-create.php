<?php
$create_account = false;
// Set up a new Cart66Account and start by pre-populating the data or load the logged in account
if($accountId = Cart66Common::isLoggedIn()) {
  $account = new Cart66Account($accountId);
}
else {
  $account = new Cart66Account();
  if(isset($_POST['account'])) {
    $acctData = Cart66Common::postVal('account');
    Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] New Account Data: " . print_r($acctData, true));
    $account->firstName = $acctData['first_name'];
    $account->lastName = $acctData['last_name'];
    $account->email = $acctData['email'];
    $account->username = $acctData['username'];
    $account->password = md5($acctData['password']);
    $errors = $account->validate();
    $jqErrors = $account->getJqErrors();
    if($acctData['password'] != $acctData['password2']) {
      $errors[] = __("Passwords do not match","cart66");
      $jqErrors[] = 'account-password';
      $jqErrors[] = 'account-password2';
    }
    if(count($errors) == 0) {
      $create_account = true;
    }
    else {
      if(count($errors)) {
        try {
          Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Unable to process order: " . print_r($errors, true));
          throw new Cart66Exception(__('Your order could not be processed for the following reasons:', 'cart66'), 66500);
        }
        catch(Cart66Exception $e) {
          $exception = Cart66Exception::exceptionMessages($e->getCode(), $e->getMessage(), $errors);
          echo Cart66Common::getView('views/error-messages.php', $exception);
        }
      }
    }
    if($create_account) { 
      $account->save(); 
      $accountId = $account->id;
      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Just created account with id: $accountId");
      $product = new Cart66Product();
      $product->load($data['attrs']['product']);
      if($product->id <= 0) {
        $product->loadByItemNumber($data['attrs']['product']);
      }
      
      if($product->id > 0) {
        $account->attachMembershipProduct($product, $account->firstName, $account->lastName);
        $accountId = $account->id;
        Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Attached membership to account id: $accountId");
      }
      
      if($account->login($acctData['username'], $acctData['password'])) {
        Cart66Session::set('Cart66AccountId', $account->id);
      }
      
      if(isset($data['attrs']['url'])) {
        wp_redirect($data['attrs']['url']);
        exit;
      }
    }
  }
}

?>
<?php
  $cartImgPath = Cart66Setting::getValue('cart_images_url');
  if($cartImgPath) {
    if(strpos(strrev($cartImgPath), '/') !== 0) {
      $cartImgPath .= '/';
    }
    $createAccountImgPath = $cartImgPath . 'create-account.png';
  }
?>
<?php if($data['render_form']): ?>
  <form action="" method="post" class="phorm2">
    <div class="account-create">
      <ul class="shortLabels">
        <?php echo Cart66Common::getView('pro/views/account-form.php', array('account' => $account, 'embed' => true, 'hide_title' => true)); ?>
        <li>
          <label>&nbsp;</label>
          <?php if($cartImgPath): ?>
            <input class="create-account Cart66CreateAccountButton" type="image" src="<?php echo $createAccountImgPath ?>" value="<?php _e( 'Create Account' , 'cart66' ); ?>" name="Create Account"/>
          <?php else: ?>
            <input class="create-account Cart66ButtonPrimary Cart66CreateAccountButton" type="submit"  value="<?php _e( 'Create Account' , 'cart66' ); ?>" name="Create Account"/>
          <?php endif; ?>
        </li>
      </ul>
    </div>
  </form>
<?php else: ?>
  <p><?php _e('Could not load product information or product attribute is missing.', 'cart66'); ?></p>
<?php endif; ?>