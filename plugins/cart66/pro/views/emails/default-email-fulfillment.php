<?php
$subject = Cart66Setting::getValue('fulfillment_subject');
$order = $data[0];
$html = $data[1];
$test = $data[2];

if(!$test) {
  $id = $data[3];
  $orderFulfillment = new Cart66OrderFulfillment($id);
  if($html) {
  ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <title><?php echo $subject; ?></title>
      <style type="text/css">
        span.yshortcuts { color:#000; background-color:none; border:none;}
        span.yshortcuts:hover,
        span.yshortcuts:active,
        span.yshortcuts:focus {color:#000; background-color:none; border:none;}
        @media only screen and (max-device-width: 480px) {
        }
        @media only screen and (min-device-width: 768px) and (max-device-width: 1024px)  {
        }
      </style>
    </head>
    <body style="font-family: Arial, Verdana, sans-serif;">
      <div id="body_style">
        <!-- Start Main Table -->
        <table width="100%" height="100%"  cellpadding="0" cellspacing="0" style="padding: 20px 0px 20px 0px" bgcolor="#ffffff">
          <tr align="center">
            <td>
              <!-- Start Header -->
              <table width="562" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="color: #333; font-weight:bold;  padding: 16px 0px 16px 14px; font-family: Arial, Verdana, sans-serif; ">
                <tr>
                  <td>
                    <span style="font-size: 20px; "><?php _e('Order Number', 'cart66'); ?>: <?php echo $order->trans_id; ?></span>
                  </td>
                  <td style="font-weight:normal;font-size: 11px;">
                    <span style="font-weight:bold;"><?php _e('Purchased', 'cart66'); ?></span>
                    <br><?php echo date(get_option('date_format'), strtotime($order->ordered_on)); ?>
                  </td>
                </tr>
              </table>
              <!-- End Header -->

              <!-- Start Ribbon -->
              <table cellpadding="0" cellspacing="0"  width="562"  bgcolor="#f9f9f9">
                <?php if($order->shipping_method != 'None'): ?>
                  <tr>
                    <td bgcolor="#f9f9f9" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 12px; color:#333;width:48%;text-align:left;vertical-align:top;" >
                      <?php if($order->hasShippingInfo()): ?>

                        <span style="text-transform: uppercase; font-size: 18px; font-weight: bold;"><?php _e('Shipping Information', 'cart66'); ?></span><br><br>
                        <span style="font-weight:bold;">
                          <?php echo $order->ship_first_name ?> <?php echo $order->ship_last_name ?><br/>
                          <?php echo $order->ship_address ?><br/>

                          <?php if(!empty($order->ship_address2)): ?>
                            <?php echo $order->ship_address2 ?><br/>
                          <?php endif; ?>

                          <?php if($order->ship_city != ''): ?>
                            <?php echo $order->ship_city ?> <?php echo $order->ship_state ?>, <?php echo $order->ship_zip ?><br/>
                          <?php endif; ?>

                          <?php if(!empty($order->ship_country)): ?>
                            <?php echo $order->ship_country ?><br/>
                          <?php endif; ?>
                          <?php if(is_array($additional_fields = maybe_unserialize($order->additional_fields)) && isset($additional_fields['shipping'])): ?><br />
                            <?php foreach($additional_fields['shipping'] as $af): ?>
                              <?php echo $af['label']; ?>: <?php echo $af['value']; ?><br />
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </span>
                        <br/><em><?php _e( 'Delivery via' , 'cart66' ); ?>: <?php echo $order->shipping_method ?></em><br/>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endif; ?>
                <tr>
                  <td bgcolor="#f9f9f9" width="562" height="13">
                  </td>
                </tr>
              </table>
              <!-- End Ribbon -->

              <!-- Start Products Table  -->
              <table cellpadding="0" cellspacing="0">
                <tr>
                  <td bgcolor="#f9f9f9" height="20" width="562"></td>
                </tr>
              </table>
              <!-- outlook fix cuz it can't handle margins on tables -->
              <div style="margin-bottom:1.5714em;margin-top:1.5714em;">
              <table cellpadding="0" cellspacing="0" width="562" bgcolor="#FFFFFF" style="border:1px solid #dfdfdf;background-color:#f9f9f9;-webkit-border-radius:3px;-moz-border-radius:3px;-ms-border-radius:3px;-o-border-radius:3px;border-radius:3px;border-spacing:0;clear:both;">
                <tr>
                  <td style="text-transform:uppercase;color:#333;background-color:#f1f1f1;border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;padding:7px 7px 8px;text-align:left;line-height:14px;font-size:14px;font-weight:bold;">
                    <?php _e('Product', 'cart66'); ?>
                  </td>
                  <td style="text-transform:uppercase;color:#333;background-color:#f1f1f1;border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;padding:7px 7px 8px;text-align:left;line-height:14px;font-size:14px;font-weight:bold;text-align:center;">
                    <?php _e('Quantity', 'cart66'); ?>
                  </td>
                </tr>
                
                <?php foreach($order->getItems() as $item): ?>
                  <?php
                  $product = new Cart66Product();
                  $product->load($item->product_id);
                  
                  $fulfillmentProducts = explode(',', $orderFulfillment->products);
                  foreach($fulfillmentProducts as $prod) {
                    Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] product: $prod item id: " . print_r($data[3], true));
                    if($prod == $item->product_id) { ?>
                      <tr>
                        <td style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top">
                          <?php if(Cart66Setting::getValue('display_item_number_receipt')): ?>
                            <?php echo $item->item_number; ?>
                          <?php endif; ?>
                          <b><?php echo $item->description; ?></b>
                        </td>
                        <td style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:center;">
                          <?php echo $item->quantity; ?>
                        </td>
                      </tr>
                      <?php
                        if(!empty($item->form_entry_ids)) {
                          $entries = explode(',', $item->form_entry_ids);
                          foreach($entries as $entryId) {
                            if(class_exists('RGFormsModel')) {
                              if(RGFormsModel::get_lead($entryId)) {
                                echo "<tr><td colspan='2'><div class='Cart66GravityFormDisplay'>" . Cart66GravityReader::displayGravityForm($entryId, false, true) . "</div></td></tr>";
                              }
                            }
                          }
                        }
                      ?>
                    <?php }
                  }
                  
                  ?>
                <?php endforeach; ?>
              </table>
              <!-- End Products Table -->
              </div>
              
              <!-- Start Footer -->
              <table cellpadding="0" cellspacing="0" width="562" height="100">
                <tr>
                  <td bgcolor="#f9f9f9" height="20">
                  </td>
                </tr>
                <tr>
                  <?php
                  $receiptPage = get_page_by_path('store/receipt');
                  $link = get_permalink($receiptPage->ID);
                  if(strstr($link,"?")){
                    $link .= '&ouid=' . $order->ouid;
                  }
                  else{
                    $link .= '?ouid=' . $order->ouid;
                  }
                  ?>
                  <td bgcolor="#f9f9f9" style="font-size: 11px; font-family: Arial, Verdana, sans-serif; color:#333; padding-left: 15px; width:350px;">
                    <span style="text-transform: uppercase; font-size: 16px; font-weight: bold;"><?php _e('View Receipt Online', 'cart66'); ?></span><br /><br />
                    <?php _e('Click the link below to view your receipt online', 'cart66'); ?>.<br />
                    <a href="<?php echo $link; ?>" style="color:#333"><?php echo $link; ?></a><br />
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#f9f9f9" height="20">
                  </td>
                </tr>
              </table>
              <!-- End Footer -->
              
            </td>
          </tr>
        </table>
        <!-- End Main Table -->
      </div>
    </body>
    </html>
  <?php
  }
  else {
    $msg = __("ORDER NUMBER","cart66") . ": " . $order->trans_id . "\n\n";
    foreach($order->getItems() as $item){
      $product = new Cart66Product();
      $product->load($item->product_id);
      
      $fulfillmentProducts = explode(',', $orderFulfillment->products);
      foreach($fulfillmentProducts as $prod) {
        if($prod == $item->product_id) {
          if(Cart66Setting::getValue('display_item_number_receipt')) {
            $msg .= $item->item_number . ' ';
          }
          $msg .= "<b>" . $item->description . "</b>\n";
          $msg .= __('Quantity: ', 'cart66') . $item->quantity . "\n";
          if(!empty($item->form_entry_ids)) {
            $entries = explode(',', $item->form_entry_ids);
            foreach($entries as $entryId) {
              if(class_exists('RGFormsModel')) {
                if(RGFormsModel::get_lead($entryId)) {
                  echo Cart66GravityReader::displayGravityForm($entryId, true, true);
                }
              }
            }
          }
        }
      }
    }
    if($order->shipping_method != 'None') {
      if($order->hasShippingInfo()) {
        $msg .= "\n\n" . __("SHIPPING INFORMATION","cart66") . "\n\n";
        $msg .= $order->ship_first_name . ' ' . $order->ship_last_name . "\n";
        $msg .= $order->ship_address . "\n";
        if(!empty($order->ship_address2)) {
          $msg .= $order->ship_address2 . "\n";
        }
        if($order->ship_city != '') {
          $msg .= $order->ship_city . ' ' . $order->ship_state . ', ' . $order->ship_zip . "\n";
        }
        if(!empty($order->ship_country)) {
          $msg .= $order->ship_country . "\n";
        }
        if(is_array($additional_fields = maybe_unserialize($order->additional_fields)) && isset($additional_fields['shipping'])) {
          foreach($additional_fields['shipping'] as $af) {
            $msg .= html_entity_decode($af['label']) . ': ' . $af['value'] . "\n";
          }
        }
        $msg .= "\n" . __('Delivery via', 'cart66') . ': ' . $order->shipping_method . "\n\n";
      }
    }
    echo $msg;
  }
}
else {
  if($html) {
  ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <title><?php _e('TEST - Email Fulfillment', 'cart66'); ?></title>
      <style type="text/css">
        span.yshortcuts { color:#000; background-color:none; border:none;}
        span.yshortcuts:hover,
        span.yshortcuts:active,
        span.yshortcuts:focus {color:#000; background-color:none; border:none;}
        @media only screen and (max-device-width: 480px) {
        }
        @media only screen and (min-device-width: 768px) and (max-device-width: 1024px)  {
        }
      </style>
    </head>
    <body style="font-family: Arial, Verdana, sans-serif;">
      <div id="body_style">
        <!-- Start Main Table -->
        <table width="100%" height="100%"  cellpadding="0" cellspacing="0" style="padding: 20px 0px 20px 0px" bgcolor="#ffffff">
          <tr align="center">
            <td>
              <!-- Start Header -->
              <table width="562" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="color: #333; font-weight:bold;  padding: 16px 0px 16px 14px; font-family: Arial, Verdana, sans-serif; ">
                <tr>
                  <td>
                    <span style="font-size: 20px; "><?php _e('Order Number', 'cart66'); ?>: ABC123DEF456GHI789J0</span>
                  </td>
                  <td style="font-weight:normal;font-size: 11px;">
                    <span style="font-weight:bold;"><?php _e('Purchased', 'cart66'); ?></span>
                    <br><?php echo date(get_option('date_format'), time()); ?>
                  </td>
                </tr>
              </table>
              <!-- End Header -->

              <!-- Start Ribbon -->
              <table cellpadding="0" cellspacing="0"  width="562"  bgcolor="#f9f9f9">
                <tr>
                  <td bgcolor="#f9f9f9" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 12px; color:#333;text-align:left;vertical-align:top;">

                    <span style="text-transform: uppercase; font-size: 18px; font-weight: bold;"><?php _e('Shipping Information', 'cart66'); ?></span><br><br>
                    <span style="font-weight:bold;">
                      <?php _e('John Doe', 'cart66'); ?><br />
                      <?php _e('1234 My Street', 'cart66'); ?><br />
                      <?php _e('Apt. 2A', 'cart66'); ?><br />
                      <?php _e('Grandville, NE 69835', 'cart66'); ?><br />
                      <?php _e('United States', 'cart66'); ?><br />
                    </span>
                    <br/><em><?php _e( 'Delivery via' , 'cart66' ); ?>: <?php _e('FedEx Free', 'cart66'); ?></em><br/>
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#f9f9f9" width="562" height="13">
                  </td>
                </tr>
              </table>
              <!-- End Ribbon -->

              <!-- Start Products Table  -->
              <table cellpadding="0" cellspacing="0">
                <tr>
                  <td bgcolor="#FFFFFF" height="20" width="562"></td>
                </tr>
              </table>
              <div style="margin-bottom:1.5714em;margin-top:1.5714em;">
              <table cellpadding="0" cellspacing="0" width="562" bgcolor="#FFFFFF" style="border:1px solid #dfdfdf;background-color:#f9f9f9;-webkit-border-radius:3px;-moz-border-radius:3px;-ms-border-radius:3px;-o-border-radius:3px;border-radius:3px;border-spacing:0;clear:both;">
                <tr>
                  <td style="text-transform:uppercase;color:#333;background-color:#f1f1f1;border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;padding:7px 7px 8px;text-align:left;line-height:14px;font-size:14px;font-weight:bold;">
                    <?php _e('Product', 'cart66'); ?>
                  </td>
                  <td style="text-transform:uppercase;color:#333;background-color:#f1f1f1;border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;padding:7px 7px 8px;text-align:left;line-height:14px;font-size:14px;font-weight:bold;text-align:center;">
                    <?php _e('Quantity', 'cart66'); ?>
                  </td>
                </tr>
                
                <tr>
                  <td style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top">
                    <?php _e('Test Product', 'cart66'); ?>
                  </td>
                  <td style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:center;">
                    2
                  </td>
                </tr>
              </table>
              <!-- End Products Table -->
              </div>
              
              <!-- Start Footer -->
              <table cellpadding="0" cellspacing="0" width="562" height="100">
                <tr>
                  <td bgcolor="#f9f9f9" height="20">
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#f9f9f9" style="font-size: 11px; font-family: Arial, Verdana, sans-serif; color:#333; padding-left: 15px; width:350px;">
                    <span style="text-transform: uppercase; font-size: 16px; font-weight: bold;"><?php _e('View Receipt Online', 'cart66'); ?></span><br /><br />
                    <?php _e('Click the link below to view your receipt online', 'cart66'); ?>.<br />
                    <a href="http://yourstore.com/store/receipt/?ouid=1234567890" style="color:#333">http://yourstore.com/store/receipt/?ouid=1234567890</a><br />
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#f9f9f9" height="20">
                  </td>
                </tr>
              </table>
              <!-- End Footer -->
              
            </td>
          </tr>
        </table>
        <!-- End Main Table -->
      </div>
    </body>
    </html>
  <?php
  }
  else {
    $msg = __("This is a test fulfillment email with test information. Please create a template in your settings to get it to look exactly the way you want.", "cart66") . "\n\n";
    $msg .= __("ORDER NUMBER","cart66") . ": TEST-TRANSACTION-ID\n\n";
    $msg .= __("Item","cart66") . ": Test Product Name\n";
    $msg .= __("Quantity","cart66") . ": 3\n";
    $msg .= "\n\n" . __("SHIPPING INFORMATION","cart66") . "\n\n";
    $msg .= "FirstName LastName\n";
    $msg .= "1234 My Address\n";
    $msg .= "Apt. 1\n";
    $msg .= "My City, ST 00000\nUnited States\n";
    $msg .= "\n" . __("Delivery via","cart66") . ": UPS Ground\n";
    $msg .= "\n" . __("Phone","cart66") . ": (000) 000-0000\n";
    $msg .= __("Email","cart66") . ": testemail@aol.com\n";
    echo $msg;
  }
}