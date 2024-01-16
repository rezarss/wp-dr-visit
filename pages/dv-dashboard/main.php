<?php
$app_setting = json_decode(get_db_row(SETTINGS_TABLE, 'meta_key', 'app_setting')[0]->meta_value);

add_action('admin_menu', 'visit_doctor_admin_menu');

function visit_doctor_admin_menu() {
  global $app_setting;
  add_menu_page(
    __('نوبت دهی ' . $app_setting->texts->operatorLabel, 'rad-doctor-visit'),
    __('نوبت دهی ' . $app_setting->texts->operatorLabel, 'rad-doctor-visit'),
    "edit_posts",
    "visit_doctor",
    "visit_doctor_function",
    null,
    2
  );
}

function visit_doctor_function() {
  global $app_setting;
  //add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style('custom-css', plugin_dir_url(__FILE__) . '../../assets/css/custom.css');
  wp_enqueue_style('persian-datepicker-css', plugin_dir_url(__FILE__) . '../../assets/css/persian-datepicker.css');
  wp_enqueue_script('persian-date-js', plugin_dir_url(__FILE__) . '../../assets/js/persian-date.js');
  wp_enqueue_script('persian-datepicker-js', plugin_dir_url(__FILE__) . '../../assets/js/persian-datepicker.js');
  wp_enqueue_script('moment-jalali-js', plugin_dir_url(__FILE__) . '../../assets/js/jalali-moment.browser.js');
  wp_enqueue_script('ckeditor', plugin_dir_url(__FILE__) . '../../assets/js/ckeditor/ckeditor.js');
  wp_enqueue_script('custom', plugin_dir_url(__FILE__) . '../../assets/js/custom.js');
  //});
  echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">';
  echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>';
  echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">';
  echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>';
  //echo '<script type="text/javascript" src="../../assets/js/ckeditor/ckeditor.js"></script>';
  
  $Shift_settings_table = SHIFT_SETTINGS_TABLE;
  $Shift_schedule_table = SHIFT_SCHEDULE_TABLE;
  $Settings_table = SETTINGS_TABLE;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST);
    //print_r($_POST);

    if ($page_code === 'dv')
      include('server-request/doctor-shifts.php');
    elseif ($page_code === 'application-settings')
      include('server-request/application-settings.php');
    elseif ($page_code === 'settings')
      include('server-request/settings.php');
  }

  ?>

  <div class="container-fluid">
    <div class="rounded shadow p-4 my-3" style="background: rgba(200,200,200,0.2)">

      <!-- Nav tabs -------------------------------------------------------------------   -->
      <ul class="nav nav-tabs" id="pluginTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">خوش آمدید</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="doctors-tab" data-bs-toggle="tab" data-bs-target="#doctors" type="button" role="tab" aria-controls="doctors" aria-selected="false"><?= $app_setting->texts->operatorsLabel ?></button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="application-settings-tab" data-bs-toggle="tab" data-bs-target="#application-settings" type="button" role="tab" aria-controls="application-settings" aria-selected="false">تنظیمات اپلیکیشن</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">تنظیمات</button>
        </li>
      </ul>
      <!-- Tab panes -->
      <div class="tab-content">
        <div class="tab-pane active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <? include('pages/welcome.php') ?>
        </div>
        <div class="tab-pane" id="doctors" role="tabpanel" aria-labelledby="doctors-tab">
          <? include('pages/doctor-shifts.php') ?>
        </div>
        <div class="tab-pane" id="application-settings" role="tabpanel" aria-labelledby="application-settings-tab">
          <? include('pages/application-settings.php') ?>
        </div>
        <div class="tab-pane" id="settings" role="tabpanel" aria-labelledby="settings-tab">
          <? include('pages/settings.php') ?>
        </div>
      </div>

    </div>

    <div id="myalert-middle-bottom" class="myalert-middle-bottom"></div>

  </div>

  <?php
}