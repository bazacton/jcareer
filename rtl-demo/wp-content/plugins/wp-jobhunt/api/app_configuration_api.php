<?php
do_action('jobcareer_cors');
//header('Access-Control-Allow-Origin: *');  // no cabe�alho
if (!class_exists('App_Configuration')) {

    class App_Configuration extends WP_REST_Controller {

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
                    'request' => 'app_configuration',
                    'methods' => 'GET',
                    'callback' => 'app_configuration_callback'
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
         * Api Configration Call
         */

        public function app_configuration_callback() {
            global $cs_plugin_options;

            $mobile_app_theme_color = isset($cs_plugin_options['cs_mobile_app_theme_color']) ? $cs_plugin_options['cs_mobile_app_theme_color'] : '';
            $mobile_app_content_text_color = isset($cs_plugin_options['cs_mobile_app_content_text_color']) ? $cs_plugin_options['cs_mobile_app_content_text_color'] : '';
            $mobile_app_font_family = isset($cs_plugin_options['cs_app_font_family']) ? $cs_plugin_options['cs_app_font_family'] : '';
            $mobile_app_scoial_login = isset($cs_plugin_options['cs_app_scoial_login']) ? $cs_plugin_options['cs_app_scoial_login'] : 'off';
            $mobile_app_registration = isset($cs_plugin_options['cs_app_registration']) ? $cs_plugin_options['cs_app_registration'] : 'off';
            $mobile_app_disable_mode = isset($cs_plugin_options['cs_app_disable_mode']) ? $cs_plugin_options['cs_app_disable_mode'] : 'off';
            $mobile_app_logo = isset($cs_plugin_options['cs_app_logo']) ? $cs_plugin_options['cs_app_logo'] : '';
            $app_home_banner = isset($cs_plugin_options['cs_app_home_banner']) ? $cs_plugin_options['cs_app_home_banner'] : '';
            $app_configuration = array(
                "app_theme_color" => $mobile_app_theme_color,
                "app_text_color" => $mobile_app_content_text_color,
                "app_font_family" => $mobile_app_font_family,
                "app_social_login_url" => $mobile_app_scoial_login,
                "app_registration" => $mobile_app_registration,
                "app_diable_mode" => $mobile_app_disable_mode,
                "app_logo" => $mobile_app_logo,
                "app_logo_url" => $mobile_app_logo,
                "app_home_banner" => $app_home_banner,
                "app_strings" => $this->app_strings_callback(),
                "reload_data" => 'false',
            );
            $info = array('status' => true,
                'data' => $app_configuration);
            return new WP_REST_Response($info, 200);
        }
        
        
        /*
         * APP Strings
         */
        public function app_strings_callback() {
            $jc_strings = array();
            
            // Tabs
            $jc_strings['tabs']['find_jobs'] = esc_html__('Find Jobs', 'jobhunt');
            $jc_strings['tabs']['my_jobs'] = esc_html__('My Jobs', 'jobhunt');
            $jc_strings['tabs']['cv_builder'] = esc_html__('CV Builder', 'jobhunt');
            $jc_strings['tabs']['job_alerts'] = esc_html__('Job Alerts', 'jobhunt');
            $jc_strings['tabs']['settings'] = esc_html__('Settings', 'jobhunt');
            
            
            //Home Page
            $jc_strings['home']['title'] = esc_html__('Home', 'jobhunt');
            $jc_strings['home']['companies_title'] = esc_html__('Top Companies', 'jobhunt');
            $jc_strings['home']['jobs_title'] = esc_html__('Featured Jobs', 'jobhunt');
            $jc_strings['home']['blog_title'] = esc_html__('Latest News', 'jobhunt');
            $jc_strings['home']['categories_title'] = esc_html__('Job Categories', 'jobhunt');
            $jc_strings['home']['all_text'] = esc_html__('See All', 'jobhunt');
            
            
            // Listings Page
            $jc_strings['jobs_listings']['title'] = esc_html__('Find Jobs', 'jobhunt');
            $jc_strings['jobs_listings']['filters'] = esc_html__('Filter', 'jobhunt');
            $jc_strings['jobs_listings']['keyw_title'] = esc_html__('Keywords Search', 'jobhunt');
            $jc_strings['jobs_listings']['keyw_placeholder'] = esc_html__('Search Keywords e.g web design', 'jobhunt');
            $jc_strings['jobs_listings']['loc_title'] = esc_html__('Location', 'jobhunt');
            $jc_strings['jobs_listings']['loc_placeholder'] = esc_html__('Search Country', 'jobhunt');
            $jc_strings['jobs_listings']['specialisms_title'] = esc_html__('Specialism', 'jobhunt');
            $jc_strings['jobs_listings']['specialisms_placeholder'] = esc_html__('Specialisms', 'jobhunt');
            $jc_strings['jobs_listings']['vacancy_title'] = esc_html__('Vacancy Type', 'jobhunt');
            $jc_strings['jobs_listings']['vacancy_placeholder'] = esc_html__('Vacancy Type', 'jobhunt');
            $jc_strings['jobs_listings']['dateposted_title'] = esc_html__('Date Posted', 'jobhunt');
            $jc_strings['jobs_listings']['search'] = esc_html__('Search Job', 'jobhunt');
            $jc_strings['jobs_listings']['clear'] = esc_html__('Clear Filters', 'jobhunt');
            
            //Job Detail Page
            $jc_strings['job_detail']['overview'] = esc_html__('Job Overview', 'jobhunt');
            $jc_strings['job_detail']['apply_btn_text'] = esc_html__('Apply For This Job', 'jobhunt');
            
            
            //My Jobs Listings
            $jc_strings['myjobs']['shortlisted_tab'] = esc_html__('Shortlisted', 'jobhunt');
            $jc_strings['myjobs']['applied_tab'] = esc_html__('Applied', 'jobhunt');
            $jc_strings['myjobs']['viewed_tab'] = esc_html__('Viewed', 'jobhunt');
            
            
            //CV Builder
            $jc_strings['cv_builder']['title'] = esc_html__('My CV', 'jobhunt');
            $jc_strings['cv_builder']['upload_text'] = esc_html__('Upload your CV', 'jobhunt');
            $jc_strings['cv_builder']['cover_title'] = esc_html__('Cover Letter', 'jobhunt');
            $jc_strings['cv_builder']['cover_placeholder'] = esc_html__('Type here', 'jobhunt');
            $jc_strings['cv_builder']['button_text'] = esc_html__('Save Setting', 'jobhunt');
            
            
            //Settings Page
            $jc_strings['settings']['title'] = esc_html__('Profile Settings', 'jobhunt');
            $jc_strings['settings']['personal_info'] = esc_html__('Personal Information', 'jobhunt');
            $jc_strings['settings']['account'] = esc_html__('Account / Password', 'jobhunt');
            $jc_strings['settings']['location'] = esc_html__('Location Settings', 'jobhunt');
            $jc_strings['settings']['logout'] = esc_html__('Log Out', 'jobhunt');
            $jc_strings['settings']['visibility_text'] = esc_html__('Turn on CV Visibility', 'jobhunt');
            $jc_strings['settings']['visibility_desc'] = esc_html__('If you would like to change the visibility of your resume for employers or Public', 'jobhunt');
            
            
            //Personal Info Page
            $jc_strings['presonal']['title'] = esc_html__('Edit Profile', 'jobhunt');
            $jc_strings['presonal']['full_nmae'] = esc_html__('Full Name', 'jobhunt');
            $jc_strings['presonal']['full_name_placeholder'] = esc_html__('Full Name', 'jobhunt');
            $jc_strings['presonal']['job_title'] = esc_html__('Job Title', 'jobhunt');
            $jc_strings['presonal']['job_title_placeholder'] = esc_html__('Job Title', 'jobhunt');
            $jc_strings['presonal']['minim_salary'] = esc_html__('Minimum Salary', 'jobhunt');
            $jc_strings['presonal']['minim_salary_placeholder'] = esc_html__('Minimum Salary', 'jobhunt');
            $jc_strings['presonal']['phone'] = esc_html__('Phone Number', 'jobhunt');
            $jc_strings['presonal']['phone_placeholder'] = esc_html__('Phone Number', 'jobhunt');
            $jc_strings['presonal']['specialisms'] = esc_html__('Specialisms', 'jobhunt');
            $jc_strings['presonal']['button_text'] = esc_html__('Save Setting', 'jobhunt');
            
            
            //Account Info Page
            $jc_strings['account']['title'] = esc_html__('Account Settings', 'jobhunt');
            $jc_strings['account']['email'] = esc_html__('Email Address', 'jobhunt');
            $jc_strings['account']['email_placeholder'] = esc_html__('Email Address', 'jobhunt');
            $jc_strings['account']['password'] = esc_html__('Password', 'jobhunt');
            $jc_strings['account']['password_placeholder'] = esc_html__('Password', 'jobhunt');
            $jc_strings['account']['confirm_pass'] = esc_html__('Confirm Password', 'jobhunt');
            $jc_strings['account']['confirm_pass_placeholder'] = esc_html__('Confirm Password', 'jobhunt');
            $jc_strings['account']['button_text'] = esc_html__('Save Setting', 'jobhunt');
            
            
            //Location Page
            $jc_strings['location']['title'] = esc_html__('Location Settings', 'jobhunt');
            $jc_strings['location']['country'] = esc_html__('Country', 'jobhunt');
            $jc_strings['location']['city'] = esc_html__('City', 'jobhunt');
            $jc_strings['location']['address'] = esc_html__('Complete Address', 'jobhunt');
            $jc_strings['location']['address_placeholder'] = esc_html__('Complete Address', 'jobhunt');
            $jc_strings['location']['button_text'] = esc_html__('Save Setting', 'jobhunt');
            
            
            //Employers Page
            $jc_strings['employers']['title'] = esc_html__('Employers', 'jobhunt');
            
            
            //Employer Detail Page
            $jc_strings['employer_detail']['overview'] = esc_html__('Company Overview', 'jobhunt');
            
            
            //Login Page
            $jc_strings['login']['title'] = esc_html__('Login', 'jobhunt');
            $jc_strings['login']['user_label'] = esc_html__('Username', 'jobhunt');
            $jc_strings['login']['user_placeholder'] = esc_html__('Username', 'jobhunt');
            $jc_strings['login']['pass_label'] = esc_html__('Password', 'jobhunt');
            $jc_strings['login']['pass_placeholder'] = esc_html__('Type Your Password', 'jobhunt');
            $jc_strings['login']['forgot_pass'] = esc_html__('Forgot Password?', 'jobhunt');
            $jc_strings['login']['sign_up'] = esc_html__('SIGN UP', 'jobhunt');
            $jc_strings['login']['sign_in'] = esc_html__('LOGIN', 'jobhunt');
            $jc_strings['login']['signup_txt'] = esc_html__("Don't have an account?", 'jobhunt');
            $jc_strings['login']['signup_link_txt'] = esc_html__('Sign Up', 'jobhunt');
            
            
            //Registration Page
            $jc_strings['register']['title'] = esc_html__('Registration', 'jobhunt');
            $jc_strings['register']['email_label'] = esc_html__('Email', 'jobhunt');
            $jc_strings['register']['email_placeholder'] = esc_html__('info@gmail.com', 'jobhunt');
            $jc_strings['register']['pass_label'] = esc_html__('Password', 'jobhunt');
            $jc_strings['register']['pass_placeholder'] = esc_html__('Type Your Password', 'jobhunt');
            $jc_strings['register']['firstname_label'] = esc_html__('First Name', 'jobhunt');
            $jc_strings['register']['firstname_placeholder'] = esc_html__('First Name', 'jobhunt');
            $jc_strings['register']['lastname_label'] = esc_html__('Last Name', 'jobhunt');
            $jc_strings['register']['lastname_placeholder'] = esc_html__('Last Name', 'jobhunt');
            $jc_strings['register']['register_btn_txt'] = esc_html__('REGISTER', 'jobhunt');
            $jc_strings['register']['login_txt'] = esc_html__('Did you have an account?', 'jobhunt');
            $jc_strings['register']['login_link_txt'] = esc_html__('Login', 'jobhunt');
            
            
            //Forgot Password Page
            $jc_strings['forgot']['title'] = esc_html__('Forgot Password', 'jobhunt');
            $jc_strings['forgot']['page_details'] = esc_html__('Enter your email address, we`ll send you the instruction on how to chnage your password', 'jobhunt');
            $jc_strings['forgot']['email_label'] = esc_html__('Email Address', 'jobhunt');
            $jc_strings['forgot']['email_placeholder'] = esc_html__('info@gmail.com', 'jobhunt');
            $jc_strings['forgot']['submit_btn_txt'] = esc_html__('SEND', 'jobhunt');
                        
            return $jc_strings;
        }

    }

}
$App_Configuration = new App_Configuration();
