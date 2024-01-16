<?php
global $wpdb;
$res = $wpdb->get_results("SELECT meta_value FROM $Settings_table WHERE meta_key = 'setting'");
$settings = json_decode($res[0]->meta_value);


$all_settings = $wpdb->get_results("SELECT * FROM $Settings_table");

foreach ($all_settings as $setting) {
  $all_settings_arr[$setting->meta_key] = $setting;
}

function get_value($setting_name, $value, $all_settings_array) {
  return json_decode($all_settings_array[$setting_name]->meta_value)->$value;
}

// get all sms providers
$resAllSmsProviders = $wpdb->get_results("SELECT meta_key, meta_value FROM $Settings_table WHERE extra = 'smsProvider'");
?>

<h2>Setting Page</h2>
<div class="app-setting-bg p-4">
  <h5>تنظیمات پیامک</h5>
  <?= substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], "/") + 1) ?>
  <form action="" method="POST">
    <input type="hidden" name="page_code" value="settings" />

    <div class="d-flex p-2">
      <span class="text-label ms-3">فعال/غیرفعال کردن پیامک</span>
      <div class="mx-2">
        <input type="radio" name="smsPanel" value="1" <?= get_value('setting', 'sms', $all_settings_arr)->smsPanel ? 'checked' : '' ?>>
        <small for="css">فعال</small>
      </div>
      <div class="mx-2">
        <input type="radio" name="smsPanel" value="0" <?= !get_value('setting', 'sms', $all_settings_arr)->smsPanel ? 'checked' : '' ?>>
        <small for="css">غیرفعال</small>
      </div>
    </div>

    <div class="mt-3 mb-2">
      <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);">انتخاب سرویس دهنده پیامک</span>
      <select name="smsProvider" id="select-sms-provider">
        <option <?= get_value('setting', 'sms', $all_settings_arr)->smsProvider === 'ippanel' ? 'selected' : '' ?> value="ippanel">Ippanel</option>
        <option <?= get_value('setting', 'sms', $all_settings_arr)->smsProvider === 'smsir' ? 'selected' : '' ?> value="smsir">SMS.IR</option>
      </select>
    </div>

    <div id="sms-providers-container">
      <? foreach ($resAllSmsProviders as $smsProviders) {
        $smsProvider_Setting = json_decode($smsProviders->meta_value);
        if ($smsProviders->meta_key === 'ippanel') {
          ?>
          <div id="sms-provider-<?= $smsProviders->meta_key ?>" class="my-3 add-shift" style="display: <?= $smsProviders->meta_key === get_value('setting', 'sms', $all_settings_arr)->smsProvider ? 'block' : 'none' ?>; background: rgba(250,250,250,0.9)">
            <div class="row d-flex justify-content-start">
              <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 m-3">
                <label class="form-label inp-label">نام کاربری پنل پیامک</label>
                <input type="text" name="<?= $smsProviders->meta_key ?>_username" value="<?= $smsProvider_Setting->username ?>" />
              </div>
              <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 m-3">
                <label class="form-label inp-label">کلمه عبور پنل پیامک</label>
                <input type="password" name="<?= $smsProviders->meta_key ?>_password" value="<?= $smsProvider_Setting->password ?>" />
              </div>
            </div>
            <div class="row d-flex justify-content-start">
              <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 m-3">
                <label class="form-label inp-label">تعداد ارقام کد OTP</label>
                <input type="number" min="4" max="6" name="<?= $smsProviders->meta_key ?>_otpQtyNumber" value="<?= $smsProvider_Setting->otpQtyNumber ?>" />
              </div>
              <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 m-3">
                <label class="form-label inp-label">زمان انقضا کد OTP</label>
                <input type="number" name="<?= $smsProviders->meta_key ?>_otpTimeOut" value="<?= $smsProvider_Setting->otpTimeOut ?>" />
              </div>
            </div>
            <div class="row d-flex justify-content-start">
              <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 m-3">
                <label class="form-label inp-label">کد پترن ارسال کد تایید</label>
                <input type="text" name="<?= $smsProviders->meta_key ?>_verificationCodePattern" value="<?= $smsProvider_Setting->verificationCodePattern ?>" />
              </div>
            </div>
          </div>
          <?php
        } else if ($smsProviders->meta_key === 'smsir') {
          ?>
          <div id="sms-provider-<?= $smsProviders->meta_key ?>" class="my-3 add-shift" style="display: <?= $smsProviders->meta_key === get_value('setting', 'sms', $all_settings_arr)->smsProvider ? 'block' : 'none' ?>; background: rgba(250,250,250,0.9)">
            <div class="row d-flex justify-content-start">
              <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 m-3">
                <label class="form-label inp-label">نام کاربری پنل پیامک</label>
                <input type="text" name="<?= $smsProviders->meta_key ?>_username" value="<?= $smsProvider_Setting->username ?>" />
              </div>
              <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 m-3">
                <label class="form-label inp-label">کلمه عبور پنل پیامک</label>
                <input type="password" name="<?= $smsProviders->meta_key ?>_password" value="<?= $smsProvider_Setting->password ?>" />
              </div>
            </div>
            <div class="row d-flex justify-content-start">
              <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 m-3">
                <label class="form-label inp-label">تعداد ارقام کد OTP</label>
                <input type="number" name="<?= $smsProviders->meta_key ?>_otpQtyNumber" value="<?= $smsProvider_Setting->otpQtyNumber ?>" />
              </div>
              <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 m-3">
                <label class="form-label inp-label">زمان انقضا کد OTP</label>
                <input type="number" name="<?= $smsProviders->meta_key ?>_otpTimeOut" value="<?= $smsProvider_Setting->otpTimeOut ?>" />
              </div>
            </div>
          </div>
          <?php
        }
      } ?>
    </div>

    <div class="my-3 section">
      <div>
        <h6>پیامک کاربران</h6>
      </div>
      <div class="in-section mb-3">
        <div class="d-flex p-2 pe-0">
          <span class="text-label ms-3">فعال/غیرفعال کردن ارسال پیامک بعد از سفارش گذاری</span>
          <div class="mx-2">
            <input type="radio" name="user_new_order_enable" value="1" <?= get_value('setting', 'sms', $all_settings_arr)->messages->user->smsNewOrder->enable ? 'checked' : '' ?>>
            <small for="css">فعال</small>
          </div>
          <div class="mx-2">
            <input type="radio" name="user_new_order_enable" value="0" <?= !get_value('setting', 'sms', $all_settings_arr)->messages->user->smsNewOrder->enable ? 'checked' : '' ?>>
            <small for="css">غیرفعال</small>
          </div>
        </div>
        <?= send_sms('09179999315', get_value('setting', 'sms', $all_settings_arr)->messages->user->smsNewOrder->message) ?>
        <div>
          <textarea name="user_new_order_message" id="user_new_order_message" rows="4" class="form-control w-100"><?= get_value('setting', 'sms', $all_settings_arr)->messages->user->smsNewOrder->message ?></textarea>
        </div>
      </div>
      <div class="in-section mb-3">
        <div class="d-flex p-2 pe-0">
          <span class="text-label ms-3">فعال/غیرفعال کردن ارسال پیامک بعد از سفارش موفق</span>
          <div class="mx-2">
            <input type="radio" name="user_success_order_enable" value="1" <?= get_value('setting', 'sms', $all_settings_arr)->messages->user->smsSuccessOrder->enable ? 'checked' : '' ?>>
            <small for="css">فعال</small>
          </div>
          <div class="mx-2">
            <input type="radio" name="user_success_order_enable" value="0" <?= !get_value('setting', 'sms', $all_settings_arr)->messages->user->smsSuccessOrder->enable ? 'checked' : '' ?>>
            <small for="css">غیرفعال</small>
          </div>
        </div>
        <div>
          <textarea name="user_success_order_message" id="user_success_order_message" rows="4" class="form-control w-100"><?= get_value('setting', 'sms', $all_settings_arr)->messages->user->smsSuccessOrder->message ?></textarea>
        </div>
      </div>
      <div class="in-section mb-3">
        <div class="d-flex p-2 pe-0">
          <span class="text-label ms-3">فعال/غیرفعال کردن ارسال پیامک یادآوری قبل از ویزیت</span>
          <div class="mx-2">
            <input type="radio" name="user_reminder_visit_enable" value="1" <?= get_value('setting', 'sms', $all_settings_arr)->messages->user->smsReminder->enable ? 'checked' : '' ?>>
            <small for="css">فعال</small>
          </div>
          <div class="mx-2">
            <input type="radio" name="user_reminder_visit_enable" value="0" <?= !get_value('setting', 'sms', $all_settings_arr)->messages->user->smsReminder->enable ? 'checked' : '' ?>>
            <small for="css">غیرفعال</small>
          </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 m-3">
          <label class="form-label inp-label">ارسال پیامک یادآوری چند ساعت قبل ویزیت انجام شود؟ (به دقیقه)</label>
          <input type="number" name="user_reminder_visit_hours_before" value="<?= get_value('setting', 'sms', $all_settings_arr)->messages->user->smsReminder->hoursBeforeVisit ?>" />
        </div>
        <div>
          <textarea name="user_reminder_visit_message" id="user_reminder_visit_message" rows="4" class="form-control w-100"><?= get_value('setting', 'sms', $all_settings_arr)->messages->user->smsReminder->message ?></textarea>
        </div>
      </div>
    </div>

    <div class="my-3 section">
      <div>
        <h6>پیامک <?= app_setting()->texts->operatorsLabel ?></h6>
      </div>
      <div class="in-section mb-3">
        <div class="d-flex p-2 pe-0">
          <span class="text-label ms-3">فعال/غیرفعال کردن ارسال پیامک بعد از سفارش گذاری</span>
          <div class="mx-2">
            <input type="radio" name="operator_new_order_enable" value="1" <?= get_value('setting', 'sms', $all_settings_arr)->messages->operator->smsNewOrder->enable ? 'checked' : '' ?>>
            <small for="css">فعال</small>
          </div>
          <div class="mx-2">
            <input type="radio" name="operator_new_order_enable" value="0" <?= !get_value('setting', 'sms', $all_settings_arr)->messages->operator->smsNewOrder->enable ? 'checked' : '' ?>>
            <small for="css">غیرفعال</small>
          </div>
        </div>
        <div>
          <textarea name="operator_new_order_message" id="operator_new_order_message" rows="4" class="form-control w-100"><?= get_value('setting', 'sms', $all_settings_arr)->messages->operator->smsNewOrder->message ?></textarea>
        </div>
      </div>
      <div class="in-section mb-3">
        <div class="d-flex p-2 pe-0">
          <span class="text-label ms-3">فعال/غیرفعال کردن ارسال پیامک بعد از سفارش موفق</span>
          <div class="mx-2">
            <input type="radio" name="operator_success_order_enable" value="1" <?= get_value('setting', 'sms', $all_settings_arr)->messages->operator->smsSuccessOrder->enable ? 'checked' : '' ?>>
            <small for="css">فعال</small>
          </div>
          <div class="mx-2">
            <input type="radio" name="operator_success_order_enable" value="0" <?= !get_value('setting', 'sms', $all_settings_arr)->messages->user->smsSuccessOrder->enable ? 'checked' : '' ?>>
            <small for="css">غیرفعال</small>
          </div>
        </div>
        <div>
          <textarea name="operator_success_order_message" id="operator_success_order_message" rows="4" class="form-control w-100"><?= get_value('setting', 'sms', $all_settings_arr)->messages->operator->smsSuccessOrder->message ?></textarea>
        </div>
      </div>
    </div>


    <div class="text-center">
      <button id="js-save-btn" class="btn btn-primary">ذخیره</button>
      <input type="submit" id="save-btn" class="btn btn-primary" style="display: none;" value="ذخیره" />
    </div>

  </form>
</div>

<script>
  $(() => {

    $('#select-sms-provider').on('change', () => {
      $('#sms-providers-container').children().hide()

      let currentSelectedSmsProvider = $('#select-sms-provider').val()
      $('#sms-provider-' + currentSelectedSmsProvider).show()
    })

    $('#js-save-btn').on('click', function() {
      // step 1 : remove all hidden sms panel settings Elements
      $('#sms-providers-container').children().each(function() {
        if ($(this).css('display') === 'none')
          $(this).remove()
      })
      // step 2 : hide js-save-btn then show and click on form submit btn with id save-btn
      $('#js-save-btn').hide()
      $('#save-btn').show()
      $('#save-btn'). trigger('click');
    })


  })
</script>