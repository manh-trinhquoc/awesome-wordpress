<?php
$tab = 'integrations-hurricane';
?>
<div id="saveResult"></div>
<div id="cart66-inner-tabs">
  <ul class="subsubsub">
    <!--li><a href="#integrations-main_settings" class="integrations-main_settings"><?php _e('Integrations', 'cart66'); ?></a> | </li-->
    <li><a href="#integrations-amazon_s3" class="integrations-amazon_s3"><?php _e('Amazon S3', 'cart66'); ?></a> | </li>
    <li><a href="#integrations-constant_contact" class="integrations-constant_contact"><?php _e('Constant Contact', 'cart66'); ?></a> | </li>
    <li><a href="#integrations-hurricane" class="integrations-hurricane"><?php _e('Hurricane Affiliate Software', 'cart66'); ?></a> | </li>
    <li><a href="#integrations-google_analytics_ecommerce" class="integrations-google_analytics_ecommerce"><?php _e('Google Analytics Ecommerce Tracking', 'cart66'); ?></a> | </li>
    <li><a href="#integrations-gravity_forms" class="integrations-gravity_forms"><?php _e('Gravity Forms', 'cart66'); ?></a> | </li>
    <li><a href="#integrations-idevaffiliate" class="integrations-idevaffiliate"><?php _e('iDevAffiliate', 'cart66'); ?></a> | </li>
    <li><a href="#integrations-mailchimp" class="integrations-mailchimp"><?php _e('MailChimp', 'cart66'); ?></a> | </li>
    <li><a href="#integrations-spreedly" class="integrations-spreedly"><?php _e('Spreedly', 'cart66'); ?></a> | </li>
    <li><a href="#integrations-zendesk" class="integrations-zendesk"><?php _e('Zendesk', 'cart66'); ?></a></li>
  </ul>
  <br clear="all">
  <form id="integrationsForm" action="" method="post" class="ajaxSettingForm">
    <input type="hidden" name="action" value="save_settings" />
    <input type="hidden" name="_success" value="<?php _e('Your integration settings have been saved', 'cart66'); ?>." />
    <!--div id="integrations-main_settings" class="pane">
      <h3><?php _e('Integrations', 'cart66'); ?></h3>
      <table class="form-table">
        <tbody>
          <tr valign="top">
            <th scope="row"></th>
            <td>
              
            </td>
          </tr>
        </tbody>
      </table>
    </div-->
    <div id="integrations-hurricane" class="pane">
      <a href="http://hurricane.io" target="_blank" style="float:right;"><img src="https://cart66.com/images/integrations/hurricane.png" align="left" alt="Hurricane"></a>
      <h3><?php _e('Hurricane Affiliate Software', 'cart66'); ?></h3>
      <table class="form-table">
        <tbody>
          <tr valign="top">
            <th scope="row"><?php _e('Your Hurricane Subdomain', 'cart66'); ?></th>
            <td>
              http://<input type="text" name="cart66_hurricane_subdomain" id="cart66_hurricane_subdomain" value="<?php echo Cart66Setting::getValue('cart66_hurricane_subdomain'); ?>" />.hurricane.io
              <p class="description"><?php _e( 'Enter your Hurricane subdomain from your custom hurricane.io address.' , 'cart66' ); ?></p>
                 <?php 
                  if(Cart66Setting::getValue('cart66_hurricane_subdomain')):
                    $hurricane_url = wp_remote_get("http://".Cart66Setting::getValue('cart66_hurricane_subdomain').".hurricane.io");
                    $response_code = wp_remote_retrieve_response_code( $hurricane_url ); 
                    if(!$response_code || $response_code != 200): ?>
                      <p class="description" style="color:red;">
                        There's a problem with your subdomain <a href="http://<?php echo Cart66Setting::getValue('cart66_hurricane_subdomain'); ?>.hurricane.io" target="_blank">http://<?php echo Cart66Setting::getValue('cart66_hurricane_subdomain'); ?>.hurricane.io</a>. Please make sure  you have entered it correctly.
                      </p>
                    <?php endif; ?>
                 <?php endif; ?>

                            
              <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="metabox-holder">
                  <div id="hurricane-help-box" class="postbox-container">
                    <div id="dashboard_right_now" class="postbox">
                      <h3 class="hndle"><span>How are commissions calculated?</span></h3>
                       <div class="inside">
                         <div class="main">


                          <p>The order total sent to Hurricane used to calculate commissions is the order total (including discounts) less shipping and taxes.</p>

                          <h4 style="padding: 10px 0px;">Example Order Amounts:</h4>

                          <table>
                            <tr>
                              <td align="right">Cart subtotal:</td>
                              <td>$50.00</td>
                            </tr>
                            <tr>
                              <td align="right">Order discount:</td>
                              <td>- $10.00</td>
                            </tr>
                            <tr>
                              <td align="right">Shipping:</td>
                              <td>$5.00</td>
                            </tr>
                            <tr>
                              <td align="right">Tax:</td>
                              <td>$2.00</td>
                            </tr>
                            <tr>
                              <td align="right">Order total:</td>
                              <td>$47.00</td>
                            </tr>
                            <tr>
                              <td align="right"><strong>Total sent to Hurricane:</strong></td>
                              <td><strong>$40.00</strong></td>
                            </tr>
                          </table>

                         </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div id="integrations-google_analytics_ecommerce" class="pane">
      <a href="#" target="_blank" style="float:right;"><img src="https://cart66.com/images/integrations/Google-Analytics-Logo.png" align="left" alt="Google Analytics"></a>
      <h3><?php _e('Google Analytics Ecommerce Tracking', 'cart66'); ?></h3>
      <?php if(CART66_PRO): ?>
        <table class="form-table">
          <tbody>
            <tr valign="top">
              <th scope="row"><?php _e('Enable Tracking', 'cart66'); ?></th>
              <td>
                <input type="radio" name="enable_google_analytics" id="enable_google_analytics_yes" value="1" <?php echo (Cart66Setting::getValue('enable_google_analytics') == 1) ? 'checked="checked" ' : ''; ?>/>
                <label for="enable_google_analytics_yes"><?php _e('Yes', 'cart66'); ?></label>
                <input type="radio" name="enable_google_analytics" id="enable_google_analytics_no" value="" <?php echo (Cart66Setting::getValue('enable_google_analytics') != 1) ? 'checked="checked" ' : ''; ?>/>
                <label for="enable_google_analytics_no"><?php _e('No', 'cart66'); ?></label>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"><?php _e('Other Plugins', 'cart66'); ?></th>
              <td>
                <select name="use_other_analytics_plugin" id="use_other_analytics_plugin">
                  <option value="1"><?php _e('Yes, I want to use Cart66 with other Google Analytics plugins', 'cart66'); ?></option>
                  <option value=""><?php _e('No, I want to use Cart66 to track on its own', 'cart66'); ?></option>
                </select>
              </td>
            </tr>
            <tr valign="top" class="google_analytics_product_id">
              <th scope="row"><?php _e('Web Product ID', 'cart66'); ?></th>
              <td>
                <input type="text" name="google_analytics_wpid" id="google_analytics_wpid" value="<?php echo Cart66Setting::getValue('google_analytics_wpid'); ?>" />
                <p class="description"><?php _e( 'Starts with UA-XXXXXXXX-X' , 'cart66' ); ?></p>
              </td>
            </tr>
          </tbody>
        </table>
      <?php else: ?>
        <p class="description"><?php _e('Google Analytics tracks the following kinds of data about the purchases visitors make from your online store', 'cart66'); ?>:
          <ul>
            <li><?php _e('Products: Which products they buy, in what quantity, and the revenue generated by those products', 'cart66'); ?>.</li>
            <li><?php _e('Transactions: The revenue, tax, shipping, and quantity information for each transaction', 'cart66'); ?>.</li>
            <li><?php _e('Time to Purchase: The number of days from the initial visit, and the total number of visits it takes for visitors to complete transactions', 'cart66'); ?>.</li>
          </ul>
        </p>
        <p class="description"><?php _e( 'This feature is only available in', 'cart66'); ?> <a href="http://cart66.com"><?php _e('Cart66 Professional', 'cart66'); ?></a>.</p>
      <?php endif; ?>
    </div>
    <div id="integrations-gravity_forms" class="pane">
      <a href="#" target="_blank" style="float:right;"><img src="https://cart66.com/images/integrations/gravity-logo.png" align="left" alt="Gravity Forms"></a>
      <h3><?php _e('Gravity Forms', 'cart66'); ?></h3>
      <?php if(CART66_PRO): ?>
        <table class="form-table">
          <tbody>
            <tr valign="top">
              <th scope="row"><?php _e('Display Form Entries Before Sale', 'cart66'); ?></th>
              <td>
                <input type="radio" name="display_form_entries_before_sale" id="display_form_entries_before_sale_yes" value="1" <?php echo (Cart66Setting::getValue('display_form_entries_before_sale') == 1) ? 'checked="checked" ' : ''; ?>/>
                <label for="display_form_entries_before_sale_yes"><?php _e('Yes', 'cart66'); ?></label>
                <input type="radio" name="display_form_entries_before_sale" id="display_form_entries_before_sale_no" value="" <?php echo (Cart66Setting::getValue('display_form_entries_before_sale') != 1) ? 'checked="checked" ' : ''; ?>/>
                <label for="display_form_entries_before_sale_no"><?php _e('No', 'cart66'); ?></label>
                <p class="description"><?php _e('Set this to yes to view Cart66 product form entries in the Gravity Form entries section before a sale has been processed.', 'cart66'); ?></p>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"><?php _e('Keep Orphaned Gravity Form Entries', 'cart66'); ?></th>
              <td>
                <input type="radio" name="keep_orphaned_gravity_entries" id="keep_orphaned_gravity_entries_yes" value="1" <?php echo (Cart66Setting::getValue('keep_orphaned_gravity_entries') == 1) ? 'checked="checked" ' : ''; ?>/>
                <label for="keep_orphaned_gravity_entries_yes"><?php _e('Yes', 'cart66'); ?></label>
                <input type="radio" name="keep_orphaned_gravity_entries" id="keep_orphaned_gravity_entries_no" value="" <?php echo (Cart66Setting::getValue('keep_orphaned_gravity_entries') != 1) ? 'checked="checked" ' : ''; ?>/>
                <label for="keep_orphaned_gravity_entries_no"><?php _e('No', 'cart66'); ?></label>
                <p class="description"><?php _e('Set this to yes to prevent orphaned Cart66 product Gravity Form entries from being removed automatically.', 'cart66'); ?></p>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"><?php _e('Display Option Labels', 'cart66'); ?></th>
              <td>
                <input type="radio" name="gravity_display_option_labels" id="gravity_display_option_labels_yes" value="1" <?php echo (Cart66Setting::getValue('gravity_display_option_labels') == 1) ? 'checked="checked" ' : ''; ?>/>
                <label for="gravity_display_option_labels_yes"><?php _e('Yes', 'cart66'); ?></label>
                <input type="radio" name="gravity_display_option_labels" id="gravity_display_option_labels_no" value="" <?php echo (Cart66Setting::getValue('gravity_display_option_labels') != 1) ? 'checked="checked" ' : ''; ?>/>
                <label for="gravity_display_option_labels_no"><?php _e('No', 'cart66'); ?></label>
                <p class="description"><?php _e('Set this to yes to display option labels instead of the default values in the cart, email and order view pages.', 'cart66'); ?></p>
              </td>
            </tr>
        </table>
      <?php else: ?>
        <p class="description"><?php _e( 'This feature is only available in', 'cart66'); ?> <a href="http://cart66.com"><?php _e('Cart66 Professional', 'cart66'); ?></a>.</p>
      <?php endif; ?>
    </div>
    <div id="integrations-spreedly" class="pane">
      <a href="#" target="_blank" style="float:right;"><img src="https://cart66.com/images/integrations/spreedly-logo.png" align="left" alt="Spreedly"></a>
      <h3><?php _e('Spreedly', 'cart66'); ?></h3>
      <?php if (CART66_PRO): ?>
        <p class="description"><?php _e( 'Configure your Spreedly account information to sell subscriptions.' , 'cart66' ); ?></p>
        <table class="form-table">
          <tbody>
            <tr valign="top">
              <th scope="row"><?php _e('Short Site Name', 'cart66'); ?></th>
              <td>
                <input type="text" name="spreedly_shortname" id="spreedly_shortname" class="regular-text" value="<?php echo Cart66Setting::getValue('spreedly_shortname'); ?>" />
                <p class="description"><?php _e( 'Look in your Spreedly account under Site Details for the short site name (Used in URLs, etc)' , 'cart66' ); ?></p>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"><?php _e('API Token', 'cart66'); ?></th>
              <td>
                <input type="text" name="spreedly_apitoken" id="spreedly_apitoken" class="regular-text" value="<?php echo Cart66Setting::getValue('spreedly_apitoken'); ?>" />
                <p class="description"><?php _e( 'Look in your Spreedly account under Site Details for the API Authentication Token.' , 'cart66' ); ?></p>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"><?php _e('Log out Link', 'cart66'); ?></th>
              <td>
                <input type="radio" name="auto_logout_link" id="auto_logout_link_yes" value="1" <?php echo Cart66Setting::getValue('auto_logout_link') == 1 ? 'checked="checked" ' : '' ?>/>
                <label for="auto_logout_link_yes"><?php _e('Yes', 'cart66'); ?></label>
                <input type="radio" name="auto_logout_link" id="auto_logout_link_no" value="" <?php echo Cart66Setting::getValue('auto_logout_link') != 1 ? 'checked="checked" ' : '' ?>/>
                <label for="auto_logout_link_no"><?php _e('No', 'cart66'); ?></label>
                <p class="description"><?php _e( 'Append a logout link to your site\'s navigation. Note, this only works with themes that build the navigation using the wp_list_pages() function. See the documentation for other log out options when using WordPress 3.0 Menus.' , 'cart66' ); ?></p>
              </td>
            </tr>
          </tbody>
        </table>
      <?php else: ?>
        <p class="description" style="font-style: normal; color: #333; width: 600px;"><?php _e( 'Spreedly is everything subscriptions. There is a lot to selling subscriptions. Spreedly delivers it all in one convenient package so you can focus on building your business!' , 'cart66' ); ?></p>
        <p class="description"><?php _e( 'This feature is only available in', 'cart66'); ?> <a href="http://cart66.com"><?php _e('Cart66 Professional', 'cart66'); ?></a>.</p>
      <?php endif; ?>
    </div>
    <div id="integrations-amazon_s3" class="pane">
      <a href="#" target="_blank" style="float:right;"><img src="https://cart66.com/images/integrations/amazon-web-services.png" align="left" alt="Amazon Web Services S3"></a>
      <h3><?php _e('Amazon S3', 'cart66'); ?></h3>
      <p class="description"><?php _e( 'Amazon S3 provides a simple web services interface for delivering digital content. It gives you access to the same highly scalable, reliable, secure, fast, inexpensive infrastructure that Amazon uses to run its own global network of web sites. Deliver your Cart66 digital products through your Amazon S3 account to increase security and performance when selling digital products.' , 'cart66' ); ?></p>
      <p class="description"><?php _e( 'Configure your Amazon S3 account information so Cart66 can distribute secure digital downloads from your Amazon S3 account.' , 'cart66' ); ?></p>
      <table class="form-table">
        <tbody>
          <tr valign="top">
            <th scope="row"><?php _e('Access Key ID', 'cart66'); ?></th>
            <td>
              <input type="text" name="amazons3_id" id="amazons3_id" class="regular-text" value="<?php echo Cart66Setting::getValue('amazons3_id'); ?>" />
            </td>
          </tr>
          <tr valign="top">
            <th scope="row"><?php _e('Secret Key', 'cart66'); ?></th>
            <td>
              <input type="text" name="amazons3_key" id="amazons3_key" class="regular-text" value="<?php echo Cart66Setting::getValue('amazons3_key'); ?>" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div id="integrations-constant_contact" class="pane">
      <a href="#" target="_blank" style="float:right;"><img src="https://cart66.com/images/integrations/ConstantContact_logo.png" align="left" alt="Constant Contact"></a>
      <h3><?php _e('Constant Contact', 'cart66'); ?></h3>
      <?php if(CART66_PRO): ?>
        <p class="description"><?php _e( 'Configure your Constant Contact account information so your buyers can opt in to your newsletter.' , 'cart66' ); ?></p>
        <table class="form-table">
          <tbody>
            <tr valign="top">
              <th scope="row"><?php _e('Username', 'cart66'); ?></th>
              <td>
                <input type="text" name="constantcontact_username" id="constantcontact_username" value="<?php echo Cart66Setting::getValue('constantcontact_username'); ?>" />
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"><?php _e('Password', 'cart66'); ?></th>
              <td>
                <input type="text" name="constantcontact_password" id="constantcontact_password" value="<?php echo Cart66Setting::getValue('constantcontact_password'); ?>" />
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"><?php _e('Opt-In Message', 'cart66'); ?></th>
              <td>
                <textarea name="constantcontact_opt_in_message" class="large-textarea"><?php echo Cart66Setting::getValue('constantcontact_opt_in_message'); ?></textarea>
                <p class="description"><?php _e( 'Provide a message to tell your buyers what your newsletter is about. For example, you might want to say something like "Yes! I would like to subscribe to:"' , 'cart66' ); ?></p>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"><?php _e('Show Lists', 'cart66'); ?></th>
              <td>
                <?php
                  // Show the constant contact lists
                  if(Cart66Setting::getValue('constantcontact_username')) { ?>
                    <input type="hidden" name="constantcontact_list_ids" value="" />
                    <?php
                    $cc = new Cart66ConstantContact();
                    $lists = $cc->get_all_lists('lists', 3);
                    if(is_array($lists)) {
                      $savedListIds = array();
                      if($savedLists = Cart66Setting::getValue('constantcontact_list_ids')) {
                        $savedListIds = explode('~', $savedLists);
                      }
                    
                      foreach($lists as $list) {
                        $checked = '';
                        $val = $list['id'] . '::' . $list['Name'];
                        Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] looking for: $val in " . print_r($savedListIds, true));
                        if(in_array($val, $savedListIds)) {
                          $checked = 'checked="checked"';
                        } ?>
                        <input type="hidden" name="constantcontact_list_ids[]" />
                        <input type="checkbox" name="constantcontact_list_ids[]" value="<?php echo $val; ?>" <?php echo $checked; ?>> <?php echo $list['Name']; ?><br />
                      <?php }
                    }
                    else { ?>
                      <p class="description"><?php _e('You do not yet have any lists', 'cart66'); ?>.</p>
                    <?php } 
                  }
                  else { ?>
                    <p class="description"><?php _e('You do not yet have any lists', 'cart66'); ?>.<br><?php _e('If you have entered your credentials above and are not seeing any lists, try refreshing this page.', 'cart66'); ?></p>
                  <?php }
                ?>
              </td>
            </tr>
          </tbody>
        </table>
      <?php else: ?>
        <p class="description" style="font-style: normal; color: #333; width: 600px;"><?php _e( 'Constant Contact is an industry leader in email marketing. Constant Contact provides email marketing software that makes it easy to create professional HTML email campaigns with no tech skills.' , 'cart66' ); ?></p>
        <p class="description"><?php _e( 'This feature is only available in', 'cart66'); ?> <a href="http://cart66.com"><?php _e('Cart66 Professional', 'cart66'); ?></a>.</p>
      <?php endif; ?>
    </div>
    <div id="integrations-mailchimp" class="pane">
      <a href="http://eepurl.com/dtQBb" target="_blank" style="float:right;"><img src="https://cart66.com/images/integrations/MC_MonkeyReward_06.png" align="left" alt="Powered by MailChimp"></a>
      <h3><?php _e('MailChimp', 'cart66'); ?></h3>
      <?php if(CART66_PRO): ?>
        <p class="description"><?php _e( 'Configure your', 'cart66'); ?> <a href="http://eepurl.com/dtQBb" target="_blank">MailChimp</a> <?php _e('account information so your buyers can opt in to your newsletter', 'cart66'); ?></p>
        <table class="form-table">
          <tbody>
            <tr valign="top">
              <th scope="row"><?php _e('MailChimp API Key', 'cart66'); ?></th>
              <td>
                <input type="text" name="mailchimp_apikey" id="mailchimp_apikey" value="<?php echo Cart66Setting::getValue('mailchimp_apikey'); ?>" class="regular-text" />
                <p class="description"><?php _e( 'Need an API key? Find out how to get one' , 'cart66' ); ?> <a href="http://kb.mailchimp.com/article/where-can-i-find-my-api-key/" title="Where can I find my API Key?" target="_blank"><?php _e('here', 'cart66'); ?>.</a></p>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"><?php _e('Opt-In Message', 'cart66'); ?></th>
              <td>
                <textarea class="large-textarea" name="mailchimp_opt_in_message"><?php echo Cart66Setting::getValue('mailchimp_opt_in_message'); ?></textarea>
                <p class="description"><?php _e( 'Provide a message to tell your buyers what your newsletter is about. For example, you might want to say something like "Yes! I would like to subscribe to:"' , 'cart66' ); ?></p>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"><?php _e('Double Opt-In', 'cart66'); ?></th>
              <td>
                <input type="radio" name="mailchimp_doubleoptin" id="mailchimp_doubleoptin" value="optin" <?php echo (Cart66Setting::getValue('mailchimp_doubleoptin') == 'optin' || !Cart66Setting::getValue('mailchimp_doubleoptin')) ? 'checked="checked" ' : '' ?>/>
                <label for="mailchimp_doubleoptin"><?php _e( 'Send a Double Opt-In email' , 'cart66' ); ?></label>
                <input type="radio" name="mailchimp_doubleoptin" id="mailchimp_nooptin" value="no-optin" <?php echo Cart66Setting::getValue('mailchimp_doubleoptin') == 'no-optin' ? 'checked="checked" ' : '' ?>/>
                <label for="mailchimp_nooptin"><?php _e( 'Don\'t send a Double Opt-In email' , 'cart66' ); ?>
                <p class="description"><?php _e( 'Send a double opt-in confirmation message.', 'cart66'); ?> <strong><?php _e('Abusing this may cause your account to be suspended', 'cart66'); ?>.</strong> <a href="http://blog.mailchimp.com/opt-in-vs-confirmed-opt-in-vs-double-opt-in/" target="blank"><?php _e( 'Read more about Opt-Ins' , 'cart66' ); ?></a></p>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"><?php _e('Show Lists', 'cart66'); ?></th>
              <td>
                <?php
                  $mcLists = false;
                  if($mailChimpKey = Cart66Setting::getValue('mailchimp_apikey')) {
                    $mc = new Cart66MailChimp($mailChimpKey);
                    $mcLists = $mc->getLists();
                  }
                
                  if(is_array($mcLists)){
                    $mcSavedListIds = array();
                    if($mcSavedLists = Cart66Setting::getValue('mailchimp_list_ids')) {
                      $mcSavedListIds = explode('~', $mcSavedLists);
                    }
                  
                    foreach ($mcLists as $list){
                      $checked = '';
                      $val = $list['id'] . '::' . $list['name'];
                      Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] looking for: $val in " . print_r($mcSavedListIds, true));
                      if(in_array($val, $mcSavedListIds)) {
                        $checked = 'checked="checked"';
                      } ?>
                      <input type="hidden" name="mailchimp_list_ids[]" />
                      <input type="checkbox" name="mailchimp_list_ids[]" value="<?php echo $val; ?>" <?php echo $checked; ?>> <?php echo $list['name']; ?> - <?php echo $list['stats']['member_count']; ?> <?php _e('Members', 'cart66'); ?><br />
                      <?php
                    }
                  }
                  else { ?>
                    <p class="description"><?php _e('You do not yet have any lists', 'cart66'); ?><br><?php _e('If you have entered your credentials above and are not seeing any lists, try refreshing this page.', 'cart66'); ?><?php echo $mcLists; ?></p>
                  <?php }
                ?>
              </td>
            </tr>
          </tbody>
        </table>
      <?php else: ?>
        <p class="description"><a href="http://eepurl.com/dtQBb" target="_blank">MailChimp</a> <?php _e('helps you design email newsletters, share them on social networks, integrate with services you already use, and track your results. It\'s like your own personal publishing platform', 'cart66'); ?>.</p>
        <p class="description"><?php _e( 'This feature is only available in', 'cart66'); ?> <a href="http://cart66.com"><?php _e('Cart66 Professional', 'cart66'); ?></a>.</p>
      <?php endif; ?>
    </div>
    <div id="integrations-idevaffiliate" class="pane">
      <a href="#" target="_blank" style="float:right;"><img src="https://cart66.com/images/integrations/idev_logo.png" align="left" alt="iDevAffiliate"></a>
      <h3><?php _e('iDevAffiliate', 'cart66'); ?></h3>
      <?php if(CART66_PRO): ?>
        <p class="description"><?php _e( 'Configure your iDevAffiliate account information so Cart66 can award commissions to your affiliates.' , 'cart66' ); ?></p>
        <table class="form-table">
          <tbody>
            <tr valign="top">
              <th scope="row"><?php _e('iDevAffiliate URL', 'cart66'); ?></th>
              <td>
                <input type="text" name="idevaff_url" id="idevaff_url" class="regular-text" value="<?php echo Cart66Setting::getValue('idevaff_url'); ?>" />
                <p class="description"><?php _e( 'Copy and paste your iDevAffiliate "3rd Party Affiliate Call" URL. It will looks like' , 'cart66' ); ?>:<br/>
                  http://www.yoursite.com/idevaffiliate/sale.php?profile=72198&amp;idev_saleamt=XXX&amp;idev_ordernum=XXX<br/>
                  <?php _e( 'Be sure to leave the XXX\'s in place and Cart66 will replace the XXX\'s with the appropriate values for each sale.' , 'cart66' ); ?>
                  <?php if(Cart66Setting::getValue('idevaff_url')): ?>
                    <br/><br/><em><?php _e( 'Note: To disable iDevAffiliate integration, simply delete this URL and click Save.' , 'cart66' ); ?></em>
                  <?php endif; ?>
                </p>
              </td>
            </tr>
            <tr valign="top">
              <th scope="row"><?php _e('Coupon Code Tracking', 'cart66'); ?></th>
              <td>
                <input type="radio" name="idev_coupon_codes" id="idev_coupon_codes_yes" value="1" <?php echo (Cart66Setting::getValue('idev_coupon_codes') == 1) ? 'checked="checked" ' : ''; ?>/>
                <label for="idev_coupon_codes_yes"><?php _e('Yes', 'cart66'); ?></label>
                <input type="radio" name="idev_coupon_codes" id="idev_coupon_codes_no" value="" <?php echo (Cart66Setting::getValue('idev_coupon_codes') != 1) ? 'checked="checked" ' : ''; ?>/>
                <label for="idev_coupon_codes_no"><?php _e('No', 'cart66'); ?></label>
                <p class="description"><?php _e('Set this to yes to enable sending coupon codes to iDevAffiliate for commission tracking.', 'cart66'); ?></p>
              </td>
            </tr>
          </tbody>
        </table>
      <?php else: ?>
        <p class="description"><a href="http://www.idevdirect.com/14717499.html">iDevAffiliate</a> <?php _e( 'is The Industry Leader in self managed affiliate program software. Started in 1999, iDevAffiliate is the original in self managed affiliate software! iDevAffiliate was hand coded from scratch by the same team that provides their technical support! iDevAffilaite is also the affilate software that runs our' , 'cart66' ); ?> <a href="http://affiliates.reality66.com/idevaffiliate/"><?php _e('Cart66 Affiliate Program', 'cart66'); ?></a>.</p>
        <p class="description"><?php _e( 'This feature is only available in', 'cart66'); ?> <a href="http://cart66.com"><?php _e('Cart66 Professional', 'cart66'); ?></a>.</p>
      <?php endif; ?>
    </div>
    <div id="integrations-zendesk" class="pane">
      <a href="#" target="_blank" style="float:right;"><img src="https://cart66.com/images/integrations/Zendesk-logo.png" align="left" alt="Zendesk"></a>
      <h3><?php _e('Zendesk', 'cart66'); ?></h3>
      <?php if(CART66_PRO): ?>
      <p class="description"><?php _e( 'Configure your Zendesk account information to enable remote authentication.' , 'cart66' ); ?></p>
      <table class="form-table">
        <tbody>
          <tr valign="top">
            <th scope="row"><?php _e('Token', 'cart66'); ?></th>
            <td>
              <input type="text" name="zendesk_token" id="zendesk_token" class="regular-text" value="<?php echo Cart66Setting::getValue('zendesk_token'); ?>" />
              <p class="description"><?php _e( 'Look in your Zendesk account under "Settings > Security > Authentication > Single Sign-On" for the Authentication Token.' , 'cart66' ); ?></p>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row"><?php _e('Prefix', 'cart66'); ?></th>
            <td>
              <input type="text" name="zendesk_prefix" id="zendesk_prefix" class="regular-text" value="<?php echo Cart66Setting::getValue('zendesk_prefix'); ?>" />
              <p class="description"><?php _e( 'The prefix is the first part of your zendesk account URL. For example, if your Zendesk URL is', 'cart66'); ?> http://<strong style="font-size: 14px;">mycompany</strong>.zendesk.com <?php _e('Then your prefix is', 'cart66'); ?> mycompany.</p>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row"><?php _e('JWT Authentication', 'cart66'); ?></th>
            <td>
              <input type="radio" name="zendesk_jwt" id="zendesk_jwt_yes" value="1" <?php echo (Cart66Setting::getValue('zendesk_jwt') == 1) ? 'checked="checked" ' : ''; ?>/>
              <label for="zendesk_jwt_yes"><?php _e('Yes', 'cart66'); ?></label>
              <input type="radio" name="zendesk_jwt" id="zendesk_jwt_no" value="" <?php echo (Cart66Setting::getValue('zendesk_jwt') != 1) ? 'checked="checked" ' : ''; ?>/>
              <label for="zendesk_jwt_no"><?php _e('No', 'cart66'); ?></label>
              <p class="description"><?php _e('Set this to yes if you want to use the JWT (JSON Web Token) authentication method.', 'cart66'); ?></p>
            </td>
          </tr>
        </tbody>
      </table>
      <?php else: ?>
      <p class="description"><a href="http://www.zendesk.com">Zendesk</a> <?php _e( 'is the industry leader in web-based help desk software with an elegant support ticket system and a self-service customer support platform. Agile, smart, and convenient.' , 'cart66' ); ?></p>
      <p class="description"><?php _e( 'This feature is only available in', 'cart66'); ?> <a href="http://cart66.com"><?php _e('Cart66 Professional', 'cart66'); ?></a>.</p>
      <?php endif; ?>
    </div>
    <table class="form-table submit-table">
      <tbody>
        <tr valign="top">
          <th scope="row">
            <?php submit_button(); ?>
          </th>
          <td></td>
        </tr>
      </tbody>
    </table>
  </form>
</div>
<script type="text/javascript">
  (function($){
    $(document).ready(function(){
      $('#cart66-inner-tabs div.pane').hide();
      $('#cart66-inner-tabs div#<?php echo $tab; ?>').show();
      $('#cart66-inner-tabs ul li a.<?php echo $tab; ?>').addClass('current');
      
      if($('#integrations-main_settings').attr('id') == '<?php echo $tab; ?>') {
        $('.submit-table').hide();
      }
      
      <?php if(!CART66_PRO): ?>
      if($('#integrations-zendesk').attr('id') == '<?php echo $tab; ?>' || $('#integrations-idevaffiliate').attr('id') == '<?php echo $tab; ?>' || $('#integrations-mailchimp').attr('id') == '<?php echo $tab; ?>') {
        $('.submit-table').hide();
      }
      <?php endif; ?>
      
      $('#cart66-inner-tabs ul li a').click(function(){
        $('#cart66-inner-tabs ul li a').removeClass('current');
        $(this).addClass('current');
        var currentTab = $(this).attr('href');
        $('#cart66-inner-tabs div.pane').hide();
        $(currentTab).show();
        $('.submit-table').show();
        if(currentTab == '#integrations-main_settings') {
          $('.submit-table').hide();
        }
        return false;
      });
      
      $("#use_other_analytics_plugin").val("<?php echo Cart66Setting::getValue('use_other_analytics_plugin'); ?>");
      setGoogleAnalytics();
      $("#use_other_analytics_plugin").change(function() {
         setGoogleAnalytics();
      });
    })
  })(jQuery);
  $jq = jQuery.noConflict();
  function setGoogleAnalytics() {
    $jq(".google_analytics_product_id").hide();
    if($jq("#use_other_analytics_plugin :selected").val() == ""){
      $jq(".google_analytics_product_id").show();
    }
  }
</script>