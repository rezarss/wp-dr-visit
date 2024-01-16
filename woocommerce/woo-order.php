<?php 
$woo_order_status_arr = array(
    'cancelled' => array('label' => 'لغو شده', 'color' => '#999999', 'text_color' => '#fff'),
    'completed' => array('label' => 'تکمیل شده', 'color' => '#00ab17', 'text_color' => '#fff'),
    'failed' => array('label' => 'ناموفق', 'color' => '#ff0000', 'text_color' => '#fff'),
    'on-hold' => array('label' => 'در انتظار بررسی', 'color' => '#ff7b00', 'text_color' => '#fff'),
    'pending' => array('label' => 'در انتظار پرداخت', 'color' => '#999999', 'text_color' => '#fff'),
    'processing' => array('label' => 'در حال انجام', 'color' => '#ffea00', 'text_color' => '#fff'),
    'refunded' => array('label' => 'مسترد شده', 'color' => '#999999', 'text_color' => '#fff')
);
////////////////////////////////// display the extra data in the order admin panel
function kia_display_order_data_in_admin($order) {
    if (get_post_meta($order->id, 'vd_order_type', true) && get_post_meta($order->id, 'vd_order_type', true) === 'visit') {
        global $woo_order_status_arr;
        $order_status = $order->status; 

        ?>
        <style>
            .vd-container {
                border-radius: 7px;
                background: rgba(200, 200, 200, 0.2);
                margin: 10px 0;
                padding: 10px;
                width: 100%;
            }
            .order-<?= $order_status ?> {
                background: <?= $woo_order_status_arr[$order_status]['color'] ?>;
                border-radius: 7px;
                padding: 8px;
                color: <?= $woo_order_status_arr[$order_status]['text_color'] ?> !important;
            }
            .order-status-label {
                text-align: left;
                margin: 25px 0 10px;
            }
            @media only screen and (max-width: 3000px) {
                .vd-container {
                    width: 300px;
                }
            }
            @media only screen and (max-width: 1280px) {
                .vd-container {
                    width: 100%;
                }
            }
        </style>
        <div class="order_data_column">
            <div class="vd-container">
                <h3>
                    <strong><?= __('ویزیت') ?>: </strong><span><?= get_user_by('id', get_post_meta($order->id, "vd_doctor_id", true))->display_name ?></span>
                </h3>
                <p>
                    <strong><?= __('تاریخ ویزیت') ?>: </strong><span dir="rtl"><?= get_post_meta($order->id, "vd_user_visit_date", true) ?></span>
                </p>
                <p>
                    <strong><?= __('ساعت ویزیت') ?>: </strong><?= get_post_meta($order->id, "vd_user_visit_time", true) ?>
                </p>
                <p>
                    <strong><?= __('نام مراجعه کننده') ?>: </strong><span><?= get_user_by('id', get_post_meta($order->id, "vd_user_id", true))->display_name ?></span>
                </p>
                <p>
                    <strong><?= __('شماره تماس مراجعه کننده') ?>: </strong><a href="tel:<?= get_user_meta(get_post_meta($order->id, "vd_user_id", true), "digits_phone", true) ?>" dir="ltr"><?= get_user_meta(get_post_meta($order->id, "vd_user_id", true), "digits_phone", true) ?></a>
                </p>
                <p>
                    <strong><?= __('کد ملی مراجعه کننده') ?>: </strong><span><?= get_user_meta(get_post_meta($order->id, "vd_user_id", true), "citizen_id", true) ?></span>
                </p>
                <div class="order-status-label">
                    <span class="order-<?= $order_status ?>"><?php _e('وضعیت سفارش: ') . _e($woo_order_status_arr[$order_status]['label']); ?></span>
                </div>
            </div>
        </div>
        <?php
    }
}
add_action('woocommerce_admin_order_data_after_order_details', 'kia_display_order_data_in_admin');


////////////////////////////////// add column to woocommerce order table page
add_filter('manage_edit-shop_order_columns', 'add_new_order_admin_list_column');

function add_new_order_admin_list_column($columns) {
    $operator_label = json_decode(get_db_row(SETTINGS_TABLE, 'meta_key', 'app_setting')[0]->meta_value)->texts->operatorLabel;
    $columns['visit_operator'] = 'ویزیت ' . $operator_label;
    return $columns;
}

add_action('manage_shop_order_posts_custom_column', 'add_new_order_admin_list_column_content');

function add_new_order_admin_list_column_content($column) {

    global $woo_order_status_arr;
    global $post;

    if ('visit_operator' === $column) {
        if (get_post_meta($post->ID, 'vd_order_type', true) === 'visit') {

            $order = wc_get_order($post->ID);
            $order_status = $order->status;
            ?>
            <style>
                .order-<?= $order_status ?> {
                    background: <?= $woo_order_status_arr[$order_status]['color'] ?>;
                    border-radius: 7px;
                    padding: 3px 6px;
                    color: <?= $woo_order_status_arr[$order_status]['text_color'] ?> !important;
                }
                .order-view {
                    display: flex;
                    justify-content: space-between;
                }
                .dv-order-item {
                    border: 1px solid rgba(180, 180, 180, 0.8);
                    border-radius: 7px;
                    padding: 3px 6px;
                }
                .patient-text {
                    font-size: 11px !important;
                }
            </style>
            <div class="order-view">
                <div class="dv-order-item">
                    <span class="patient-text"><?= get_user_by('id', get_post_meta($post->ID, 'vd_doctor_id', true))->display_name ?></span>
                </div>
                <div class="dv-order-item">
                    <span class="patient-text" dir="ltr"><?= get_post_meta($post->ID, 'vd_user_visit_time', true) ?></span>
                    <span class="patient-text"> - </span>
                    <span class="patient-text" dir="ltr"><?= get_post_meta($post->ID, 'vd_user_visit_date', true) ?></span>
                </div>
                <span class="order-<?= $order_status ?> patient-text" dir="ltr"><?= $woo_order_status_arr[$order_status]['label'] ?></span>
            </div>

            <?php
        }
    }
}