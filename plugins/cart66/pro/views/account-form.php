<?php
  $account = isset($data['account']) ? $data['account'] : new Cart66Account();
?>
<?php if(!isset($data['embed']) || $data['embed'] != true) { echo '<div id="accountInfo"><ul class="shortLabels">'; } ?>
  <?php if(!isset($data['hide_title']) || !$data['hide_title']): ?>
    <li><h2>Account Information</h2></li>
  <?php endif; ?>
  <li>
    <label for="account-first_name">First name:</label><input type="text" name="account[first_name]" value="<?php echo $account->firstName ?>" id="account-first_name">
  </li>
  <li>
    <label for="account-last_name">Last name:</label><input type="text" name="account[last_name]" value="<?php echo $account->lastName ?>" id="account-last_name">
  </li>
  <li>
    <label for="account-email">Email:</label><input type="text" name="account[email]" value="<?php echo $account->email ?>" id="account-email">
  </li>
  <li>
    <label for="account-username">Username:</label><input type="text" name="account[username]" value="<?php echo $account->username ?>" id="account-username">
  </li>
  <li>
    <label for="account-password">Password:</label><input type="password" name="account[password]" value="" id="account-password">
  </li>
  <li>
    <label for="account-password2">&nbsp;</label><input type="password" name="account[password2]" value="" id="account-password2">
    <p class="description">Repeat password</p>
  </li>
<?php if(!isset($data['embed']) || $data['embed'] != true) { echo '</ul></div>'; } ?>