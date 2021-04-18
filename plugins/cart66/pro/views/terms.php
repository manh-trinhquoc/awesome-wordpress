<div class="Cart66TermsOfServiceWrapper <?php echo $data['location']; ?>">
  <div class="Cart66TermsTitle"><?php echo Cart66Setting::getValue('cart_terms_title'); ?></div>
  <div class="Cart66TermsText"><?php echo Cart66Setting::getValue('cart_terms_text'); ?></div>

  <form action="<?php echo Cart66Common::getPageLink('store/cart'); ?>" method="POST" class="Cart66TermsAcceptance">
    <input type="hidden" name="terms_acceptance" value="I_Accept" />
    <input type="submit" name="accept_terms" value="<?php echo Cart66Setting::getValue('cart_terms_acceptance_label'); ?>" class="Cart66AcceptTermsButton" />
  </form>
</div>