<?php if($data['form'] == 'form'): ?>
  <p><?php _e('Are you sure you want to remove', 'cart66'); ?> <strong><?php echo $data['email']; ?></strong> <?php _e('from any future mailings', 'cart66'); ?>?</p>
  <form action="?cart66-action=opt_out" method="post" style="display:inline-block;">
    <input type="hidden" name="email" value="<?php echo $data['email']; ?>">
    <input type="hidden" name="token" value="<?php echo $data['token']; ?>">
    <input type="submit" value="<?php _e('Yes, I am sure', 'cart66'); ?>" name="submit">
  </form>
  <form action="?cart66-action=cancel_opt_out" method="post" style="display:inline-block;">
    <input type="submit" value="No, I want to keep getting emails!" name="submit">
  </form>
<?php elseif($data['form'] == 'opt_out'): ?>
  <p><?php _e('You have successfully unsubscribed', 'cart66'); ?> <?php echo $data['email']; ?> <?php _e('from receiving any future emails', 'cart66'); ?>.</p>
<?php elseif($data['form'] == 'cancel'): ?>
  <p><?php _e('You have cancelled the request to unsubscribe. If you want to unsubscribe in the future, just click on the link in your email to come back here', 'cart66'); ?>.</p>
<?php elseif($data['form'] == 'error'): ?>
<?php echo $data['message']; ?>
<?php endif; ?>