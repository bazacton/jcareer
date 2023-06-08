<?php
/**
 * File Type: Google Login Callback
 */
if (!class_exists('jobhunt_google_login')) {

    class jobhunt_google_login {

        public function __construct() {
            add_filter('wp_ajax_google_api_login', array($this, 'wp_ajax_google_api_login_callback'));
            add_action('wp_ajax_nopriv_google_api_login', array($this, 'wp_ajax_google_api_login_callback'));
        }

        /*
         * Callback Function for Google Login
         */

        public function wp_ajax_google_api_login_callback() {
            global $wp, $wpdb, $cs_google_settings, $cs_plugin_options;

            if( !is_user_logged_in() ){
            $role = 'cs_candidate';
            $role = apply_filters('jobhunt_register_user_role_frontend', $role);

            $email = isset( $_POST['email_address'] )? $_POST['email_address'] : '';
            $image_url = isset( $_POST['image_url'] )? $_POST['image_url'] : '';
            if (!is_user_logged_in()) {

                $ID = email_exists($email);

                if ($ID == NULL) { // Register
                    if ($ID == false) { // Real register
                        $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
                        if (!isset($cs_google_settings['google_user_prefix']))
                            $cs_google_settings['google_user_prefix'] = 'Google - ';
                        $sanitized_user_login = isset( $_POST['given_name'] )? $_POST['given_name'] : '';
                        if (!validate_username($sanitized_user_login)) {
                            $sanitized_user_login = isset( $_POST['id'] )? $_POST['id'] : '';
                        }
                        $defaul_user_name = $sanitized_user_login;
                        $i = 1;
                        while (username_exists($sanitized_user_login)) {
                            $sanitized_user_login = $defaul_user_name . $i;
                            $i++;
                        }
                        //$ID = wp_create_user($sanitized_user_login, $random_password, $email);
                        $userdata = array('user_login' => $sanitized_user_login, 'user_email' => $email, 'user_pass' => $random_password, 'role' => $role);
                        // Create a new user
                        $ID = wp_insert_user($userdata);

                        if (!is_wp_error($ID)) {

                            $reg_user = get_user_by('ID', $ID);
                            // Site owner email hook
                            do_action('jobhunt_new_user_notification_site_owner', $reg_user->data->user_login, $reg_user->data->user_email);

                            if ($role == 'cs_employer') {
                                // send candidate email template hook
                                do_action('jobhunt_employer_register', $reg_user, $random_password);
                            } else {
                                // send candidate email template hook
                                do_action('jobhunt_candidate_register', $reg_user, $random_password);
                            }

                            $new_user = new WP_User($ID);
                            $user_info = get_userdata($ID);
                            wp_update_user(array(
                                'ID' => $ID,
                                'display_name' => isset( $_POST['full_name'] )? $_POST['full_name'] : '',
                                'first_name' => isset( $_POST['given_name'] )? $_POST['given_name'] : '',
                                'last_name' => isset( $_POST['family_name'] )? $_POST['family_name'] : '',
                                'googleplus' => '',
                            ));
                            // update user meta
                            $new_user->set_role($role);
                            update_user_meta($ID, 'cs_user_last_activity_date', strtotime(current_time('d-m-Y H:i:s')));
                            update_user_meta($ID, 'cs_allow_search', 'yes');
                            // update_user_meta($ID, 'cs_user_status', 'active');

                            update_user_meta($ID, 'cs_google_default_password', $user_info->user_pass);
                            do_action('cs_google_user_registered', $ID, $_POST, $oauth2);
                            update_user_meta($ID, 'cs_user_registered', 'google');

                            if ($role == 'cs_employer') {
                                $review_option = 'cs_employer_review_option';
                            } else {
                                $review_option = 'cs_candidate_review_option';
                            }

                            if (isset($cs_plugin_options[$review_option]) && $cs_plugin_options[$review_option] != 'on') {

                                $wpdb->update(
                                        $wpdb->prefix . 'users', array('user_status' => 1), array('ID' => esc_sql($ID))
                                );
                                update_user_meta($ID, 'cs_user_status', 'active');
                            } else {
                                $wpdb->update(
                                        $wpdb->prefix . 'users', array('user_status' => 1), array('ID' => esc_sql($ID))
                                );
                                update_user_meta($ID, 'cs_user_status', 'inactive');
                            }
                        } else {
                            return;
                        }
                    }

                    if (isset($cs_google_settings['google_redirect_reg']) && $cs_google_settings['google_redirect_reg'] != '' && $cs_google_settings['google_redirect_reg'] != 'auto') {
                        set_transient(cs_google_uniqid() . '_google_r', $cs_google_settings['google_redirect_reg'], 3600);
                    }
                }
                if ($ID) {

                    $current_user = get_userdata($ID);
                    $user_roles = isset($current_user->roles) ? $current_user->roles : '';
                    if (($user_roles != '' && in_array($role, $user_roles))) {
                        $user_info = get_userdata($ID);
                        // update user meta
                        update_user_meta($ID, 'cs_user_last_activity_date', strtotime(current_time('d-m-Y H:i:s')));

                        update_user_meta($ID, 'cs_google_default_password', $user_info->user_pass);
                        do_action('cs_google_user_registered', $ID, $_POST, $oauth2);
                        update_user_meta($ID, 'cs_user_registered', 'google');
                        $secure_cookie = is_ssl();
                        $secure_cookie = apply_filters('secure_signon_cookie', $secure_cookie, array());
                        global $auth_secure_cookie; // XXX ugly hack to pass this to wp_authenticate_cookie

                        $auth_secure_cookie = $secure_cookie;
                        wp_set_auth_cookie($ID, true, $secure_cookie);

                        do_action('wp_login', $user_info->user_login, $user_info);
                        update_user_meta($ID, 'google_profile_picture', $image_url);
                    } else {
                        ?>
                        <script>
                            alert("<?php echo esc_html__('This Google profile is already linked with other account. Linking process failed!', 'jobhunt'); ?>");
                            window.opener.location.reload();
                            window.close();
                        </script>
                        <?php
                        $ID = Null;     // set null bcz this user exist in other Role
                    }
                }
            } else {

                $user_info = wp_get_current_user();
                set_transient($user_info->ID . '_cs_google_admin_notice', esc_html__('This Google profile is already linked with other account. Linking process failed!', 'jobhunt'), 3600);
            }
            
            echo 'Loggedin';
            }
            wp_die();
        }

    }

    new jobhunt_google_login();
}
