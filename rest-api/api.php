<?php
date_default_timezone_set('Asia/Tehran');

include_once(plugin_dir_path(__DIR__) . 'constant.php');
include_once(plugin_dir_path(__DIR__) . 'assets/php/jdf.php');

$dv_settings_table = SETTINGS_TABLE;

//////////////////////////////////////////////////////////////// test
{

  ////// control api_key
  add_filter('rest_pre_echo_response', function($response, $object, $request) {
    extract($_POST);
    extract($_GET);

    $current_rest_route = current_api_endpoint();
    //return array_key_exists($current_rest_route['full_path'], rest_get_server()->get_routes($current_rest_route['namespace'])) ? 'hast' : 'nist';


    if ((SECURE_ENDPOINTS === 'all' && !in_array($current_rest_route['full_path'], WHILE_LIST_ENDPOINTS)) || (SECURE_ENDPOINTS === 'rad' && $current_rest_route['namespace'] === 'rad' && !in_array($current_rest_route['full_path'], WHILE_LIST_ENDPOINTS))) {
      /////// get token jwt
      if (!empty($api_key))
        $jwt = $api_key;
      elseif (!empty($_SERVER['HTTP_API_KEY']))
        $jwt = $_SERVER['HTTP_API_KEY'];
      else
        $jwt = '';
      /////// end of getting token jwt


      if (!$jwt)
        return rad_response_http('api_key missing', 401);

      if (!is_jwt_valid($jwt, JWT_SECRET))
        return rad_response_http('api_key is invalid', 401);
    }

    //* Return the new response 
    return $response;
  },
    10,
    3);
  ////// end of controlling api_key


  add_action('rest_api_init',
    'validate_jwt_route');
  function validate_jwt_route() { 
    register_rest_route('rad',
      "/validate-jwt/",
      ['methods' => 'GET',
        'callback' => function() {
          extract($_GET);
          extract($_POST);

          if (!$public_api_key || $public_api_key !== PUBLIC_API_KEY)
            return new WP_Error('error', 'public api key error', array('status' => 401));

          if ($api_key)
            $jwt = $api_key;
          elseif ($_SERVER['HTTP_API_KEY'])
            $jwt = $_SERVER['HTTP_API_KEY'];
          else
            $jwt = '';

          //$jwt = (($api_key ? $api_key : $_SERVER['HTTP_API_KEY']) ? $_SERVER['HTTP_API_KEY'] : null);

          if (!$jwt)
            return false;

          if (!is_jwt_valid($jwt, JWT_SECRET))
            return false;

          $tokenParts = explode('.', $jwt);
          $user_id = json_decode(base64_decode($tokenParts[1]))->user_id;

          $user = is_user_exists($user_id);

          return $user ? $user : false;

        }]);
  }

  add_action('rest_api_init',
    'mytest_route');
  function mytest_route() {
    register_rest_route('rad',
      "/test/",
      ['methods' => 'GET',
        'callback' => 'mytest']);
  }

  function mytest() {
    try
    {
      extract($_GET);
      //return generate_jwt(array('user_id' => 1, 'exp' => 1702970148), JWT_SECRET);
      //return get_doctor_visit_price($doctor_id);
      //return get_doctor_shift_setting_on_shift_type($doctor_id, $shift_time = 'ed', $shift_type = 'ed');
      //return create_dr_visit_woo_order($user_id, $doctor_id);
      //return get_doctor_shift_schedule($doctor_id, $shift_time = 'ed', $shift_type = 'ed');
      //return is_shift_enable($doctor_id, $shift_time = 'ed', $shift_type = 'ed');
      //return shift_count($doctor_id, $shift_time , $shift_type);
      //print_r(get_doctor_shift_json('shifts', 6, '1401-01-30', 'date'));
      //return (get_doctor_shifts_starts_ends(6, 'even', 'even'));
      //return get_day_of_date('1401-01-25');
      //return is_shift_available('1400-02-15', '05:40');
      //return match_userreservationdate_with_doctor_shifts('1401-06-15', 6);
      // return get_dr_shift_json_by_date('1401-06-07', 6);
      //return find_shift_of_shift_schedule('06:20', '1401-01-30', 'date', 6);
      //return get_dr_shift_json_by_date_time('1401-05-10 14:10', 6);

      //return gregorianToHijri('2022/05/29');

      //return is_user_logged_in();

      //return logoutUser();

      //$message = "patterncode:7yev7tayz3;code:1234;company:test";
      //return $result_sms = send_sms_ippanel('+989051066543', $message);

      return setting();

      return send_sms('09179999315',
        'ثبت نام شما با موفقیا انجام شد. خانه کیفیت ایرانیان');

      return sendOTP('09179999315');

      return create_dr_visit_woo_order(2,
        6,
        '1401-05-24',
        '18:20',
        'pending');

      return get_activated_sms_setting()->verificationCodePattern;

      return get_woocommerce_gateways();

      return setting();


      return get_user_orders(2);

      return get_order_dv_meta(2381);

      return get_doctor_shift_schedule_index_by_dateTime('1401-04-05',
        '03:23',
        6);

      print_r(get_avatar_url(6, isset($attributes['avatarSize']) ? $attributes['avatarSize'] : 150));
      return;

      print_r(is_mobile_exists('989051066543'));


      return is_logged_in();



      return login_with_mobile_otp('+989179999315',
        '4968');



      return logoutUser();



      return setOTP('+989179999315');
      return verifyOTP('+989179999315',
        '9877');



      session_start();
      print_r($_SESSION);
      return;





      //return login_with_password('radreza90', '1037221614');

      return doctor_next_available_days(6);

      return is_date_off('1401-10-06');
      return jalali_to_hijri('1401-03-08');

      return iran_holidays();
      return forMyTest(6);
      return get_dr_reserved_appointment_by_date('',
        4);


    }
    catch (error $e) {
      return new WP_Error('error',
        __($e->getMessage(),
          'rad-api'),
        array('status' => 401));
    }
  }
}






//////////////////////////////////////////////////////////////// Authentication

//check if user only mobile already signed up
add_action('rest_api_init', 'mobile_not_exists_route');
function mobile_not_exists_route() {
  register_rest_route('rad',
    "/mobile-exists/",
    ['methods' => 'GET',
      'callback' => function () {
        try {
          extract($_GET);

          if (empty($mobile))
            throw new Exception('شماره موبایل را وارد کنید');;

          $user = is_user_exists($mobile);
          if (!$user)
            throw new Exception('شما ثبت نام نکرده اید.لطفا ابتدا ثبت نام کنید');

          return $user->data;

        } catch (Exception $e) {
          return new WP_Error('error', $e->getMessage(), array('status' => 401));
        }

      }]);
}
//check if user mobile or email already signed up
add_action('rest_api_init', 'mobile_email_not_exists_route');
function mobile_email_not_exists_route() {
  register_rest_route('rad',
    "/mobile-email-not-exists/",
    ['methods' => 'GET',
      'callback' => function () {
        try {
          extract($_GET);

          if (empty($mobile))
            throw new Exception('شماره موبایل را وارد کنید');
          if (empty($email))
            throw new Exception('ایمیل را وارد کنید');

          $mobile = is_user_exists($mobile);
          if ($mobile)
            throw new Exception('این شماره موبایل در سیستم موجود است. شماره موبایل دیگری را وارد کنید');

          $email = is_user_exists($email);
          if ($email)
            throw new Exception('این ایمیل در سیستم موجود است. ایمیل دیگری را وارد کنید');

          return true;

        } catch (Exception $e) {
          return new WP_Error('error', $e->getMessage(), array('status' => 401));
        }

      }]);
}

///////// Register
add_action('rest_api_init', 'register_user_route');
function register_user_route() {
  register_rest_route('rad',
    "/register-user/",
    ['methods' => 'GET',
      'callback' => function () {
        try {
          extract($_GET);

          if (empty($first_name))
            throw new Exception("نام را وارد کنید");
          if (empty($last_name))
            throw new Exception("نام خانوادگی را وارد کنید");
          if (empty($password))
            throw new Exception("کلمه عبور را وارد کنید");
          if (empty($country_code))
            throw new Exception("کد کشور را وارد کنید");
          if (empty($mobile))
            throw new Exception("شماره موبایل را وارد کنید");
          if (empty($email))
            throw new Exception("ایمیل را وارد کنید");
          if (empty($citizen_id))
            throw new Exception("کد ملی را وارد کنید");

          $user_id = register_user($first_name, $last_name, $password, $country_code, $mobile, $email, $citizen_id);

          if (!$user_id['status'])
            throw new Exception(!$user_id['result']);

          return $user_id['result'];

        } catch (Exception $e) {
          return new WP_Error('error', $e->getMessage(), array('status' => 401));
        }

      }]);
}

///////// Verify OTP then Register
add_action('rest_api_init', 'verify_otp_register_route');
function verify_otp_register_route() {
  register_rest_route('rad',
    "/verify-otp-register-user/",
    ['methods' => 'GET',
      'callback' => function () {
        try {
          extract($_GET);

          //if (empty($otp))
          //throw new Exception('کد تایید را وارد کنید');

          if (empty($first_name))
            throw new Exception("نام را وارد کنید");
          if (empty($last_name))
            throw new Exception("نام خانوادگی را وارد کنید");
          if (empty($password))
            throw new Exception("کلمه عبور را وارد کنید");
          if (empty($country_code))
            throw new Exception("کد کشور را وارد کنید");
          if (empty($mobile))
            throw new Exception("شماره موبایل را وارد کنید");
          if (empty($email))
            throw new Exception("ایمیل را وارد کنید");
          if (empty($citizen_id))
            throw new Exception("کد ملی را وارد کنید");


          //////// check otp
          if (!empty($otp)) {
            // verify otp
            $verify_mobile = remove_from_first(array('+', '0', ' '), $country_code . $mobile);
            $otp_result = verifyOTP($verify_mobile, $otp);

            if (!$otp_result)
              throw new Exception("کد تایید اشتباه است");
          }


          // create user
          $user_id = register_user($first_name, $last_name, $password, $country_code, $mobile, $email, $citizen_id);

          if (!$user_id['status'])
            throw new Exception(!$user_id['result']);

          // login user
          $login = login_with_password($user_id['result'], $password);

          if (!$login['status'])
            throw new Exception($login['result']);

          return $login['result'];


        } catch (Exception $e) {
          return new WP_Error('error', $e->getMessage(), array('status' => 401));
        }

      }]);
}


///////// Login with password => Login contains: user_login, user_email meta_mobile:+9891....
add_action('rest_api_init', 'login_with_password_route');
function login_with_password_route() {
  register_rest_route('rad',
    "/login-with-password/",
    ['methods' => 'GET',
      'callback' => function () {
        try {
          extract($_GET);

          if (empty($login))
            throw new Exception("نام کاربری را وارد کنید");
          if (empty($password))
            throw new Exception("کلمه عبور را وارد کنید");

          $login = login_with_password($login, $password);

          if ($login['status'])
            return $login['result'];
          else {
            // $login['status'] is false and error msg throwed to catch
            throw new Exception($login['result']);
          }

        } catch (Exception $e) {
          return new WP_Error('error', $e->getMessage(), array('status' => 401));
        }

      }]);
}

///////// Send otp
add_action('rest_api_init', 'send_otp_route');
function send_otp_route() {
  register_rest_route('rad',
    "/send-otp/",
    ['methods' => 'GET',
      'callback' => function () {
        try {
          extract($_GET);

          if (empty($mobile))
            throw new Exception("موبایل خود را وارد کنید");

          $mobile = remove_from_first(array('+', '0', ' '), $mobile);
          $res = sendOTP($mobile);
          //$res = 'sent';

          if ($res !== 'sent')
            throw new Exception("کد تایید ارسال نشد. کد خطا: $res");

          return $res;

        } catch (Exception $e) {
          return new WP_Error('error', $e->getMessage(), array('status' => 401));
        }

      }]);
}

///////// Verify otp
add_action('rest_api_init', 'verify_otp_route');
function verify_otp_route() {
  register_rest_route('rad',
    "/verify-otp/",
    ['methods' => 'GET',
      'callback' => function () {
        try {
          extract($_GET);

          if (empty($mobile))
            throw new Exception("موبایل خود را وارد کنید");

          if (empty($otp))
            throw new Exception("کد تایید را وارد کنید");

          $mobile = remove_from_first(array('+', '0', ' '), $mobile);
          $res = verifyOTP($mobile, $otp);

          if (!$res)
            throw new Exception("کد تایید اشتباه است");

          return $res;

        } catch (Exception $e) {
          return new WP_Error('error', $e->getMessage(), array('status' => 401));
        }

      }]);
}

///////// Login with mobile otp
add_action('rest_api_init', 'login_with_mobile_otp_route');
function login_with_mobile_otp_route() {
  register_rest_route('rad',
    "/login-with-mobile-otp/",
    ['methods' => 'GET',
      'callback' => function () {
        try {
          extract($_GET);

          if (empty($mobile))
            throw new Exception("موبایل خود را وارد کنید");
          if (empty($otp))
            throw new Exception("کد تایید را وارد کنید");

          $login = login_with_mobile_otp($mobile, $otp);

          if ($login['status'])
            return $login['result'];
          else {
            // $login['status'] is false and error msg throwed to catch
            throw new Exception($login['result']);
          }

        } catch (Exception $e) {
          return new WP_Error('error', $e->getMessage(), array('status' => 401));
        }

      }]);
}

///////// Login with mobile witout password and otp
add_action('rest_api_init', 'login_by_userLogin_route');
function login_by_userLogin_route() {
  register_rest_route('rad',
    "/login-by-user-login/",
    ['methods' => 'GET',
      'callback' => function () {
        try {
          extract($_GET);

          if (empty($user_login))
            throw new Exception("موبایل خود را وارد کنید");

          $login = login_by_userLogin($user_login);

          if ($login['status'])
            return $login['result'];
          else {
            // $login['status'] is false and error msg throwed to catch
            throw new Exception($login['result']);
          }

        } catch (Exception $e) {
          return new WP_Error('error', $e->getMessage(), array('status' => 401));
        }

      }]);
}

///////// Login with browser and redirect
add_action('rest_api_init', 'login_with_browser_redirect_route');
function login_with_browser_redirect_route() {
  register_rest_route('rad',
    "/login-with-browser-redirect/",
    ['methods' => 'GET',
      'callback' => function () {
        try {
          extract($_GET);

          if (empty($user_login))
            throw new Exception("نام کاربری (شماره تماس، ایمیل، نام کاربری) خود را وارد کنید");

          if (empty($user_pass))
            throw new Exception('رمز عبور را وارد کنید');

          if (empty($url))
            throw new Exception('لینک ریدایرکت را وارد کنید');

          $user = is_user_exists($user_login);
          if (!$user)
            throw new Exception('کاربر وجود ندارد');

          $authenticate = authenticate_user_table($user->data->user_login, $user_pass);
          if (!$authenticate['status'])
            throw new Exception($authenticate['result']);


          $login = login_by_userLogin($user_login);

          if ($login['status']) {
            header("Location: $url");
            exit;
          } else {
            throw new Exception($login['result']);
          }

        } catch (Exception $e) {
          return new WP_Error('error', $e->getMessage(), array('status' => 401));
        }

      }]);
}
///////// Login with browser and redirect
add_action('rest_api_init', 'login_with_browser_redirect_pay_route');
function login_with_browser_redirect_pay_route() {
  register_rest_route('rad',
    "/login-with-browser-redirect-pay/",
    ['methods' => 'GET',
      'callback' => function () {
        try {
          extract($_GET);

          if (empty($user_login))
            throw new Exception("نام کاربری (شماره تماس، ایمیل، نام کاربری) خود را وارد کنید");

          if (empty($user_pass))
            throw new Exception('رمز عبور را وارد کنید');

          if (empty($order_id))
            throw new Exception('شماره فاکتور را وارد کنید');

          if (empty($gateway_id))
            throw new Exception('آیدی درگاه پرداخت را وارد کنید');

          if (empty($gateway_title))
            throw new Exception('عنوان درگاه پرداخت را وارد کنید');

          $user = is_user_exists($user_login);
          if (!$user)
            throw new Exception('کاربر وجود ندارد');


          $authenticate = authenticate_user_table($user->data->user_login, $user_pass);
          if (!$authenticate['status'])
            throw new Exception($authenticate['result']);


          $login = login_by_userLogin($user_login);

          if ($login['status']) {

            update_post_meta($order_id, '_payment_method', $gateway_id);
            update_post_meta($order_id, '_payment_method_title', $gateway_title);


            // redirect to gateway with order_id
            $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
            $result = $available_gateways[$gateway_id]->process_payment($order_id);

            // Redirect to success/confirmation/payment page
            if ($result['result'] == 'success') {

              $result = apply_filters('woocommerce_payment_successful_result', $result, $order_id);

              wp_redirect($result['redirect']);
              exit;
            }

          } else {
            throw new Exception($login['result']);
          }

        } catch (Exception $e) {
          return new WP_Error('error', $e->getMessage(), array('status' => 401));
        }

      }]);
}


///////// is user logged in
add_action('rest_api_init', 'is_logged_in_route');
function is_logged_in_route() {
  register_rest_route('rad',
    "/is-logged-in/",
    ['methods' => 'GET',
      'callback' => function () {

        extract($_GET);

        return is_logged_in();

      }]);
}

///////// Logout
add_action('rest_api_init', 'logoutUser_route');
function logoutUser_route() {
  register_rest_route('rad',
    "/logout/",
    ['methods' => 'GET',
      'callback' => function () {

        extract($_GET);

        return logoutUser();

      }]);
}

//////////////////////////////////////////////////////////////// user profile

{
  add_action('rest_api_init',
    'update_user_password_route');
  function update_user_password_route() {
    register_rest_route('rad',
      "/update-user-password/",
      ['methods' => 'GET',
        'callback' => function() {
          try {
            extract($_GET);

            if (empty($user_id))
              throw new Exception('آیدی کاربر خالی است');

            if (empty($password))
              throw new Exception('رمز عبور خالی است');

            wp_set_password($password, $user_id);

            // logout user
            logoutUser();

            return true;

          }catch(error $e) {
            return new WP_Error('error', __($e->getMessage(), 'rad-api'), array('status' => 401));
          }
        }]);
  }

  add_action('rest_api_init',
    'update_user_profile_route');
  function update_user_profile_route() {
    register_rest_route('rad',
      "/update-user-profile/",
      ['methods' => 'GET',
        'callback' => function() {
          try {
            extract($_GET);

            if (empty($user_id))
              throw new Exception('آیدی کاربر خالی است');

            if (empty($first_name))
              throw new Exception('نام کاربر خالی است');

            if (empty($last_name))
              throw new Exception('نام خانوادگی کاربر خالی است');

            $updated_user = wp_update_user(array ('ID' => $user_id, 'first_name' => $first_name, 'last_name' => $last_name, 'display_name' => $first_name . ' ' . $last_name));

            if (is_wp_error($updated_user))
              throw new Exception('به دلیل بروز خطا، کاربر بروزرسانی نشد');

            $new_user_profile = is_user_exists($user_id)->data;

            session_start();
            $_SESSION['login'] = $new_user_profile;

            return $new_user_profile;

          }catch(error $e) {
            return new WP_Error('error', __($e->getMessage(), 'rad-api'), array('status' => 401));
          }
        }]);
  }



  add_action('rest_api_init',
    'get_user_orders_route');
  function get_user_orders_route() {
    register_rest_route('rad',
      "/get-user-orders/",
      ['methods' => 'GET',
        'callback' => function() {
          try {
            extract($_GET);

            if (empty($user_id))
              throw new Exception('آیدی کاربر را وارد کنید');

            return get_user_orders($user_id);

          } catch(error $e) {
            return new WP_Error('error', __($e->getMessage(), 'rad-api'), array('status' => 401));
          }
        }]);
  }

  add_action('rest_api_init',
    'update_user_profile_image_route');
  function update_user_profile_image_route() {
    register_rest_route('rad',
      "/update-user-profile-image/",
      ['methods' => 'POST',
        'callback' => function() {
          try {
            extract($_POST);

            if (empty($user_id))
              throw new Exception('آیدی کاربر را وارد کنید');

            if (!is_logged_in())
              throw new Exception('شما لاگین نیستید');


            $uploaded_image_id = upload_to_wordpress_library('profileImg');

            //return $uploaded_image_id;

            if (!$uploaded_image_id)
              throw new Exception('مشکلی در آپلود فایل بوجود آمد');


            //$profile_image_url = mmp_plugin_change_profile_picture($user_id, $uploaded_image_id); => doesn't work idk, bellow is direct code of this function
            ////////// mmp_plugin_change_profile_picture ///////////
            $post_id = get_mmp_post_id($user_id);

            //return "done";

            // Save user meta.
            update_user_option($user_id, 'metronet_post_id', $post_id);
            update_user_option($user_id, 'metronet_image_id', $uploaded_image_id); // Added via this thread (Props Solinx) - https://wordpress.org/support/topic/storing-image-id-directly-as-user-meta-data.

            set_post_thumbnail($post_id, $uploaded_image_id);

            $attachment_url = wp_get_attachment_url($uploaded_image_id);

            if (!$attachment_url)
              throw new Exception('مشکل در نسبت عکس به کاربر');


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
              throw new Exception('مشکل در نسبت عکس به کاربر');

            // update user profile image in session login
            $user = is_user_exists($user_id);
            session_start();
            $_SESSION['login'] = $user->data;

            return $user->data;

            //return $result['full'];
            ////////// mmp_plugin_change_profile_picture ///////////




          } catch(error $e) {
            return new WP_Error('error', __($e->getMessage(), 'rad-api'), array('status' => 401));
          }
        }]);
  }

}

//////////////////////////////////////////////////////////////// website setting
{
  add_action('rest_api_init',
    'get_setting_route');
  function get_setting_route() {
    register_rest_route('rad',
      "/get-setting/",
      ['methods' => 'GET',
        'callback' => 'get_setting']);
  }

  function get_setting() {
    try {
      global $dv_settings_table;
      extract($_GET);

      if (!$public_api_key || $public_api_key !== PUBLIC_API_KEY)
        return new WP_Error('error', 'public api key error', array('status' => 401));

      return setting();

    } catch(error $e) {
      return new WP_Error('error', __($e->getMessage(), 'rad-api'), array('status' => 401));
    }
  }
}


//////////////////////////////////////////////////////////////// website setting
{

  add_action('rest_api_init',
    'get_website_setting_route');
  function get_website_setting_route() {
    register_rest_route('rad',
      "/get-website-setting/",
      ['methods' => 'GET',
        'callback' => 'get_website_setting']);
  }

  function get_website_setting() {
    try {
      global $dv_settings_table;
      extract($_GET);

      if (!$public_api_key || $public_api_key !== PUBLIC_API_KEY)
        return new WP_Error('error', 'public api key error', array('status' => 401));

      return website_setting();

    } catch(error $e) {
      return new WP_Error('error', __($e->getMessage(), 'rad-api'), array('status' => 401));
    }
  }

  add_action('rest_api_init',
    'get_woocommerce_gateways_route');
  function get_woocommerce_gateways_route() {
    register_rest_route('rad',
      "/get-woocommerce-gateways/",
      ['methods' => 'GET',
        'callback' => 'get_woocommerce_gateways_api']);
  }

  function get_woocommerce_gateways_api() {
    try {
      global $dv_settings_table;
      extract($_GET);

      return get_woocommerce_gateways();

    } catch(error $e) {
      return new WP_Error('error', __($e->getMessage(), 'rad-api'), array('status' => 401));
    }
  }

  add_action('rest_api_init',
    'get_dv_user_route');
  function get_dv_user_route() {
    register_rest_route('rad',
      "/get-dv-user/",
      ['methods' => 'GET',
        'callback' => function() {
          try {
            global $dv_settings_table;
            extract($_GET);

            if (empty($user_login))
              return new WP_Error('error', 'کاربر را وارد کنید', array('status' => 401));

            if (empty($user_title))
              $user_title = 'کاربر';


            $user = is_user_exists($user_login);

            if (!$user)
              return new WP_Error('error', $user_title . ' پیدا نشد', array('status' => 401));

            return $user->data;

          } catch(error $e) {
            return new WP_Error('error', __($e->getMessage(), 'rad-api'), array('status' => 401));
          }
        }]);
  }

}



//////////////////////////////////////////////////////////////// app setting
{
  add_action('rest_api_init',
    'get_app_setting_route');
  function get_app_setting_route() {
    register_rest_route('rad',
      "/get-app-setting/",
      ['methods' => 'GET',
        'callback' => 'get_app_setting']);
  }

  function get_app_setting() {
    try {
      global $dv_settings_table;
      extract($_GET);

      if (!$public_api_key || $public_api_key !== PUBLIC_API_KEY)
        return new WP_Error('error', 'public api key error', array('status' => 401));

      return app_setting();

    } catch(error $e) {
      return new WP_Error('error', __($e->getMessage(), 'rad-api'), array('status' => 401));
    }
  }
}

//////////////////////////////////////////////////////////////// get doctors
{
  add_action('rest_api_init', 'get_drs_route');
  function get_drs_route() {
    register_rest_route('rad', "/doctors/", ['methods' => 'GET', 'callback' => 'get_drs']);
  }

  function get_drs() {
    try {
      extract($_GET);

      if (!$public_api_key || $public_api_key !== PUBLIC_API_KEY)
        return new WP_Error('error', 'public api key error', array('status' => 401));

      $doctors = get_users(array('role__in' => array(OPERATOR_ROLE)));
      //print_r($doctors);
      $new_doctors_arr = [];
      foreach ($doctors as $dr) {
        // remove some properties
        unset($dr->user_pass);
        unset($dr->user_login);
        unset($dr->user_email);
        unset($dr->user_url);
        unset($dr->user_activation_key);

        // add some properties
        $profile_picture = wp_get_attachment_url(get_user_meta($dr->ID, 'wp_metronet_image_id', true));
        $profile_picture = $profile_picture ? $profile_picture : get_avatar_url($dr->ID);
        $dr->profile_picture = $profile_picture;

        $dr->data->vd_dr_enable = get_doctor_setting_json('enable', $dr->data->ID);

        $dr->data->operator_title_description = get_user_meta($dr->data->ID, 'operator_title_description', true);

        $new_doctors_arr[] = $dr->data;
      }
      return($new_doctors_arr);

    } catch(error $e) {
      return new WP_Error('error', __($e->getMessage(),
        'rad-api'), array('status' => 401));
    }
  }
}
//////////////////////////////////////////////////////////////// test
{
  add_action('rest_api_init', 'get_dr_product_route');
  function get_dr_product_route() {
    register_rest_route('rad', "/get-dr-product/", ['methods' => 'GET', 'callback' => 'get_dr_product']);
  }

  function get_dr_product() {
    try {
      extract($_GET);
      return get_dr_product_id($doctor_id);
    } catch(error $e) {
      return new WP_Error('error', __($e->getMessage(),
        'rad-api'), array('status' => 401));
    }
  }
}
//////////////////////////////////////////////////////////////// get doctor shifts
{
  add_action('rest_api_init', 'get_dr_shift_setting_route');
  function get_dr_shift_setting_route() {
    register_rest_route('rad', "/get-dr-shift-setting/", ['methods' => 'GET', 'callback' => 'get_dr_shift_setting']);
  }

  function get_dr_shift_setting() {
    try {
      global $dv_settings_table;
      extract($_GET);

      return get_doctor_shift_setting($doctor_id);

    } catch(error $e) {
      return new WP_Error('error', __($e->getMessage(), 'rad-api'), array('status' => 401));
    }
  }
}
//////////////////////////////////////////////////////////////// get doctor shifts
{
  add_action('rest_api_init', 'get_dr_shifts_route');
  function get_dr_shifts_route() {
    register_rest_route('rad', "/get-dr-shifts/", ['methods' => 'GET', 'callback' => 'get_dr_shifts']);
  }

  function get_dr_shifts() {
    try {
      global $dv_settings_table;
      extract($_GET);

      return get_doctor_enabled_shifts($doctor_id);

    } catch(error $e) {
      return new WP_Error('error', __($e->getMessage(), 'rad-api'), array('status' => 401));
    }
  }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
{
  add_action('rest_api_init', 'get_next_available_reservable_route');
  function get_next_available_reservable_route() {
    register_rest_route('rad', "/next-available-reservable/", ['methods' => 'GET', 'callback' => 'get_next_available_reservable_api']);
  }

  function get_next_available_reservable_api() {
    try {
      global $dv_settings_table;
      extract($_GET);

      if (!$public_api_key || $public_api_key !== PUBLIC_API_KEY)
        return new WP_Error('error', 'public api key error', array('status' => 401));

      if (empty($doctor_id))
        return new WP_Error('error', 'آیدی دکتر را وارد کنید', array('status' => 401));

      return get_next_available_reservable($doctor_id);

    } catch(error $e) {
      return new WP_Error('error', __($e->getMessage(), 'rad-api'), array('status' => 401));
    }
  }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
{
  add_action('rest_api_init', 'create_dr_order_route');
  function create_dr_order_route() {
    register_rest_route('rad', "/create-dr-order/", ['methods' => 'GET', 'callback' => 'create_dr_order']);
  }

  function create_dr_order() {
    try {
      global $dv_settings_table;
      extract($_GET);

      if (empty($user_id) || empty($doctor_id) || empty($user_visit_date) || empty($user_visit_time))
        throw new Exception('همه پارامترها را وارد کنید');

      $order_number = create_dr_visit_woo_order($user_id, $doctor_id, $user_visit_date, $user_visit_time, 'pending');

      if (!$order_number)
        throw new Exception('مشکل در ایجاد فاکتور');

      return $order_number;

    } catch(error $e) {
      return new WP_Error('error', __($e->getMessage(), 'rad-api'), array('status' => 401));
    }
  }

  add_action('rest_api_init', 'get_woo_order_status_route');
  function get_woo_order_status_route() {
    register_rest_route('rad', "/get-woo-order-status/", ['methods' => 'GET', 'callback' => 'get_woo_order_status']);
  }

  function get_woo_order_status() {
    try {
      extract($_GET);

      if (empty($order_id))
        throw new Exception('شماره سفارش را وارد کنید');

      $order_status = get_post_status($order_id);
      if ($order_status)
        return $order_status;
      else
        throw new Exception('مشکلی در دریافت وضعیت سفارش بوجود آمد');

    } catch(error $e) {
      return new WP_Error('error', __($e->getMessage(), 'rad-api'), array('status' => 401));
    }
  }


  add_action('rest_api_init', 'get_dr_all_queues_and_reserved_times_by_date_route');
  function get_dr_all_queues_and_reserved_times_by_date_route() {
    register_rest_route('rad', "/get-dr-all-queues-and-reserved-times-by-date/", ['methods' => 'GET', 'callback' => 'get_dr_all_queues_and_reserved_times_by_date']);
  }

  function get_dr_all_queues_and_reserved_times_by_date() {
    try {
      extract($_GET);

      if (!$public_api_key || $public_api_key !== PUBLIC_API_KEY)
        return new WP_Error('error', 'public api key error', array('status' => 401));

      if (empty($doctor_id))
        throw new Exception('آیدی دکتر را وارد کنید');

      if (empty($date))
        throw new Exception('تاریخ را وارد کنید');

      ///// adding to all_queue
      $all_queue["date" . str_replace("-", "", $date)] = get_dr_all_start_queue_of_shifts_of_date($date, $doctor_id);

      ////// adding to reserved_reservations
      $reserved_reservations["date" . str_replace("-", "", $date)] = get_dr_reserved_appointment_by_date($date, $doctor_id);

      $result['all_queues'] = $all_queue;
      $result['reserved_orders'] = $reserved_reservations;

      return $result;

    } catch(error $e) {
      return new WP_Error('error', __($e->getMessage(), 'rad-api'), array('status' => 401));
    }
  }



}




///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////