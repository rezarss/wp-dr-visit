<?php
extract($_POST);

$errors = [];

global $wpdb;
$wpdb->query("DELETE FROM `$Shift_settings_table` WHERE `doctor_id` = $doctor_id AND `meta_key` = 'shift_setting' ");
$wpdb->query("DELETE FROM `$Shift_schedule_table` WHERE `doctor_id` = $doctor_id ");
if ($wpdb->last_error)
  $errors[] = $wpdb->last_error;

$dr_shift_setting_arr = [];
$dr_shift_schedule_arr = [];


foreach ($_POST as $key => $value) {
  $splt_shift = explode('_', $key);

  // adding dr shift setting to new array
  if ($splt_shift[0] === 'drs') {
    $dr_shift_setting_arr[$key] = $value;
  }

  // adding dr shift schedules to new array (only shift parts => [ed, odd, even, weekday-saturday, ... , weekday-friday, special dates])
  //************ ino anjam bede alan va beriz too araye jadid
  if ($splt_shift[0] === 'enable') {

    // detect code => ed, odd, even, weekday_saturday, ..., date_1401_01_09_4
    $code = "";
    if ($splt_shift[1] === "date") {
      $code = "date_" . $splt_shift[2] . "_" . $splt_shift[3] . "_" . $splt_shift[4];
    } elseif ($splt_shift[1] === "weekday") {
      $code = $splt_shift[1] . "_" . $splt_shift[2];
    } else {
      $code = $splt_shift[1];
    }

    $shifts = [];
    for ($i = 1; $i <= $ {
      'count_' . $code . '_shift_' . $doctor_id
    }; $i++) {
      if (!empty($ {
        $code . '_shift' . $i . '_start_' . $doctor_id
      }) || !empty($ {
        $code . '_shift' . $i . '_end_' . $doctor_id
      })) {


        array_push($shifts, array(
          'shift_price' => $ {
            $code . '_shift' . $i . '_price_' . $doctor_id
          },
          'shift_start' => $ {
            $code . '_shift' . $i . '_start_' . $doctor_id
          },
          'shift_end' => $ {
            $code . '_shift' . $i . '_end_' . $doctor_id
          },
          'shift_visit_time' => $ {
            $code . '_shift' . $i . '_visit_time_' . $doctor_id
          },
          'shift_visit_qty' => $ {
            $code . '_shift' . $i . '_visit_qty_' . $doctor_id
          },
          'shift_address' => $ {
            $code . '_shift' . $i . '_address_' . $doctor_id
          },
          'shift_latitude' => $ {
            $code . '_shift' . $i . '_latitude_' . $doctor_id
          },
          'shift_longitude' => $ {
            $code . '_shift' . $i . '_longitude_' . $doctor_id
          },
        ));
      }
    }
    $shift_setting = array(
      'enable' => $ {
        'enable_' . $code . '_' . $doctor_id
      },
      "shifts" => $shifts,
      'shift_count' => count($shifts), //${'count_' . $code . '_shift_' . $doctor_id} in namadin ast chon niaz nadarim vaghti shifti mowjoud nist
    );

    //echo "$code: " . json_encode($shift_setting) . "<br>";

    // insert to db
    // detect shift_time and shift_type
    if (explode('_', $code)[0] === 'weekday' || explode('_', $code)[0] === 'date') {
      if (explode('_', $code)[0] === 'weekday') {
        $shift_time = explode('_', $code)[1];
        $shift_type = "weekday";
      } elseif (explode('_', $code)[0] === 'date') {
        $shift_time = trim($code, "date_");
        $shift_type = "date";
      }
    } else {
      $shift_time = $code;
      $shift_type = $code;
    }
    // insert
    global $wpdb;
    $now = date('Y/m/d H:i:s');
    $json_shift_setting = json_encode($shift_setting);
    $shift_time = $shift_type === 'date' ? str_replace("_", "-", $shift_time) : $shift_time;

    $insert_shift_schedule = array(
      "doctor_id" => $doctor_id,
      "shift_time" => $shift_time,
      "shift_setting" => $json_shift_setting,
      "shift_type" => $shift_type,
      "extra" => "added: " . date("Y-m-d H:i:s"),
    );
    $wpdb->insert($Shift_schedule_table, $insert_shift_schedule);
    if ($wpdb->last_error)
      $errors[] = $wpdb->last_error;


  }
}
if (count($errors) > 0)
  print_r($errors);

// insert to dv_shift_setting
$shift_setting = array(
  "enable" => $dr_shift_setting_arr['drs_dr_enable_' . $doctor_id],
  "next_available_days" => $dr_shift_setting_arr['drs_next_available_days'],
  "visit_price" => $dr_shift_setting_arr['drs_visit_price'],
  "average_visit_time" => $dr_shift_setting_arr['drs_average_visit_time'],
  "clinic_address" => $dr_shift_setting_arr['drs_clinic_address'],
  "clinic_latitude" => $dr_shift_setting_arr['drs_clinic_latitude'],
  "clinic_longitude" => $dr_shift_setting_arr['drs_clinic_longitude']
);
$setting_data = array(
  "doctor_id" => $doctor_id,
  "meta_key" => "shift_setting",
  "meta_value" => json_encode($shift_setting)
);
$wpdb->insert($Shift_settings_table, $setting_data);
if ($wpdb->last_error)
  $errors[] = $wpdb->last_error;


?>
<div class="container-fluid">
  <div class="rounded shadow p-4 my-3" style="background: rgba(200,200,200,0.2)">
    <?php
    if (count($errors) >= 1)
      echo "<span class='px-2' style='border-right: 3px solid red';> " . __('ذخیره اطلاعات با مشکل مواجه شد', 'rad-ref-coupon') . ": " . $wpdb->last_error . " </span>";
    else
      echo "<span class='px-2' style='border-right: 3px solid green'> " . __('اطلاعات با موفقیت ذخیره شدند.', 'rad-ref-coupon') . " </span>";
    //*/
    ?>
  </div>
</div>