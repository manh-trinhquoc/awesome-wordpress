<?php
$subject = Cart66Setting::getValue('followup_subject');
$order = $data[0];
$html = $data[1];
$test = $data[2];

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
                <tr>
                  <td bgcolor="#f9f9f9" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 12px; color:#333;text-align:left;" >
                    <span style="text-transform: uppercase; font-size: 18px; font-weight: bold;"><?php _e('Order Followup', 'cart66'); ?></span><br><br>
                  </td>
                </tr>
              </table>
              <!-- End Ribbon -->
              
              <!-- Start Message Intro -->
              <?php if(Cart66Setting::getValue('followup_message_intro')): ?>
                <table width="562" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 14px; color:#333;">
                  <tr>
                    <td>
                      <?php echo Cart66Setting::getValue('followup_html_email'); ?>
                    </td>
                  </tr>
                </table>
              <?php else: ?>
                <!-- End Message Intro -->
                <!-- Start Products Table  -->
                <table width="562" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 14px; color:#333;">
                  <tr>
                    <td>
                      <?php echo $order->bill_first_name; ?>,
                      <br /><br />
                      <?php _e('Thank you for your order!  This is a followup email for order number:', 'cart66'); ?> <?php echo $order->trans_id; ?>. <?php _e('If you have any questions, please feel free to contact us and let us know', 'cart66'); ?>.
                      <br /><br />
                    </td>
                  </tr>
                </table>
                <!-- End Products Table -->
              <?php endif; ?>
              
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
    $msg = __("Order Number","cart66") . ": " . $order->trans_id . "\n";
    $msg .= __("Purchased","cart66") . " " . date(get_option('date_format'), strtotime($order->ordered_on)) . "\n\n";
    $msg .= __("Order Followup","cart66") . "\n\n";
    if(Cart66Setting::getValue('followup_message_intro')) {
      $msg .= Cart66Setting::getValue('followup_plain_email') . "\n\n";
    }
    else {
      $msg .= $order->bill_first_name . ",\n\n";
      $msg .= __("Thank you for your order!  This is a followup email for order number:","cart66") . " " . $order->trans_id . __("If you have any questions, please feel free to contact us and let us know","cart66") . "\n\n";
    }
    $msg .= __("You can view your receipt online by clicking on the link below.","cart66") . "\n\n";
    $receiptPage = get_page_by_path('store/receipt');
    $link = get_permalink($receiptPage->ID);
    if(strstr($link,"?")){
      $link .= '&ouid=' . $order->ouid;
    }
    else{
      $link .= '?ouid=' . $order->ouid;
    }
    $msg .= __("View Receipt Online","cart66") . "\n";
    $msg .= $link;
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
      <title><?php _e('TEST - Email Followup', 'cart66'); ?></title>
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
                  <td bgcolor="#f9f9f9" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 12px; color:#333;">
                    <span style="text-transform: uppercase; font-size: 18px; font-weight: bold;"><?php _e('Order Followup', 'cart66'); ?></span><br><br>
                  </td>
                </tr>
              </table>
              <!-- End Ribbon -->

              <!-- Start Message Intro -->
              <?php if(Cart66Setting::getValue('followup_message_intro')): ?>
                <table width="562" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 14px; color:#333;">
                  <tr>
                    <td>
                      <?php echo Cart66Setting::getValue('followup_html_email'); ?>
                    </td>
                  </tr>
                </table>
              <?php else: ?>
                <!-- End Message Intro -->
                <!-- Start Products Table  -->
                <table width="562" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 14px; color:#333;">
                  <tr>
                    <td>
                      <?php _e('Dear John', 'cart66'); ?>,
                      <br /><br />
                      <?php _e('Thank you for your order!  This is a followup email for order number:', 'cart66'); ?> ABCDEFGHIJKLMNOP1234567. <?php _e('If you have any questions, please feel free to contact us and let us know', 'cart66'); ?>.  <br /><br />
                    </td>
                  </tr>
                </table>
                <!-- End Products Table -->
              <?php endif; ?>

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
    $msg = __("Order Number","cart66") . ": ABC123DEF456GHI789J0\n";
    $msg .= __("Purchased April 24, 2012","cart66") . "\n\n";
    $msg .= __("Order Followup","cart66") . "\n\n";
    if(Cart66Setting::getValue('followup_message_intro')) {
      $msg .= Cart66Setting::getValue('followup_plain_email') . "\n\n";
    }
    $msg .= __("John","cart66") . ",\n\n";
    $msg .= __("Thank you for your order!  This is a test followup email for an order.  You can set up the email to send out based on days, weeks or months.  You can use HTML or Plain text in the email in the advanced notifications tab inside your Cart66 settings.","cart66") . "\n\n";
    $msg .= __("You can add links and images as well. Visit Cart66 to find out more.","cart66") . "\n\n";
    $msg .= __("Sincerely,","cart66") . "\n\n";
    $msg .= __("Jane Doe","cart66") . "\n";
    $msg .= __("My Company","cart66") . "\n";
    echo $msg;
  }
}