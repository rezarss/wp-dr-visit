<?php

try {

  $current_dir = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], "/") + 1);

  if (!require_once "$current_dir../../../../wp-load.php")
    throw new Exception("wp-load include failed");

  //$txt = ghfghjcbkg();

  $fp = fopen('data.txt', 'a'); //opens file in append mode
  fwrite($fp, ' - ' . date("Y/m/d H:i:s") . "\n");
  fclose($fp);


} catch (Exception $e) {
  echo $e->getMessage();
}