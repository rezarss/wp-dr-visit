<?php

add_action("init", "dv_create_initial_tables", -99);
function dv_create_initial_tables() {
  //header("Location: https://www.rtl-theme.com/dashboard/#/ticket/401684"); 
  create_table("dv_shifts_schedule", "CREATE TABLE `dv_shifts_schedule` ( `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL, `doctor_id` int(10) UNSIGNED NOT NULL, `shift_time` varchar(32) NOT NULL, `shift_setting` json NOT NULL, `shift_type` varchar(32) NOT NULL, `extra` text ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
  create_table("dv_shift_settings", "CREATE TABLE `dv_shift_settings` ( `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL, `doctor_id` int(10) UNSIGNED NOT NULL, `meta_key` varchar(64) CHARACTER SET utf8 NOT NULL, `meta_value` json NOT NULL, `extra` text CHARACTER SET utf8 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
  create_table("dv_settings", "CREATE TABLE `dv_settings` ( `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL, `meta_key` varchar(64) CHARACTER SET utf8 NOT NULL, `meta_value` text CHARACTER SET utf8 NOT NULL, `extra` text CHARACTER SET utf8 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}
