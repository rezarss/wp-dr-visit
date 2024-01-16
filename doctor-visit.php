<?php
/*
* Plugin Name: افزونه نوبت دهی دکتر
* Plugin URI: https://radwebacademy.ir
* Description: تنظیمات نوبت دهی دکتر
* Version: 1.0
* Author: رضا راد
* Author URI: https://radwebacademy.ir
* License: GPLv2 or later
* Text Domain: rad-dv-visit
* Domain Path: /languages
*/

date_default_timezone_set("Asia/Tehran");

include_once('constant.php');

/////////////////////////////////////////////////////////////////////////////////////////// init

/// Role management
include_once('user/role-management.php');
include_once('user/user-custom-field.php');

/// Include functions
include_once('functions.php');

/// Create init tables in db
include_once('database/create-init-tables.php');
include_once('database/default-values/setting-default-values.php');

/// Include functions
include_once('rest-api/api.php');

/// woocommerce
include_once('woocommerce/woo-order.php');
include_once('woocommerce/woo-product.php');

/// sms
include_once('sms/send-sms.php');


/////////////////////////////////////////////////////////////////////////////////////////// Add plugin menu to wordpress dashboard

/// dv page in wp dashboard
include_once('pages/dv-dashboard/main.php');



?>