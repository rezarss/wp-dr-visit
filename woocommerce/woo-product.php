<?php
date_default_timezone_set("Asia/Tehran");

add_action("init", "add_category_dr_visit");

function add_category_dr_visit() {

  $cat_exists = term_exists('dr-visit', 'product_cat'); // array is returned if taxonomy is given
  $operator_label = json_decode(get_db_row(SETTINGS_TABLE, 'meta_key', 'app_setting')[0]->meta_value)->texts->operatorLabel;
  if (!$cat_exists) {
    wp_insert_term(
      'ویزیت ' . $operator_label, // the term
      'product_cat', // the taxonomy
      array(
        'description' => 'این دسته را حذف نکنید', // opti
        'slug' => 'dr-visit',
        'parent' => 0,
      )
    );
  }
}
add_action("init", "check_each_doctor_product_exists");

function check_each_doctor_product_exists() {

  $doctors = get_users(array('role' => OPERATOR_ROLE, 'orderby' => 'user_nicename', 'order' => 'ASC'));
  $doctors_count = count($doctors);



  foreach ($doctors as $doctor) {
    global $wpdb;
    $Shift_settings_table = "dv_shift_settings";
    $Shift_schedule_table = "dv_shifts_schedule";

    //$shift_settings = $wpdb->get_results("SELECT * FROM $Shift_settings_table WHERE doctor_id = '$doctor->ID' AND meta_key = 'shift_setting' ");
    $shift_settings = $wpdb->get_results("SELECT * FROM $Shift_settings_table WHERE doctor_id = '$doctor->ID' AND meta_key = 'shift_setting' ");
    $visit_price = json_decode($shift_settings[0]->meta_value)->visit_price;
    if (empty($visit_price))
      $visit_price = 0;

    $get_shift_woo_product = $wpdb->get_results(" SELECT * FROM $Shift_settings_table WHERE doctor_id = $doctor->ID AND meta_key = 'shift_woo_product' ");


    if (count($get_shift_woo_product) == 0) {
      $dr_visit_product_id = create_woocommerce_product_for_doctor_visit($doctor->ID, $visit_price);
      // insert to db
      $insert_shift_product = array(
        "doctor_id" => $doctor->ID,
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
          'ID' => $visit_product_id,
          'post_status' => 'publish'
        ));
        // update product price with visit doctor price
        update_woo_product_price($visit_product_id, $visit_price);
      } elseif (empty($visit_product_status)) {
        // 1. remove previous product id from db
        $wpdb->query("DELETE FROM $Shift_settings_table WHERE doctor_id = $doctor->ID AND meta_key = 'shift_woo_product' AND meta_value = $visit_product_id");

        // 2. create new product and add to db
        $dr_visit_product_id = create_woocommerce_product_for_doctor_visit($doctor->ID, $visit_price);
        // insert to db
        $insert_shift_product = array(
          "doctor_id" => $doctor->ID,
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
}