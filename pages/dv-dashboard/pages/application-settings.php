<?php

$animations = ANIMATABLE_ANIMATIONS;

global $wpdb;
$res = $wpdb->get_results("SELECT meta_value FROM $Settings_table WHERE meta_key = 'app_setting'");

$settings = json_decode($res[0]->meta_value);

?>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<h2>Application Settings Page</h2>

<form action="" method="POST">
  <input type="hidden" name="page_code" value="application-settings" />


  <div class="row d-flex justify-content-center">
    <div class="col-sm-12 col-md-10 border border1 rounded m-2 p-3">

      <div>
        <div class="d-flex justify-content-between mb-3">
          <span style="font-size: 12px; display: block;"><? _e('اسلایدر صفحه اصلی', 'rad-dv-visit') ?></span>
          <div add-to="splashScreenLineGradientColorsPrimary-color" var="splashScreenLineGradientColorsPrimary" class="add-color-btn" onclick="addSlider('#mainSliders', '#mainSliders')">
            افزودن اسلاید
          </div>
        </div>

        <div id="mainSliders">
          <? for ($i = 0; $i <= count($settings->imageSliders->mainScreenImageSlider) - 1; $i++): ?>
          <div class="app-setting-bg p-3 mb-3" id="mainSlider-<?= $i + 1 ?>">
            <div class="d-flex justify-content-between mb-2">
              <span>اسلایدر <span id="sliderTitle-<?= $i + 1 ?>"><?= $i + 1 ?></span></span>
              <div class="remove-color-btn bg-danger rounded px-2" id="removeSlider-<?= $i +1 ?>" onclick="removeSlider('#mainSlider-<?= $i + 1 ?>', <?= $i + 1 ?>)">
                X
              </div>
            </div>
            <div class="d-flex mb-1">
              <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('لینک عکس', 'rad-dv-visit') ?></span>
              <input type="url" name="imageSlider_mainScreenImageSlider_url_<?= $i + 1 ?>" id="imageSlider_mainScreenImageSlider_url_<?= $i + 1 ?>" value="<?= $settings->imageSliders->mainScreenImageSlider[$i]->url ?>" class="form-control mx-1 w-100">
            </div>
            <div class="mb-2">
              <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('لینک به', 'rad-dv-visit') ?></span>
              <select name="imageSlider_mainScreenImageSlider_linkTo_<?= $i + 1 ?>" id="imageSlider_mainScreenImageSlider_linkTo_<?= $i + 1 ?>" onchange="checkSliderLinkToSelect(this, <?= $i + 1 ?>)">
                <option value="false" <?= !$settings->imageSliders->mainScreenImageSlider[$i]->linkTo ? 'selected' : '' ?>>بدون لینک</option>
                <option value="announcement-page" <?= 'announcement-page' === $settings->imageSliders->mainScreenImageSlider[$i]->linkTo ? 'selected' : '' ?>>صفحه اعلامیه</option>
                <?php $doctors = get_users(array('role' => OPERATOR_ROLE, 'orderby' => 'ID', 'order' => 'ASC'));
                $doctors_count = count($doctors);
                foreach ($doctors as $doctor) : ?>
                <option value="<?= $doctor->ID ?>" <?= $doctor->ID == $settings->imageSliders->mainScreenImageSlider[$i]->linkTo ? 'selected' : '' ?>>صفحه نوبت دهی  <?= $doctor->display_name ?></option>
                <? endforeach ?>
              </select>
            </div>
            <div id="mainSlider-announcement-page-<?= $i + 1 ?>">
              <? if ($settings->imageSliders->mainScreenImageSlider[$i]->linkTo === 'announcement-page'): ?>
              <div id="mainSlider-announcement-page-added-<?= $i + 1 ?>">
                <div class="d-flex mb-1">
                  <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('عنوان', 'rad-dv-visit') ?></span>
                  <input type="text" name="imageSlider_mainScreenImageSlider_announcementTitle_<?= $i + 1 ?>" id="imageSlider_mainScreenImageSlider_announcementTitle_<?= $i + 1 ?>" value="<?= $settings->imageSliders->mainScreenImageSlider[$i]->announcement->title ?>" class="form-control mx-1 w-100">
                </div>
                <div>
                  <textarea name="imageSlider_mainScreenImageSlider_announcementContent_<?= $i + 1 ?>" id="imageSlider_mainScreenImageSlider_announcementContent_<?= $i + 1 ?>" rows="4" class="form-control w-100"><?= $settings->imageSliders->mainScreenImageSlider[$i]->announcement->content ?></textarea>
                </div>
              </div>
              <script>$(document).ready(function () { CKEDITOR.replace('imageSlider_mainScreenImageSlider_announcementContent_<?= $i + 1 ?>'); })</script>
              <? endif ?>
            </div>
          </div>
          <? endfor ?>
        </div>

      </div>

    </div>
  </div>


  <div class="row d-flex justify-content-center">
    <div class="col-sm-12 col-md-5 border border1 rounded m-2 p-3">

      <div class="app-setting-bg p-3 mb-3">
        <div class="d-flex mb-1">
          <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('لینک لوگو', 'rad-dv-visit') ?></span>
          <input type="url" name="image_logo" id="image_logo" value="<?= $settings->images->logo ?>" class="form-control  text-center mx-1 w-100">
        </div>
        <div class="mb-2">
          <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('انیمیشن', 'rad-dv-visit') ?></span>
          <select name="animation_logo" id="animation_logo">
            <? foreach ($animations as $animation): ?>
            <option value="<?= $animation ?>" <?= $animation === $settings->animations->logo ? 'selected' : '' ?>><?= $animation ?></option>
            <? endforeach ?>
          </select>
        </div>
      </div>

      <div class="app-setting-bg p-3 mb-3">
        <div class="d-flex mb-1">
          <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('عنوان اپلیکیشن', 'rad-dv-visit') ?></span>
          <input type="text" name="text_splashT1" id="splashT1" value="<?= $settings->texts->splashT1 ?>" class="form-control  text-center mx-1 w-100">
        </div>
        <div class="d-flex mb-2">
          <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('رنگ', 'rad-dv-visit') ?></span>
          <input type="color" value="<?= $settings->colors->splashT1 ?>" name="color_splashT1" id="splashT1Color" class="color-input mx-1 w-100">
        </div>
        <div class="mb-2">
          <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('انیمیشن', 'rad-dv-visit') ?></span>
          <select name="animation_splashT1" id="animation_splashT1">
            <? foreach ($animations as $animation): ?>
            <option value="<?= $animation ?>" <?= $animation === $settings->animations->splashT1 ? 'selected' : '' ?>><?= $animation ?></option>
            <? endforeach ?>
          </select>
        </div>
      </div>

      <div class="app-setting-bg p-3 mb-3">
        <div class="d-flex mb-1">
          <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('عنوان معرفی اپلیکیشن', 'rad-dv-visit') ?></span>
          <input type="text" name="text_splashT2" id="splashT2" value="<?= $settings->texts->splashT2 ?>" class="form-control  text-center mx-1 w-100">
        </div>
        <div class="d-flex mb-2">
          <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('رنگ', 'rad-dv-visit') ?></span>
          <input type="color" value="<?= $settings->colors->splashT2 ?>" name="color_splashT2" id="splashT2Color" class="color-input mx-1 w-100">
        </div>
        <div class="mb-2">
          <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('انیمیشن', 'rad-dv-visit') ?></span>
          <select name="animation_splashT2" id="animation_splashT2">
            <? foreach ($animations as $animation): ?>
            <option value="<?= $animation ?>" <?= $animation === $settings->animations->splashT2 ? 'selected' : '' ?>><?= $animation ?></option>
            <? endforeach ?>
          </select>
        </div>
      </div>

      <div class="app-setting-bg p-3 mb-3">
        <div class="d-flex mb-1">
          <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('آدرس سایت جهت نمایش', 'rad-dv-visit') ?></span>
          <input type="text" name="text_splashT3" id="splashT3" value="<?= $settings->texts->splashT3 ?>" class="form-control  text-center mx-1 w-100">
        </div>
        <div class="d-flex mb-2">
          <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('رنگ', 'rad-dv-visit') ?></span>
          <input type="color" value="<?= $settings->colors->splashT3 ?>" name="color_splashT3" id="splashT3Color" class="color-input mx-1 w-100">
        </div>
        <div class="mb-2">
          <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('انیمیشن', 'rad-dv-visit') ?></span>
          <select name="animation_splashT3" id="animation_splashT3">
            <? foreach ($animations as $animation): ?>
            <option value="<?= $animation ?>" <?= $animation === $settings->animations->splashT3 ? 'selected' : '' ?>><?= $animation ?></option>
            <? endforeach ?>
          </select>
        </div>
      </div>

      <div class="app-setting-bg p-3 mb-3">
        <div class="d-flex mb-1">
          <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('عنوان اپراتور', 'rad-dv-visit') ?></span>
          <input type="text" name="text_operatorLabel" id="operatorLabel" value="<?= $settings->texts->operatorLabel ?>" class="form-control  text-center mx-1 w-100">
        </div>
        <div class="d-flex mb-1">
          <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('عنوان جمع اپراتور', 'rad-dv-visit') ?></span>
          <input type="text" name="text_operatorsLabel" id="operatorsLabel" value="<?= $settings->texts->operatorsLabel ?>" class="form-control  text-center mx-1 w-100">
        </div>
      </div>

    </div>

    <div class="col-sm-12 col-md-5 border border1 rounded m-2 p-3">

      <div class="app-setting-bg p-3 mb-3">
        <div class="my-3" id="primaryColor-color-container">
          <span class="mb-2" style="font-size: 12px; display: block;"><? _e('رنگ اصلی', 'rad-dv-visit') ?></span>
          <input type="color" value="<?= $settings->colors->primaryColor ?>" name="color_primaryColor" id="primaryColor" class="color-input mx-1 w-100" />
        </div>
        <hr />
        <div class="my-3" id="secondaryColor-color-container">
          <span class="mb-2" style="font-size: 12px; display: block;"><? _e('رنگ دوم', 'rad-dv-visit') ?></span>
          <input type="color" value="<?= $settings->colors->secondaryColor ?>" name="color_secondaryColor" id="secondaryColor" class="color-input mx-1 w-100" />
        </div>
        <hr />
        <div class="my-3" id="successColor-color-container">
          <span class="mb-2" style="font-size: 12px; display: block;"><? _e('رنگ موفق', 'rad-dv-visit') ?></span>
          <input type="color" value="<?= $settings->colors->successColor ?>" name="color_successColor" id="successColor" class="color-input mx-1 w-100" />
        </div>
        <hr />
        <div class="my-3" id="warningColor-color-container">
          <span class="mb-2" style="font-size: 12px; display: block;"><? _e('رنگ  هشدار', 'rad-dv-visit') ?></span>
          <input type="color" value="<?= $settings->colors->warningColor ?>" name="color_warningColor" id="warningColor" class="color-input mx-1 w-100" />
        </div>
        <hr />
        <div class="my-3" id="dangerColor-color-container">
          <span class="mb-2" style="font-size: 12px; display: block;"><? _e('رنگ ناموفق', 'rad-dv-visit') ?></span>
          <input type="color" value="<?= $settings->colors->dangerColor ?>" name="color_dangerColor" id="dangerColor" class="color-input mx-1 w-100" />
        </div>
        <hr />
        <div class="my-3" id="infoColor-color-container">
          <span class="mb-2" style="font-size: 12px; display: block;"><? _e('رنگ  اطلاع دهنده', 'rad-dv-visit') ?></span>
          <input type="color" value="<?= $settings->colors->infoColor ?>" name="color_infoColor" id="infoColor" class="color-input mx-1 w-100" />
        </div>
        <hr />
        <div class="my-3" id="disabledColor-color-container">
          <span class="mb-2" style="font-size: 12px; display: block;"><? _e('رنگ غیرفعال', 'rad-dv-visit') ?></span>
          <input type="color" value="<?= $settings->colors->disabledColor ?>" name="color_disabledColor" id="disabledColor" class="color-input mx-1 w-100" />
        </div>
      </div>

      <div class="app-setting-bg p-3 mb-3" id="splashScreenLineGradientColorsPrimary-color-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span style="font-size: 12px;"><? _e('رنگ پس زمینه اسپلش اسکرین (گردینت)', 'rad-dv-visit') ?></span>
          <div add-to="splashScreenLineGradientColorsPrimary-color" var="splashScreenLineGradientColorsPrimary" class="add-color-btn" onclick="addColorAppSetting(this)">
            افزودن رنگ
          </div>
        </div>
        <div id="splashScreenLineGradientColorsPrimary-color">
          <? for ($i = 0; $i < count($settings->colors->splashScreenLineGradientColorsPrimary); $i++): ?>
          <div id="splashScreenLineGradientColorsPrimary-color-<?= $i+1 ?>" class="d-flex m-2">
            <input type="color" value="<?= $settings->colors->splashScreenLineGradientColorsPrimary[$i] ?>" name="color_splashScreenLineGradientColorsPrimary_<?= $i+1 ?>" id="splashScreenLineGradientColorsPrimary_<?= $i+1 ?>" class="color-input mx-1 w-100">
            <? if (count($settings->colors->splashScreenLineGradientColorsPrimary) > 1) : ?>
            <div id="splashScreenLineGradientColorsPrimary-btn-<?= $i+1 ?>" remove="splashScreenLineGradientColorsPrimary-color-<?= $i+1 ?>" class="remove-color-btn bg-danger rounded px-2" onclick="removeColorAppSetting('#splashScreenLineGradientColorsPrimary-color-<?= $i+1 ?>', 'splashScreenLineGradientColorsPrimary-color', 'splashScreenLineGradientColorsPrimary')">
              X
            </div>
            <? endif ?>
          </div>
          <? endfor;
          ?>
        </div>
      </div>

      <div class="app-setting-bg p-3 mb-3" id="lineGradientColorsPrimary-color-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span style="font-size: 12px;"><? _e('رنگ پس زمینه صفحات داخلی اپلیکیشن (گردینت)', 'rad-dv-visit') ?></span>
          <div add-to="lineGradientColorsPrimary-color" var="lineGradientColorsPrimary" class="add-color-btn" onclick="addColorAppSetting(this)">
            افزودن رنگ
          </div>
        </div>
        <div id="lineGradientColorsPrimary-color">
          <? for ($i = 0; $i < count($settings->colors->lineGradientColorsPrimary); $i++): ?>
          <div id="lineGradientColorsPrimary-color-<?= $i+1 ?>" class="d-flex m-2">
            <input type="color" value="<?= $settings->colors->lineGradientColorsPrimary[$i] ?>" name="color_lineGradientColorsPrimary_<?= $i+1 ?>" id="lineGradientColorsPrimary_<?= $i+1 ?>" class="color-input mx-1 w-100">
            <? if (count($settings->colors->lineGradientColorsPrimary) > 1) : ?>
            <div id="lineGradientColorsPrimary-btn-<?= $i+1 ?>" remove="lineGradientColorsPrimary-color-<?= $i+1 ?>" class="remove-color-btn bg-danger rounded px-2" onclick="removeColorAppSetting('#lineGradientColorsPrimary-color-<?= $i+1 ?>', 'lineGradientColorsPrimary-color', 'lineGradientColorsPrimary')">
              X
            </div>
            <? endif ?>
          </div>
          <? endfor;
          ?>
        </div>
        <hr class="my-4">
        <div>
          <span class="mb-2" style="font-size: 12px; display: block;"><? _e('رنگ نوشته های روی پس زمینه صفحات داخلی اپلیکیشن', 'rad-dv-visit') ?></span>
          <input type="color" value="<?= $settings->colors->textOnBg ?>" name="color_textOnBg" id="textOnBg" class="color-input mx-1 w-100">
        </div>
      </div>

      <div class="app-setting-bg p-3 mb-3" id="lineGradientHeaderTitle-color-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span style="font-size: 12px;"><? _e('رنگ پس زمینه هدر صفحات (گردینت)', 'rad-dv-visit') ?></span>
          <div add-to="lineGradientHeaderTitle-color" var="lineGradientHeaderTitle" class="add-color-btn" onclick="addColorAppSetting(this)">
            افزودن رنگ
          </div>
        </div>
        <div id="lineGradientHeaderTitle-color">
          <? for ($i = 0; $i < count($settings->colors->lineGradientHeaderTitle); $i++): ?>
          <div id="lineGradientHeaderTitle-color-<?= $i+1 ?>" class="d-flex m-2">
            <input type="color" value="<?= $settings->colors->lineGradientHeaderTitle[$i] ?>" name="color_lineGradientHeaderTitle_<?= $i+1 ?>" id="lineGradientHeaderTitle_<?= $i+1 ?>" class="color-input mx-1 w-100">
            <? if (count($settings->colors->lineGradientHeaderTitle) > 1) : ?>
            <div id="lineGradientHeaderTitle-btn-<?= $i+1 ?>" remove="lineGradientHeaderTitle-color-<?= $i+1 ?>" class="remove-color-btn bg-danger rounded px-2" onclick="removeColorAppSetting('#lineGradientHeaderTitle-color-<?= $i+1 ?>', 'lineGradientHeaderTitle-color', 'lineGradientHeaderTitle')">
              X
            </div>
            <?endif ?>
          </div>
          <? endfor;
          ?>
        </div>
        <div class="d-flex my-4">
          <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('لینک تصویر روی هدر صفحات', 'rad-dv-visit') ?></span>
          <input type="url" name="image_overlayHeaderTitle" id="image_overlayHeaderTitle" value="<?= $settings->images->overlayHeaderTitle ?>" class="form-control  text-center mx-1 w-100">
        </div>
      </div>

      <div class="app-setting-bg p-3 mb-3" id="lineGradientTitleCard-color-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span style="font-size: 12px;"><? _e('رنگ پس زمینه کارت ها روی هدر (گردینت)', 'rad-dv-visit') ?></span>
          <div add-to="lineGradientTitleCard-color" var="lineGradientTitleCard" class="add-color-btn" onclick="addColorAppSetting(this)">
            افزودن رنگ
          </div>
        </div>
        <div id="lineGradientTitleCard-color">
          <? for ($i = 0; $i < count($settings->colors->lineGradientTitleCard); $i++): ?>
          <div id="lineGradientTitleCard-color-<?= $i+1 ?>" class="d-flex m-2">
            <input type="color" value="<?= $settings->colors->lineGradientTitleCard[$i] ?>" name="color_lineGradientTitleCard_<?= $i+1 ?>" id="lineGradientTitleCard_<?= $i+1 ?>" class="color-input mx-1 w-100">
            <? if (count($settings->colors->lineGradientTitleCard) > 1) : ?>
            <div id="lineGradientTitleCard-btn-<?= $i+1 ?>" remove="lineGradientTitleCard-color-<?= $i+1 ?>" class="remove-color-btn bg-danger rounded px-2" onclick="removeColorAppSetting('#lineGradientTitleCard-color-<?= $i+1 ?>', 'lineGradientTitleCard-color', 'lineGradientTitleCard')">
              X
            </div>
            <? endif ?>
          </div>
          <? endfor;
          ?>
        </div>
      </div>

      <div class="app-setting-bg p-3 mb-3" id="cardSlider-color-container">
        <div class="mb-3">
          <span class="mb-2" style="font-size: 12px; display: block;"><? _e('رنگ پس زمینه کارت اسلایدرها', 'rad-dv-visit') ?></span>
          <input type="color" value="<?= $settings->colors->cardSlider ?>" name="color_cardSlider" id="cardSlider" class="color-input mx-1 w-100">
        </div>
      </div>

      <div class="app-setting-bg p-3 mb-3" id="inCardSliderBg-color-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span style="font-size: 12px;"><? _e('رنگ پس زمینه کادر داخل کارت اسلایدرها (گردینت)', 'rad-dv-visit') ?></span>
          <div add-to="inCardSliderBg-color" var="inCardSliderBg" class="add-color-btn" onclick="addColorAppSetting(this)">
            افزودن رنگ
          </div>
        </div>
        <div id="inCardSliderBg-color">
          <? for ($i = 0; $i < count($settings->colors->inCardSliderBg); $i++): ?>
          <div id="inCardSliderBg-color-<?= $i+1 ?>" class="d-flex m-2">
            <input type="color" value="<?= $settings->colors->inCardSliderBg[$i] ?>" name="color_inCardSliderBg_<?= $i+1 ?>" id="inCardSliderBg_<?= $i+1 ?>" class="color-input mx-1 w-100">
            <? if (count($settings->colors->inCardSliderBg) > 1) : ?>
            <div id="inCardSliderBg-btn-<?= $i+1 ?>" remove="inCardSliderBg-color-<?= $i+1 ?>" class="remove-color-btn bg-danger rounded px-2" onclick="removeColorAppSetting('#inCardSliderBg-color-<?= $i+1 ?>', 'inCardSliderBg-color', 'inCardSliderBg')">
              X
            </div>
            <? endif ?>
          </div>
          <? endfor;
          ?>
        </div>
      </div>

      <div class="app-setting-bg p-3 mb-3" id="lineGradientTitleCard-color-container">
        <div class="my-3" id="selectDateEnabledBgColor-color-container">
          <span class="mb-2" style="font-size: 12px;">رنگ پس زمینه انتخاب تاریخ ویزیت (فعال)</span>
          <input type="color" value="<?= $settings->colors->selectDateEnabledBgColor ?>" name="color_selectDateEnabledBgColor" id="selectDateEnabledBgColor" class="color-input mx-1 w-100">
        </div>
        <hr>
        <div class="my-3" id="selectDateDisabledBgColor-color-container">
          <span class="mb-2" style="font-size: 12px;">رنگ پس زمینه انتخاب تاریخ ویزیت (غیرفعال)</span>
          <input type="color" value="<?= $settings->colors->selectDateDisabledBgColor ?>" name="color_selectDateDisabledBgColor" id="selectDateDisabledBgColor" class="color-input mx-1 w-100">
        </div>
      </div>

      <div class="app-setting-bg p-3 mb-3" id="modalBg-color-container">
        <div class="mb-3">
          <span class="mb-2" style="font-size: 12px; display: block;"><? _e('رنگ پس زمینه مدال', 'rad-dv-visit') ?></span>
          <input type="color" value="<?= $settings->colors->modalBg ?>" name="color_modalBg" id="modalBg" class="color-input mx-1 w-100">
        </div>
        <div class="mb-2">
          <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('انیمیشن', 'rad-dv-visit') ?></span>
          <select name="animation_modal" id="animation_modal">
            <? foreach ($animations as $animation): ?>
            <option value="<?= $animation ?>" <?= $animation === $settings->animations->modal ? 'selected' : '' ?>><?= $animation ?></option>
            <? endforeach ?>
          </select>
        </div>
      </div>

    </div>
  </div>

  <div class="row d-flex justify-content-center">

    <div class="col-sm-5 col-md-5 col-sm-11 border border1 rounded m-2 p-3">
      <div class="app-setting-bg p-3 mb-3" id="panelBottom">
        <div class="mb-3">
          <span class="mb-2" style="font-size: 12px; display: block;"><? _e('رنگ پس زمینه منو پایین', 'rad-dv-visit') ?></span>
          <input type="color" value="<?= $settings->colors->panelBottom ?>" name="color_panelBottom" id="panelBottom" class="color-input mx-1 w-100">
        </div>

        <div class="mb-3">
          <span class="mb-2" style="font-size: 12px; display: block;"><? _e('رنگ متن های منو پایین (کلیک شده)', 'rad-dv-visit') ?></span>
          <input type="color" value="<?= $settings->colors->panelBottomTextFocused ?>" name="color_panelBottomTextFocused" id="panelBottomTextFocused" class="color-input mx-1 w-100">
        </div>

        <div class="mb-3">
          <span class="mb-2" style="font-size: 12px; display: block;"><? _e('رنگ متن های منو پایین (کلیک نشده)', 'rad-dv-visit') ?></span>
          <input type="color" value="<?= $settings->colors->panelBottomTextNotFocused ?>" name="color_panelBottomTextNotFocused" id="panelBottomTextNotFocused" class="color-input mx-1 w-100">
        </div>
      </div>
      <div class="grid">
        <div class="responsive-item0 app-setting-bg p-3">
          <span style="font-size: 12px; display: block;">آیکون سمت راست </span>
          <span class="mb-2" style="font-size: 10px; display: block;">منو پایین</span>
          <div class="d-flex mb-1">
            <span class="rounded p-1 ms-1" style="font-size: 12px; background: rgba(200,200,200,0.5);">آیکون</span>
            <select name="icon_panelBottomStart" id="icon-panelBottomStart" class="form-select" aria-label="size 3 select example">
              <? foreach (IONICONS as $ficon): ?>
              <option value="<?= $ficon ?>" <?= $settings->icons->panelBottomStart === $ficon ? 'selected' : '' ?> data-content="<ion-icon name='nutrition'></ion-icon>"><?= $ficon ?></option>
              <? endforeach ?>
            </select>
          </div>
          <div class="d-flex mb-1">
            <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('متن', 'rad-dv-visit') ?></span>
            <input type="text" name="text_panelBottomStart" id="panelBottomStart" value="<?= $settings->texts->panelBottomStart ?>" class="form-control  text-center mx-1 w-100">
          </div>
        </div>

        <div class="responsive-item0 app-setting-bg p-3">
          <span style="font-size: 12px; display: block;">آیکون وسط</span>
          <span class="mb-2" style="font-size: 10px; display: block;">منو پایین</span>
          <div class="d-flex mb-1">
            <span class="rounded p-1 ms-1" style="font-size: 12px; background: rgba(200,200,200,0.5);">آیکون</span>
            <select name="icon_panelBottomMiddle" id="icon-panelBottomMiddle" class="form-select" aria-label="size 3 select example">
              <? foreach (IONICONS as $ficon): ?>
              <option value="<?= $ficon ?>" <?= $settings->icons->panelBottomMiddle === $ficon ? 'selected' : '' ?> data-content="<ion-icon name='nutrition'></ion-icon>"><?= $ficon ?></option>
              <? endforeach ?>
            </select>
          </div>
          <div class="d-flex mb-1">
            <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('متن', 'rad-dv-visit') ?></span>
            <input type="text" name="text_panelBottomMiddle" id="panelBottomMiddle" value="<?= $settings->texts->panelBottomMiddle ?>" class="form-control  text-center mx-1 w-100">
          </div>
        </div>

        <div class="responsive-item0 app-setting-bg p-3">
          <span style="font-size: 12px; display: block;">آیکون سمت چپ</span>
          <span class="mb-2" style="font-size: 10px; display: block;">منو پایین</span>
          <div class="d-flex mb-1">
            <span class="rounded p-1 ms-1" style="font-size: 12px; background: rgba(200,200,200,0.5);">آیکون</span>
            <select name="icon_panelBottomEnd" id="icon-panelBottomEnd" class="form-select" aria-label="size 3 select example">
              <? foreach (IONICONS as $ficon): ?>
              <option value="<?= $ficon ?>" <?= $settings->icons->panelBottomEnd === $ficon ? 'selected' : '' ?> data-content="<ion-icon name='nutrition'></ion-icon>"><?= $ficon ?></option>
              <? endforeach ?>
            </select>
          </div>
          <div class="d-flex mb-1">
            <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);"><? _e('متن', 'rad-dv-visit') ?></span>
            <input type="text" name="text_panelBottomEnd" id="panelBottomEnd" value="<?= $settings->texts->panelBottomEnd ?>" class="form-control  text-center mx-1 w-100">
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-5 col-md-5 col-sm-11 border border1 rounded m-2 p-3">
      <div class="responsive-container0 grid">

        <div class="responsive-item0 app-setting-bg p-3">
          <span style="font-size: 12px; display: block;">دکمه بازگشت</span>
          <span class="mb-2" style="font-size: 10px; display: block;">آیکون و رنگ دکمه</span>
          <div class="d-flex mb-1">
            <span class="rounded p-1 ms-1" style="font-size: 12px; background: rgba(200,200,200,0.5);">آیکون</span>
            <select name="icon_back" id="icon-back" class="form-select" aria-label="size 3 select example">
              <?php
              $filtered_icons = array_filter(IONICONS, function($icon) {
                return(strpos($icon, 'back'));
              });
              foreach ($filtered_icons as $ficon):
              ?>
              <option value="<?= $ficon ?>" <?= $settings->icons->back === $ficon ? 'selected' : '' ?> data-content="<ion-icon name='nutrition'></ion-icon>"><?= $ficon ?></option>
              <? endforeach ?>
            </select>
          </div>
        </div>

        <div class="responsive-item0 app-setting-bg p-3">
          <span style="font-size: 12px; display: block;">دکمه پروفایل</span>
          <span class="mb-2" style="font-size: 10px; display: block;">آیکون، رنگ آیکون و رنگ پس زمینه آیکون</span>
          <div class="d-flex mb-1">
            <span class="rounded p-1 ms-1" style="font-size: 12px; background: rgba(200,200,200,0.5);">آیکون</span>
            <select name="icon_profile" id="icon-profile" class="form-select" aria-label="size 3 select example">
              <?php
              $filtered_icons = array_filter(IONICONS, function($icon) {
                return(strpos($icon, 'person'));
              });
              foreach ($filtered_icons as $ficon):
              ?>
              <option value="<?= $ficon ?>" <?= $settings->icons->profile === $ficon ? 'selected' : '' ?> data-content="<ion-icon name='nutrition'></ion-icon>"><?= $ficon ?></option>
              <? endforeach ?>
            </select>
          </div>
          <div class="d-flex mb-1">
            <span class="rounded p-1 ms-1" style="font-size: 12px; background: rgba(200,200,200,0.5);">رنگ</span>
            <input type="color" value="<?= $settings->colors->profileIcon ?>" name="color_profileIcon" id="profileIcon" class="color-input w-100">
          </div>
          <div class="d-flex mb-1">
            <span class="rounded p-1 ms-1" style="font-size: 12px; background: rgba(200,200,200,0.5);">پس زمینه</span>
            <input type="color" value="<?= $settings->colors->bgProfileIcon ?>" name="color_bgProfileIcon" id="bgProfileIcon" class="color-input w-100">
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="text-center">
    <input type="submit" class="btn btn-primary" value="ذخیره" />
  </div>
</form>

<script>
  function addSlider(addTo, father) {
    let fatherChildrenCount = $(father).children().length

    $(addTo).append(`<div class="app-setting-bg p-3 mb-3" id="mainSlider-${fatherChildrenCount + 1}">
      <div class="d-flex justify-content-between mb-2">
      <span>اسلایدر <span id="sliderTitle-${fatherChildrenCount + 1}">${fatherChildrenCount + 1}</span></span>
      <div class="remove-color-btn bg-danger rounded px-2" id="removeSlider-${fatherChildrenCount + 1}" onclick="removeSlider('#mainSlider-${fatherChildrenCount + 1}', ${fatherChildrenCount + 1})">
      X
      </div>
      </div>
      <div class="d-flex mb-1">
      <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);">لینک عکس</span>
      <input type="url" name="imageSlider_mainScreenImageSlider_url_${fatherChildrenCount + 1}" id="imageSlider_mainScreenImageSlider_url_${fatherChildrenCount + 1}" class="form-control mx-1 w-100">
      </div>
      <div class="mb-2">
      <span class="rounded p-1" style="font-size: 12px; background: rgba(200,200,200,0.5);">لینک به</span>
      <select name="imageSlider_mainScreenImageSlider_linkTo_${fatherChildrenCount + 1}" id="imageSlider_mainScreenImageSlider_linkTo_${fatherChildrenCount + 1}" onchange="checkSliderLinkToSelect(this, ${fatherChildrenCount + 1})">
      <option value="false">بدون لینک</option>
      <option value="announcement-page">صفحه اعلامیه</option>
      <?php $doctors = get_users(array('role' => OPERATOR_ROLE, 'orderby' => 'ID', 'order' => 'ASC'));
      $doctors_count = count($doctors);
      foreach ($doctors as $doctor) : ?>
      <option value="<?= $doctor->ID ?>" >صفحه نوبت دهی  <?= $doctor->display_name ?></option>
      <? endforeach ?>
      </select>
      </div>
      <div id="mainSlider-announcement-page-${fatherChildrenCount + 1}">
      </div>
      </div>`)

    // scroll to new added
    $("html, body").animate(
      {
        scrollTop: $(`#mainSlider-${fatherChildrenCount + 1}`).offset()
        .top,
      },
      500
    );

  }
</script>