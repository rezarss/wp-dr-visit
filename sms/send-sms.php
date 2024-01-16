<?php

//add_action("woocommerce_order_status_processing", "comission_user_mehrad", 10, 1);
//add_action("woocommerce_order_status_completed", "comission_user_mehrad", 10, 1);

//add_action('woocommerce_new_order', 'send_after_successful_order', 10, 1);
//add_action('woocommerce_thankyou', 'send_after_successful_order', 10, 1);
add_action('woocommerce_thankyou', 'send_after_successful_order', 10, 1);
function send_after_successful_order($order_id) {
  $user_id = get_current_user_id();
  $order = wc_get_order($order_id);
  
  header("Location: http://iranvideoo.com/%D9%86%D8%AA%DB%8C%D8%AC%D9%87-%D9%BE%D8%B1%D8%AF%D8%A7%D8%AE%D8%AA/");
  
  /*
  if ($order && get_post_meta($order_id, 'vd_order_type', true)) {
    //print_r(get_order_dv_meta($order_id));
    print_r(send_sms());

  }
  */
}