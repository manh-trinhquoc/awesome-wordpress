<?php
$subject = Cart66Setting::getValue('reminder_subject');
$subId = $data[0];
$html = $data[1];
$test = $data[2];

if(!$test) {
  if($html) {
    $sub = new Cart66AccountSubscription($subId);
    $account = new Cart66Account($sub->account_id);
    $reminderId = $data[3];
    $reminder = new Cart66MembershipReminders($reminderId);
  ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <title><?php echo $subject; ?></title>
      <style type="text/css">
        .ExternalClass {width:100%;}
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;}
        body {-webkit-text-size-adjust:none; -ms-text-size-adjust:none;}
        body {margin:0; padding:0;}
        table td {border-collapse:collapse;}
        p {margin:0; padding:0; margin-bottom:0;}
        h1, h2, h3, h4, h5, h6 {
          color: black; 
          line-height: 100%; 
        }
        a, a:link {
          color:#2A5DB0;
          text-decoration: underline;
        }
        body, #body_style {
          background:#ececec;
          font-family:Arial, Helvetica, sans-serif;
          font-size:12px;
        }
        span.yshortcuts { color:#000; background-color:none; border:none;}
        span.yshortcuts:hover,
        span.yshortcuts:active,
        span.yshortcuts:focus {color:#000; background-color:none; border:none;}
        a:visited { color: #3c96e2; text-decoration: none}
        a:focus   { color: #3c96e2; text-decoration: underline}
        a:hover   { color: #3c96e2; text-decoration: underline}
        .Cart66GravityFormDisplay {
          display: block;
        }
        table.form-table {
          border-bottom:1px solid #ccc;
          margin: 0;
          width: 100%;
        }
        table .entry-details tbody {
          padding: 0px;
          margin: 0px;
          background-color: white;
        }
        td .entry-view-field-name {
          font-weight: bold;
          background-color: 
          #EEE;
          margin: 0px;
          border: none;
          padding:0 15px;
        }
        td .entry-view-field-value {
          padding-left: 25px;
          border: none;
        }
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
                    <span style="font-size: 20px; "><?php _e('Membership', 'cart66'); ?>: <?php echo $sub->subscription_plan_name; ?></span>
                  </td>
                  <td style="font-weight:normal;font-size: 11px;">
                    <span style="font-weight:bold;"><?php _e('Purchased', 'cart66'); ?></span>
                    <br><?php echo date(get_option('date_format'), strtotime($sub->created_at)); ?>
                  </td>
                </tr>
              </table>
              <!-- End Header -->

              <!-- Start Ribbon -->
              <table cellpadding="0" cellspacing="0"  width="562"  bgcolor="#ccc">
                <tr>
                  <td bgcolor="#ccc" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 12px; color:#333;">
                    <span style="text-transform: uppercase; font-size: 18px; font-weight: bold;"><?php _e('Membership Reminder', 'cart66'); ?></span><br><br>
                  </td>
                </tr>
              </table>
              <!-- End Ribbon -->

              <!-- Start Products Table  -->
              <table cellpadding="0" cellspacing="0">
                <tr>
                  <td bgcolor="#FFFFFF" height="20" width="562">
                    <div style="padding:0 10px">
                    <br />
                    <?php echo $sub->billing_first_name . ' ' . $sub->billing_last_name; ?>,
                    <br /><br />
                    <?php _e('This is an email reminder letting you know that your account expires on', 'cart66'); ?> <?php echo date(get_option('date_format'), strtotime($sub->active_until)); ?>.
                    <br /><br />
                    <?php _e('Please log-in to your account and renew your subscription', 'cart66'); ?>.
                    <br /><br />
                    <?php _e('Sincerely', 'cart66'); ?>, 
                    <br /><br />
                    <?php echo $reminder->from_name; ?><br />
                    <?php echo $reminder->from_email; ?><br /><br /><br />
                    </div>
                  </td>
                </tr>
              </table>
              <!-- End Products Table -->

              <!-- Start Footer -->
              <table cellpadding="0" cellspacing="0" width="562" height="100">
                <tr>
                  <td bgcolor="#ccc" height="20">
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#ccc" style="font-size: 11px; font-family: Arial, Verdana, sans-serif; color:#333; padding-left: 15px; width:350px;">
                    <span style="text-transform: uppercase; font-size: 16px; font-weight: bold;"><?php _e('Renew your account by visiting our site.', 'cart66'); ?></span><br />
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#ccc" height="20">
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
    $sub = new Cart66AccountSubscription($subId);
    $account = new Cart66Account($sub->account_id);
    $reminderId = $data[3];
    $reminder = new Cart66MembershipReminders($reminderId);
    $msg = "Dear $sub->billing_first_name $sub->billing_last_name,\n\n";
    $msg .= "Your subscription : $sub->subscription_plan_name expires " . date(get_option('date_format'), strtotime($sub->active_until)) . ".\n\n";
    $msg .= "=========================\n\n";
    $msg .= "Please log-in to your account and renew your subscription.\n\n";
    $msg .= "Your login details:\n";
    $msg .= "Your User ID: $account->username\n";
    $msg .= "You can reset your password at the membership page\n\n";
    $msg .= "Thank you for your attention!";
    $msg .= "--\n\n";
    $msg .= "Best Regards,\n";
    $msg .= "$reminder->from_name\n";
    $msg .= "$reminder->from_email";
    echo $msg;
  }
}
else {
  if($html) {
    $reminderId = $data[3];
    $reminder = new Cart66MembershipReminders($reminderId);
  ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <title><?php _e('TEST - Membership Reminder', 'cart66'); ?></title>
      <style type="text/css">
        .ExternalClass {width:100%;}
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;}
        body {-webkit-text-size-adjust:none; -ms-text-size-adjust:none;}
        body {margin:0; padding:0;}
        table td {border-collapse:collapse;}
        p {margin:0; padding:0; margin-bottom:0;}
        h1, h2, h3, h4, h5, h6 {
          color: black; 
          line-height: 100%; 
        }
        a, a:link {
          color:#2A5DB0;
          text-decoration: underline;
        }
        body, #body_style {
          background:#ececec;
          font-family:Arial, Helvetica, sans-serif;
          font-size:12px;
        }
        span.yshortcuts { color:#000; background-color:none; border:none;}
        span.yshortcuts:hover,
        span.yshortcuts:active,
        span.yshortcuts:focus {color:#000; background-color:none; border:none;}
        a:visited { color: #3c96e2; text-decoration: none}
        a:focus   { color: #3c96e2; text-decoration: underline}
        a:hover   { color: #3c96e2; text-decoration: underline}
        .Cart66GravityFormDisplay {
          display: block;
        }
        table.form-table {
          border-bottom:1px solid #ccc;
          margin: 0;
          width: 100%;
        }
        table .entry-details tbody {
          padding: 0px;
          margin: 0px;
          background-color: white;
        }
        td .entry-view-field-name {
          font-weight: bold;
          background-color: 
          #EEE;
          margin: 0px;
          border: none;
          padding:0 15px;
        }
        td .entry-view-field-value {
          padding-left: 25px;
          border: none;
        }
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
                    <span style="font-size: 20px; "><?php _e('Membership: Test Membership', 'cart66'); ?></span>
                  </td>
                  <td style="font-weight:normal;font-size: 11px;">
                    <span style="font-weight:bold;"><?php _e('Purchased', 'cart66'); ?></span>
                    <br><?php echo date(get_option('date_format'), time()); ?>
                  </td>
                </tr>
              </table>
              <!-- End Header -->

              <!-- Start Ribbon -->
              <table cellpadding="0" cellspacing="0"  width="562"  bgcolor="#ccc">
                <tr>
                  <td bgcolor="#ccc" style="font-family: Arial, Verdana, sans-serif; padding: 10px 25px 0px 15px; font-size: 12px; color:#333;">
                    <span style="text-transform: uppercase; font-size: 18px; font-weight: bold;"><?php _e('Membership Reminder', 'cart66'); ?></span><br><br>
                  </td>
                </tr>
              </table>
              <!-- End Ribbon -->

              <!-- Start Products Table  -->
              <table cellpadding="0" cellspacing="0">
                <tr>
                  <td bgcolor="#FFFFFF" height="20" width="562">
                    <div style="padding:0 10px">
                    <br />
                    <?php _e('John Doe', 'cart66'); ?>,
                    <br /><br />
                    <?php _e('This is an email reminder letting you know that your account expires on', 'cart66'); ?> <?php echo date(get_option('date_format'), strtotime('+ 30 days', Cart66Common::localTs())); ?>.
                    <br /><br />
                    <?php _e('Please login to your account and renew if you want to continue to have access to our site', 'cart66'); ?>.
                    <br /><br />
                    <?php _e('Sincerely', 'cart66'); ?>, 
                    <br /><br />
                    <?php echo $reminder->from_name; ?><br />
                    <?php echo $reminder->from_email; ?><br /><br /><br />
                    </div>
                  </td>
                </tr>
              </table>
              <!-- End Products Table -->

              <!-- Start Footer -->
              <table cellpadding="0" cellspacing="0" width="562" height="100">
                <tr>
                  <td bgcolor="#ccc" height="20">
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#ccc" style="font-size: 11px; font-family: Arial, Verdana, sans-serif; color:#333; padding-left: 15px; width:350px;">
                    <span style="text-transform: uppercase; font-size: 16px; font-weight: bold;"><?php _e('Renew Your Account', 'cart66'); ?></span><br /><br />
                    <?php _e('Click the link below to login to your account and renew your membership', 'cart66'); ?>.<br />
                    <a href="http://yourstore.com/store/member-login" style="color:#333">http://yourstore.com/store/member-login</a><br />
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#ccc" height="20">
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
    $reminderId = $data[3];
    $reminder = new Cart66MembershipReminders($reminderId);
    $msg = "Dear Test User,\n\n";
    $msg .= "Your subscription : Default Membership expires " . date(get_option('date_format'), strtotime('+ 30 days', Cart66Common::localTs())) . ".\n\n";
    $msg .= "=========================\n\n";
    $msg .= "Please log-in to your account and renew your subscription.\n\n";
    $msg .= "Your login details:\n";
    $msg .= "Your User ID: username\n";
    $msg .= "You can reset your password at the membership page\n\n";
    $msg .= "Thank you for your attention!";
    $msg .= "--\n\n";
    $msg .= "Best Regards,\n";
    $msg .= "$reminder->from_name\n";
    $msg .= "$reminder->from_email";
  }
}