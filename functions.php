<?php

date_default_timezone_set("Asia/Tehran");

include_once('constant.php');
include_once('assets/php/jdf.php');
include_once('assets/php/ippanel.php');

$shift_settings_table = SHIFT_SETTINGS_TABLE;
$shift_schedule_table = SHIFT_SCHEDULE_TABLE;
$dv_settings_table = SETTINGS_TABLE;

////////////////////////////////////////////////////////////////////////////// Database functions
function get_db_row($table, $col, $value) {
  global $wpdb;
  $result = $wpdb->get_results("SELECT * FROM $table WHERE $col = '$value'");
  return $result;
}

////////////////////////////////////////////////////////////////////////////// api tokens
function base64url_encode($str) {
    return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
}

function generate_jwt($payload, $secret, $headers = array("alg" => "HS256", "typ" => "JWT")) {
	$headers_encoded = base64url_encode(json_encode($headers));
	
	$payload_encoded = base64url_encode(json_encode($payload));
	
	$signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
	$signature_encoded = base64url_encode($signature);
	
	$jwt = "$headers_encoded.$payload_encoded.$signature_encoded";
	
	return $jwt;
}

function is_jwt_valid($jwt, $secret) {
	// split the jwt
	$tokenParts = explode('.', $jwt);
	$header = base64_decode($tokenParts[0]);
	$payload = base64_decode($tokenParts[1]);
	$signature_provided = $tokenParts[2];

	// check the expiration time - note this will cause an error if there is no 'exp' claim in the jwt
	$expiration = json_decode($payload)->exp;
	$is_token_expired = ($expiration - time()) < 0;

	// build a signature based on the header and payload using the secret
	$base64_url_header = base64url_encode($header);
	$base64_url_payload = base64url_encode($payload);
	$signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
	$base64_url_signature = base64url_encode($signature);

	// verify it matches the signature provided in the jwt
	$is_signature_valid = ($base64_url_signature === $signature_provided);
	
	if ($is_token_expired || !$is_signature_valid) {
		return false;
	} else {
		return true;
	}
}


/////////////////////////
  function rad_response_http($message, $status_code, $code = 'error') {
    http_response_code($status_code);
    return array('code' => $code, 'message' => $message, 'data' => array('status' => $status_code));
  }
  function is_rest() {
	    if (defined('REST_REQUEST') && REST_REQUEST // (#1)
		    	|| isset($_GET['rest_route']) // (#2)
			    		&& strpos( $_GET['rest_route'] , '/', 0 ) === 0)
			return true;

		// (#3)
		global $wp_rewrite;
		if ($wp_rewrite === null) $wp_rewrite = new WP_Rewrite();
			
		// (#4)
		$rest_url = wp_parse_url( trailingslashit( rest_url( ) ) );
		$current_url = wp_parse_url( add_query_arg( array( ) ) );
		return strpos( $current_inurl['path'] ?? '/', $rest_url['path'], 0 ) === 0;
	}
	
	function current_api_endpoint() {
	    $path = wp_parse_url( add_query_arg( array( ) ) );
	    
	    if (strpos($path['path'], 'wp-json')) 
	        $x = substr($path['path'], strpos($path['path'], 'wp-json') + strlen('wp-json'));
	    else
	        $x = str_replace('%2F', '/', end(explode('=', $path['query'])));
	
	    
	    return array('namespace' => explode("/", $x)[1], 'full_path' => $x);
	   
	}
////////////////////////////////////////////////////////////////////////////// end of api tokens


////////////////////////////////////////////////////////////////////////////// other functions
function iran_holidays($separator = '-') {
  $holidays = array(
    'jalali' => array(
      '01' . $separator . '01' => 'جشن نوروز',
      '01' . $separator . '02' => 'عید نوروز',
      '01' . $separator . '03' => 'عید نوروز',
      '01' . $separator . '04' => 'عید نوروز',
      '01' . $separator . '12' => 'روز جمهوری اسلامی ایران',
      '01' . $separator . '13' => 'روز طبیعت',
      '03' . $separator . '14' => 'رحلت حضرت امام خمینی (ره)',
      '03' . $separator . '15' => 'قیام خونین 15 خرداد (1342 هـ ش)',
      '11' . $separator . '12' => 'پیروزی انقلاب اسلامی ایران',
      '12' . $separator . '29' => 'روز ملی شدن صنعت نفت ایران (1329 هـ ش)',
    ),
    'hijri' => array(
      '01' . $separator . '09' => 'تاسوعای حسینی',
      '01' . $separator . '10' => 'عاشورای حسینی',
      '02' . $separator . '20' => 'اربعین حسینی',
      '02' . $separator . '29' => 'رحلت حضرت رسول اکرم صلی الله علیه و آله ( 11 هـ ق )ـ شهادت حضرت امام حسن مجتبی علیه السلام ( 50 هـ ق)',
      '02' . $separator . '20' => 'شهادت حضرت امام رضا علیه السلام (203 هـ ق )',
      '03' . $separator . '08' => 'شهادت حضرت امام حسن عسگری (ع) (260 هـ ق) و آغاز ولایت حضرت ولی‌عصر(عج)',
      '03' . $separator . '17' => 'میلاد حضرت رسول اکرم صلی الله علیه و آله (53 سال قبل از هجرت ) – میلاد حضرت امام جعفر صادق علیه‌السلام مؤسس مذهب جعفری (83 هـ ق)',
      '06' . $separator . '03' => 'شهادت حضرت فاطمة زهرا سلام الله علیها (11 هـ ق)',
      '07' . $separator . '13' => 'ولادت حضرت امام علی علیه السلام (23 سال قبل از هجرت )',
      '07' . $separator . '27' => 'مبعث حضرت رسول اکرم صلی الله علیه و آله (13 سال قبل از هجرت)',
      '08' . $separator . '15' => 'ولادت حضرت قائم عجل الله تعالی فرجه (255 هـ ق)',
      '09' . $separator . '21' => 'شهادت حضرت علی علیه السلام (40 هـ ق)',
      '10' . $separator . '01' => 'عید سعید فطر',
      '10' . $separator . '02' => 'تعطیلی به مناسبت روز بعد از عید سعید فطر',
      '10' . $separator . '25' => 'شهادت حضرت امام جعفر صادق علیه السلام (148 هـ ق)',
      '12' . $separator . '10' => 'عید سعید قربان',
      '12' . $separator . '18' => 'عید سعید غدیرخم (10 هـ ق)',
    )
  );
  return $holidays;
}

///////////////////////////////////////////////////////////////////////////////////////////////
// number_english_persian
function ToPersian($number) // $x = "ali"   => $x[0]
{
  $persian = ['۰',
    '۱',
    '۲',
    '۳',
    '۴',
    '۵',
    '۶',
    '۷',
    '۸',
    '۹'];
  $english = ['0',
    '1',
    '2',
    '3',
    '4',
    '5',
    '6',
    '7',
    '8',
    '9'];
  return str_replace($english, $persian, $number);
};

// number_persian_english
function ToEnglish($number) {
  $persian = ['۰',
    '۱',
    '۲',
    '۳',
    '۴',
    '۵',
    '۶',
    '۷',
    '۸',
    '۹'];
  $persian2 = ['٠',
    '١',
    '٢',
    '٣',
    '۴',
    '۵',
    '۶',
    '٧',
    '٨',
    '٩']; // apple
  $english = ['0',
    '1',
    '2',
    '3',
    '4',
    '5',
    '6',
    '7',
    '8',
    '9'];
  $number = str_replace($persian, $english, $number);
  return str_replace($persian2, $english, $number);
};

function clean($input) {
  return addslashes(htmlspecialchars(ToEnglish(trim($input))));
}

function remove_from_first($filter = array('+98', '98', '+', '0'), $str) {
  foreach ($filter as $f) {
    if (substr($str, 0, strlen($f)) == $f) {
      $str = substr($str, strlen($f));
    }
  }
  return $str;
}
///////////////////////////////////////////////////////////////////////////////////////////////

/////////////////// Hijri Qamari
{
  function julianToHijri($julianDay, $separator, $diff_jalali_hijri = 0) {
    $y = 10631.0 / 30.0;
    $epochAstro = 1948084;
    $shift1 = 8.01 / 60.0;
    $z = $julianDay - $epochAstro;
    $cyc = floor($z / 10631.0);
    $z = $z - 10631 * $cyc;
    $j = floor(($z - $shift1) / $y);
    $z = $z - floor($j * $y + $shift1);
    $year = 30 * $cyc + $j;
    $month = (int)floor(($z + 28.5001) / 29.5);
    if ($month === 13) {
      $month = 12;
    }
    $day = $z - floor(29.5001 * $month - 29) + (int)$diff_jalali_hijri;

    return (int)$year . $separator . (int)$month . $separator . (int)$day;
    return array('year' => (int) $year, 'month' => (int) $month, 'day' => (int) $day);
  }

  function gregorianToJulian($year, $month, $day) {
    if ($month < 3) {
      $year -= 1;
      $month += 12;
    }
    $a = floor($year / 100.0);
    $b = ($year === 1582 && ($month > 10 || ($month === 10 && $day > 4)) ? -10 :
      ($year === 1582 && $month === 10 ? 0 :
        ($year < 1583 ? 0 : 2 - $a + floor($a / 4.0))));
    return floor(365.25 * ($year + 4716)) + floor(30.6001 * ($month + 1)) + $day + $b - 1524;
  }



  function gregorianToHijri($date, $diff_jalali_hijri = 0) {

    // $date format => yyyy - mm - dd
    if (strpos($date, '-'))
      $separator = '-';
    elseif (strpos($date, '/'))
      $separator = '/';
    else
      $separator = false;

    $date = explode($separator, $date);

    $jd = gregorianToJulian((int)$date[0], (int)$date[1], (int)$date[2]);
    return julianToHijri($jd, $separator, $diff_jalali_hijri);
  }

}
/////////////////// End of Hijri Qamari

function jalali_to_hijri($date, $diff_jalali_hijri = -1) {
  // $date format => yyyy - mm - dd
  if (strpos($date, '-'))
    $separator = '-';
  elseif (strpos($date, '/'))
    $separator = '/';
  else
    $separator = false;

  $date = explode($separator, $date);


  $jalali_to_gregorian = jalali_to_gregorian((int)$date[0], (int)$date[1], (int)$date[2], $separator);

  $result = explode($separator, gregorianToHijri($jalali_to_gregorian, $diff_jalali_hijri));

  $yyyy = $result[0];
  $mm = $result[1];
  $dd = $result[2];

  if ($mm < 10 && $mm[0] !== '0')
    $mm = '0' . $mm;

  if ($dd < 10 && $dd[0] !== '0')
    $dd = '0' . $dd;


  $result = $yyy . $separator . $mm . $separator . $dd;

  return $result;
}


function is_date_off($jalali_date) {
  if (strpos($jalali_date, '-'))
    $separator = '-';
  elseif (strpos($jalali_date, '/'))
    $separator = '/';
  else
    $separator = false;

  if (!$separator)
    return 'تاریخ صحیح را وارد کنید';

  $jalali_day = explode($separator, $jalali_date)[1] . '-' . explode($separator, $jalali_date)[2];
  $hijri_day = explode($separator, jalali_to_hijri($jalali_date))[1] . '-' . explode($separator, jalali_to_hijri($jalali_date))[2];

  $holidays = iran_holidays($separator);

  $result = array('status' => false, 'description' => '');
  foreach ($holidays as $calendarType => $offDays) {
    foreach ($offDays as $offDay => $occasion) {
      //echo "offDay: $offDay = jalali_day: $jalali_day = qamari_day: $qamari_day ---------------";
      if (($calendarType === 'jalali' && $offDay === $jalali_day) || ($calendarType === 'hijri' && $offDay === $hijri_day)) {
        $result['status'] = true;
        $result['description'] = $occasion;
      }
    }
  }

  return $result;
}

function get_farsi_weekdays($jalali_date) {
  $splitted_jalali_date = explode('-', $jalali_date);
  $year = $splitted_jalali_date[0];
  $month = $splitted_jalali_date[1];
  $day = $splitted_jalali_date[2];

  // $date: string => '2022-04-14'
  $date = jalali_to_gregorian($year, $month, $day, '-');


  $timestamp = strtotime($date);
  $jalali_date_day = strtolower(date('l', $timestamp)); // $jalali_date_day = saturday or sunday or ...

  foreach (WEEKDAYS as $en_day => $fa_day) {
    if ($jalali_date_day === $en_day)
      return $fa_day;
  }

}

function get_day_of_date($jalali_date) {
  $splitted_jalali_date = explode('-', $jalali_date);
  $year = $splitted_jalali_date[0];
  $month = $splitted_jalali_date[1];
  $day = $splitted_jalali_date[2];

  // $date: string => '2022-04-14'
  $date = jalali_to_gregorian($year, $month, $day, '-');


  $timestamp = strtotime($date);
  $day = strtolower(date('l', $timestamp));

  $day_detail = [];
  $day_detail['date'] = $jalali_date;
  $day_detail['day'] = $day;

  if ($day === 'saturday' || $day === 'monday' || $day === 'wednesday' || $day === 'friday')
    $day_detail['oddeven'] = 'even';
  elseif ($day === 'sunday' || $day === 'tuesday' || $day === 'thursday')
    $day_detail['oddeven'] = 'odd';
  else
    $day_detail['oddeven'] = 'friday';


  return $day_detail;
}

function time_to_seconds($time) {

  $arr = explode(':', $time);
  if (count($arr) === 3) {
    return $arr[0] * 3600 + $arr[1] * 60 + $arr[2];
  }
  return ($arr[0] * 60 + $arr[1]) * 60;

}

function diff_two_times_in_minutes($time1, $time2) {

  $time_min1 = time_to_seconds($time1) / 60;
  $time_min2 = time_to_seconds($time2) / 60;

  return abs($time_min2 - $time_min1);

}

function get_queue_starts_of_shift($time1, $time2, $divide_in_min) {

  $start = time_to_seconds($time1) < time_to_seconds($time2) ? $time1 : $time2;
  $end = time_to_seconds($time2) > time_to_seconds($time1) ? $time2 : $time1;

  $diff = diff_two_times_in_minutes($start, $end);


  $result = [];

  $result[] = $start;

  for ($i = 1; $i <= floor($diff / $divide_in_min); $i++) {
    $new_start = date('H:i', strtotime($start. ' +' . $divide_in_min . ' minutes'));
    if ($new_start !== $end)
      $result[] = $new_start;
    $start = $new_start;
  }

  return $result;
}
////////////////////////////////////////////////////////////////////////////// end of other functions


function validateDate($date, $format = 'Y-m-d H:i:s') {

  $d = DateTime::createFromFormat($format, $date);

  return $d && $d->format($format) == $date;

}


function getVarName(&$var, $definedVars = null) {
  $definedVars = (!is_array($definedVars) ? $GLOBALS : $definedVars);
  $val = $var;
  $rand = 1;
  while (in_array($rand, $definedVars, true)) {
    $rand = md5(mt_rand(10000, 1000000));
  }
  $var = $rand;

  foreach ($definedVars as $dvName => $dvVal) {
    if ($dvVal === $rand) {
      $var = $val;
      return $dvName;
    }
  }

  return null;
}

///////////////////// Get SMS

function get_activated_sms_provider() {
  global $wpdb;
  global $dv_settings_table;
  $res = $wpdb->get_results("SELECT meta_value FROM $dv_settings_table WHERE meta_key = 'setting'");
  $settings = json_decode($res[0]->meta_value);
  return $settings->sms->smsProvider;
}

function get_activated_sms_setting() {
  $smsProvider = get_activated_sms_provider();

  // get smsProvider setting
  global $wpdb;
  global $dv_settings_table;
  $res = $wpdb->get_results("SELECT meta_value FROM $dv_settings_table WHERE meta_key = '$smsProvider'");
  return json_decode($res[0]->meta_value);
}

function send_sms($mobile, $message) {
  $result_sms = false;

  $smsProvider = get_activated_sms_provider();

  // send via Ippanel
  if ($smsProvider === 'ippanel') {
    $result_sms = send_sms_ippanel($mobile, $message);
  }

  return $result_sms;

}

///////////////////// setting

function setting() {
  global $wpdb;
  global $dv_settings_table;

  $res = $wpdb->get_results("SELECT meta_value FROM $dv_settings_table WHERE meta_key = 'setting' LIMIt 1");

  if (count($res) == 0)
    return false;

  $main_setting = json_decode($res[0]->meta_value);
  $result['setting'] = $main_setting;

  // attach activated smsProvider setting
  $smsProvider_setting = get_activated_sms_setting();

  if ($main_setting->sms->smsProvider === 'ippanel') {
    unset($smsProvider_setting->username);
    unset($smsProvider_setting->password);
    unset($smsProvider_setting->verificationCodePattern);
  }
  $result['smsProvider'] = $smsProvider_setting;

  return $result;
}

///////////////////// Login ans signup

function is_mobile_exists($mobile) {
  $mobile = clean($mobile);
  $mobile = remove_from_first(array('+', '0', ' '), $mobile);

  global $wpdb;
  $usermeta_table = $wpdb->prefix . "usermeta";
  $res = $wpdb->get_results("SELECT user_id FROM $usermeta_table WHERE (meta_key = 'dv_user_phone' OR meta_key = 'digits_phone_no') AND meta_value = '$mobile' ");

  if (count($res)) {
    $user_id = $res[0]->user_id;
    return get_user_by('id', $user_id);
  } else
    return false;
}

function is_user_exists($user_login) {
  $user = get_user_by('id', $user_login);
  if (!$user)
    $user = get_user_by('login', $user_login);
  if (!$user)
    $user = get_user_by('email', $user_login);
  if (!$user)
    $user = is_mobile_exists($user_login);

  if (!$user)
    return false;

  // adding meta
  $user->data->first_name = get_user_meta($user->data->ID, 'first_name', true);
  $user->data->last_name = get_user_meta($user->data->ID, 'last_name', true);

  $profile_picture = wp_get_attachment_url(get_user_meta($user->data->ID, 'wp_metronet_image_id', true));
  $profile_picture = $profile_picture ? $profile_picture : false; //get_avatar_url($user->data->ID);
  $user->data->profile_picture = $profile_picture;

  $user->data->phone_number = get_user_meta($user->data->ID, 'dv_user_phone', true);

  return $user;
}


//Login with password => Login contains: user_login, user_email meta_mobile:+9891....
function login_with_password($user_login, $user_password) {

  $user = is_user_exists($user_login);

  $username = $user->data->user_login;


  if (!is_wp_error(wp_authenticate($username, $user_password))) {
    wp_clear_auth_cookie();
    wp_set_current_user ($user->ID);
    wp_set_auth_cookie ($user->ID);
    session_set_cookie_params(3600 * 24 * 30); // seconds * hours * days = 1 month
    session_start();
    $_SESSION['login'] = $user->data;

    $res['status'] = true;
    $res['result'] = $user->data;
    return $res;
  } else {
    $res['status'] = false;
    $res['result'] = 'نام کاربری (شماره موبایل، نام کاربری، ایمیل) یا رمز عبور اشتباه است.';
    return $res;
  }
}

function register_user($first_name, $last_name, $password, $country_code, $mobile, $email, $citizen_id) {
  // check if user already signed up by mobile or email
  $mobile_exists = is_user_exists($mobile);
  if ($mobile_exists) {
    $res['status'] = false;
    $res['result'] = 'این اشماره موبایل در سیستم وجود دارد. لطفا شماره دیگری را وارد کنید';
    return $res;
  }

  $email_exists = is_user_exists($email);
  if ($email_exists) {
    $res['status'] = false;
    $res['result'] = 'این ایمیل در سیستم وجود داردو لطفا ایمیل دیگری را وارد کنید';
    return $res;
  }

  // start creating user
  $user_id = wp_insert_user(array(
    'user_login' => remove_from_first(array('+', ' '), $country_code) . $mobile,
    'user_pass' => $password,
    'user_email' => $email,
    'first_name' => $first_name,
    'last_name' => $last_name,
    'display_name' => $first_name . ' ' . $last_name,
    //'user_nicename' => $first_name . ' ' . $last_name, // not working
    'nickname' => $first_name . ' ' . $last_name,
    'role' => 'subscriber',
  ));

  if (! is_wp_error($user_id)) {
    // adding user meta
    add_user_meta($user_id, 'citizen_id', $citizen_id);
    add_user_meta($user_id, 'digits_phone', '+' . remove_from_first(array('+', ' '), $country_code) . $mobile);
    add_user_meta($user_id, 'digt_countrycode', '+' . remove_from_first(array('+', ' '), $country_code));
    add_user_meta($user_id, 'digits_phone_no', $mobile);
    add_user_meta($user_id, 'dv_user_phone', remove_from_first(array('+', ' '), $country_code) . $mobile);
    add_user_meta($user_id, 'wp_metronet_post_id', '');
    add_user_meta($user_id, 'wp_metronet_image_id', '');
    // result
    $res['status'] = true;
    $res['result'] = $user_id;
    return $res;
  } else {
    $res['status'] = false;
    $res['result'] = $user_id->get_error_message();
    return $res;
  }
}


function is_logged_in() {
  session_start();
  if ($_SESSION['login'])
    return $_SESSION['login'];
  else
    return false;
}

function logoutUser() {
  wp_logout();
  session_start();
  $_SESSION['login'] = false;
  return true;
}

function sendOTP($mobile) {
  $mobile = clean($mobile);

  $smsProvider = get_activated_sms_setting();
  $otpQtyNumber = (int)$smsProvider->otpQtyNumber;
  $otpTimeOut = (int)$smsProvider->otpTimeOut * 60;

  if ($otpQtyNumber == 4)
    $otp = rand(1000, 9999);
  elseif ($otpQtyNumber == 5)
    $otp = rand(10000, 99999);
  elseif ($otpQtyNumber == 6)
    $otp = rand(100000, 999999);

  $otp_time_creation = time();

  session_start();
  $_SESSION['otp']['otp'] = $otp;
  $_SESSION['otp']['creation-time'] = $otp_time_creation;
  $_SESSION['otp']['expire'] = $otp_time_creation + $otpTimeOut;
  $_SESSION['otp']['user'] = $mobile;


  $blog_title = get_bloginfo('name');
  $result_sms = false;

  //////////////// send sms
  // get activated smsProvider
  if (get_activated_sms_provider() === 'ippanel') {
    $pattern_code = get_activated_sms_setting()->verificationCodePattern;
    $message = "patterncode:$pattern_code;code:$otp;company:$blog_title";
    $result_sms = send_sms_ippanel($mobile, $message);
  }

  return $result_sms;
}


function verifyOTP($mobile, $user_otp) {
  session_start();

  if ($user_otp == $_SESSION['otp']['otp'] && $mobile === $_SESSION['otp']['user'] && time() <= $_SESSION['otp']['expire'])
    return true;
  else
    return false;

}

function login_with_mobile_otp($mobile, $user_otp) {
  $mobile = clean($mobile);


  $user = is_user_exists($mobile)->data;

  if (!$user) {
    $res['status'] = false;
    $res['result'] = "شما ثبت نام نکرده اید، لطفا قبل از ورود ثبت نام کنید";
    return $res;
  }

  $verify_mobile = remove_from_first(array('+', '0', ' '), $mobile);
  if (verifyOTP($verify_mobile, $user_otp)) {
    wp_clear_auth_cookie();
    wp_set_current_user ($user->ID);
    wp_set_auth_cookie ($user->ID);
    session_set_cookie_params(3600 * 24 * 30); // seconds * hours * days = 1 month
    session_start();
    $_SESSION['login'] = $user;

    $res['status'] = true;
    $res['result'] = $user;
    return $res;
  } else {
    $res['status'] = false;
    $res['result'] = "کد تایید اشتباه است";
    return $res;
  }
}

function login_by_userLogin($user_login) {

  $user = is_user_exists($user_login)->data;
  if (!$user) {
    $res['status'] = false;
    $res['result'] = "شما ثبت نام نکرده اید، لطفا قبل از ورود ثبت نام کنید";
    return $res;
  }

  wp_clear_auth_cookie();
  wp_set_current_user ($user->ID);
  wp_set_auth_cookie ($user->ID);
  session_set_cookie_params(3600 * 24 * 30); // seconds * hours * days = 1 month
  session_start();
  $_SESSION['login'] = $user;

  $res['status'] = true;
  $res['result'] = $user;
  return $res;

}

function authenticate_user_table($user_login, $user_pass) {
  global $wpdb;

  $result = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "users WHERE user_login = '$user_login' AND user_pass = '$user_pass'");
  if ($wpdb->last_error) {
    $res['status'] = false;
    $res['result'] = $wpdb->last_error;
    return $res;
  }

  if (!count($result)) {
    $res['status'] = false;
    $res['result'] = 'نام کاربری یا رمز عبور اشتباه است.';
    return $res;
  }

  $res['status'] = true;
  $res['result'] = $result[0];
  return $res;

}

///////////////////// related to Wordpress and plugins functions
{
  function upload_to_wordpress_library($post_file_name = 'file') {
    extract($_POST);

    $file_name = $_FILES[$post_file_name]['name'];
    $file_temp = $_FILES[$post_file_name]['tmp_name'];

    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($file_temp);
    $filename = basename($file_name);
    $filetype = wp_check_filetype($file_name);
    $filename = time().'.'.$filetype['ext'];


    if (wp_mkdir_p($upload_dir['path'])) {
      $file = $upload_dir['path'] . '/' . $filename;
    } else {
      $file = $upload_dir['basedir'] . '/' . $filename;
    }

    file_put_contents($file, $image_data);
    $wp_filetype = wp_check_filetype($filename, null);
    $attachment = array(
      'post_mime_type' => $wp_filetype['type'],
      'post_title' => sanitize_file_name($filename),
      'post_content' => '',
      'post_status' => 'inherit'
    );

    $attach_id = wp_insert_attachment($attachment, $file);

    if (!$attach_id)
      return false;

    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
    wp_update_attachment_metadata($attach_id, $attach_data);

    return $attach_id;
  }

  ///////////// mmp plugin
  function mmp_plugin_change_profile_picture($user_id, $uploaded_image_id) {

    //if (! current_user_can('upload_files', $user_id)) {
    //return new WP_Error('mpp_insufficient_privs', __('You must be able to upload files.', 'metronet-profile-picture'), array('status' => 403));
    //}

    $post_id = get_post_id($user_id);

    // Save user meta.
    update_user_option($user_id, 'metronet_post_id', $post_id);
    update_user_option($user_id, 'metronet_image_id', $uploaded_image_id); // Added via this thread (Props Solinx) - https://wordpress.org/support/topic/storing-image-id-directly-as-user-meta-data.

    set_post_thumbnail($post_id, $uploaded_image_id);

    $attachment_url = wp_get_attachment_url($uploaded_image_id);

    if (!$attachment_url)
      return false;


    $result = array(
      '24' => wp_get_attachment_image_url($uploaded_image_id, 'profile_24', false, ''),
      '48' => wp_get_attachment_image_url($uploaded_image_id, 'profile_48', false, ''),
      '96' => wp_get_attachment_image_url($uploaded_image_id, 'profile_96', false, ''),
      '150' => wp_get_attachment_image_url($uploaded_image_id, 'profile_150', false, ''),
      '300' => wp_get_attachment_image_url($uploaded_image_id, 'profile_300', false, ''),
      'thumbnail' => wp_get_attachment_image_url($uploaded_image_id, 'thumbnail', false, ''),
      'full' => $attachment_url,
    );

    if (empty($result))
      return false;

    return $result;

  }
  function get_mmp_post_id($user_id = 0) {

    $user = get_user_by('id', $user_id);

    // Get/Create Profile Picture Post.
    $post_args = array(
      'post_type' => 'mt_pp',
      'author' => $user_id,
      'post_status' => 'publish',
    );
    $posts = get_posts($post_args);
    if (! $posts) {
      $post_id = wp_insert_post(
        array(
          'post_author' => $user_id,
          'post_type' => 'mt_pp',
          'post_status' => 'publish',
          'post_title' => $user->data->display_name,
        )
      );
    } else {
      $post = end($posts);
      $post_id = $post->ID;
    }
    return $post_id;
  }
  ///////////// mmp plugin

  function get_woocommerce_gateways() {
    $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
    foreach ($available_gateways as $gateway) {
      $res[] = $gateway;
    }
    return $res;
  }

}


///////////////////// Creating Tables
function create_table($table_name, $sql) {


  global $wpdb;
  //// Create table

  // check if table exists

  $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_name));



  if (!$wpdb->get_var($query) == $table_name) {

    $charset_collate = $wpdb->get_charset_collate();



    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql);

    $success = empty($wpdb->last_error);

  }

}
///////////////////// Creating Tables

//////////////////////////////////////////////////////////////////////////////  doctor shifts
function app_setting() {
  global $wpdb;
  global $dv_settings_table;

  $res = $wpdb->get_results("SELECT * FROM $dv_settings_table WHERE meta_key = 'app_setting' LIMIt 1");

  return json_decode($res[0]->meta_value);
}
//////////////////////////////////////////////////////////////////////////////  doctor shifts
function website_setting() {
  global $wpdb;

  $res['woocommerce'] = array('currency' => get_woocommerce_currency());

  return $res;

}
//////////////////////////////////////////////////////////////////////////////  doctor shifts




function has_shift_type_shifts($array, $shift_type, $day = '') {



  if ($shift_type === 'ed' || $shift_type === 'odd' || $shift_type === 'even') {



    foreach ($array as $arr) {

      if ($arr->shift_type === $shift_type && json_decode($arr->shift_setting)->shift_count >= 1) {

        return $arr;

      }

    }

  } elseif ($shift_type === 'weekday' || $shift_type === 'date') {

    foreach ($array as $arr) {

      // if ($arr->shift_type === $shift_type && $arr->shift_time === $day && json_decode($arr->shift_setting)->shift_count >= 1) {

      if ($arr->shift_type === $shift_type && $arr->shift_time === $day) {

        return $arr;

      }

    }

  }

  return false;

}

function find_shift_type_in_array($array, $shift_type, $day = '') {

  if ($shift_type === 'ed' || $shift_type === 'odd' || $shift_type === 'even') {

    foreach ($array as $arr) {

      if ($arr->shift_type === $shift_type) {

        return $arr;

      }

    }

  } elseif ($shift_type === 'weekday' || $shift_type === 'date') {
    foreach ($array as $arr) {
      if ($arr->shift_type === $shift_type && $arr->shift_time === $day) {
        return $arr;
      }
    }
  }
  return false;
}

//////////////////////////////////////////////////////////////////////////////  doctor shifts
function get_doctor_shift_setting($doctor_id) {
  global $wpdb;
  global $shift_settings_table;

  $results = $wpdb->get_results("SELECT * FROM `$shift_settings_table` WHERE doctor_id = $doctor_id AND meta_key = 'shift_setting' LIMIT 1");

  return $results[0]->meta_value;
}
///
function get_doctor_shift_schedule($doctor_id, $shift_time = 'ed', $shift_type = 'ed') {
  global $wpdb;
  global $shift_schedule_table;

  $results = $wpdb->get_results("SELECT * FROM `$shift_schedule_table` WHERE doctor_id = $doctor_id AND shift_time = '$shift_time' AND shift_type = '$shift_type' LIMIT 1");

  return $results[0]->shift_setting;
}

///////////////

function get_doctor_setting_json($json_property = 'visit_price', $doctor_id) {
  $dr_shift_setting = get_doctor_shift_setting($doctor_id);
  $result = json_decode($dr_shift_setting)->$json_property;

  if (is_numeric($result))
    return (int)$result;
  else
    return $result;
}

function get_doctor_shift_json($json_property = 'enable', $doctor_id, $shift_time = 'ed', $shift_type = 'ed') {
  $dr_shift_setting = get_doctor_shift_schedule($doctor_id, $shift_time, $shift_type);

  $result = json_decode($dr_shift_setting)->$json_property;

  if (is_numeric($result))
    return (int)$result;
  else
    return $result;
}

function get_doctor_enabled_shifts($doctor_id) {
  global $wpdb;
  global $shift_schedule_table;

  $results = $wpdb->get_results("SELECT * FROM `$shift_schedule_table` WHERE doctor_id = $doctor_id");

  $shifts = [];
  foreach ($results as $res) {
    if (json_decode($res->shift_setting)->enable === '1')
      $shifts[] = $res;
  }

  return $shifts;
}

function get_doctor_shifts_starts_ends($doctor_id, $shift_time = 'ed', $shift_type = 'ed') {
  $res = [];
  $dr_shift_setting = get_doctor_shift_schedule($doctor_id, $shift_time, $shift_type);
  $results = json_decode($dr_shift_setting)->shifts;

  $i = 0;
  foreach ($results as $shift) {
    $res[$i]['start'] = $shift->shift_start;
    $res[$i]['end'] = $shift->shift_end;
    $i++;
  }
  return $res;
}

function get_doctor_shifts_starts_ends_timepervisit($doctor_id, $shift_time = 'ed', $shift_type = 'ed') {
  $res = [];
  $dr_shift_setting = get_doctor_shift_schedule($doctor_id, $shift_time, $shift_type);
  $results = json_decode($dr_shift_setting)->shifts;

  $i = 0;
  foreach ($results as $shift) {
    $res[$i]['start'] = $shift->shift_start;
    $res[$i]['end'] = $shift->shift_end;
    $res[$i]['shift_visit_time'] = $shift->shift_visit_time;
    $i++;
  }
  return $res;
}

function find_shift_of_shift_schedule($time, $shift_time, $shift_type, $doctor_id) {
  $res = get_doctor_shifts_starts_ends($doctor_id, $shift_time, $shift_type);

  for ($i = 0; $i <= count($res)-1; $i++) {
    if (strtotime($res[$i]['start']) <= strtotime($time) && strtotime($time) < strtotime($res[$i]['end']))
      return $i;
  }
  return false;
}



////////////////////////////////// important
function get_shift_type_of_date($date, $doctor_id) {
  // eg: date = '1400-01-26'
  global $shift_schedule_table;

  $day = get_day_of_date($date)['day'];
  $oddeven = get_day_of_date($date)['oddeven'];

  global $wpdb;
  $date_shift = $wpdb->get_results("SELECT * FROM $shift_schedule_table WHERE shift_time = '$date' AND doctor_id = $doctor_id");

  if (count($date_shift) >= 1) {
    $shift_time = $date;
    $shift_type = 'date';
    //return $date_shift; //////////////// do something here => return true
  } else {
    $weekday_shift = $wpdb->get_results("SELECT * FROM $shift_schedule_table WHERE shift_time = '$day' AND shift_type = 'weekday' AND doctor_id = $doctor_id");
    if (json_decode($weekday_shift[0]->shift_setting)->enable === '1' || json_decode($weekday_shift[0]->shift_setting)->enable === '0') {
      $shift_time = $day;
      $shift_type = 'weekday';
      //return $weekday_shift[0]; //////////////// do something here
    } elseif (json_decode($weekday_shift[0]->shift_setting)->enable === '2') {
      // first check for "even or odd" then check for "ed"
      $ed_shift = $wpdb->get_results("SELECT * FROM $shift_schedule_table WHERE shift_time = 'ed' AND shift_type = 'ed' AND doctor_id = $doctor_id");
      $even_shift = $wpdb->get_results("SELECT * FROM $shift_schedule_table WHERE shift_time = 'even' AND shift_type = 'even' AND doctor_id = $doctor_id");
      $odd_shift = $wpdb->get_results("SELECT * FROM $shift_schedule_table WHERE shift_time = 'odd' AND shift_type = 'odd' AND doctor_id = $doctor_id");
      if (json_decode($ed_shift[0]->shift_setting)->enable === '1') {
        $shift_time = 'ed';
        $shift_type = 'ed';
      } else {
        if ($oddeven === 'even') {
          $shift_time = 'even';
          $shift_type = 'even';
        } elseif ($oddeven === 'odd') {
          $shift_time = 'odd';
          $shift_type = 'odd';
        }
      }
    }
  }

  $result = [];
  $result['date'] = $date;
  $result['shift_time'] = $shift_time;
  $result['shift_type'] = $shift_type;

  return $result;

}

function get_dr_shift_json_by_date($date, $doctor_id) {
  // eg: date = '1400-01-26'
  global $shift_schedule_table;

  $day = get_day_of_date($date)['day'];
  $oddeven = get_day_of_date($date)['oddeven'];

  global $wpdb;
  $date_shift = $wpdb->get_results("SELECT * FROM $shift_schedule_table WHERE shift_time = '$date' AND doctor_id = $doctor_id");

  if (count($date_shift) >= 1) {
    return json_decode($date_shift[0]->shift_setting);
  } else {
    $weekday_shift = $wpdb->get_results("SELECT * FROM $shift_schedule_table WHERE shift_time = '$day' AND shift_type = 'weekday' AND doctor_id = $doctor_id");
    if (json_decode($weekday_shift[0]->shift_setting)->enable === '1' || json_decode($weekday_shift[0]->shift_setting)->enable === '0') {
      return json_decode($weekday_shift[0]->shift_setting);
    } elseif (json_decode($weekday_shift[0]->shift_setting)->enable === '2') {
      // first check for "even or odd" then check for "ed"
      $ed_shift = $wpdb->get_results("SELECT * FROM $shift_schedule_table WHERE shift_time = 'ed' AND shift_type = 'ed' AND doctor_id = $doctor_id");
      $even_shift = $wpdb->get_results("SELECT * FROM $shift_schedule_table WHERE shift_time = 'even' AND shift_type = 'even' AND doctor_id = $doctor_id");
      $odd_shift = $wpdb->get_results("SELECT * FROM $shift_schedule_table WHERE shift_time = 'odd' AND shift_type = 'odd' AND doctor_id = $doctor_id");
      if (json_decode($ed_shift[0]->shift_setting)->enable === '1') {
        return json_decode($ed_shift[0]->shift_setting);
      } elseif (json_decode($even_shift[0]->shift_setting)->enable === '1' && $oddeven === 'even') {
        return json_decode($even_shift[0]->shift_setting);
      } elseif (json_decode($odd_shift[0]->shift_setting)->enable === '1' && $oddeven === 'odd') {
        return json_decode($odd_shift[0]->shift_setting);
      } else {
        return false;
      }
    }
  }

  return false;
}

function get_doctor_shift_schedule_index_by_dateTime($date, $time, $doctor_id) {
  $shift = get_shift_type_of_date($date, $doctor_id);
  $shift_index = find_shift_of_shift_schedule($time, $shift['shift_time'], $shift['shift_type'], $doctor_id);

  if ($shift_index === false)
    return false;

  return get_dr_shift_json_by_date($date, $doctor_id)->shifts[$shift_index];
}

function doctor_next_available_days($doctor_id) {
  $next_available_days = get_doctor_setting_json('next_available_days', $doctor_id);

  $today = jdate('Y-m-d', time(), '', 'Asia/Tehran', 'en');

  $next_days = [];
  $next_days[] = $today;

  for ($i = 1; $i <= $next_available_days; $i++)
    $next_days[] = date('Y-m-d', strtotime($today . ' +' . $i . ' day'));

  return $next_days;
}

function get_dr_reserved_time_of_date_full_details($date, $doctor_id) {

  $reserved_time_arr = [];
  $order_details_arr = [];

  global $wpdb;
  $table = $wpdb->prefix . "postmeta";

  $orders_id = $wpdb->get_results("SELECT post_id FROM $table WHERE meta_key = 'vd_doctor_id' AND meta_value = $doctor_id");

  foreach ($orders_id as $order) {
    if (get_post_status($order->post_id) === 'wc-completed') {
      $reserved_date = get_post_meta($order->post_id, 'vd_user_visit_date', true);

      if ($reserved_date === $date) {
        $order_details_arr['order_id'] = $order->post_id;
        $order_details_arr['doctor_id'] = get_post_meta($order->post_id, 'vd_doctor_id', true);
        $order_details_arr['doctor_id'] = get_post_meta($order->post_id, 'vd_visit_status', true);
        $order_details_arr['patient_id'] = get_post_meta($order->post_id, 'vd_user_id', true);
        $order_details_arr['reserved_date'] = get_post_meta($order->post_id, 'vd_user_visit_date', true);
        $order_details_arr['reserved_time'] = get_post_meta($order->post_id, 'vd_user_visit_time', true);
        $order_details_arr['visit_time_per_patient'] = get_post_meta($order->post_id, 'vd_visit_time_per_patient', true);

        $reserved_time_arr[] = $order_details_arr;
      }
    }
  }
  return $reserved_time_arr;
}

function get_dr_reserved_time_of_date($date, $doctor_id) {

  $reserved_time_arr = [];

  global $wpdb;
  $table = $wpdb->prefix . "postmeta";

  $orders_id = $wpdb->get_results("SELECT post_id FROM $table WHERE meta_key = 'vd_doctor_id' AND meta_value = $doctor_id");

  foreach ($orders_id as $order) {
    if (get_post_status($order->post_id) === 'wc-completed') {
      $reserved_date = get_post_meta($order->post_id, 'vd_user_visit_date', true);

      if ($reserved_date === $date)
        $reserved_time_arr[] = get_post_meta($order->post_id, 'vd_user_visit_time', true);
    }
  }
  return $reserved_time_arr;
}

function get_dr_all_start_queue_of_shifts_of_date($date, $doctor_id) {
  $date_shift = get_shift_type_of_date($date, $doctor_id);
  $start_end = get_doctor_shifts_starts_ends_timepervisit($doctor_id, $date_shift['shift_time'], $date_shift['shift_type']);

  $result = [];

  foreach ($start_end as $shift) {
    $result[] = get_queue_starts_of_shift($shift['start'], $shift['end'], $shift['shift_visit_time']);
  }

  return $result;

}

function is_date_time_shift_available($date, $time, $doctor_id) {
  $arr = [];

  global $wpdb;
  $table = $wpdb->prefix . "posts";
  $orders_id = $wpdb->get_results("SELECT ID FROM $table WHERE post_type = 'shop_order'");
  //print_r($orders_id);

  foreach ($orders_id as $order) {
    $reseved_doctor_id = get_post_meta($order->ID, 'vd_doctor_id', true);
    $order_visit_date = get_post_meta($order->ID, 'vd_user_visit_date', true);
    $order_visit_time = get_post_meta($order->ID, 'vd_user_visit_time', true);

    if ($doctor_id && $order_visit_date && $order_visit_time && $reseved_doctor_id === $doctor_id && $order_visit_date === $date && $order_visit_time === $time)
      $arr[] = (int)$order->ID;
  }

  if (count($arr) == 0)
    return false;

  return $arr;
}

function get_dr_shift_json_by_date_time($date_time, $doctor_id) {
  // eg: date_time = '1400-01-26 14:00'
  global $shift_schedule_table;

  $date = explode(' ', $date_time)[0];
  $time = explode(' ', $date_time)[1];

  $day = get_day_of_date($date)['day'];
  $oddeven = get_day_of_date($date)['oddeven'];

  global $wpdb;
  $date_shift = $wpdb->get_results("SELECT * FROM $shift_schedule_table WHERE shift_time = '$date' AND doctor_id = $doctor_id");


  if (count($date_shift) >= 1) {
    $shift_queue = find_shift_of_shift_schedule($time, $date, 'date', $doctor_id); // returns array index of shifts in json
    $res = json_decode($date_shift[0]->shift_setting);
    $res->shift_datetime_match = $shift_queue;
    $res->shift_time = $date;
    $res->shift_type = 'date';
    $res->doctor_id = $doctor_id;
    $res->shift_available = is_date_time_shift_available($date, $time);
    return $res;
  } else {
    $weekday_shift = $wpdb->get_results("SELECT * FROM $shift_schedule_table WHERE shift_time = '$day' AND shift_type = 'weekday' AND doctor_id = $doctor_id");
    if (json_decode($weekday_shift[0]->shift_setting)->enable === '1' || json_decode($weekday_shift[0]->shift_setting)->enable === '0') {
      $shift_queue = find_shift_of_shift_schedule($time, $day, 'weekday', $doctor_id); // returns array index of shifts in json
      $res = json_decode($weekday_shift[0]->shift_setting);
      $res->shift_datetime_match = $shift_queue;
      $res->shift_time = $day;
      $res->shift_type = 'weekday';
      $res->doctor_id = $doctor_id;
      $res->shift_available = is_date_time_shift_available($date, $time) ? 0 : 1;
      return $res;
    } elseif (json_decode($weekday_shift[0]->shift_setting)->enable === '2') {
      // first check for "even or odd" then check for "ed"
      $ed_shift = $wpdb->get_results("SELECT * FROM $shift_schedule_table WHERE shift_time = 'ed' AND shift_type = 'ed' AND doctor_id = $doctor_id");
      $even_shift = $wpdb->get_results("SELECT * FROM $shift_schedule_table WHERE shift_time = 'even' AND shift_type = 'even' AND doctor_id = $doctor_id");
      $odd_shift = $wpdb->get_results("SELECT * FROM $shift_schedule_table WHERE shift_time = 'odd' AND shift_type = 'odd' AND doctor_id = $doctor_id");
      if (json_decode($ed_shift[0]->shift_setting)->enable === '1') {
        $shift_queue = find_shift_of_shift_schedule($time, 'ed', 'ed', $doctor_id); // returns array index of shifts in json
        $res = json_decode($ed_shift[0]->shift_setting);
        $res->shift_datetime_match = $shift_queue;
        $res->shift_time = 'ed';
        $res->shift_type = 'ed';
        $res->doctor_id = $doctor_id;
        $res->shift_available = is_date_time_shift_available($date, $time) ? 0 : 1;
        return $res;
      } elseif (json_decode($even_shift[0]->shift_setting)->enable === '1' && $oddeven === 'even') {
        $shift_queue = find_shift_of_shift_schedule($time, 'even', 'even', $doctor_id); // returns array index of shifts in json
        $res = json_decode($even_shift[0]->shift_setting);
        $res->shift_datetime_match = $shift_queue;
        $res->shift_time = 'even';
        $res->shift_type = 'even';
        $res->doctor_id = $doctor_id;
        $res->shift_available = is_date_time_shift_available($date, $time) ? 0 : 1;
        return $res;
      } elseif (json_decode($odd_shift[0]->shift_setting)->enable === '1' && $oddeven === 'odd') {
        $shift_queue = find_shift_of_shift_schedule($time, 'odd', 'odd', $doctor_id); // returns array index of shifts in json
        $res = json_decode($odd_shift[0]->shift_setting);
        $res->shift_datetime_match = $shift_queue;
        $res->shift_time = 'odd';
        $res->shift_type = 'odd';
        $res->doctor_id = $doctor_id;
        $res->shift_available = is_date_time_shift_available($date, $time) ? 0 : 1;
        return $res;
      } else {
        return false;
      }
    }
  }

  return false;
}
////////////////////////////////// end of important

function is_shift_available($date = null, $time = null, $date_time = null, $doctor_id) {

  if (empty($date) && empty($date_time))
    return 'include necessary arguments';

  if (!empty($date_time)) {
    $result = explode(' ', $date_time);
    if (count($result) == 1)
      return 'Date time is invalid';
    $date = $result[0];
    $time = $result[1];
  }

  // get doctor shifts starts and ends of the specific day
  $shift = get_shift_type_of_date($date, $doctor_id);

  get_doctor_shifts_starts_ends($doctor_id, $shift['shift_time'], $shift['shift_type']);

  /// this function is not completed ...

}

function get_json_shift_property_by_shift_time($date = null, $time = null, $date_time = null, $json_property, $doctor_id) {
  if (empty($date) && empty($date_time))
    return 'include necessary arguments';

  if (!empty($date_time)) {
    $result = explode(' ', $date_time);
    if (count($result) == 1)
      return 'Date time is invalid';
    $date = $result[0];
    $time = $result[1];
  }

  // get doctor shifts starts and ends of the specific day
  $shift = get_shift_type_of_date($date, $doctor_id);

  $shift_start_end = get_doctor_shifts_starts_ends($doctor_id, $shift['shift_time'], $shift['shift_type']);


  foreach ($shift_start_end as $index => $shse) {
    if (strtotime($shse['start']) <= strtotime($time) && strtotime($time) <= strtotime($shse['end']))
      return get_dr_shift_json_by_date($date, $doctor_id)->shifts[$index]->$json_property;
  }
}


////////////////////////////////////////////////////////////////////////////// end of doctor shifts


function create_woocommerce_product_for_doctor_visit($doctor_id, $price) {



  // getting user display name

  $doctor_obj = get_user_by('id', $doctor_id);

  $doctor_name = $doctor_obj->display_name;



  /// get woocommerce product category for visit-doctor

  //$new_product_cats_arr = [];

  $dr_visit_cat_id = get_term_by('slug', 'dr-visit', 'product_cat')->term_id;

  //$new_product_cats_arr[] = $dr_visit_cat_id;

  //$term_ids = array_unique(array_map('intval', $new_product_cats_arr));



  // creating product

  $product = new WC_Product_Simple();

  $product->set_name('ویزیت ' . $doctor_name);

  $product->set_status('publish');

  $product->set_catalog_visibility('hidden');

  $product->set_price($price);

  $product->set_category_ids(array($dr_visit_cat_id));

  //$product->set_category_ids( array_map('intval', [$new_product_cats_arr]) );

  //$product->set_regular_price(19.99);

  $product->set_sold_individually(true);

  //$product->set_image_id( $image_id );

  $product->set_downloadable(true);

  $product->set_virtual(true);

  //$src_img = wp_get_attachment_image_src( $image_id, 'full' );

  //$file_url = reset( $src_img );

  //$file_md5 = md5( $file_url );

  // $download = new WC_Product_Download();

  // $download->set_name( get_the_title( $image_id ) );

  // $download->set_id( $file_md5 );

  // $download->set_file( $file_url );

  // $downloads[$file_md5] = $download;

  // $product->set_downloads( $downloads );

  $product->save();

  add_post_meta($product->id, '_purchase_note', '<a href="#">باز کردن اپلیکیشن</a>');



  //// set category for this product => not working

  //$dr_visit_product_category_term_id = get_term_by('slug', 'dr-visit	', 'product_cat');

  //wp_set_object_terms($product->id, $dr_visit_product_category_term_id, 'product_cat');



  return $product->id;

}



function has_user_role($user_id, $role) {

  $user = get_user_by('id', $user_id);

  if (in_array($role, (array) $user->roles)) {

    return true;

  }

  return false;

}



function update_woo_product_price($product_id, $price) {

  if (get_post_type($product_id) && get_post_type($product_id) === 'product') {

    $regular_price = update_post_meta($product_id, '_regular_price', $price);

    $price = update_post_meta($product_id, '_price', $price);

    if ($regular_price && $price)

      return true;

    else

      return false;

  } else

    return false;

}


function get_dr_product_id($doctor_id) {
  global $wpdb;
  global $shift_settings_table;
  //return $doctor_id;
  $result = $wpdb->get_results("SELECT * from `$shift_settings_table` WHERE `doctor_id` = $doctor_id AND `meta_key` = 'shift_woo_product'");
  //print_r($result);
  if (count($result) >= 1) {
    return $result[0]->meta_value;
  }
  return false;
}



function create_dr_visit_woo_order($user_id, $doctor_id, $user_visit_date, $user_visit_time, $order_status = 'completed', $visit_price = null) {
  $user = get_user_by('id', $user_id);

  if (empty($visit_price))
    $visit_price = get_doctor_shift_schedule_index_by_dateTime($user_visit_date, $user_visit_time, $doctor_id)->shift_price;
  //$visit_price = get_doctor_setting_json('visit_price', $doctor_id);

  global $woocommerce;
  $address = array(
    'first_name' => $user->user_firstname,
    'last_name' => $user->user_lastname,
    'email' => $user->user_email,
    'phone' => $user->phone,
    'citizen_id' => $user->citizen_id
  );

  // get doctor product id
  $dr_product_id = get_dr_product_id($doctor_id);

  // Now we create the order
  $order = wc_create_order(array('customer_id' => $user_id));

  // The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php
  $order->add_product(get_product($dr_product_id), 1); // This is an existing SIMPLE product
  $order->set_address($address, 'shipping');
  $order->set_address($address, 'billing');

  //$order->add_coupon('','','');

  $order->calculate_totals();
  $order->set_total($visit_price);

  $order->update_status($order_status, 'ثبت سفارش', TRUE);
  $order->save();

  if ($order->id) {
    add_post_meta($order->id, 'vd_order_type', 'visit');
    add_post_meta($order->id, 'vd_order_total_price', $visit_price);
    add_post_meta($order->id, 'vd_user_id', $user_id);
    add_post_meta($order->id, 'vd_doctor_id', $doctor_id);
    add_post_meta($order->id, 'vd_user_visit_date', $user_visit_date); // reserved dr visit date buy user
    add_post_meta($order->id, 'vd_user_visit_time', $user_visit_time); // reserved dr visit time buy user
    add_post_meta($order->id, 'vd_visit_time_per_patient', get_json_shift_property_by_shift_time($user_visit_date, $user_visit_time, null, 'shift_visit_time', $doctor_id));
    add_post_meta($order->id, 'vd_visit_status', 0); // reserved order status
    add_post_meta($order->id, 'vd_visit_address', get_json_shift_property_by_shift_time($user_visit_date, $user_visit_time, null, 'shift_address', $doctor_id));
    add_post_meta($order->id, 'vd_visit_phone', get_json_shift_property_by_shift_time($user_visit_date, $user_visit_time, null, 'shift_phone', $doctor_id));
    add_post_meta($order->id, 'vd_visit_latitude', get_json_shift_property_by_shift_time($user_visit_date, $user_visit_time, null, 'shift_latitude', $doctor_id));
    add_post_meta($order->id, 'vd_visit_longitude', get_json_shift_property_by_shift_time($user_visit_date, $user_visit_time, null, 'shift_longitude', $doctor_id));

    @WC()->session->order_awaiting_payment = $order->id;

    return $order->id;
  } else
    return false;
}

function get_order_dv_meta($order_id) {

  $res['vd_order_type'] = get_post_meta($order_id, 'vd_order_type', true);
  $res['vd_user_id'] = get_post_meta($order_id, 'vd_user_id', true);
  $res['vd_doctor_id'] = get_post_meta($order_id, 'vd_doctor_id', true);
  $res['vd_doctor_name'] = get_user_by('id', get_post_meta($order_id, 'vd_doctor_id', true))->display_name;
  $res['vd_user_visit_date'] = get_post_meta($order_id, 'vd_user_visit_date', true);
  $res['vd_user_visit_time'] = get_post_meta($order_id, 'vd_user_visit_time', true);
  $res['vd_visit_time_per_patient'] = get_post_meta($order_id, 'vd_visit_time_per_patient', true);
  $res['vd_visit_status'] = get_post_meta($order_id, 'vd_visit_status', true);
  $res['vd_visit_phone'] = get_post_meta($order_id, 'vd_visit_phone', true);
  $res['vd_visit_address'] = get_post_meta($order_id, 'vd_visit_address', true);
  $res['vd_visit_latitude'] = get_post_meta($order_id, 'vd_visit_latitude', true);
  $res['vd_visit_longitude'] = get_post_meta($order_id, 'vd_visit_longitude', true);
  $res['vd_order_total_price'] = get_post_meta($order_id, 'vd_order_total_price', true);

  return $res;

}

/////////////// from here: https://gist.github.com/lukecav/05afef12feaf980c121da9afb9291ad5
function get_orders_ids_by_product_id($product_id, $order_status = array('wc-completed')) {
  global $wpdb;

  $results = $wpdb->get_col("
        SELECT order_items.order_id
        FROM {$wpdb->prefix}woocommerce_order_items as order_items
        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
        WHERE posts.post_type = 'shop_order'
        AND posts.post_status IN ( '" . implode("','", $order_status) . "' )
        AND order_items.order_item_type = 'line_item'
        AND order_item_meta.meta_key = '_product_id'
        AND order_item_meta.meta_value = '$product_id'
    ");

  return $results;
}


function get_dr_reserved_appointment_by_date($date, $doctor_id) {

  $dr_product_id = get_dr_product_id($doctor_id);

  $doctor_orders = get_orders_ids_by_product_id($dr_product_id);

  $doctor_next_available_days = doctor_next_available_days($doctor_id);

  $result = [];

  $reserved_times_arr = [];
  foreach ($doctor_orders as $order) {
    $reserved_date = get_post_meta($order, 'vd_user_visit_date', true);
    $reserved_time = get_post_meta($order, 'vd_user_visit_time', true);
    $time_per_visit = get_post_meta($order, 'vd_visit_time_per_patient', true);
    $reserved_datetime = $reserved_date . ' ' . $reserved_time;

    if ($reserved_date === $date && get_post_status($order) === 'wc-completed') {

      $reserved_times_arr['order_id'] = $order;
      $reserved_times_arr['date'] = $reserved_date;
      $reserved_times_arr['time'] = $reserved_time;
      $reserved_times_arr['time-per-visit'] = $time_per_visit;

      $shift = get_shift_type_of_date($date, $doctor_id);
      $reserved_times_arr['shift-index'] = find_shift_of_shift_schedule($reserved_time, $shift['shift_time'], $shift['shift_type'], $doctor_id);

      $result[] = $reserved_times_arr;

    }
  }

  return $result;

}


function get_next_available_reservable($doctor_id) {
  global $wpdb;
  global $shift_schedule_table;
  global $shift_settings_table;

  $doctor_shift_setting = get_doctor_shift_setting($doctor_id);


  $next_days = doctor_next_available_days($doctor_id);

  // defining arrays
  $result = [];

  $doctor_enable = get_doctor_setting_json('enable', $doctor_id);
  $days = [];
  $shifts = [];
  $next_days_shift_types = [];
  $shift_start_end = [];
  $available_reservations = [];
  //end of  defining arrays


  foreach ($next_days as $date) {
    $shift_type = get_shift_type_of_date($date, $doctor_id);

    ////// adding to days
    $days[] = array(date => $date, day => get_farsi_weekdays($date), enable => get_doctor_shift_json('enable', $doctor_id, $shift_type['shift_time'], $shift_type['shift_type']), holiday => is_date_off($date));

    ////// adding to next_days_shift_types
    $next_days_shift_types["date" . str_replace("-", "", $date)] = $shift_type;

    ////// adding to shifts

    $shifts["date" . str_replace("-", "", $date)] = get_doctor_shift_schedule($doctor_id, $shift_type['shift_time'], $shift_type['shift_type']);

    ////// adding to shift_start_end
    $shift_start_end["date" . str_replace("-", "", $date)] = get_doctor_shifts_starts_ends($doctor_id, $shift_type['shift_time'], $shift_type['shift_type']);

    ///// adding to all_queue
    $all_queue["date" . str_replace("-", "", $date)] = get_dr_all_start_queue_of_shifts_of_date($date, $doctor_id);

    ////// adding to reserved_reservations
    $reserved_reservations["date" . str_replace("-", "", $date)] = get_dr_reserved_appointment_by_date($date, $doctor_id);

  }



  ///////////// the end: droping to result arr
  $result['doctor_enable'] = $doctor_enable;
  $result['days'] = $days;
  $result['shift_setting'] = $doctor_shift_setting;
  $result['shifts'] = $shifts;
  $result['shift_types'] = $next_days_shift_types;
  $result['shift_start_end'] = $shift_start_end;
  $result['all_queues'] = $all_queue;
  $result['reserved_orders'] = $reserved_reservations;

  return $result;

}



//////////////////////////////////users profile

// get user orders
function get_user_orders($user_id) {
  //$dv_order = get_order_dv_meta($order_id);
  global $wpdb;
  $table = $wpdb->prefix . 'postmeta';
  $user_orders = $wpdb->get_results("SELECT post_id FROM $table WHERE meta_key = 'vd_user_id' AND meta_value = $user_id ORDER BY post_id DESC");

  if (!count($user_orders))
    return false;

  foreach ($user_orders as $user_order) {
    $order_id = $user_order->post_id;

    $order = wc_get_order($order_id); //getting order Object

    //if ($order === false)
    //    return false;

    //$order_arr['id'] = $order_details->

    $order_arr = array(
      'id' => $order->get_id(),
      'order_number' => $order->get_order_number(),
      'created_at' => $order->get_date_created()->date('Y-m-d H:i'),
      'created_at_jalali' => gregorian_to_jalali($order->get_date_created()->date('Y'), $order->get_date_created()->date('m'), $order->get_date_created()->date('d'), '-').' '.$order->get_date_created()->date('H:i'),
      'updated_at' => $order->get_date_modified()->date('Y-m-d H:i'),
      'updated_at_jalali' => gregorian_to_jalali($order->get_date_modified()->date('Y'), $order->get_date_modified()->date('m'), $order->get_date_modified()->date('d'), '-').' '.$order->get_date_modified()->date('H:i'),
      'completed_at' => !empty($order->get_date_completed()) ? $order->get_date_completed()->date('Y-m-d H:i') : '',
      'completed_at_jalali' => !empty($order->get_date_completed()) ? gregorian_to_jalali($order->get_date_completed()->date('Y'), $order->get_date_completed()->date('m'), $order->get_date_completed()->date('d'), '-').' '.$order->get_date_completed()->date('H:i') : '',
      'status' => $order->get_status(),
      'currency' => $order->get_currency(),
      'total' => wc_format_decimal($order->get_total(), $dp),
      'subtotal' => wc_format_decimal($order->get_subtotal(), $dp),
      'total_line_items_quantity' => $order->get_item_count(),
      'total_tax' => wc_format_decimal($order->get_total_tax(), $dp),
      'total_shipping' => wc_format_decimal($order->get_total_shipping(), $dp),
      'cart_tax' => wc_format_decimal($order->get_cart_tax(), $dp),
      'shipping_tax' => wc_format_decimal($order->get_shipping_tax(), $dp),
      'total_discount' => wc_format_decimal($order->get_total_discount(), $dp),
      'shipping_methods' => $order->get_shipping_method(),
      'order_key' => $order->get_order_key(),
      'payment_details' => array(
        'method_id' => $order->get_payment_method(),
        'method_title' => $order->get_payment_method_title(),
        'paid_at' => !empty($order->get_date_paid()) ? $order->get_date_paid()->date('Y-m-d H:i') : '',
        'paid_at_jalali' => !empty($order->get_date_paid()) ? gregorian_to_jalali($order->get_date_paid()->date('Y'), $order->get_date_paid()->date('m'), $order->get_date_paid()->date('d'), '-').' '.$order->get_date_paid()->date('H:i') : '',
      ),
      'billing_address' => array(
        'first_name' => $order->get_billing_first_name(),
        'last_name' => $order->get_billing_last_name(),
        'company' => $order->get_billing_company(),
        'address_1' => $order->get_billing_address_1(),
        'address_2' => $order->get_billing_address_2(),
        'city' => $order->get_billing_city(),
        'state' => $order->get_billing_state(),
        'formated_state' => WC()->countries->states[$order->get_billing_country()][$order->get_billing_state()], //human readable formated state name
        'postcode' => $order->get_billing_postcode(),
        'country' => $order->get_billing_country(),
        'formated_country' => WC()->countries->countries[$order->get_billing_country()], //human readable formated country name
        'email' => $order->get_billing_email(),
        'phone' => $order->get_billing_phone()
      ),
      'shipping_address' => array(
        'first_name' => $order->get_shipping_first_name(),
        'last_name' => $order->get_shipping_last_name(),
        'company' => $order->get_shipping_company(),
        'address_1' => $order->get_shipping_address_1(),
        'address_2' => $order->get_shipping_address_2(),
        'city' => $order->get_shipping_city(),
        'state' => $order->get_shipping_state(),
        'formated_state' => WC()->countries->states[$order->get_shipping_country()][$order->get_shipping_state()], //human readable formated state name
        'postcode' => $order->get_shipping_postcode(),
        'country' => $order->get_shipping_country(),
        'formated_country' => WC()->countries->countries[$order->get_shipping_country()] //human readable formated country name
      ),
      'note' => $order->get_customer_note(),
      'customer_ip' => $order->get_customer_ip_address(),
      'customer_user_agent' => $order->get_customer_user_agent(),
      'customer_id' => $order->get_user_id(),
      'view_order_url' => $order->get_view_order_url(),
      'line_items' => array(),
      'shipping_lines' => array(),
      'tax_lines' => array(),
      'fee_lines' => array(),
      'coupon_lines' => array(),
      'dv_details' => get_order_dv_meta($order_id)
    );


    $res[] = $order_arr;
  }

  return $res;

}

















function forMyTest($doctor_id) {

  return get_post_status(2281);

  return get_dr_reserved_appointment_by_date('1401-03-08', 6);

  return get_next_available_reservable(6); 

  return;

  return get_dr_reserved_appointment_by_date();


  //return get_queue_starts_of_shift('04:59', '08:59', 12);

  //return diff_two_times_in_minutes('01:59', '04:02');

  return get_dr_all_start_queue_of_shifts_of_date('1401-03-01', 6);

  return get_shift_type_of_date('1401-03-02', 4);


  print_r(get_dr_reserved_time_of_date('1401-03-02', 4));
  return;

  print_r(doctor_next_available_days($doctor_id));
  return;


  print_r(doctor_next_available_days($doctor_id));
  return;

  echo create_dr_visit_woo_order(1, 6, '1401-03-08', '06:20', $visit_price = null);
  return;

  //echo get_json_shift_property_by_shift_time('1401-03-08', '06:20', null, 'shift_visit_time', 6);

  //print_r( get_dr_shift_json_by_date('1401-03-01', $doctor_id)  );
  //print_r( get_doctor_shifts_starts_ends(6, 'sunday', 'weekday') );
  //$date = "1401-03-01";

  //return get_day_of_date($date);
  //return get_shift_type_of_date($date, 4);
}