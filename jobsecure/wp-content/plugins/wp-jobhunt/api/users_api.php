<?php
do_action('jobcareer_cors');
//header('Access-Control-Allow-Origin: *');  // no cabe�alho
if (!class_exists('Users_Api')) {

    class Users_Api extends WP_REST_Controller {

        /**
         * Register the routes for the objects of the controller.
         */
        function __construct() {
            add_action('rest_api_init', array($this, 'register_routes_callback'));
        }

        public function register_routes_callback() {
            $version = '1';
            $namespace = 'api/v' . $version;
            $base = '';
            $routes = array(
                array(
                    'request' => 'registration_submit',
                    'methods' => 'POST',
                    'callback' => 'registration_submit_callback',
                    'args' => array(
                        'email' => isset($_POST['email']) ? ($_POST['email']) : (''),
                        'password' => isset($_POST['password']) ? ($_POST['password']) : (''),
                        'first_name' => isset($_POST['first_name']) ? ($_POST['first_name']) : (''),
                        'last_name' => isset($_POST['last_name']) ? ($_POST['last_name']) : (''),
                    )
                ),
                array(
                    'request' => 'login_submit',
                    'methods' => 'POST',
                    'callback' => 'login_submit_callback',
                    'args' => array('username' => isset($_POST['username']) ? ($_POST['username']) : (''), 'password' => isset($_POST['password']) ? ($_POST['password']) : (''))
                ),
                array(
                    'request' => 'login_form',
                    'methods' => 'GET',
                    'callback' => 'login_form_callback'
                ),
                array(
                    'request' => 'forgot_password',
                    'methods' => 'POST',
                    'callback' => 'forgot_password_callback'
                ),
                
                array(
                    'request' => 'update_password',
                    'methods' => 'POST',
                    'callback' => 'update_password_callback',
                    'args' => array(
                        'email' => isset($_POST['email']) ? ($_POST['email']) : (''),
                        'old_password' => isset($_POST['old_password']) ? ($_POST['old_password']) : (''),
                        'new_password' => isset($_POST['new_password']) ? ($_POST['new_password']) : (''),
                        'confirm_password' => isset($_POST['confirm_password']) ? ($_POST['confirm_password']) : ('')
                    )
                ),
                array(
                    'request' => 'account_settings_save',
                    'methods' => 'POST',
                    'callback' => 'account_settings_save_callback',
                    'args' => array(
                        'user_id' => isset($_POST['user_id']) ? ($_POST['user_id']) : (''),
                        'first_name' => isset($_POST['first_name']) ? ($_POST['first_name']) : (''),
                        'last_name' => isset($_POST['last_name']) ? ($_POST['last_name']) : (''),
                        'email_address' => isset($_POST['email_address']) ? ($_POST['email_address']) : (''),
                        'phone_number' => isset($_POST['phone_number']) ? ($_POST['phone_number']) : (''),
                        'profile_privacy' => isset($_POST['profile_privacy']) ? ($_POST['profile_privacy']) : ('')
                    )
                ),
                array(
                    'request' => 'account_settings_fetching',
                    'methods' => 'GET',
                    'callback' => 'account_settings_fetching_callback'
                ),
                array(
                    'request' => 'user_settings',
                    'methods' => 'GET',
                    'callback' => 'user_settings_callback'
                ),
                array(
                    'request' => 'user_profile_data',
                    'methods' => 'GET',
                    'callback' => 'user_profile_data_callback'
                ),
                array(
                    'request' => 'update_cv_search',
                    'methods' => 'POST',
                    'callback' => 'update_cv_search_callback',
                ),
                array(
                    'request' => 'cv_builder',
                    'methods' => 'GET',
                    'callback' => 'cv_builder_callback',
                ),
                array(
                    'request' => 'cv_builder_update',
                    'methods' => 'POST',
                    'callback' => 'cv_builder_update_callback',
                ),
                array(
                    'request' => 'user_location',
                    'methods' => 'GET',
                    'callback' => 'user_location_callback'
                ),
                array(
                    'request' => 'user_location_update',
                    'methods' => 'POST',
                    'callback' => 'user_location_update_callback'
                ),
                array(
                    'request' => 'get_cities',
                    'methods' => 'GET',
                    'callback' => 'get_cities_callback'
                ),
                array(
                    'request' => 'user_image_update',
                    'methods' => 'POST',
                    'callback' => 'user_image_update_callback'
                ),
                array(
                    'request' => 'user_profile_data_update',
                    'methods' => 'POST',
                    'callback' => 'user_profile_data_update_callback',
                    'args' => array(
                        'user_id' => isset($_POST['user_id']) ? ($_POST['user_id']) : (''),
                        'city' => isset($_POST['city']) ? ($_POST['city']) : (''),
                        'state' => isset($_POST['state']) ? ($_POST['state']) : (''),
                        'country' => isset($_POST['country']) ? ($_POST['country']) : (''),
                        'specialisms' => isset($_POST['specialisms']) ? ($_POST['specialisms']) : (''),
                        'job_title' => isset($_POST['job_title']) ? ($_POST['job_title']) : ('')
                    )
                ),
            );
            foreach ($routes as $val) {
                $uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
                $uriSegments = array_pop($uriSegments);
                if ($uriSegments == $val['request']) {
                    register_rest_route($namespace, $base . '/' . $val['request'], array(
                        'methods' => $val['methods'],
                        'callback' => array($this, $val['callback']),
                        isset($val['args']) ? ($val['args']) : (''),
                    ));
                }
            }
        }
        
        
        /*
         * Forgot Password
         */
        
        public function forgot_password_callback(){
            global $wpdb, $wp_hasher;
            $data   =   '';

            $user_login = isset($_POST['user_input']) ? $_POST['user_input'] : '';
            $status = true;
            $home_url = home_url();

            $user_login = sanitize_text_field($user_login);
            if (empty($user_login)) {
                $data = esc_html__('Please enter a username or email address.', 'jobhunt');
                $status = false;
            } else if (strpos($user_login, '@')) {
                $user_data = get_user_by('email', trim($user_login));
                if (empty($user_data)) {
                    $data = esc_html__('There is no user registered with that email address.', 'jobhunt');
                    $status = false;
                }
            } else {
                $login = trim($user_login);
                $user_data = get_user_by('login', $login);
                if (empty($user_data)) {
                    $data = esc_html__('There is no user registered with that username.', 'jobhunt');
                    $status = false;
                }
            }

            do_action('lostpassword_post');

            // redefining user_login ensures we return the right case in the email
            $user_login = $user_data->user_login;
            $user_email = $user_data->user_email;
            
            do_action('retreive_password', $user_login);  // Misspelled and deprecated
            do_action('retrieve_password', $user_login);
            $allow = apply_filters('allow_password_reset', true, $user_data->ID);
            if (!$allow) {
                $data = esc_html__('Sorry! password reset is not allowed.', 'jobhunt');
                $status = false;
            } else if (is_wp_error($allow)) {
                $data = esc_html__('Sorry! there is a wp error.', 'jobhunt');
                $status = false;
            }
            
            if( $status == true){
                $key = wp_generate_password(20, false);
                do_action('retrieve_password_key', $user_login, $key);

                if (empty($wp_hasher)) {
                    require_once ABSPATH . 'wp-includes/class-phpass.php';
                    $wp_hasher = new PasswordHash(8, true);
                }
                $hashed = $wp_hasher->HashPassword($key);
                $wpdb->update($wpdb->users, array('user_activation_key' => time() . ":" . $hashed), array('user_login' => $user_login));

                $user_data = get_user_by('login', $user_login);
                update_user_meta($user_data->ID, 'reset_pass_key', $key);

                $reset_link = $home_url . "?reset_pass=true&key=$key&login=" . rawurlencode($user_login) . '&popup=true';
                if (is_multisite()) {
                    $blogname = $GLOBALS['current_site']->site_name;
                } else {
                    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
                }
                $title = sprintf(esc_html__('%s Password Reset', 'jobhunt'), $blogname);
                $args['user_login'] = $user_login;
                $args['user_email'] = $user_email;
                $args['title'] = $title;
                $args['reset_link'] = '<a href="' . $reset_link . '">' . $reset_link . '</a>';
                $args['home_url'] = $home_url;

                do_action('jobhunt_confirm_reset_password_email', $args);

                $data = esc_html__('Link for password reset has been emailed to you. Please check your email.', 'jobhunt');
                $status = true;
            }
            $info = array('status' => $status,
                'data' => $data);
            return new WP_REST_Response($info, 200);
        }
        
        /*
         * User Registration Call
         */

        public function registration_submit_callback($req) {
            $user_email = isset( $_POST['email'] )? $_POST['email'] : '';
            $first_name = isset( $_POST['first_name'] )? $_POST['first_name'] : '';
            $last_name = isset( $_POST['last_name'] )? $_POST['last_name'] : '';
            $password = isset( $_POST['password'] )? $_POST['password'] : '';
            $parts = explode("@", $user_email); // getting username from email
            $username = isset( $parts[0] )? $parts[0] : ''; //username
            $errors = '';
            if (empty($username)) {
                $errors = esc_html__("User name should not be empty.", "jobhunt");
            } elseif (!preg_match('/^[a-zA-Z0-9_]{5,}$/', $username)) { // for english chars + numbers only
                $errors = esc_html__("Please enter a valid username. You can only enter alphanumeric value and only ( _ ) longer than or equals 5 chars", "jobhunt");
            }
            if (empty($user_email)) {
                $errors = esc_html__("Email should not be empty.", "jobhunt");
            }
            if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
                $errors = esc_html__("Please enter a valid email.", "jobhunt");
            }
            if (empty($password)) {
                $errors = esc_html__("Password should not be empty.", "jobhunt");
            }
            if (isset($errors) && $errors != '') {
                $info = array('status' => false,
                    'data' => $errors);
                return new WP_REST_Response($info, 200);
            } else {
                global $wpdb;
                $status = wp_create_user($username, $password, $user_email);
                $signup_user_role = 'cs_candidate';
                wp_update_user(array('ID' => esc_sql($status), 'first_name' => esc_sql($first_name), 'last_name' => esc_sql($last_name), 'role' => esc_sql($signup_user_role), 'user_status' => 1));
                $wpdb->update(
                        $wpdb->prefix . 'users', array('user_status' => 1), array('ID' => esc_sql($status))
                );
                update_user_meta($status, 'show_admin_bar_front', false);
                $reg_user = get_user_by('ID', $status);
               
                $user_shortlisted_jobs = get_user_meta($reg_user->data->ID, 'cs-user-jobs-wishlist', true);
                $shortlisted_jobs_array = array();
                if( !empty( $user_shortlisted_jobs ) ){
                    foreach( $user_shortlisted_jobs as $shortlisted_jobs){
                        $shortlisted_jobs_array[]   = isset( $shortlisted_jobs['post_id'] )? $shortlisted_jobs['post_id'] : 0;
                    }
                }
                $info = array('status' => true,
                    'userInfo' => $reg_user,
                    'user_shortlisted_jobs' => $shortlisted_jobs_array);
                return new WP_REST_Response($info, 200);
            }
        }
        /*
         * User login Call
         */

        public function login_submit_callback($req) {
            $requested_params = $req->get_params();
            $login_details = wp_authenticate($requested_params['username'], $requested_params['password']);
            if (isset($login_details->data)) {
                $userinfo = array(
                    'userId' => $login_details->data->ID,
                    'email' => $login_details->data->user_email,
                );
                update_user_meta($login_details->data->ID, 'user_app_login', 'yes');
                $user_shortlisted_jobs = get_user_meta($login_details->data->ID, 'cs-user-jobs-wishlist', true);
                $shortlisted_jobs_array = array();
                if( !empty( $user_shortlisted_jobs ) ){
                    foreach( $user_shortlisted_jobs as $shortlisted_jobs){
                        $shortlisted_jobs_array[]   = isset( $shortlisted_jobs['post_id'] )? $shortlisted_jobs['post_id'] : 0;
                    }
                }
                $info = array('status' => true,
                    'userInfo' => $login_details,
                    'user_shortlisted_jobs' => $shortlisted_jobs_array);
                return new WP_REST_Response($info, 200);
            } else {
                $errors = '';
                if (isset($login_details->errors['empty_username'][0])) {
                    $errors = "The username field is empty";
                }
                if (isset($login_details->errors['empty_password'][0])) {
                    $errors = "The password field is empty";
                }
                if (isset($login_details->errors['invalid_username'][0])) {
                    $errors = "Invalid username";
                }
                if (isset($login_details->errors['incorrect_password'][0])) {
                    $errors = "The password you entered for the email address is incorrect";
                }
                $info = array('status' => false,
                    'data' => $errors);
                return new WP_REST_Response($info, 200);
            }
        }
        /*
         * Login Form Get(method)
         */

        public function login_form_callback() {

            $fields[] = array(
                "field_name" => "username",
                "field_type" => "text",
                "data_type" => "text",
                "order" => 1,
                "required" => true,
                "multiple" => false,
                "value" => "User Name",
                "icon" => "",
                "data" => [array("name" => "username", "key" => "", "value" => "")]
            );
            $fields[] = array(
                "field_name" => "password",
                "field_type" => "password",
                "data_type" => "text",
                "order" => 2,
                "required" => true,
                "multiple" => false,
                "value" => "Password",
                "icon" => "",
                "data" => [array("name" => "password", "key" => "", "value" => "")]
            );
            $fields[] = array(
                "field_name" => "submit",
                "field_type" => "button",
                "data_type" => "submit",
                "API_URL" => "login_submit",
                "order" => 3,
                "required" => false,
                "multiple" => false,
                "value" => "Login",
                "icon" => "",
                "data" => []
            );
            $form[] = array(
                'section_id' => 1,
                'section_name' => "",
                'fields' => $fields);
            $info = array('status' => true,
                'title' => "Login",
                'page_type' => "form",
                'data' => array("form" => $form));
            return new WP_REST_Response($info, 200);
        }
        /*
         * User login Call
         */

        public function update_password_callback($req) {
            $requested_params = $req->get_params();

            $email = $requested_params['email'];
            $old_password = $requested_params['old_password'];
            $new_password = $requested_params['new_password'];
            $confirm_password = $requested_params['confirm_password'];

            $user_data = get_user_by('email', $email);
            if ($user_data && wp_check_password($old_password, $user_data->data->user_pass, $user_data->ID)) {
                if ($new_password == $confirm_password) {
                    wp_set_password($new_password, $user_data->ID);
                    $status = true;
                    $info_msg = array(
                        'info_msg' => 'Password Update Successfully',
                        'userId' => $user_data->ID,
                        'new_password' => $new_password,
                    );
                } else {
                    $status = false;
                    $info_msg = "New Passowrd and Confirm Password does not match";
                }
            } else {
                $status = false;
                $info_msg = "Old Password is Incorrect";
            }
            $info = array('status' => $status,
                'data' => $info_msg);
            return new WP_REST_Response($info, 200);
        }
        /*
         * User Account Settings Call
         */

        public function account_settings_save_callback($req) {
            //$requested_params = $req->get_params();
            $user_id = isset( $_POST['user_id'] )? $_POST['user_id'] : '';
            $email = isset( $_POST['email_address'] )? $_POST['email_address'] : '';
            $password = isset( $_POST['password'] )? $_POST['password'] : '';
            $confirm_password = isset( $_POST['confirm_password'] )? $_POST['confirm_password'] : '';
            $user_id = wp_update_user( array( 'ID' => $user_id, 'user_email' => $email ) );
            if (isset($user_id)) {
                if( $password!= '' && $confirm_password != ''){
                    if( $password == $confirm_password ){
                        wp_set_password( $password, $user_id );
                    }
                }
                $status = true;
                $info_msg = array(
                    'info_msg' => 'Account Settings Update Successfully',
                    'userId' => $user_id
                );
            } else {
                $status = false;
                $info_msg = "Error";
            }
            $info = array('status' => $status,
                'data' => $info_msg);
            return new WP_REST_Response($info, 200);
        }
        /*
         * User Account Settings Fetching Call
         */

        public function account_settings_fetching_callback() {

            $user_id = $_GET['user_id'];

            if (isset($user_id)) {
                $user_data = get_userdata($user_id);
                
                $user_email = $user_data->data->user_email;

                $status = true;
                $user_data = array(
                    'info_msg' => 'Data Received Successfully',
                    'user_email' => $user_email,
                );
            } else {
                $status = false;
                $user_data = "Error";
            }
            $info = array('status' => $status,
                'data' => $user_data);
            return new WP_REST_Response($info, 200);
        }
        
        /*
         * Fetching user Profile Data
         */
        
        public function user_profile_data_callback(){
            $user_id = $_GET['user_id'];

            $cs_spec_args = array(
                'orderby' => 'name',
                'order' => 'ASC',
                'fields' => 'all',
                'slug' => '',
                'hide_empty' => false,
            );
            $specialisms_list = get_terms('specialisms', $cs_spec_args);
            if (isset($user_id)) {
                $userObj = get_userdata($user_id);
                $user_data = array();
                $user_data['full_name'] = $userObj->data->display_name;
                $user_data['job_title'] = get_user_meta( $user_id, 'cs_job_title', true);
                $user_data['minimum_salary'] = get_user_meta( $user_id, 'cs_minimum_salary', true);
                $user_data['specialisms'] = get_user_meta( $user_id, 'cs_specialisms', true);
                $user_data['facebook'] = get_user_meta( $user_id, 'cs_facebook', true);
                $user_data['twitter'] = get_user_meta( $user_id, 'cs_twitter', true);
                $user_data['inkedin'] = get_user_meta( $user_id, 'cs_linkedin', true);
                $user_data['google_plus'] = get_user_meta( $user_id, 'cs_google_plus', true);
                $user_data['phone_number'] = get_user_meta( $user_id, 'cs_phone_number', true);
                $user_data['specialisms_list'] = $specialisms_list;

                $status = true;
            } else {
                $status = false;
                $user_data = "Error";
            }
            $info = array('status' => $status,
                'data' => $user_data);
            return new WP_REST_Response($info, 200);
        }
        
        /*
         * User Settins
         */
        
        public function user_settings_callback(){
            $user_id = isset( $_GET['user_id'] )? $_GET['user_id'] : 0;
            
            $userObj = get_userdata($user_id);
            $user_img   = get_user_meta($user_id, 'user_img', true);
            $user_img   = cs_get_image_url($user_img);
            
            $cv_allow_search    = get_user_meta($user_id, 'cs_allow_search', true);
            
            $user_data  = array();
            $user_data['user_img']  = $user_img;
            $user_data['allow_search']  = ( $cv_allow_search == 'yes')? true : false;
            $user_data['user_full_name'] = $userObj->data->display_name;
            $user_data['user_email'] = $userObj->data->user_email;
            
            $info = array('status' => $status,
                'data' => $user_data);
            return new WP_REST_Response($info, 200);
            
        }
        
        /*
         * User Edit Profile Call
         */
        
        public function user_profile_data_update_callback() {
            $user_id = isset( $_POST['user_id'] )? $_POST['user_id'] : 0;
            $full_name = isset( $_POST['full_name'] )? $_POST['full_name'] : '';
            $job_title = isset( $_POST['job_title'] )? $_POST['job_title'] : '';
            $minimum_salary = isset( $_POST['minimum_salary'] )? $_POST['minimum_salary'] : 0;
            $phone_number = isset( $_POST['phone_number'] )? $_POST['phone_number'] : '';
            $cs_specialisms = isset( $_POST['user_specialisms'] )? $_POST['user_specialisms'] : array();
            if (isset($user_id)) {
                $userid = wp_update_user( array( 'ID' => $user_id, 'display_name' => $full_name ) );
                if (!empty($cs_specialisms)) {
                    update_user_meta($user_id, 'cs_specialisms', $cs_specialisms);
                }
                update_user_meta($user_id, 'cs_job_title', $job_title);
                update_user_meta($user_id, 'cs_minimum_salary', $minimum_salary);
                update_user_meta($user_id, 'cs_phone_number', $phone_number);
                $status = true;
                $info_msg = array(
                    'info_msg' => 'Profile Update Successfully',
                    'userId' => $user_id
                );
            } else {
                $status = false;
                $info_msg = "Error";
            }
            $info = array('status' => $status,
                'data' => $info_msg);
            return new WP_REST_Response($info, 200);
        }
        
        /*
         * Fetching user Location Data
         */
        
        public function user_location_callback(){
            $user_id = $_GET['user_id'];
            
            $country_args = array(
                'orderby' => 'name',
                'order' => 'ASC',
                'fields' => 'all',
                'slug' => '',
                'hide_empty' => false,
                'parent' => 0,
            );
            $cs_location_countries = get_terms('cs_locations', $country_args);
            
            $current_country = get_term_by('slug', get_user_meta( $user_id, 'cs_post_loc_country', true), 'cs_locations');
            $current_country = $current_country->term_id;
            
            $cities_args = array(
                'orderby' => 'name',
                'order' => 'ASC',
                'fields' => 'all',
                'slug' => '',
                'hide_empty' => false,
                'parent' => $current_country,
            );
            $cities_list = get_terms('cs_locations', $cities_args);

            if (isset($user_id)) {
                $user_data = array();
                $user_data['user_country'] = get_user_meta( $user_id, 'cs_post_loc_country', true);
                $user_data['user_city'] = get_user_meta( $user_id, 'cs_post_loc_city', true);
                $user_data['user_address'] = get_user_meta( $user_id, 'cs_post_comp_address', true);

                $status = true;
            } else {
                $status = false;
                $user_data = "Error";
            }
            $user_data['countries_list'] = $cs_location_countries;
            $user_data['cities_list'] = $cities_list;
            $info = array('status' => $status,
                'data' => $user_data);
            return new WP_REST_Response($info, 200);
        }
        
        
        /*
         * Get All cities based on Country Slug
         */
        
        public function get_cities_callback(){
            $country_slug = $_GET['country_slug'];
            $current_country = get_term_by('slug', $country_slug, 'cs_locations');
            $current_country = $current_country->term_id;
            
            $cities_args = array(
                'orderby' => 'name',
                'order' => 'ASC',
                'fields' => 'all',
                'slug' => '',
                'hide_empty' => false,
                'parent' => $current_country,
            );
            $cities_list = get_terms('cs_locations', $cities_args);
            $user_data = array();
            $user_data['cities_list'] = $cities_list;
            $info = array('status' => $status,
                'data' => $user_data);
            return new WP_REST_Response($info, 200);
        }
        
        
        /*
         * User Location Update
         */
        
        public function user_location_update_callback() {
            $user_id = isset( $_POST['user_id'] )? $_POST['user_id'] : 0;
            $user_country = isset( $_POST['user_country'] )? $_POST['user_country'] : '';
            $user_city = isset( $_POST['user_city'] )? $_POST['user_city'] : '';
            $user_address = isset( $_POST['user_address'] )? $_POST['user_address'] : 0;
            if (isset($user_id)) {
                update_user_meta($user_id, 'cs_post_loc_country', $user_country);
                update_user_meta($user_id, 'cs_post_loc_city', $user_city);
                update_user_meta($user_id, 'cs_post_comp_address', $user_address);
                $status = true;
                $info_msg = array(
                    'info_msg' => 'Locations Updated Successfully',
                    'userId' => $user_id
                );
            } else {
                $status = false;
                $info_msg = "Error";
            }
            $info = array('status' => $status,
                'data' => $info_msg);
            return new WP_REST_Response($info, 200);
        }
        
        /*
         * Updte CV Search For User
         */
        public function update_cv_search_callback(){
            $user_id = isset( $_POST['user_id'] )? $_POST['user_id'] : 0;
            $cv_allow_search = isset( $_POST['cv_allow_search'] )? $_POST['cv_allow_search'] : '';
            if (isset($user_id)) {
                update_user_meta($user_id, 'cs_allow_search', $cv_allow_search);
                $status = true;
                $info_msg = array(
                    'info_msg' => 'Updated Successfully',
                    'userId' => $user_id
                );
            } else {
                $status = false;
                $info_msg = "Error";
            }
            $info = array('status' => $status,
                'data' => $info_msg);
            return new WP_REST_Response($info, 200);
        }
        
        /*
         * CV Builder API Data
         */
        
        public function cv_builder_callback(){
            $user_id = isset( $_GET['user_id'] )? $_GET['user_id'] : 0;
            $cs_candidate_cv = get_user_meta($user_id, "cs_candidate_cv", true);
            $cs_cover_letter = get_user_meta($user_id, "cs_cover_letter", true);
            $candidate_cv   = array();
            $candidate_cv['link']   = '';
            $candidate_cv['cover_letter']   = $cs_cover_letter;
            
            if (cs_check_coverletter_exist($cs_candidate_cv)) {
                $parts = preg_split('~_(?=[^_]*$)~', basename($cs_candidate_cv));
                $candidate_cv['label']  = esc_html($parts[0]); // outputs "one_two_three"
                $candidate_cv['link']   = $cs_candidate_cv;
            }
            $is_cv_exists = empty( $candidate_cv['link'] )? false : true;
            $user_data = array();
            $user_data['cities_list'] = $cities_list;
            $candidate_cv['is_cv_exists']   = $is_cv_exists;
            $info = array('status' => true,
                'data' => $candidate_cv);
            return new WP_REST_Response($info, 200);
        }
        
        
        /*
         * CV Building
         * Update CV File
         */
        public function cv_builder_update_callback(){
            
            $user_id = isset( $_POST['user_id'] )? $_POST['user_id'] : 0;
            $cover_letter = isset( $_POST['cover_letter'] )? $_POST['cover_letter'] : '';
            $user_cv = upload_user_cv();
            if (isset($user_cv['error']) && $user_cv['error'] == 1) {
                $status = false;
                $info_msg = $user_cv['message'];
            } else {
                $status = true;
                if (isset($_FILES['user_cv'])){
                    update_user_meta($user_id, 'cs_candidate_cv', $user_cv);
                }
                update_user_meta($user_id, 'cs_cover_letter', $cover_letter);
                $info_msg = array(
                    'info_msg' => 'CV Uploaded Successfully',
                    'userId' => $user_id
                );
            }
            $info = array('status' => $status,
                'data' => $info_msg);
            return new WP_REST_Response($info, 200);
        }
        
        /*
         * Update User Image
         */
        
        public function user_image_update_callback(){
            $user_id = isset( $_POST['user_id'] )? $_POST['user_id'] : 0;
            $cs_media_image = upload_user_image('user_image');
            $status = false;
            if( $cs_media_image != ''){
                update_user_meta($user_id, 'user_img', $cs_media_image);
                $status = true;
                $info_msg = array(
                    'info_msg' => 'Image Uploaded Successfully',
                    'userId' => $user_id,
                    'user_img' => cs_get_image_url($cs_media_image)
                );
            }
            $info = array('status' => $status,
                'data' => $info_msg);
            return new WP_REST_Response($info, 200);
        }

    }

}
$Users_Api = new Users_Api();

/**
 * Upload User CV FILE
 */
if (!function_exists('upload_user_cv')) {
    function upload_user_cv() {
        $resized_url = '';
        if (isset($_FILES['user_cv']) && $_FILES['user_cv']['name'] != '') {
            $json = array();
            require_once ABSPATH . 'wp-admin/includes/file.php';
            $current_user_id = get_current_user_id();
            $cs_allowed_file_types = array(
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'rtf' => 'application/rtf',
                'txt' => 'text/plain',
            );
            $status = wp_handle_upload($_FILES['user_cv'], array('test_form' => false, 'mimes' => $cs_allowed_file_types, 'unique_filename_callback' => 'my_cust_filename'));
            if (isset($status) && !isset($status['error'])) {
                $uploads = wp_upload_dir();
                $resized_url = $status['url'];
            } else {
                if (isset($status['error'])) {
                    $resized_url = array('error' => 1, 'message' => $status['error']);
                } else {
                    $resized_url = '';
                }
            }
        }
        return $resized_url;
    }

}



/**
 * Start Function  Upload User image(Avatar)
 */
if (!function_exists('upload_user_image')) {

    function upload_user_image($Fieldname = 'media_upload') {
        global $plugin_user_images_directory;
        $img_resized_name = '';
        $attach_id = 0;
        // Register our new path for user images.
        add_filter('upload_dir', 'cs_user_images_custom_directory');
        if (isset($_FILES[$Fieldname]) && $_FILES[$Fieldname] != '') {
            $json = array();
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            $cs_allowed_image_types = array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            );
            $status = wp_handle_upload($_FILES[$Fieldname], array('test_form' => false, 'mimes' => $cs_allowed_image_types));

            if (empty($status['error'])) {
                $image = wp_get_image_editor($status['file']);
                if (!is_wp_error($image)) {
                    $sizes_array = array(
                        array('width' => 270, 'height' => 203, 'crop' => true),
                        array('width' => 236, 'height' => 168, 'crop' => true),
                        array('width' => 200, 'height' => 200, 'crop' => true),
                        array('width' => 180, 'height' => 135, 'crop' => true),
                        array('width' => 150, 'height' => 113, 'crop' => true),
                    );
                    $resize = $image->multi_resize($sizes_array, true);
                }
                if (is_wp_error($image)) {
                    echo '<span class="error-msg">' . $image->get_error_message() . '</span>';
                } else {
                    $wp_upload_dir = wp_upload_dir();
                    $img_resized_name = isset($resize[0]['file']) ? basename($resize[0]['file']) : '';
                    $filename = '/' . $plugin_user_images_directory . '/' . $img_resized_name;
                    $filetype = wp_check_filetype(basename($filename), null);
                    if ($filename != '') {
                        // Prepare an array of post data for the attachment.
                        $attachment = array(
                            'guid' => $status['url'],
                            'post_mime_type' => $filetype['type'],
                            'post_title' => preg_replace('/\.[^.]+$/', '', ($filename)),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        // Insert the attachment.
                        $attach_id = wp_insert_attachment($attachment, $filename);

                        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                        require_once( ABSPATH . 'wp-admin/includes/image.php' );
                        // Generate the metadata for the attachment, and update the database record.
                        $attach_data = wp_generate_attachment_metadata($attach_id, $status['file']);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                    }
                }
            } else {
                $img_resized_name = '';
            }
        }
        // Set everything back to normal.
        remove_filter('upload_dir', 'cs_user_images_custom_directory');
        return $attach_id;
    }

}