<?php

$result = [];

$texts = [];
$colors = [];
$animations = [];
$images = [];
$imageSliders = [];
$icons = [];




foreach ($_POST as $var => $value) {
  $splitted_var_name = explode('_', $var);
  if ($splitted_var_name[0] === 'text') {
    $texts[$splitted_var_name[1]] = $value;
  } elseif ($splitted_var_name[0] === 'color' || $splitted_var_name[0] === 'animation' || $splitted_var_name[0] === 'image' || $splitted_var_name[0] === 'icon') {
    if (count($splitted_var_name) == 2)
      $ {
      $splitted_var_name[0] . 's'
    }[$splitted_var_name[1]] = $value; // ${$splitted_var_name[0] . 's'} is variable $colors or $animatins or ...
    elseif ((count($splitted_var_name) == 3)) {
      $ {
        $splitted_var_name[1]} = [];

      if ($splitted_var_name[count($splitted_var_name) - 1] > 1)
        continue;

      $i = 1;
      while (!empty($ {
        'color_' . $splitted_var_name[1] . '_' . $i
      })) {
        $ {
          $splitted_var_name[1]}[] = $ {
          'color_' . $splitted_var_name[1] . '_' . $i
        };
        $i++;
      }
      if ($i == 2)
        $ {
        $splitted_var_name[1]}[] = $ {
        'color_' . $splitted_var_name[1] . '_1'
      };

      $ {
        $splitted_var_name[0] . 's'
      }[$splitted_var_name[1]] = $ {
        $splitted_var_name[1]}; // ${$splitted_var_name[0] . 's'} is variable $colors or $animatins or ...
    }

  } elseif (empty($imageSlider_mainScreenImageSlider_url_1)) {
    $imageSliders['mainScreenImageSlider'] = [];
  } elseif ($splitted_var_name[0] === 'imageSlider') {
    if ($splitted_var_name[count($splitted_var_name) - 1] > 1 || $splitted_var_name[2] === 'linkTo' || $splitted_var_name[2] === 'announcementTitle' || $splitted_var_name[2] === 'announcementContent')
      continue;

    $i = 1;
    while (!empty($ {
      'imageSlider_' . $splitted_var_name[1] . '_url_' . $i
    })) {

      $announcement['title'] = $ {
        'imageSlider_' . $splitted_var_name[1] . '_announcementTitle_' . $i
      };
      $announcement['content'] = str_replace(PHP_EOL,"\n", trim(json_encode($ { 'imageSlider_' . $splitted_var_name[1] . '_announcementContent_' . $i }),'\'"')); 

      $sub1['url'] = $ {
        'imageSlider_' . $splitted_var_name[1] . '_url_' . $i
      };
      $sub1['linkTo'] = $ {
        'imageSlider_' . $splitted_var_name[1] . '_linkTo_' . $i
      };
      $sub1['announcement'] = $announcement;

      $ar2[] = $sub1;
      $i++;
    }

    $imageSliders[$splitted_var_name[1]] = $ar2;
  }
}




$result['texts'] = $texts;
$result['colors'] = $colors;
$result['animations'] = $animations;
$result['images'] = $images;
$result['imageSliders'] = $imageSliders;
$result['icons'] = $icons;


// save result array to db
$table_name = $wpdb->prefix . $Settings_table;
$data = json_encode($result, JSON_UNESCAPED_UNICODE);


global $wpdb;
if ($wpdb->query($wpdb->prepare("UPDATE $table_name SET meta_value = '$data' WHERE meta_key = 'app_setting'")))
  echo "saved successfuly";
else
  echo "failed to save the data";