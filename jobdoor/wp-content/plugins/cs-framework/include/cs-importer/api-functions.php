<?php

/*
 * Reload all the information for user
 * @Based on Purchase code
 */

function reload_user_data() {
    $envato_purchase_code_verification = get_option('item_purchase_code_verification');
    $purchase_code = isset($envato_purchase_code_verification['item_puchase_code']) ? $envato_purchase_code_verification['item_puchase_code'] : '';
    $theme_obj = wp_get_theme();

    $reload_data = array(
        'theme_puchase_code' => $purchase_code,
        'theme_name' => $theme_obj->get('Name'),
        'theme_id' => $envato_purchase_code_verification['item_id'],
        'user_email' => isset($envato_purchase_code_verification['envato_email_address']) ? $envato_purchase_code_verification['envato_email_address'] : '',
        'theme_version' => $theme_obj->get('Version'),
        'site_url' => site_url(),
    );
    echo json_encode($reload_data);
    wp_die();
}

add_action('wp_ajax_reaload_user_data', 'reload_user_data');
add_action('wp_ajax_nopriv_reaload_user_data', 'reload_user_data');

function cs_cron_schedules($schedules) {
    if ( ! isset($schedules["10days"]) ) {
        $schedules["10days"] = array(
            //'interval' => 1 * 60,
            'interval' => 864000,
            'display' => __('Once every 10 Days') );
    }
    return $schedules;
}

add_filter('cron_schedules', 'cs_cron_schedules');

function check_theme_is_active() {
    // Use wp_next_scheduled to check if the event is already scheduled.
    $timestamp = wp_next_scheduled('check_theme_is_active');

    // If $timestamp == false schedule daily alerts since it hasn't been done previously.
    if ( $timestamp == false ) {
        // Schedule the event for right now, then to repeat daily using the hook 'create_daily_properties_check'.
        wp_schedule_event(time(), '10days', 'check_theme_is_active_action');
    }
}

/*
 * Addint theme information into stats
 */

if ( ! function_exists('add_to_active_themes_callback') ) {

    function add_to_active_themes_callback() {
        $remote_api_url = REMOTE_API_URL;
        $envato_purchase_code_verification = get_option('item_purchase_code_verification');
        $selected_demo = isset($_POST['selected_demo']) ? $_POST['selected_demo'] : '';
        $envato_purchase_code_verification['selected_demo'] = $selected_demo;
        $supported_until = isset($envato_purchase_code_verification['supported_until']) ? $envato_purchase_code_verification['supported_until'] : '';
        $supported_year = strtotime($supported_until);
        $supported_year = date("Y", $supported_year);
        if ( $supported_year == '1970' ) {
            $verify_post_data = array(
                'action' => 'verify_purchase_code',
                'item_purchase_code' => $envato_purchase_code_verification['item_puchase_code'],
                'site_url' => site_url(),
                'item_id' => $envato_purchase_code_verification['item_id']
            );
            $item_data = wp_remote_post($remote_api_url, array( 'body' => $verify_post_data ));
            $supported_until = date("Y-m-d H:i:s", strtotime(json_decode($item_data['body'])->supported_until));
        }
        update_option('item_purchase_code_verification', $envato_purchase_code_verification);
        $theme_obj = wp_get_theme();
        $demo_data = array(
            'theme_puchase_code' => $envato_purchase_code_verification['item_puchase_code'],
            'theme_name' => $theme_obj->get('Name'),
            'theme_id' => $envato_purchase_code_verification['item_id'],
            'user_email' => isset($envato_purchase_code_verification['envato_email_address']) ? $envato_purchase_code_verification['envato_email_address'] : '',
            'theme_demo' => $selected_demo,
            'theme_version' => $theme_obj->get('Version'),
            'site_url' => site_url(),
            'supported_until' => $supported_until,
            'action' => 'add_to_active_themes',
        );
        $url = $remote_api_url;
        $response = wp_remote_post($url, array( 'body' => $demo_data ));
        check_theme_is_active();
        wp_die();
    }

    add_action('wp_ajax_add_to_active_themes', 'add_to_active_themes_callback');
}

function check_theme_is_active_callback() {
    $remote_api_url = REMOTE_API_URL;
    $envato_purchase_code_verification = get_option('item_purchase_code_verification');
    $purchase_code = isset($envato_purchase_code_verification['item_puchase_code']) ? $envato_purchase_code_verification['item_puchase_code'] : '';
    $theme_obj = wp_get_theme();
    $demo_data = array(
        'theme_puchase_code' => $purchase_code,
        'theme_name' => $theme_obj->get('Name'),
        'theme_version' => $theme_obj->get('Version'),
        'action' => 'check_active_theme',
    );
    $url = $remote_api_url;
    $response = wp_remote_post($url, array( 'body' => $demo_data ));
    wp_die();
}

add_action('check_theme_is_active_action', 'check_theme_is_active_callback');


/*
 * Releasing Purchase Code
 */

if ( ! function_exists('release_purchase_code_callback') ) {

    function release_purchase_code_callback() {
        $remote_api_url = REMOTE_API_URL;
        $envato_purchase_code_verification = get_option('item_purchase_code_verification');
        $purchase_code = isset($envato_purchase_code_verification['item_puchase_code']) ? $envato_purchase_code_verification['item_puchase_code'] : '';
        $update_data = array(
            'theme_puchase_code' => $purchase_code,
            'site_url' => site_url(),
            'action' => 'realse_purchase_code',
        );
        $url = $remote_api_url;
        $response = wp_remote_post($url, array( 'body' => $update_data ));
        delete_option( 'item_purchase_code_verification' );
        echo json_encode($response);
        wp_die();
    }

    add_action('wp_ajax_release_purchase_code', 'release_purchase_code_callback');
}