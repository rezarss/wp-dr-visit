<?php

//print_r($_POST);

global $wpdb;

///////////////////////////////////////// save setting meta_key
$res = $wpdb->get_results("SELECT meta_value FROM $Settings_table WHERE meta_key = 'setting'");
$settings = json_decode($res[0]->meta_value);

$settings->sms->smsPanel = $smsPanel;
$settings->sms->smsProvider = $smsProvider;

/////// sms users
$settings->sms->messages->user->smsNewOrder->enable = $user_new_order_enable;
$settings->sms->messages->user->smsNewOrder->message =  str_replace(PHP_EOL,"\n", trim(json_encode($user_new_order_message),'\'"'));

$settings->sms->messages->user->smsSuccessOrder->enable = $user_success_order_enable;
$settings->sms->messages->user->smsSuccessOrder->message =  str_replace(PHP_EOL,"\n", trim(json_encode($user_success_order_message),'\'"'));

$settings->sms->messages->user->smsReminder->enable = $user_reminder_visit_enable;
$settings->sms->messages->user->smsReminder->hoursBeforeVisit = $user_reminder_visit_hours_before;
$settings->sms->messages->user->smsReminder->message =  str_replace(PHP_EOL,"\n", trim(json_encode($user_reminder_visit_message),'\'"'));

/////// sms operator
$settings->sms->messages->operator->smsNewOrder->enable = $operator_new_order_enable;
$settings->sms->messages->operator->smsNewOrder->message =  str_replace(PHP_EOL,"\n", trim(json_encode($operator_new_order_message),'\'"'));

$settings->sms->messages->operator->smsSuccessOrder->enable = $operator_success_order_enable;
$settings->sms->messages->operator->smsSuccessOrder->message =  str_replace(PHP_EOL,"\n", trim(json_encode($operator_success_order_message),'\'"'));


$result_settings = json_encode((array)$settings);

$res = $wpdb->get_results("UPDATE $Settings_table set meta_value = '$result_settings' WHERE meta_key = 'setting'");

///////////////////////////////////////// save smsProvider meta_key
if ($smsProvider === 'ippanel') {
  $sms_provider_setting = array(
    'username' => ${$smsProvider . '_username'},
    'password' => ${$smsProvider . '_password'},
    'otpQtyNumber' => ${$smsProvider . '_otpQtyNumber'},
    'otpTimeOut' => ${$smsProvider . '_otpTimeOut'},
    'verificationCodePattern' => ${$smsProvider . '_verificationCodePattern'},
  );
} else {
  $sms_provider_setting = array(
    'username' => ${$smsProvider . '_username'},
    'password' => ${$smsProvider . '_password'},
    'otpQtyNumber' => ${$smsProvider . '_otpQtyNumber'},
    'otpTimeOut' => ${$smsProvider . '_otpTimeOut'},
  );
}


$result_settings = json_encode($sms_provider_setting);

$res = $wpdb->get_results("UPDATE $Settings_table set meta_value = '$result_settings' WHERE meta_key = '$smsProvider'");