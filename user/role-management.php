<?php
date_default_timezone_set("Asia/Tehran"); 

/// Add role "doctor" to wordpress at init
add_action('init', 'dv_add_role');
function dv_add_role()
{
  $operator_label = json_decode(get_db_row(SETTINGS_TABLE, 'meta_key', 'app_setting')[0]->meta_value)->texts->operatorLabel;
  add_role(OPERATOR_ROLE, __($operator_label, 'rad-doctor-visit'), array('read' => true, 'level_0' => true));
}


/// bellow codes fires when new doctor created: if created user role is "doctor" then add doctor to db dv_doctors

//when new user created with role "doctor"
add_action('user_register', 'new_doctor_added', 10, 1);

//when user role updated and role "doctor" added
add_action('set_user_role', 'new_doctor_added', 10, 1);

function new_doctor_added($user_id)
{
  /* 
  $user_id(int)
  The user ID.
  ///////
  $role(строка)
  The new role.
  //////
  $old_roles(string[])
  An array of the user's previous roles.
  */

  // get user role
  $user_info = get_userdata($user_id);
  $user_roles = $user_info->roles; // array with the user's roles

  ///////////////////////////////////////////////////////////////////////////////////// Create product for new doctor added
  // if created user role is "rad-doctor" then add doctor to db dv_doctors
  if (in_array(OPERATOR_ROLE, $user_roles)) {
    global $wpdb;
    $Shift_settings_table = "dv_shift_settings";
    $Shift_schedule_table = "dv_shifts_schedule";

    //$shift_settings = $wpdb->get_results("SELECT * FROM $Shift_settings_table WHERE doctor_id = '$doctor->ID' AND meta_key = 'shift_setting' ");
    $shift_settings = $wpdb->get_results("SELECT * FROM $Shift_settings_table WHERE doctor_id = '$doctor->ID' AND meta_key = 'shift_setting' ");
    $visit_price = json_decode($shift_settings[0]->meta_value)->visit_price;
    if (empty($visit_price))
      $visit_price = 0;

    $get_shift_woo_product = $wpdb->get_results(" SELECT * FROM $Shift_settings_table WHERE doctor_id = $user_id AND meta_key = 'shift_woo_product' LIMIT 1 ");


    if (count($get_shift_woo_product) == 0) {
      $dr_visit_product_id = create_woocommerce_product_for_doctor_visit($user_id, $visit_price);
      // insert to db
      $insert_shift_product = array(
        "doctor_id" => $user_id,
        "meta_key" => "shift_woo_product",
        "meta_value" => $dr_visit_product_id,
        "extra" => "added: " . date("Y-m-d H:i:s"),
      );
      $wpdb->insert($Shift_settings_table, $insert_shift_product);
      if ($wpdb->last_error)
        echo $error = $wpdb->last_error;
    } else {
      $visit_product_id = $get_shift_woo_product[0]->meta_value;
      $visit_product_status = get_post_status($visit_product_id);

      if ($visit_product_status && $visit_product_status !== 'publish') {
        // change product post status to publish
        wp_update_post(array(
          'ID'    =>  $visit_product_id,
          'post_status'   =>  'publish'
        ));
        // update product price with visit doctor price
        update_woo_product_price($visit_product_id, $visit_price);
      } elseif (empty($visit_product_status)) {
        // 1. remove previous product id from db
        $wpdb->query("DELETE FROM $Shift_settings_table WHERE doctor_id = $user_id AND meta_key = 'shift_woo_product' AND meta_value = $visit_product_id");

        // 2. create new product and add to db
        $dr_visit_product_id = create_woocommerce_product_for_doctor_visit($user_id, $visit_price);
        // insert to db
        $insert_shift_product = array(
          "doctor_id" => $user_id,
          "meta_key" => "shift_woo_product",
          "meta_value" => $dr_visit_product_id,
          "extra" => "added: " . date("Y-m-d H:i:s"),
        );
        $wpdb->insert($Shift_settings_table, $insert_shift_product);
      } elseif ($visit_product_status && $visit_product_status === 'publish') {
        // update product price with visit doctor price
        update_woo_product_price($visit_product_id, $visit_price);
      }
    }
  }
  ///////////////////////////////////////////////////////////////////////////////////// End of Creation product for new doctor added

}
