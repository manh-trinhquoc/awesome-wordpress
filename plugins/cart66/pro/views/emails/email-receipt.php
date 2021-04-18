<?php
$order = $data['order'];
if($data['type'] == 'html'): ?>
  <table cellpadding="0" cellspacing="0"  class="email_receipt_table first_table" width="100%">
    <tr>
      <td class="billing_information_column"  width="50%" style="vertical-align:top;">
        <h3 class="billing_information_label"><?php _e('Billing Information', 'cart66'); ?></h3>
        <span class="billing_information_content">
          <?php echo $order->bill_first_name; ?> <?php echo $order->bill_last_name; ?><br />
          <?php echo $order->bill_address; ?><br />
          <?php
          if(!empty($order->bill_address2)) {
            echo $order->bill_address2 . '<br />';
          }
          ?>
          <?php echo $order->bill_city; ?> <?php echo $order->bill_state; ?><?php echo $order->bill_zip != null ? ',' : ''; ?> <?php echo $order->bill_zip; ?><br />
          <?php echo $order->bill_country; ?><br />
          <?php if(is_array($additional_fields = maybe_unserialize($order->additional_fields)) && isset($additional_fields['billing'])): ?><br />
            <?php foreach($additional_fields['billing'] as $af): ?>
              <?php echo $af['label']; ?>: <?php echo $af['value']; ?><br />
            <?php endforeach; ?>
          <?php endif; ?>
        </span>
      </td>
      <td class="contact_information_column" width="50%" style="vertical-align:top;">
        <h3 class="contact_information_label"><?php _e('Contact Information', 'cart66'); ?></h3>
        <span class="contact_information_content">
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
        <td class="shipping_information_column" style="vertical-align:top;">
          <?php if($order->hasShippingInfo()): ?>

            <h3 class="shipping_information_label"><?php _e('Shipping Information', 'cart66'); ?></h3>
            <span class="shipping_information_content">
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
            <br/><span class="delivery_method_content"><?php _e( 'Delivery via' , 'cart66' ); ?>: <?php echo $order->shipping_method ?></span><br/>
          <?php endif; ?>
        </td>
      </tr>
    <?php endif; ?>
    <tr>
      <td class="email_receipt_spacer_column first_table">
      </td>
    </tr>
  </table>
  <!-- End Ribbon -->

  <!-- Start Products Table  -->
  <table cellpadding="0" cellspacing="0" class="email_receipt_table second_table">
    <tr>
      <td class="email_receipt_spacer_column second_table"></td>
    </tr>
  </table>

  <table cellpadding="0" cellspacing="0" class="email_receipt_table third_table table-settings" style="border:1px solid #dfdfdf;background-color:#f9f9f9;-webkit-border-radius:3px;-moz-border-radius:3px;-ms-border-radius:3px;-o-border-radius:3px;border-radius:3px;border-spacing:0;width:100%;clear:both;margin-bottom:1.5714em;">
    <tr><td></td></tr>
    <tr>
      <td class="product_name_header" style="color:#333;background-color:#f1f1f1;border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;padding:7px 7px 8px;text-align:left;line-height:14px;font-size:14px;font-weight:bold;">
        <?php _e('Product', 'cart66'); ?>
      </td>
      <td class="quantity_header" style="color:#333;background-color:#f1f1f1;border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;padding:7px 7px 8px;text-align:center;line-height:14px;font-size:14px;font-weight:bold;">
        <?php _e('Quantity', 'cart66'); ?>
      </td>
      <td class="item_price_header" style="color:#333;background-color:#f1f1f1;border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;padding:7px 7px 8px;text-align:right;line-height:14px;font-size:14px;font-weight:bold;">
        <?php _e('Item Price', 'cart66'); ?>
      </td>
      <td class="item_total_header" style="color:#333;background-color:#f1f1f1;border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;padding:7px 7px 8px;text-align:right;line-height:14px;font-size:14px;font-weight:bold;">
        <?php _e('Item Total', 'cart66'); ?>
      </td>
    </tr>
    <?php $hasDigital = false; ?>
    <?php foreach($order->getItems() as $item): ?>
      <?php
      $product = new Cart66Product();
      $product->load($item->product_id);
      if($product->isDigital()) {
        $hasDigital = true;
        $receiptPage = get_page_by_path('store/receipt');
        $receiptPageLink = get_permalink($receiptPage);
        $receiptPageLink .= (strstr($receiptPageLink, '?')) ? '&duid=' . $item->duid : '?duid=' . $item->duid;
      }
      $price = $item->product_price * $item->quantity;
      ?>
      <tr>
        <td class="product_name" style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top">
          <?php if(Cart66Setting::getValue('display_item_number_receipt')): ?>
            <?php echo $item->item_number; ?>
          <?php endif; ?>
          <?php echo $item->description; ?>
          <?php if($product->isDigital()): ?>
            <br/><a href='<?php echo $receiptPageLink; ?>'><?php _e('Download', 'cart66'); ?></a>
          <?php endif; ?>
        </td>
        <td class="quantity" style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:center;">
          <?php echo $item->quantity; ?>
        </td>
        <td class="item_price" style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
          <?php echo Cart66Common::currency($item->product_price); ?>
        </td>
        <td class="item_total" style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
          <?php echo Cart66Common::currency($price); ?>
        </td>
      </tr>
      <?php
        if(!empty($item->form_entry_ids)) {
          $entries = explode(',', $item->form_entry_ids);
          foreach($entries as $entryId) {
            if(class_exists('RGFormsModel')) {
              if(RGFormsModel::get_lead($entryId)) {
                echo "<tr><td colspan='4' style=\"background-color:#ffffff;\"><div class='Cart66GravityFormDisplay'>" . Cart66GravityReader::displayGravityForm($entryId, false, true) . "</div></td></tr>";
              }
            }
          }
        }
      ?>
    <?php endforeach; ?>

    <!-- Start Subtotal -->
    <tr>
      <td colspan="3" class="subtotal_label" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
        <?php _e('Subtotal', 'cart66'); ?>
      </td>
      <td class="subtotal" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
        <?php echo Cart66Common::currency($order->subtotal); ?>
      </td>
    </tr>
    <!-- End Subtotal -->

    <?php if($order->shipping_method != 'None'): ?>
      <!-- Start Shipping -->
      <tr>
        <td colspan="3" class="shipping_label" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
          <?php _e('Shipping', 'cart66'); ?>
        </td>
        <td class="shipping" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
          <?php echo Cart66Common::currency($order->shipping); ?>
        </td>
      </tr>
      <!-- End Shipping -->
    <?php endif; ?>

    <?php if($order->discount_amount > 0): ?>
      <!-- Start Coupon -->
      <tr>
        <td colspan="3" class="discount_label" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
          <?php _e('Discount', 'cart66'); ?>
        </td>
        <td class="discount" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
          -<?php echo Cart66Common::currency($order->discount_amount); ?>
        </td>
      </tr>
      <!-- End Coupon -->
    <?php endif;?>

    <?php if($order->tax > 0): ?>
      <!-- Start Tax -->
      <tr>
        <td colspan="3" class="tax_label" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
          <?php _e('Tax', 'cart66'); ?>
        </td>
        <td class="tax" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
          <?php echo Cart66Common::currency($order->tax); ?>
        </td>
      </tr>
      <!-- End Tax -->
    <?php endif; ?>

    <!-- Start Grand Total -->
    <tr>
      <td colspan ="3" class="grand_total_label" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
        <?php _e('Total', 'cart66'); ?>
      </td>
      <td class="grand_total" style="color:#555;font-size:12px;padding:4px 7px;vertical-align:top;text-align:right;">
        <?php echo Cart66Common::currency($order->total); ?>
      </td>
    </tr>
    <!-- End Grand Total -->
  </table>
  <!-- End Products Table -->
<?php elseif($data['type'] == 'plain'):
  $product = new Cart66Product();
  $hasDigital = false;
  $msg = '';
  foreach($order->getItems() as $item) {
    $product->load($item->product_id);
    if($product->isDigital()) {
      $hasDigital = true;
    }
    $price = $item->product_price * $item->quantity;
    // echo "Item: " . $item->item_number . ' ' . $item->description . "\n";
    $msg .= __("Item","cart66") . ": ";
    if(Cart66Setting::getValue('display_item_number_receipt')) {
      $msg .= $item->item_number . ' ';
    }
    $msg .= $item->description . "\n";
    if($product->isDigital()) {
      $receiptPage = get_page_by_path('store/receipt');
      $receiptPageLink = get_permalink($receiptPage);
      $receiptPageLink .= (strstr($receiptPageLink, '?')) ? '&duid=' . $item->duid : '?duid=' . $item->duid;
      $msg .= $receiptPageLink . "\n\n";
    }
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
    $msg .= __("Shipping","cart66") . ": " . Cart66Common::currency($order->shipping) . "\n";
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
  $msg .= $order->bill_city . ' ' . $order->bill_state;
  $msg .= $order->bill_zip != null ? ', ' : ' ';
  $msg .= $order->bill_zip . "\n" . $order->bill_country . "\n";
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
  echo $msg;
endif; ?>