<?php
include_once(plugin_dir_path(__DIR__) . 'constant.php');

///////////////// add custom field to user profile
add_action('user_new_form', 'dv_custom_user_profile_fields_new_user');
add_action('show_user_profile', 'dv_custom_user_profile_fields');
add_action('edit_user_profile', 'dv_custom_user_profile_fields');

function dv_custom_user_profile_fields_new_user($user) {
  //$user->ID
  $operator_label = json_decode(get_db_row(SETTINGS_TABLE, 'meta_key', 'app_setting')[0]->meta_value)->texts->operatorLabel;
  // include jquery
  //wp_register_script('jQuery', 'https://code.jquery.com/jquery-3.6.0.min.js', null, null, true);
  //wp_enqueue_script('jQuery');
  ?>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <div id="operator_title_description_section"></div>
  <script>
    $("#role").change(function() {
      if ($("#role").val() === '<?= OPERATOR_ROLE ?>') {
        $("#operator_title_description_section").html(`<h3 class="heading">فیلدهای اضافی <?= $operator_label ?></h3> <table class="form-table"> <tr> <th><label for="contact">عنوان شغلی <?= $operator_label ?></label></th> <td><input type="text" class="input-text form-control" name="operator_title_description" value="<?= get_user_meta($user->ID, 'operator_title_description', true) ?>" id="operator-title-description" /> </td> </tr> </table>`)
      } else {
        $("#operator_title_description_section").empty();
      }
    });
  </script>
  <?php
}

function dv_custom_user_profile_fields($user) {
  //$user->ID
  $operator_label = json_decode(get_db_row(SETTINGS_TABLE, 'meta_key', 'app_setting')[0]->meta_value)->texts->operatorLabel;
  ?>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <div id="operator_title_description_section">
    <? if (in_array(OPERATOR_ROLE, (array) $user->roles)) : ?>
    <h3 class="heading">فیلدهای اضافی <?= $operator_label ?></h3> <table class="form-table">
      <tr> <th><label for="contact">عنوان شغلی <?= $operator_label ?></label></th> <td><input type="text" class="input-text form-control" name="operator_title_description" value="<?= get_user_meta($user->ID,
        'operator_title_description',
        true) ?>" id="operator-title-description" /> </td> </tr>
    </table>
    <? endif ?>
  </div>
  <script>
    $("#role").change(function() {
      if ($("#role").val() === '<?= OPERATOR_ROLE ?>') {
        $("#operator_title_description_section").html(`<h3 class="heading">فیلدهای اضافی <?= $operator_label ?></h3> <table class="form-table"> <tr> <th><label for="contact">عنوان شغلی <?= $operator_label ?></label></th> <td><input type="text" class="input-text form-control" name="operator_title_description" value="<?= get_user_meta($user->ID, 'operator_title_description', true) ?>" id="operator-title-description" /> </td> </tr> </table>`)
      } else {
        $("#operator_title_description_section").empty();
      }
    });
  </script>
  <?php
}

///////////////// save new custom fields
add_action('personal_options_update', 'save_custom_user_profile_fields');
add_action('edit_user_profile_update', 'save_custom_user_profile_fields');

add_action('user_register', 'save_custom_user_profile_fields');
add_action('profile_update', 'save_custom_user_profile_fields');

function save_custom_user_profile_fields($user_id) {
  if (empty($_POST['_wpnonce']) || ! wp_verify_nonce($_POST['_wpnonce'], 'update-user_' . $user_id)) {
    return;
  }

  if (!current_user_can('edit_user', $user_id)) {
    return false;
  }
  if (!empty($_POST['operator_title_description']))
    update_user_meta($user_id, 'operator_title_description', $_POST['operator_title_description']);
}