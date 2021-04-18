<?php
$order = $data[0];
$html = $data[1];
$test = $data[2];
$status = $data[3];

$subject = Cart66Setting::getValue($status . '_subject');

if(!$test) {
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
  <body style="background:#ececec;margin-top:0;margin-bottom:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0; font-family: Arial, Verdana, sans-serif;">
    <div id="body_style">
      <!-- Start Main Table -->
      <table width="100%" height="100%"  cellpadding="0" cellspacing="0" style="padding: 20px 0px 20px 0px" bgcolor="#ececec">
        <tr align="center">
          <td>
            <!-- Start Header -->
            <table width="562" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="color: #333; font-weight:bold;  padding: 16px 0px 16px 14px; font-family: Arial, Verdana, sans-serif; ">
              <tr>
                <td>
                  <span style="font-size: 20px; "><?php _e('Order Number', 'cart66'); ?>: <?php echo $order->trans_id; ?></span><br /><br />
                  <?php _e('Order Status', 'cart66'); ?>: <?php echo str_replace('_', ' ', strtoupper($status)); ?>
                </td>
                <td style="font-weight:normal;font-size: 11px;">
                  <span style="font-weight:bold;"><?php _e('Purchased', 'cart66'); ?></span>
                  <br><?php echo date(get_option('date_format'), strtotime($order->ordered_on)); ?>
                </td>
              </tr>
            </table>
            <!-- End Header -->
            
            <!-- Start Message Intro -->
            <?php if(Cart66Setting::getValue($status . '_message_intro')): ?>
              <table width="562" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 14px; color:#333;">
                <tr>
                  <td>
                    <?php echo Cart66Setting::getValue($status . '_html_email'); ?>
                  </td>
                </tr>
              </table>
            <?php endif; ?>
            <!-- End Message Intro -->

            <!-- Start Ribbon -->
            <table cellpadding="0" cellspacing="0"  width="562"  bgcolor="#f9f9f9">
              <tr>
                <td bgcolor="#f9f9f9" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 12px; color:#333;width:48%;text-align:left;vertical-align:top;" >
                  <span style="text-transform: uppercase; font-size: 18px; font-weight: bold;"><?php _e('Billing Information', 'cart66'); ?></span><br><br>
                  <span style="font-weight: bold;">
                    <?php echo $order->bill_first_name; ?> <?php echo $order->bill_last_name; ?><br />
                    <?php echo $order->bill_address; ?><br />
                    <?php
                    if(!empty($order->bill_address2)) {
                      echo $order->bill_address2 . '<br />';
                    }
                    ?>
                    <?php echo $order->bill_city; ?> <?php echo $order->bill_state; ?>, <?php echo $order->bill_zip; ?><br />
                    <?php echo $order->bill_country; ?><br />
                    <?php if(is_array($additional_fields = maybe_unserialize($order->additional_fields)) && isset($additional_fields['billing'])): ?><br />
                      <?php foreach($additional_fields['billing'] as $af): ?>
                        <?php echo $af['label']; ?>: <?php echo $af['value']; ?><br />
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </span>
                </td>
                <td bgcolor="#f9f9f9" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 12px; color:#333;width:51%;text-align:left;vertical-align:top;" >
                  <span style="text-transform: uppercase; font-size: 18px; font-weight: bold;"><?php _e('Contact Information', 'cart66'); ?></span><br><br>
                  <span style="font-weight: bold;">
                    <?php
                    if(!empty($order->phone)) {
                      $phone = Cart66Common::formatPhone($order->phone);
                      echo __('Phone', 'cart66') . ': ' . $phone . '<br />';
                    }
                    ?>
                    <?php _e( 'Email' , 'cart66' ); ?>: <?php echo $order->email ?><br/>
                    <?php _e( 'Date' , 'cart66' ); ?>: <?php echo date(get_option('date_format'), strtotime($order->ordered_on)) ?> <?php echo date(get_option('time_format'), strtotime($order->ordered_on)) ?><br /><br />
                    <?php if(is_array($additional_fields = maybe_unserialize($order->additional_fields)) && isset($additional_fields['payment'])): ?><br />
                      <?php foreach($additional_fields['payment'] as $af): ?>
                        <?php echo $af['label']; ?>: <?php echo $af['value']; ?><br />
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </span>
                </td>
              </tr>
              <?php if($order->shipping_method != 'None'): ?>
                <tr>
                  <td bgcolor="#f9f9f9" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 12px; color:#333;text-align:left;vertical-align:top;">
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
            <div style="margin-bottom:1.5714em;margin-top:1.5714em;">
            <!-- Start Products Table  -->
            <table cellpadding="0" cellspacing="0" width="562" bgcolor="#FFFFFF" style="border:1px solid #dfdfdf;background-color:#f9f9f9;-webkit-border-radius:3px;-moz-border-radius:3px;-ms-border-radius:3px;-o-border-radius:3px;border-radius:3px;border-spacing:0;clear:both;">
              <tr>
                <td style="text-transform:uppercase;color:#333;background-color:#f1f1f1;border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;padding:7px 7px 8px;text-align:left;line-height:14px;font-size:14px;font-weight:bold;">
                  <?php _e('Product', 'cart66'); ?>
                </td>
                <td style="text-transform:uppercase;color:#333;background-color:#f1f1f1;border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;padding:7px 7px 8px;text-align:left;line-height:14px;font-size:14px;font-weight:bold;text-align:center;">
                  <?php _e('Quantity', 'cart66'); ?>
                </td>
                <td style="text-transform:uppercase;color:#333;background-color:#f1f1f1;border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;padding:7px 7px 8px;text-align:left;line-height:14px;font-size:14px;font-weight:bold;text-align:right;">
                  <?php _e('Item Price', 'cart66'); ?>
                </td>
                <td style="text-transform:uppercase;color:#333;background-color:#f1f1f1;border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;padding:7px 7px 8px;text-align:left;line-height:14px;font-size:14px;font-weight:bold;text-align:right;">
                  <?php _e('Item Total', 'cart66'); ?>
                </td>
              </tr>
              <?php
              $hasDigital = false;
              ?>
              <?php foreach($order->getItems() as $item): ?>
                <?php
                $product = new Cart66Product();
                $product->load($item->product_id);
                if($hasDigital == false) {
                  $hasDigital = $product->isDigital();
                }
                $price = $item->product_price * $item->quantity;
                ?>
                <tr>
                  <td style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top">
                    <?php if(Cart66Setting::getValue('display_item_number_receipt')): ?>
                      <?php echo $item->item_number; ?>
                    <?php endif; ?>
                    <?php echo $item->description; ?>
                  </td>
                  <td style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:center;">
                    <?php echo $item->quantity; ?>
                  </td>
                  <td style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top; text-align:right;">
                    <?php echo Cart66Common::currency($item->product_price); ?>
                  </td>
                  <td style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top; text-align:right;">
                    <?php echo Cart66Common::currency($price); ?>
                  </td>
                </tr>
                <?php
                  if(!empty($item->form_entry_ids)) {
                    $entries = explode(',', $item->form_entry_ids);
                    foreach($entries as $entryId) {
                      if(class_exists('RGFormsModel')) {
                        if(RGFormsModel::get_lead($entryId)) {
                          echo "<tr><td colspan='4'><div class='Cart66GravityFormDisplay'>" . Cart66GravityReader::displayGravityForm($entryId, false, true) . "</div></td></tr>";
                        }
                      }
                    }
                  }
                ?>
              <?php endforeach; ?>

              <!-- Start Subtotal -->
              <tr>
                <td colspan="3" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                  <?php _e('Subtotal', 'cart66'); ?>
                </td>
                <td style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                  <?php echo Cart66Common::currency($order->subtotal); ?>
                </td>
              </tr>
              <!-- End Subtotal -->

              <?php if($order->shipping_method != 'None'): ?>
                <!-- Start Shipping -->
                <tr>
                  <td colspan="3" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                    <?php _e('Shipping', 'cart66'); ?>
                  </td>
                  <td style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                    <?php echo Cart66Common::currency($order->shipping); ?>
                  </td>
                </tr>
                <!-- End Shipping -->
              <?php endif; ?>

              <?php if($order->discount_amount > 0): ?>
                <!-- Start Coupon -->
                <tr>
                  <td colspan="3" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                    <?php _e('Discount', 'cart66'); ?>
                  </td>
                  <td style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                    -<?php echo Cart66Common::currency($order->discount_amount); ?>
                  </td>
                </tr>
                <!-- End Coupon -->
              <?php endif;?>

              <?php if($order->tax > 0): ?>
                <!-- Start Tax -->
                <tr>
                  <td colspan="3" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                    <?php _e('Tax', 'cart66'); ?>
                  </td>
                  <td style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                    <?php echo Cart66Common::currency($order->tax); ?>
                  </td>
                </tr>
                <!-- End Tax -->
              <?php endif; ?>

              <!-- COUPON & TAX -->

              <!-- Start Grand Total -->
              <tr>
                <td colspan="3" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                  <?php _e('Total', 'cart66'); ?>
                </td>
                <td style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                  <?php echo Cart66Common::currency($order->total); ?>
                </td>
              </tr>
              <!-- End Grand Total -->
            </table>
            <!-- End Products Table -->
            </div>
            <!-- Start Footer -->
            <table cellpadding="0" cellspacing="0" width="562" height="100">
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
                  <?php if($hasDigital): ?>
                    <span style="text-transform: uppercase; font-size: 16px; font-weight: bold;"><?php _e('View Receipt Online and Download Order', 'cart66'); ?></span><br /><br />
                    <?php _e('Click the link below to view your receipt online and download your order', 'cart66'); ?>.<br />
                    <a href="<?php echo $link; ?>" style="color:#333"><?php echo $link; ?></a><br />
                  <?php else: ?>
                    <span style="text-transform: uppercase; font-size: 16px; font-weight: bold;"><?php _e('View Receipt Online', 'cart66'); ?></span><br /><br />
                    <?php _e('Click the link below to view your receipt online', 'cart66'); ?>.<br />
                    <a href="<?php echo $link; ?>" style="color:#333"><?php echo $link; ?></a><br />
                  <?php endif; ?>
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
    $msg .= __("Order Status", "cart66") . ": " . str_replace('_', ' ', strtoupper($status)) . "\n\n";
    $hasDigital = false;
    $product = new Cart66Product();
    foreach($order->getItems() as $item) {
      $product->load($item->product_id);
      if($hasDigital == false) {
        $hasDigital = $product->isDigital();
      }
      $price = $item->product_price * $item->quantity;
      // $msg .= "Item: " . $item->item_number . ' ' . $item->description . "\n";
      $msg .= __("Item","cart66") . ": ";
      if(Cart66Setting::getValue('display_item_number_receipt')) {
        $msg .= $item->item_number . ' ';
      }
      $msg .= $item->description . "\n";
      if($item->quantity > 1) {
        $msg .= __("Quantity","cart66") . ": " . $item->quantity . "\n";
      }
      $msg .= __("Item Price","cart66") . ": " . Cart66Common::currency($item->product_price, false) . "\n";
      $msg .= __("Item Total","cart66") . ": " . Cart66Common::currency($item->product_price * $item->quantity, false) . "\n\n";
      
      if($product->isGravityProduct()) {
        $msg .= Cart66GravityReader::displayGravityForm($item->form_entry_ids, true);
      }
    }
    
    if($order->shipping_method != 'None' && $order->shipping_method != 'Download') {
      $msg .= __("Shipping","cart66") . ": " . Cart66Common::currency($order->shipping, false) . "\n";
    }
    
    if(!empty($order->coupon) && $order->coupon != 'none') {
      $msg .= __("Coupon","cart66") . ": " . $order->coupon . "\n";
    }
    
    if($order->tax > 0) {
      $msg .= __("Tax","cart66") . ": " . Cart66Common::currency($order->tax, false) . "\n";
    }
    
    $msg .= "\n" . __("TOTAL","cart66") . ": " . Cart66Common::currency($order->total, false) . "\n";
    
    if($order->shipping_method != 'None' && $order->shipping_method != 'Download') {
      $msg .= "\n\n" . __("SHIPPING INFORMATION","cart66") . "\n\n";
      
      $msg .= $order->ship_first_name . ' ' . $order->ship_last_name . "\n";
      $msg .= $order->ship_address . "\n";
      if(!empty($order->ship_address2)) {
        $msg .= $order->ship_address2 . "\n";
      }
      $msg .= $order->ship_city . ' ' . $order->ship_state . ' ' . $order->ship_zip . "\n" . $order->ship_country . "\n";
      if(is_array($additional_fields = maybe_unserialize($order->additional_fields)) && isset($additional_fields['shipping'])) {
        foreach($additional_fields['shipping'] as $af) {
          $msg .= html_entity_decode($af['label']) . ': ' . $af['value'] . "\n";
        }
      }
      $msg .= "\n" . __("Delivery via","cart66") . ": " . $order->shipping_method . "\n";
    }
    
    $msg .= "\n\n" . __("BILLING INFORMATION","cart66") . "\n\n";
    
    $msg .= $order->bill_first_name . ' ' . $order->bill_last_name . "\n";
    $msg .= $order->bill_address . "\n";
    if(!empty($order->bill_address2)) {
      $msg .= $order->bill_address2 . "\n";
    }
    $msg .= $order->bill_city . ' ' . $order->bill_state . ' ' . $order->bill_zip . "\n" . $order->bill_country . "\n";
    if(is_array($additional_fields = maybe_unserialize($order->additional_fields)) && isset($additional_fields['billing'])) {
      foreach($additional_fields['billing'] as $af) {
        $msg .= html_entity_decode($af['label']) . ': ' . $af['value'] . "\n";
      }
    }
    if(!empty($order->phone)) {
      $phone = Cart66Common::formatPhone($order->phone);
      $msg .= "\n" . __("Phone","cart66") . ": $phone\n";
    }
    
    if(!empty($order->email)) {
      $msg .= __("Email","cart66") . ': ' . $order->email . "\n";
    }
    if(is_array($additional_fields = maybe_unserialize($order->additional_fields)) && isset($additional_fields['payment'])) {
      foreach($additional_fields['payment'] as $af) {
        $msg .= html_entity_decode($af['label']) . ': ' . $af['value'] . "\n";
      }
    }
    $receiptPage = get_page_by_path('store/receipt');
    $link = get_permalink($receiptPage->ID);
    if(strstr($link,"?")){
      $link .= '&ouid=' . $order->ouid;
    }
    else{
      $link .= '?ouid=' . $order->ouid;
    }
    
    if($hasDigital) {
      $msg .= "\n" . __('DOWNLOAD LINK','cart66') . "\n" . __('Click the link below to download your order.','cart66') . "\n$link";
    }
    else {
      $msg .= "\n" . __('VIEW RECEIPT ONLINE','cart66') . "\n" . __('Click the link below to view your receipt online.','cart66') . "\n$link";
    }
    
    $msgIntro = Cart66Setting::getValue($status . '_message_intro') ? Cart66Setting::getValue($status . '_plain_email') : '';
    $msg = $msgIntro . " \n----------------------------------\n\n" . $msg;
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
      <title><?php echo 'TEST - Email Fulfillment'; ?></title>
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
    <body style="background:#ececec;margin-top:0;margin-bottom:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0; font-family: Arial, Verdana, sans-serif;">
      <div id="body_style">
        <!-- Start Main Table -->
        <table width="100%" height="100%"  cellpadding="0" cellspacing="0" style="padding: 20px 0px 20px 0px" bgcolor="#ececec">
          <tr align="center">
            <td>
              <!-- Start Header -->
              <table width="562" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="color: #333; font-weight:bold;  padding: 16px 0px 16px 14px; font-family: Arial, 	 Verdana, sans-serif; ">
                <tr>
                  <td>
                    <span style="font-size: 20px; "><?php echo 'Order Number'; ?>: ABC123DEF456GHI789J0</span><br /><br />
                    <?php _e('Order Status', 'cart66'); ?>: <?php echo str_replace('_', ' ', strtoupper($status)); ?>
                  </td>
                  <td style="font-weight:normal;font-size: 11px;">
                    <span style="font-weight:bold;"><?php echo 'Purchased'; ?></span>
                    <br><?php echo date(get_option('date_format'), time()); ?>
                  </td>
                </tr>
              </table>
              <!-- End Header -->
              
              <!-- Start Message Intro -->
              <?php if(Cart66Setting::getValue($status . '_message_intro')): ?>
                <table width="562" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 14px; color:#333;">
                  <tr>
                    <td>
                      <?php echo Cart66Setting::getValue($status . '_html_email'); ?>
                    </td>
                  </tr>
                </table>
              <?php endif; ?>
              <!-- End Message Intro -->

              <!-- Start Ribbon -->
              <table cellpadding="0" cellspacing="0"  width="562"  bgcolor="#f9f9f9">
                <tr>
                  <td bgcolor="#f9f9f9" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 12px; color:#333;width:48%;text-align:left;" >
                    <span style="text-transform: uppercase; font-size: 18px; font-weight: bold;"><?php _e('Billing Information', 'cart66'); ?></span><br><br>
                    <span style="font-weight: bold;">
                      <?php _e('John Doe', 'cart66'); ?><br />
                      <?php _e('1234 My Street', 'cart66'); ?><br />
                      <?php _e('Apt. 2A', 'cart66'); ?><br />
                      <?php _e('Grandville, NE 69835', 'cart66'); ?><br />
                      <?php _e('United States', 'cart66'); ?><br />
                    </span>
                  </td>
                  <td bgcolor="#f9f9f9" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 12px; color:#333;width:51%;text-align:left;" >
                    <span style="text-transform: uppercase; font-size: 18px; font-weight: bold;"><?php _e('Contact Information', 'cart66'); ?></span><br><br>
                    <span style="font-weight: bold;">
                      <?php _e('Phone: (900) 123-6598', 'cart66'); ?>
                      <?php _e( 'Email' , 'cart66' ); ?>: <?php _e('johndoe@mydomain.min', 'cart66'); ?><br/>
                      <?php _e( 'Date' , 'cart66' ); ?>: <?php echo date(get_option('date_format'), time()) ?> <?php echo date(get_option('time_format'), time()) ?><br /><br />
                    </span>
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#f9f9f9" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 12px; color:#333;">

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
                  <td bgcolor="#f9f9f9" height="20" width="562"></td>
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
                  <td style="text-transform:uppercase;color:#333;background-color:#f1f1f1;border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;padding:7px 7px 8px;text-align:left;line-height:14px;font-size:14px;font-weight:bold;text-align:right;">
                    <?php _e('Item Price', 'cart66'); ?>
                  </td>
                  <td style="text-transform:uppercase;color:#333;background-color:#f1f1f1;border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;padding:7px 7px 8px;text-align:left;line-height:14px;font-size:14px;font-weight:bold;text-align:right;">
                    <?php _e('Item Total', 'cart66'); ?>
                  </td>
                </tr>
                
                <tr>
                  <td style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top">
                    <?php _e('Test Product', 'cart66'); ?>
                  </td>
                  <td style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:center;">
                    2
                  </td>
                  <td style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top; text-align:right;">
                    <?php echo Cart66Common::currency(25); ?>
                  </td>
                  <td style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top; text-align:right;">
                    <?php echo Cart66Common::currency(50); ?>
                  </td>
                </tr>

                <!-- Start Subtotal -->
                <tr>
                  <td colspan="3" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                    <?php _e('Subtotal', 'cart66'); ?>
                  </td>
                  <td style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                    <?php echo Cart66Common::currency(50); ?>
                  </td>
                </tr>
                <!-- End Subtotal -->

                <!-- Start Shipping -->
                <tr>
                  <td colspan="3" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                    <?php _e('Shipping', 'cart66'); ?>
                  </td>
                  <td style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                    <?php echo Cart66Common::currency(3); ?>
                  </td>
                </tr>
                <!-- End Shipping -->

                <!-- Start Coupon -->
                <tr>
                  <td colspan="3" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                    <?php _e('Discount', 'cart66'); ?>
                  </td>
                  <td style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                    -<?php echo Cart66Common::currency(4); ?>
                  </td>
                </tr>
                <!-- End Coupon -->

                <!-- Start Tax -->
                <tr>
                  <td colspan="3" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                    <?php _e('Tax', 'cart66'); ?>
                  </td>
                  <td style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                    <?php echo Cart66Common::currency(0.5); ?>
                  </td>
                </tr>
                <!-- End Tax -->

                <!-- Start Grand Total -->
                <tr>
                  <td colspan="3" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                    <?php _e('Total', 'cart66'); ?>
                  </td>
                  <td style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
                    <?php echo Cart66Common::currency(49.5); ?>
                  </td>
                </tr>
                <!-- End Grand Total -->
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
    $msg = __("This is a test email receipt with test information. Please create a template in your settings to get it to look exactly the way you want.", "cart66") . "\n\n";
    $msg .= __("ORDER NUMBER","cart66") . ": TEST-TRANSACTION-ID\n\n";
    $msg .= __("Order Status") . ": " . str_replace('_', ' ', strtoupper($status)) . "\n\n";
    $msg .= __("Item","cart66") . ": Test Product Name\n";
    $msg .= __("Quantity","cart66") . ": 3\n";
    $msg .= __("Item Price","cart66") . ": " . Cart66Common::currency(15.00, false) . "\n";
    $msg .= __("Item Total","cart66") . ": " . Cart66Common::currency(45.00, false) . "\n\n";
    $msg .= __("Shipping","cart66") . ": " . Cart66Common::currency(5.00, false) . "\n";
    $msg .= __("Coupon","cart66") . ": TEST-COUPON -(". Cart66Common::currency(15.00, false) . ")\n";
    $msg .= __("Tax","cart66") . ": " . Cart66Common::currency(1.87, false) . "\n";
    $msg .= "\n" . __("TOTAL","cart66") . ": " . Cart66Common::currency(36.87, false) . "\n";
    $msg .= "\n\n" . __("SHIPPING INFORMATION","cart66") . "\n\n";
    $msg .= "FirstName LastName\n";
    $msg .= "1234 My Address\n";
    $msg .= "Apt. 1\n";
    $msg .= "My City, ST 00000\nUnited States\n";
    $msg .= "\n" . __("Delivery via","cart66") . ": UPS Ground\n";
    $msg .= "\n\n" . __("BILLING INFORMATION","cart66") . "\n\n";
    $msg .= "FirstName LastName\n";
    $msg .= "1234 My Address\n";
    $msg .= "Apt. 1\n";
    $msg .= "My City, ST 00000\nUnited States\n";
    $msg .= "\n" . __("Phone","cart66") . ": (000) 000-0000\n";
    $msg .= __("Email","cart66") . ": testemail@aol.com\n";
    $msg .= "\n" . __('DOWNLOAD LINK','cart66') . "\n" . __('Click the link below to download your order.','cart66') . "\nhttp://yoursite.com/store/receipt/?ouid=randomstring\n";
    $msg .= "\n" . __('VIEW RECEIPT ONLINE','cart66') . "\n" . __('Click the link below to view your receipt online.','cart66') . "\nhttp://yoursite.com/store/receipt/?ouid=randomstring\n";
    $msgIntro = Cart66Setting::getValue($status . '_message_intro') ? Cart66Setting::getValue($status . '_plain_email') : '';
    $msg = $msgIntro . " \n----------------------------------\n\n" . $msg;
    echo $msg;
  }
}