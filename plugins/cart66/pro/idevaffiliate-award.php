<?php
$url = trim($url);
$saleAmt = $order->subtotal - $order->discount_amount;
$saleAmt = number_format($saleAmt, 2, '.', '');
$url = str_replace('idev_saleamt=XXX', 'idev_saleamt=' . $saleAmt, $url);
$url = str_replace('idev_ordernum=XXX', 'idev_ordernum=' . $order->trans_id, $url);
$ip = $_SERVER['REMOTE_ADDR'];
if($order->ip != '') {
  $ip = $order->ip;
}
Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] order ip: $ip");
$url .= '&ip_address=' . $ip;
$promotionCode = Cart66Session::get('Cart66PromotionCode');
if(Cart66Setting::getValue('idev_coupon_codes') && $promotionCode) {
  $url .= '&coupon_code=' . $promotionCode;
}
Cart66Common::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] Commission notification sent to: $url");
Cart66Common::curl($url);