<?php

//include_once(plugin_dir_path(__DIR__) . 'constant.php');

$dv_settings_table = SETTINGS_TABLE;

global $wpdb;

$res = $wpdb->get_results("SELECT * FROM $dv_settings_table WHERE meta_key = 'app_setting' LIMIt 1");


if (count($res) == 0) {

  /*
  $lineGradientColorsPrimary = [];
  array_push($lineGradientColorsPrimary, '#03538c', '#02aebb');

  $json_app_setting = array(
    'texts' => array(
      'splashT1' => 'دکتر رضا راد',
      'splashT2' => 'نوبت دهی',
      'splashT3' => 'RadWebAcademy.ir',
    ),
    'colors' => array(
      'primaryColor' => '#1c1c1c',
      'secondaryColor' => '#f0bc00',
      'lineGradientColorsPrimary' => $lineGradientColorsPrimary,
    ),
  );
  */

  $app_setting = '{"texts":{"splashT1":"ارزباما","splashT2":"سامانه سرمایه گذاری","splashT3":"RadWebAcademy.ir","operatorLabel":"پزشک","operatorsLabel":"پزشکان","panelBottomStart":"راد","panelBottomMiddle":"نوبت","panelBottomEnd":"بیشتر"},"colors":{"splashT1":"#ffffff","splashT2":"#ffffff","splashT3":"#ffffff","primaryColor":"#007573","secondaryColor":"#f5f5f5","successColor":"#357500","warningColor":"#853c00","dangerColor":"#d10000","infoColor":"#0069cc","disabledColor":"#adadad","splashScreenLineGradientColorsPrimary":["#203eb6","#00c2db"],"lineGradientColorsPrimary":["#ebf2ff","#f4ebff"],"textOnBg":"#1a1a1a","lineGradientHeaderTitle":["#2e82ff","#9147ff"],"lineGradientTitleCard":["#e0e0e0","#009dff","#8f00d1"],"cardSlider":"#ffffff","inCardSliderBg":["#2e82ff","#9147ff"],"panelBottom":"#09345d","panelBottomTextFocused":"#ffffff","panelBottomTextNotFocused":"#ffffff","modalBg":"#ffffff","backIcon":"#292929","profileIcon":"#292929","bgProfileIcon":"#f7f7f7"},"animations":{"logo":"bounceInRight","splashT1":"shake","splashT2":"rubberBand","splashT3":"fadeInUpBig","modal":"bounceIn"},"images":{"logo":"https://radwebacademy.ir/wp-content/uploads/2022/06/app-logo.png","overlayHeaderTitle":"https://radwebacademy.ir/wp-content/uploads/2022/07/ill-overlay.png"},"imageSliders":{"mainScreenImageSlider":[{"url":"https://radwebacademy.ir/wp-content/uploads/2022/05/photo-1651938101235-fbf6d66944c3-scaled.jpg","linkTo":"false","notification":{"title":null,"content":null}},{"url":"https://radwebacademy.ir/wp-content/uploads/2022/05/photo-1651938101235-fbf6d66944c3-scaled.jpg","linkTo":"notification-page","notification":{"title":"2222","content":"2222222222222222"}},{"url":"https://radwebacademy.ir/wp-content/uploads/2022/05/photo-1651938101235-fbf6d66944c3-scaled.jpg","linkTo":"false","notification":{"title":null,"content":null}}]},"icons":{"back":"arrow-back","profile":"ios-person-outline","panelBottomStart":"push","panelBottomMiddle":"ios-create-outline","panelBottomEnd":"ellipsis-horizontal"}}';

  // insert all records of app settings
  $insert_default_app_setting = array(
    "meta_key" => "app_setting",
    //"meta_value" => json_encode($json_app_setting),
    "meta_value" => $app_setting,
    "extra" => "added: " . date("Y-m-d H:i:s"),
  );
  $wpdb->insert($dv_settings_table, $insert_default_app_setting);
  if ($wpdb->last_error)
    echo $wpdb->last_error;


} elseif (count($res) == 1) {

  $x = json_decode($res[0]->meta_value);

  //print_r($x);

  //echo $x->texts->splashT2;

}