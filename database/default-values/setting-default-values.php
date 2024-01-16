<?php

include_once('app-setting-default-values.php');

//include_once(plugin_dir_path(__DIR__) . 'constant.php');

$dv_settings_table = SETTINGS_TABLE;

global $wpdb;
$res = $wpdb->get_results("SELECT meta_key FROM $dv_settings_table");

$setting_names_arr = [];
foreach($res as $setting_name) {
  $setting_names_arr[] = $setting_name->meta_key;
}

//////////////////////////////////////////////////////////// meta_value = setting
if (!in_array('setting', $setting_names_arr)) {

  $setting = '{"smsPanel":"1"}';

  $insert_default_setting = array(
    "meta_key" => "setting",
    "meta_value" => $setting,
    "extra" => "added: " . date("Y-m-d H:i:s"),
  );
  $wpdb->insert($dv_settings_table, $insert_default_setting);
  if ($wpdb->last_error)
    echo $wpdb->last_error;
}
//////////////////////////////////////////////////////////// end of meta_value = setting

//////////////////////////////////////////////////////////// meta_value = ippanel
if (!in_array('ippanel', $setting_names_arr)) {

  $setting = '{"otpQtyNumber":"4","otpTimeOut":"4000"}';

  $insert_default_setting = array(
    "meta_key" => "ippanel",
    "meta_value" => $setting,
    "extra" => "smsProvider",
  );
  $wpdb->insert($dv_settings_table, $insert_default_setting);
  if ($wpdb->last_error)
    echo $wpdb->last_error;
}
//////////////////////////////////////////////////////////// end of meta_value = ippanel