<?php
$order = $data['order'];
if($data['type'] == 'html'):
  if(isset($data['code']) && $data['code'] == 'fulfillment_products') { ?>
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
      <?php foreach($order->getItems() as $item): ?>
        <?php
        $product = new Cart66Product();
        $product->load($item->product_id);
        $price = $item->product_price * $item->quantity;
        $orderFulfillment = new Cart66OrderFulfillment($data['variable']);
        $fulfillmentProducts = explode(',', $orderFulfillment->products);
        foreach($fulfillmentProducts as $prod) {
          if($prod == $item->product_id) { ?>
            <tr>
              <td class="product_name" style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top">
                <b><?php echo $item->description; ?></b>
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
                      echo "<tr><td colspan='4'><div class='Cart66GravityFormDisplay'>" . Cart66GravityReader::displayGravityForm($entryId, false, true) . "</div></td></tr>";
                    }
                  }
                }
              }
            ?>
          <?php }
        } ?>
      <?php endforeach; ?>
    </table>
    <!-- End Products Table -->
  <?php }
  elseif(isset($data['code']) && $data['code'] == 'products') { ?>
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
          <td class="product_name" style="border-top:1px solid #fff;border-bottom:1px solid #dfdfdf;color:#555;font-size:12px;padding:4px 7px;vertical-align:top">
            <?php if(Cart66Setting::getValue('display_item_number_receipt')): ?>
              <?php echo $item->item_number; ?>
            <?php endif; ?>
            <?php echo $item->description; ?>
            <?php
              if($hasDigital) {
                $receiptPage = get_page_by_path('store/receipt');
                $receiptPageLink = get_permalink($receiptPage);
                $receiptPageLink .= (strstr($receiptPageLink, '?')) ? '&duid=' . $item->duid : '?duid=' . $item->duid; ?>
                <br/><a href='<?php echo $receiptPageLink; ?>'><?php _e('Download', 'cart66'); ?></a>
              <?php }
            ?>
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
                  echo "<tr><td colspan='4'><div class='Cart66GravityFormDisplay'>" . Cart66GravityReader::displayGravityForm($entryId, false, true) . "</div></td></tr>";
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
  <?php } ?>
<?php elseif($data['type'] == 'plain'):
  if(isset($data['code']) && $data['code'] == 'fulfillment_products') {
    $product = new Cart66Product();
    $hasDigital = false;
    foreach($order->getItems() as $item) {
      $product->load($item->product_id);
      $orderFulfillment = new Cart66OrderFulfillment($data['variable']);
      $fulfillmentProducts = explode(',', $orderFulfillment->products);
      foreach($fulfillmentProducts as $prod) {
        if($prod == $item->product_id) {
          if($hasDigital == false) {
            $hasDigital = $product->isDigital();
          }
          $price = $item->product_price * $item->quantity;
          // echo "Item: " . $item->item_number . ' ' . $item->description . "\n";
          echo __("Item","cart66") . ": ";
          if(Cart66Setting::getValue('display_item_number_receipt')) {
            echo $item->item_number . ' ';
          }
          echo $item->description . "\n";
          if($hasDigital) {
            $receiptPage = get_page_by_path('store/receipt');
            $receiptPageLink = get_permalink($receiptPage);
            $receiptPageLink .= (strstr($receiptPageLink, '?')) ? '&duid=' . $item->duid : '?duid=' . $item->duid;
            echo "\n" . $receiptPageLink . "\n";
          }
          if($item->quantity > 1) {
            echo __("Quantity","cart66") . ": " . $item->quantity . "\n";
          }
          echo __("Item Price","cart66") . ": " . Cart66Common::currency($item->product_price) . "\n";
          echo __("Item Total","cart66") . ": " . Cart66Common::currency($item->product_price * $item->quantity) . "\n\n";
    
          if($product->isGravityProduct()) {
            echo Cart66GravityReader::displayGravityForm($item->form_entry_ids, true);
          }
        }
      }
    }
    
    if($order->shipping_method != 'None' && $order->shipping_method != 'Download') {
      echo __("Shipping","cart66") . ": " . Cart66Common::currency($order->shipping) . "\n";
    }

    if(!empty($order->coupon) && $order->coupon != 'none') {
      echo __("Coupon","cart66") . ": " . $order->coupon . "\n";
    }

    if($order->tax > 0) {
      echo __("Tax","cart66") . ": " . Cart66Common::currency($order->tax, false) . "\n";
    }

    echo "\n" . __("TOTAL","cart66") . ": " . Cart66Common::currency($order->total, false) . "\n";
  }
  elseif(isset($data['code']) && $data['code'] == 'products') {
    $product = new Cart66Product();
    $hasDigital = false;
    foreach($order->getItems() as $item) {
      $product->load($item->product_id);
      if($hasDigital == false) {
        $hasDigital = $product->isDigital();
      }
      $price = $item->product_price * $item->quantity;
      // echo "Item: " . $item->item_number . ' ' . $item->description . "\n";
      echo __("Item","cart66") . ": " . $item->description . "\n";
      if($hasDigital) {
        $receiptPage = get_page_by_path('store/receipt');
        $receiptPageLink = get_permalink($receiptPage);
        $receiptPageLink .= (strstr($receiptPageLink, '?')) ? '&duid=' . $item->duid : '?duid=' . $item->duid;
        echo "\n" . $receiptPageLink . "\n";
      }
      if($item->quantity > 1) {
        echo __("Quantity","cart66") . ": " . $item->quantity . "\n";
      }
      echo __("Item Price","cart66") . ": " . Cart66Common::currency($item->product_price) . "\n";
      echo __("Item Total","cart66") . ": " . Cart66Common::currency($item->product_price * $item->quantity) . "\n\n";
    
      if($product->isGravityProduct()) {
        echo Cart66GravityReader::displayGravityForm($item->form_entry_ids, true);
      }
    }

    if($order->shipping_method != 'None' && $order->shipping_method != 'Download') {
      echo __("Shipping","cart66") . ": " . Cart66Common::currency($order->shipping) . "\n";
    }

    if(!empty($order->coupon) && $order->coupon != 'none') {
      echo __("Coupon","cart66") . ": " . $order->coupon . "\n";
    }

    if($order->tax > 0) {
      echo __("Tax","cart66") . ": " . Cart66Common::currency($order->tax, false) . "\n";
    }

    echo "\n" . __("TOTAL","cart66") . ": " . Cart66Common::currency($order->total, false) . "\n";
  }
endif; ?>