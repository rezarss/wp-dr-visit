<?php
$appSetting = json_decode(get_db_row(SETTINGS_TABLE, 'meta_key', 'app_setting')[0]->meta_value);
?>
<!-- Doctors tab nav ----------------------------------------------------------------   -->
<ul class="nav nav-tabs mt-2" id="doctorTabs" role="tablist">
  <?php $doctors = get_users(array('role' => OPERATOR_ROLE, 'orderby' => 'user_nicename', 'order' => 'ASC'));
  $doctors_count = count($doctors);
  foreach ($doctors as $doctor) : ?>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="dr<?= $doctor->ID ?>-tab" data-bs-toggle="tab" data-bs-target="#dr<?= $doctor->ID ?>" type="button" role="tab" aria-controls="dr<?= $doctor->ID ?>" aria-selected="true"><?= $doctor->display_name ?></button>
  </li>
  <? endforeach; ?>
</ul>

<!-- Doctors tab content -->
<div class="tab-content">
  <?php $doctors = get_users(array('role' => OPERATOR_ROLE, 'orderby' => 'user_nicename', 'order' => 'ASC'));
  foreach ($doctors as $doctor) : ?>

  <div class="tab-pane" id="dr<?= $doctor->ID ?>" role="tabpanel" aria-labelledby="dr<?= $doctor->ID ?>-tab">
    <?php
    // get doctor shifts from db
    global $wpdb;

    $shift_settings = $wpdb->get_results("SELECT * FROM $Shift_settings_table WHERE doctor_id = '$doctor->ID' AND meta_key = 'shift_setting' ");
    $dr_shift_setting = json_decode($shift_settings[0]->meta_value);

    $shift_schedule = $wpdb->get_results("SELECT * FROM $Shift_schedule_table WHERE doctor_id = '$doctor->ID' ");

    //print_r($shift_schedule);

    $ed_tg = has_shift_type_shifts($shift_schedule, 'ed');
    $even_tg = has_shift_type_shifts($shift_schedule, 'even');
    $odd_tg = has_shift_type_shifts($shift_schedule, 'odd');

    ?>
    <h4 class="m-4"><? _e('برنامه شیفت  ' . $appSetting->texts->operatorLabel . ' ' . $doctor->display_name, 'rad-ref-coupon') ?></h4>
    <form action="" method="POST" id="<?= $doctor->ID ?>">
      <div class="border border2 rounded p-2 mb-3">
        <strong>
          <p id="quick-setting-<?= $doctor->ID ?>" class="p-2">
            تنظیمات سریع شیفت <?= $appSetting->texts->operatorLabel ?>
          </p>
        </strong>
        <div class="d-flex mx-4 p-2">
          <span class="ms-3" style="font-size: 12px;">فعال/غیرفعال کردن  <?= $appSetting->texts->operatorLabel ?></span>
          <div class="mx-2">
            <input type="radio" id="enable-<?= $doctor->ID ?>" name="drs_dr_enable_<?= $doctor->ID ?>" value="1" <?= $dr_shift_setting->enable ? 'checked' : '' ?>>
            <small for="css">فعال</small>
          </div>
          <div class="mx-2">
            <input type="radio" id="disable--<?= $doctor->ID ?>" name="drs_dr_enable_<?= $doctor->ID ?>" value="0" <?= !$dr_shift_setting->enable ? 'checked' : '' ?>>
            <small for="css">غیرفعال</small>
          </div>
        </div>
        <div class="row d-flex justify-content-start mx-4 my-2">
          <div class="col-md-3 col-sm-12">
            <label class="form-label inp-label">فروش ویزیت تا چند روز آینده؟</label>
            <input type="number" min="1" class="form-control" name="drs_next_available_days" id="next-available-days-<?= $doctor->ID ?>" value="<?= $dr_shift_setting->next_available_days ?>" aria-describedby="next-available-days-help">
          </div>
        </div>
        <div class="row d-flex justify-content-start mx-4 my-2">
          <div class="col-md-3 col-sm-12">
            <label class="form-label inp-label">هزینه ویزیت</label>
            <input type="number" min="1" class="form-control" name="drs_visit_price" id="visit-price-<?= $doctor->ID ?>" value="<?= $dr_shift_setting->visit_price ?>" aria-describedby="visit-price-help">
          </div>
          <div class="col-md-3 col-sm-12">
            <label class="form-label inp-label">مدت زمان هر ویزیت</label>
            <input type="number" min="1" class="form-control" name="drs_average_visit_time" id="average-visit-time-<?= $doctor->ID ?>" value="<?= $dr_shift_setting->average_visit_time ?>" aria-describedby="visit-time-help">
            <div id="visit-time-help" class="form-text inp-label">
              مدت زمان ویزیت هر مراجعه کننده به دقیقه
            </div>
          </div>
        </div>
        <div class="row d-flex justify-content-start mx-4">
          <div class="col-md-8 col-sm-12 my-2">
            <label class="form-label inp-label">آدرس مطب</label>
            <input type="text" class="form-control" name="drs_clinic_address" id="clinic-address-<?= $doctor->ID ?>" value="<?= $dr_shift_setting->clinic_address ?>" aria-describedby="clinic-address-help">
          </div>
          <div class="col-md-2 col-sm-12 my-2">
            <label class="form-label inp-label">latitude</label>
            <input type="text" class="form-control" name="drs_clinic_latitude" id="clinic-latitude-<?= $doctor->ID ?>" value="<?= $dr_shift_setting->clinic_latitude ?>" aria-describedby="clinic-latitude-help">
            <div id="clinic-latitude-help" class="form-text inp-label">
              عرض جغرافیایی مطب در نقشه
            </div>
          </div>
          <div class="col-md-2 col-sm-12 my-2">
            <label class="form-label inp-label">Longitude</label>
            <input type="text" class="form-control" name="drs_clinic_longitude" id="clinic-longitude-<?= $doctor->ID ?>" value="<?= $dr_shift_setting->clinic_longitude ?>" aria-describedby="clinic-longitude-help">
            <div id="clinic-longitude-help" class="form-text inp-label">
              طول جغرافیایی مطب در نقشه
            </div>
          </div>
        </div>
      </div>
      <div class="row d-flex justify-content-around">
        <div class="col-lg-10 col-sm-12 rounded p-3 mb-4">
          <input type="hidden" name="doctor_id" id="doctor-id" value="<?= $doctor->ID ?>" />
          <input type="hidden" name="page_code" value="dv" />

          <?php
          global $wpdb;
          $weekday_sub = array(
            'saturday' => 'شنبه',
            'sunday' => 'یکشنبه',
            'monday' => 'دوشنبه',
            'tuesday' => 'سه شنبه',
            'wednesday' => 'چهارشنبه',
            'thursday' => 'پنج شنبه',
            'friday' => 'جمعه'
          );

          $all_shifts_time_type = json_decode('{ "ed":{"name":"هر روز"},"even":{"name":"روزهای زوج"},"odd":{"name":"روزهای فرد"},"weekday":{"name":"روزهای هفته"},"date":{"name":"تاریخ مورد نظر"} }');

          //$results = $wpdb->get_results(" SELECT * FROM $Shift_schedule_table WHERE doctor_id = '$doctor->ID' ");
          //$visit_settings = json_decode($results[0]->value);
          ?>

          <div class="row border border-1 rounded shadow p-3 mb-3">
            <div class="col-md-6 col-sm-12">
              <p>
                شیفت کاری  <?= $appSetting->texts->operatorLabel ?>، روزانه است یا طبق روزهای زوج و فرد متفاوتند؟
              </p>
            </div>
            <div class="col-md-6 col-sm-12 ed-odd-even-div-<?= $doctor->ID ?>">
              <div id="ed-btn-toggle-<?= $doctor->ID ?>" class="btn <?= $ed_tg ? 'btn-primary edoddeven-' . $doctor->ID : 'btn-outline-primary' ?> mx-1" onclick="showEdOddEven('ed-btn-toggle',<?= $doctor->ID ?>)">
                هر روز
              </div>
              <div id="odd-even-btn-toggle-<?= $doctor->ID ?>" class="btn <?= $even_tg || $odd_tg ? 'btn-primary edoddeven-' . $doctor->ID : 'btn-outline-primary' ?> mx-1" onclick="showEdOddEven('odd-even-btn-toggle', <?= $doctor->ID ?>)">
                روزهای زوج و فرد
              </div>
              <div id="none-btn-toggle-<?= $doctor->ID ?>" class="btn <?= !$ed_tg && !$even_tg && !$odd_tg ? 'btn-primary edoddeven-' . $doctor->ID : 'btn-outline-primary' ?> mx-1" onclick="showEdOddEven('none-btn-toggle', <?= $doctor->ID ?>)">
                هیچ کدام
              </div>
            </div>
          </div>

          <?php
          foreach ($all_shifts_time_type as $shift_type => $each_shift) :
          // for all => ed, odd, even, saturday, ..., friday, all dates
          if ($shift_type != 'weekday' && $shift_type != 'date') {
            if ($ {
              $shift_type . '_tg'
            }) {
              $ {
                $shift_type . '_style_none'
              } = '';
            } else {
              $ {
                $shift_type . '_style_none'
              } = 'display: none;';
            }
          }

          // only for odd and even
          if ($shift_type === 'odd' || $shift_type === 'even')
            if ($even_tg || $odd_tg)
            $ {
            $shift_type . '_style_none'
          } = '';

          ?>
          <div id="<?= $shift_type ?>-box-<?= $doctor->ID ?>" class="border border-1 rounded shadow p-3 mb-3" style="<?= $ { $shift_type . '_style_none' } ?>">
            <? if ($shift_type != 'weekday' && $shift_type != 'date') : ///////////////////////
            $get_shift_schedule = has_shift_type_shifts($shift_schedule, $shift_type);
            $get_shifts = json_decode($get_shift_schedule->shift_setting);
            ?>
            <h6 class="mb-4">تعیین شیفت بر اساس <?= $each_shift->name ?> <?= $each_shift->name === "روزهای فرد" ? "" : "(بجز جمعه ها)" ?></h6>
            <div class="border border1 rounded p-2" style="background: rgba(150,150,150,0.1);">
              <div class="d-flex mb-3">
                <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('ویزیت فعال', 'rad-ref-coupon') ?></span>
                <div class="d-flex mx-3">
                  <div class="mx-2">
                    <input type="radio" name="enable_<?= $shift_type ?>_<?= $doctor->ID ?>" value="1" <?= $get_shifts->enable ? 'checked' : '' ?>>
                    <label for="html">باز</label><br>
                  </div>
                  <div class="mx-2">
                    <input type="radio" name="enable_<?= $shift_type ?>_<?= $doctor->ID ?>" value="0" <?= !$get_shifts->enable ? 'checked' : '' ?>>
                    <label for="css">بسته</label><br>
                  </div>
                </div>
              </div>
              <div class="d-flex mb-3">
                <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('تعداد شیفت', 'rad-ref-coupon') ?></span>
                <input type="number" name="count_<?= $shift_type ?>_shift_<?= $doctor->ID ?>" id="count-<?= $shift_type ?>-shift-<?= $doctor->ID ?>" min="0" value="<?= $get_shifts->shift_count ?>" oninput="addShifts('<?= $shift_type ?>', <?= $doctor->ID ?>)" class="form-control search-inp text-center mx-1 w-25">
              </div>
              <div id="<?= $shift_type ?>-shift-count-inputs-<?= $doctor->ID ?>">
                <?php for ($i = 1; $i <= $get_shifts->shift_count; $i++) : ?>
                <div id="<?= $get_shift_schedule->shift_type ?>-shift<?= $i ?>-<?= $get_shift_schedule->doctor_id ?>" class="container add-shift mb-2" style="background: rgba(250,250,250,0.9)">
                  <div>
                    <p>
                      شیفت <?= $i ?>
                    </p>
                    <div class="col-md-3 col-sm-12 my-2">
                      <label for="<?= $get_shift_schedule->shift_type ?>-shift<?= $i ?>-price-<?= $get_shift_schedule->doctor_id ?>" class="form-label inp-label">هزینه ویزیت</label>
                      <input type="number" id="<?= $get_shift_schedule->shift_type ?>-shift<?= $i ?>-price-<?= $get_shift_schedule->doctor_id ?>" name="<?= $get_shift_schedule->shift_type ?>_shift<?= $i ?>_price_<?= $get_shift_schedule->doctor_id ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_price ?>" class="inp-shift">
                    </div>
                  </div>
                  <div class="row d-flex justify-content-center my-3">
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 my-1">
                      <label class="form-label inp-label">ساعت شروع</label>
                      <input type="time" id="<?= $get_shift_schedule->shift_type ?>-shift<?= $i ?>-start-<?= $get_shift_schedule->doctor_id ?>" name="<?= $get_shift_schedule->shift_type ?>_shift<?= $i ?>_start_<?= $get_shift_schedule->doctor_id ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_start ?>" oninput="calculateVisitQty(this)" class="inp-shift">
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 my-1">
                      <label class="form-label inp-label">ساعت پایان</label>
                      <input type="time" id="<?= $get_shift_schedule->shift_type ?>-shift<?= $i ?>-end-<?= $get_shift_schedule->doctor_id ?>" name="<?= $get_shift_schedule->shift_type ?>_shift<?= $i ?>_end_<?= $get_shift_schedule->doctor_id ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_end ?>" oninput="calculateVisitQty(this)" class="inp-shift">
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 my-1">
                      <label class="form-label inp-label">زمان هر ویزیت (دقیقه)</label>
                      <input type="number" id="<?= $get_shift_schedule->shift_type ?>-shift<?= $i ?>-visit-time-<?= $get_shift_schedule->doctor_id ?>" name="<?= $get_shift_schedule->shift_type ?>_shift<?= $i ?>_visit_time_<?= $get_shift_schedule->doctor_id ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_visit_time ?>" oninput="calQtyOrVisitTime(this)" class="inp-shift">
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 my-1">
                      <label class="form-label inp-label">تعداد ویزیت</label>
                      <input type="number" min="0" id="<?= $get_shift_schedule->shift_type ?>-shift<?= $i ?>-visit-qty-<?= $get_shift_schedule->doctor_id ?>" name="<?= $get_shift_schedule->shift_type ?>_shift<?= $i ?>_visit_qty_<?= $get_shift_schedule->doctor_id ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_visit_qty ?>" oninput="calQtyOrVisitTime(this)" class="inp-shift">
                    </div>
                    <div>
                      <div class="row d-flex justify-content-center my-3">
                        <div class="col-md-8 col-sm-12">
                          <label class="form-label inp-label">آدرس مطب</label>
                          <input type="text" id="<?= $get_shift_schedule->shift_type ?>-shift<?= $i ?>-address-<?= $get_shift_schedule->doctor_id ?>" name="<?= $get_shift_schedule->shift_type ?>_shift<?= $i ?>_address_<?= $get_shift_schedule->doctor_id ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_address ?>" class="inp-shift">
                        </div>
                        <div class="col-md-2 col-sm-12">
                          <label class="form-label inp-label">latitude</label>
                          <input type="text" id="<?= $get_shift_schedule->shift_type ?>-shift<?= $i ?>-latitude-<?= $get_shift_schedule->doctor_id ?>" name="<?= $get_shift_schedule->shift_type ?>_shift<?= $i ?>_latitude_<?= $get_shift_schedule->doctor_id ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_latitude ?>" class="inp-shift">
                        </div>
                        <div class="col-md-2 col-sm-12">
                          <label class="form-label inp-label">longitude</label>
                          <input type="text" id="<?= $get_shift_schedule->shift_type ?>-shift<?= $i ?>-longitude-<?= $get_shift_schedule->doctor_id ?>" name="<?= $get_shift_schedule->shift_type ?>_shift<?= $i ?>_longitude_<?= $get_shift_schedule->doctor_id ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_longitude ?>" class="inp-shift">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <? endfor; ?>
              </div>
            </div>
            <? elseif ($shift_type === 'weekday') : ///////////////////////
            ?>
            <h6 class="mb-4">تعیین شیفت بر اساس <?= $each_shift->name ?></h6>
            <?php foreach ($weekday_sub as $key => $wd) {
              $get_shift_schedule = has_shift_type_shifts($shift_schedule, $shift_type, $key);
              $get_shifts = json_decode($get_shift_schedule->shift_setting);

              if ($key === 'friday' && empty($get_shifts->enable) && empty($get_shifts->shift_count)) {
                @$get_shifts->enable = "0";
                @$get_shifts->shift_count = "0";
              }

              ?>
              <div class="border border1 rounded mb-2 p-2" style="background: rgba(150,150,150,0.1);">
                <span class="dw-title">
                  <?= $wd ?> ها
                </span>
                <div class="d-flex my-3">
                  <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('ویزیت فعال', 'rad-ref-coupon') ?></span>
                  <div class="d-flex mx-3">
                    <div class="mx-2">
                      <input type="radio" name="enable_<?= $shift_type . "_" . $key ?>_<?= $doctor->ID ?>" value="1" <?= $get_shifts->enable === "1" ? 'checked' : '' ?>>
                      <label for="html">باز</label><br>
                    </div>
                    <div class="mx-2">
                      <input type="radio" name="enable_<?= $shift_type . "_" . $key ?>_<?= $doctor->ID ?>" value="0" <?= $get_shifts->enable === "0" ? 'checked' : '' ?>>
                      <label for="css">بسته</label><br>
                    </div>
                    <div class="mx-2">
                      <input type="radio" name="enable_<?= $shift_type . "_" . $key ?>_<?= $doctor->ID ?>" value="2" <?= $get_shifts->enable === "2" ? 'checked' : '' ?>>
                      <label for="css">قبلا تعیین شده</label><br>
                    </div>
                  </div>
                </div>
                <div class="d-flex mb-3">
                  <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('تعداد شیفت', 'rad-ref-coupon') ?></span>
                  <input type="number" name="count_<?= $shift_type . "_" . $key ?>_shift_<?= $doctor->ID ?>" id="count-<?= $shift_type . "-" . $key ?>-shift-<?= $doctor->ID ?>" min="0" value="<?= $get_shifts->shift_count ?>" oninput="addShifts('<?= $shift_type . "-" . $key ?>', <?= $doctor->ID ?>)" class="form-control search-inp text-center mx-1 w-25">
                </div>

                <div id="<?= $shift_type . '-' . $key ?>-shift-count-inputs-<?= $doctor->ID ?>">
                  <?php for ($i = 1; $i <= $get_shifts->shift_count; $i++) : ?>
                  <div id="<?= $get_shift_schedule->shift_type . '-' . $key ?>-shift<?= $i ?>-<?= $get_shift_schedule->doctor_id ?>" class="container add-shift mb-2" style="background: rgba(250,250,250,0.9)">
                    <div>
                      <p>
                        شیفت <?= $i ?>
                      </p>
                      <div class="col-md-3 col-sm-12 my-2">
                        <label for="<?= $get_shift_schedule->shift_type . '-' . $key ?>-shift<?= $i ?>-price-<?= $get_shift_schedule->doctor_id ?>" class="form-label inp-label">هزینه ویزیت</label>
                        <input type="number" id="<?= $get_shift_schedule->shift_type . '-' . $key ?>-shift<?= $i ?>-price-<?= $get_shift_schedule->doctor_id ?>" name="<?= $get_shift_schedule->shift_type . '_' . $key ?>_shift<?= $i ?>_price_<?= $get_shift_schedule->doctor_id ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_price ?>" class="inp-shift">
                      </div>
                    </div>
                    <div class="row d-flex justify-content-center my-3">
                      <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 my-1">
                        <label class="form-label inp-label">ساعت شروع</label>
                        <input type="time" id="<?= $get_shift_schedule->shift_type . '-' . $key ?>-shift<?= $i ?>-start-<?= $get_shift_schedule->doctor_id ?>" name="<?= $get_shift_schedule->shift_type . '_' . $key ?>_shift<?= $i ?>_start_<?= $get_shift_schedule->doctor_id ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_start ?>" oninput="calculateVisitQty(this)" class="inp-shift">
                      </div>
                      <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 my-1">
                        <label class="form-label inp-label">ساعت پایان</label>
                        <input type="time" id="<?= $get_shift_schedule->shift_type . '-' . $key ?>-shift<?= $i ?>-end-<?= $get_shift_schedule->doctor_id ?>" name="<?= $get_shift_schedule->shift_type . '_' . $key ?>_shift<?= $i ?>_end_<?= $get_shift_schedule->doctor_id ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_end ?>" oninput="calculateVisitQty(this)" class="inp-shift">
                      </div>
                      <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 my-1">
                        <label class="form-label inp-label">زمان هر ویزیت (دقیقه)</label>
                        <input type="number" id="<?= $get_shift_schedule->shift_type . '-' . $key ?>-shift<?= $i ?>-visit-time-<?= $get_shift_schedule->doctor_id ?>" name="<?= $get_shift_schedule->shift_type . '_' . $key ?>_shift<?= $i ?>_visit_time_<?= $get_shift_schedule->doctor_id ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_visit_time ?>" oninput="calQtyOrVisitTime(this)" class="inp-shift">
                      </div>
                      <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 my-1">
                        <label class="form-label inp-label">تعداد ویزیت</label>
                        <input type="number" min="0" id="<?= $get_shift_schedule->shift_type . '-' . $key ?>-shift<?= $i ?>-visit-qty-<?= $get_shift_schedule->doctor_id ?>" name="<?= $get_shift_schedule->shift_type . '_' . $key ?>_shift<?= $i ?>_visit_qty_<?= $get_shift_schedule->doctor_id ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_visit_qty ?>" oninput="calQtyOrVisitTime(this)" class="inp-shift">
                      </div>
                      <div>
                        <div class="row d-flex justify-content-center my-3">
                          <div class="col-md-8 col-sm-12">
                            <label class="form-label inp-label">آدرس مطب</label>
                            <input type="text" id="<?= $get_shift_schedule->shift_type . '-' . $key ?>-shift<?= $i ?>-address-<?= $get_shift_schedule->doctor_id ?>" name="<?= $get_shift_schedule->shift_type . '_' . $key ?>_shift<?= $i ?>_address_<?= $get_shift_schedule->doctor_id ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_address ?>" class="inp-shift">
                          </div>
                          <div class="col-md-2 col-sm-12">
                            <label class="form-label inp-label">latitude</label>
                            <input type="text" id="<?= $get_shift_schedule->shift_type . '-' . $key ?>-shift<?= $i ?>-latitude-<?= $get_shift_schedule->doctor_id ?>" name="<?= $get_shift_schedule->shift_type . '_' . $key ?>_shift<?= $i ?>_latitude_<?= $get_shift_schedule->doctor_id ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_latitude ?>" class="inp-shift">
                          </div>
                          <div class="col-md-2 col-sm-12">
                            <label class="form-label inp-label">longitude</label>
                            <input type="text" id="<?= $get_shift_schedule->shift_type . '-' . $key ?>-shift<?= $i ?>-longitude-<?= $get_shift_schedule->doctor_id ?>" name="<?= $get_shift_schedule->shift_type . '_' . $key ?>_shift<?= $i ?>_longitude_<?= $get_shift_schedule->doctor_id ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_longitude ?>" class="inp-shift">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <? endfor; ?>
                </div>
              </div>
              <?
            } elseif ($shift_type === 'date') : ///////////////////////
            ?>
            <h6 class="mb-4">تعیین شیفت در <?= $each_shift->name ?></h6>
            <div class="d-flex">
              <input type="text" id="new-date-shift-input<?= $doctor->ID ?>" class="mx-1 babakhani" />
              <button type="button" onclick="addNewDateShift('#new-date-shift<?= $doctor->ID ?>', <?= $doctor->ID ?>)" class="btn btn-primary mx-1">افزودن شیفت جدید</button>
            </div>
            <div id="new-date-shift<?= $doctor->ID ?>" class="my-2">

              <? $date_shifts = $wpdb->get_results(" SELECT * FROM $Shift_schedule_table WHERE shift_type = 'date' AND doctor_id = '$doctor->ID' ");
              foreach ($date_shifts as $dsh) {
                $get_shifts = json_decode($dsh->shift_setting);
                ?>

                <div id="date-<?= $dsh->shift_time ?>-section" class="border border1 rounded new-date-shift-bg mb-2 p-2">
                  <div class="d-flex position-relative">
                    <div class="position-absolute top-0 start-0 bg-danger rounded px-2 py-1" role="button" onclick="removeElement('#date-<?= $dsh->shift_time ?>-section')">
                      <span class="text-white">x</span>
                    </div>
                    <div class="dw-title">
                      <span> شیفت </span><span dir="ltr"> <?= $dsh->shift_time ?> </span>
                    </div>
                  </div>
                  <div class="d-flex mb-3">
                    <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);">ویزیت فعال</span>
                    <div class="d-flex mx-3">
                      <div class="mx-2">
                        <input type="radio" name="enable_date_<?= str_replace("-", "_", $dsh->shift_time); ?>_<?= $doctor->ID ?>" value="1" <?= $get_shifts->enable === '1' ? 'checked' : '' ?>>
                        <label for="html">باز</label><br>
                      </div>
                      <div class="mx-2">
                        <input type="radio" name="enable_date_<?= str_replace("-", "_", $dsh->shift_time); ?>_<?= $doctor->ID ?>" value="0" <?= $get_shifts->enable === '0' ? 'checked' : '' ?>>
                        <label for="css">بسته</label><br>
                      </div>
                    </div>
                  </div>
                  <div class="d-flex mb-3">
                    <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);">تعداد شیفت</span>
                    <input type="number" name="count_date_<?= str_replace("-", "_", $dsh->shift_time); ?>_shift_<?= $doctor->ID ?>" id="count-date-<?= $dsh->shift_time ?>-shift-<?= $doctor->ID ?>" min="0" value="<?= $get_shifts->shift_count ?>" oninput="addShifts('date-<?= $dsh->shift_time ?>', <?= $doctor->ID ?>)" class="form-control search-inp text-center mx-1 w-25">
                  </div>
                  <div id="date-<?= $dsh->shift_time ?>-shift-count-inputs-<?= $doctor->ID ?>">

                    <?php for ($i = 1; $i <= $get_shifts->shift_count; $i++) : ?>
                    <div id="date-<?= $dsh->shift_time ?>-shift<?= $i ?>-<?= $doctor->ID ?>" class="container add-shift mb-2" style="background: rgba(250,250,250,0.9)">
                      <div>
                        <p>
                          شیفت <?= $i ?>
                        </p>
                        <div class="col-md-3 col-sm-12 my-2">
                          <label for="date-<?= $dsh->shift_time ?>-shift<?= $i ?>-price-<?= $doctor->ID ?>" class="form-label inp-label">هزینه ویزیت</label>
                          <input type="number" id="date-<?= $dsh->shift_time ?>-shift<?= $i ?>-price-<?= $doctor->ID ?>" name="date_<?= str_replace("-", "_", $dsh->shift_time); ?>_shift<?= $i ?>_price_<?= $doctor->ID ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_price ?>" class="inp-shift">
                        </div>
                      </div>
                      <div class="row d-flex justify-content-center my-3">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 my-1">
                          <label class="form-label inp-label">ساعت شروع</label>
                          <input type="time" id="date-<?= $dsh->shift_time ?>-shift<?= $i ?>-start-<?= $doctor->ID ?>" name="date_<?= str_replace("-", "_", $dsh->shift_time); ?>_shift<?= $i ?>_start_<?= $doctor->ID ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_start ?>" oninput="calculateVisitQty(this)" class="inp-shift">
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 my-1">
                          <label class="form-label inp-label">ساعت پایان</label>
                          <input type="time" id="date-<?= $dsh->shift_time ?>-shift<?= $i ?>-end-<?= $doctor->ID ?>" name="date_<?= str_replace("-", "_", $dsh->shift_time); ?>_shift<?= $i ?>_end_<?= $doctor->ID ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_end ?>" oninput="calculateVisitQty(this)" class="inp-shift">
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 my-1">
                          <label class="form-label inp-label">زمان هر ویزیت (دقیقه)</label>
                          <input type="number" id="date-<?= $dsh->shift_time ?>-shift<?= $i ?>-visit-time-<?= $doctor->ID ?>" name="date_<?= str_replace("-", "_", $dsh->shift_time); ?>_shift<?= $i ?>_visit_time_<?= $doctor->ID ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_visit_time ?>" oninput="calQtyOrVisitTime(this)" class="inp-shift">
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 my-1">
                          <label class="form-label inp-label">تعداد ویزیت</label>
                          <input type="number" min="0" id="date-<?= $dsh->shift_time ?>-shift<?= $i ?>-visit-qty-<?= $doctor->ID ?>" name="date_<?= str_replace("-", "_", $dsh->shift_time); ?>_shift<?= $i ?>_visit_qty_<?= $doctor->ID ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_visit_qty ?>" oninput="calQtyOrVisitTime(this)" class="inp-shift">
                        </div>
                        <div>
                          <div class="row d-flex justify-content-center my-3">
                            <div class="col-md-8 col-sm-12">
                              <label class="form-label inp-label">آدرس مطب</label>
                              <input type="text" id="date-<?= $dsh->shift_time ?>-shift<?= $i ?>-address-<?= $doctor->ID ?>" name="date_<?= str_replace("-", "_", $dsh->shift_time); ?>_shift<?= $i ?>_address_<?= $doctor->ID ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_address ?>" class="inp-shift">
                            </div>
                            <div class="col-md-2 col-sm-12">
                              <label class="form-label inp-label">latitude</label>
                              <input type="text" id="date-<?= $dsh->shift_time ?>-shift<?= $i ?>-latitude-<?= $doctor->ID ?>" name="date_<?= str_replace("-", "_", $dsh->shift_time); ?>_shift<?= $i ?>_latitude_<?= $doctor->ID ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_latitude ?>" class="inp-shift">
                            </div>
                            <div class="col-md-2 col-sm-12">
                              <label class="form-label inp-label">longitude</label>
                              <input type="text" id="date-<?= $dsh->shift_time ?>-shift<?= $i ?>-longitude-<?= $doctor->ID ?>" name="date_<?= str_replace("-", "_", $dsh->shift_time); ?>_shift<?= $i ?>_longitude_<?= $doctor->ID ?>" value="<?= $get_shifts->shifts[$i - 1]->shift_longitude ?>" class="inp-shift">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <? endfor; ?>

                  </div>
                </div>

                <?
              }
              ?>
            </div>
            <? endif ?>
          </div>

          <? endforeach; ?>

        </div>
        <div class="row d-flex justify-content-center mt-4">
          <div class="col d-flex justify-content-center">
            <span id="js-submit-<?= $doctor->ID ?>" class="btn btn-primary js-submit" onclick="checkBeforeSubmit(<?= $doctor->ID ?>)">ذخیره</span>
            <input type="submit" id="submit-<?= $doctor->ID ?>" name="submit" value="<? _e('ذخیره', 'rad-ref-coupon') ?>" class="btn btn-primary" style="display: none;" />
          </div>
        </div>
      </div>
    </form>


  </div>


  <? endforeach; ?>
</div>